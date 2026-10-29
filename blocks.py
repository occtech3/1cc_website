"""Turn the page sources into WordPress block markup, so every page can be edited in the block editor.

The same normalised HTML feeds both builds, so the static preview and the WordPress pages render alike:
  - a card that is one big link (<a class="promo" href>…</a>) becomes a <div> whose heading carries the link,
    stretched over the card by CSS (.is-linked, .card-link)
  - text that isn't in a paragraph, heading or list item (a chip, a button row, a date) goes inside a
    <p class="x-inline">, which CSS makes layout-transparent: a lone inline element among blocks gets its own,
    and a container holding only inline content puts all of it in one
Then each element maps to a core block:
  p → paragraph, h1–h6 → heading, ul/ol → list, blockquote → quote, details → details, a div holding one
  image → image, any other container → group (same tag, class and id), a form placeholder → shortcode.
  Decorative parts with no text (backgrounds, videos, icons, controls) and anything marked data-block="html"
  stay as Custom HTML blocks.
Placeholders become shortcodes ({{icon:arrow}} → [occ_icon name="arrow"]); {{url:…}} and {{asset:…}} stay
as tokens that the theme fills in with this site's addresses when it creates the pages.
"""
import html
import json
import re
from html.parser import HTMLParser

VOID = {"area", "br", "col", "embed", "hr", "img", "input", "link", "meta", "source", "track", "wbr"}
INLINE = {"a", "abbr", "b", "br", "cite", "code", "em", "i", "mark", "s", "small", "span", "strong", "sub", "sup", "time", "u"}
CONTAINERS = {"div", "section", "article", "header", "footer", "aside", "main", "nav", "figure"}
HEADINGS = {"h1", "h2", "h3", "h4", "h5", "h6"}
TOKEN = re.compile(r"\{\{[^{}]+\}\}")
FORM = re.compile(r"\{\{form:\w+\}\}")
DROPPED_ATTRS = {"aria-labelledby"}  # fine to lose: the heading inside still names the section


class Node:
    def __init__(self, tag, attrs=None):
        self.tag, self.attrs, self.children = tag, dict(attrs or {}), []

    def classes(self):
        return (self.attrs.get("class") or "").split()


class _Tree(HTMLParser):
    def __init__(self):
        super().__init__(convert_charrefs=False)
        self.root = Node("root")
        self.stack = [self.root]

    def handle_starttag(self, tag, attrs):
        node = Node(tag, attrs)
        self.stack[-1].children.append(node)
        if tag not in VOID:
            self.stack.append(node)

    def handle_startendtag(self, tag, attrs):
        node = Node(tag, attrs)
        node.selfclose = True
        self.stack[-1].children.append(node)

    def handle_endtag(self, tag):
        for i in range(len(self.stack) - 1, 0, -1):
            if self.stack[i].tag == tag:
                del self.stack[i:]
                return

    def handle_data(self, data):
        self.stack[-1].children.append(data)

    def handle_entityref(self, name):
        self.stack[-1].children.append(f"&{name};")

    def handle_charref(self, name):
        self.stack[-1].children.append(f"&#{name};")


def parse(text: str) -> Node:
    t = _Tree()
    t.feed(text)
    t.close()
    return t.root


def attrs_html(attrs: dict) -> str:
    out = []
    for k, v in attrs.items():
        out.append(f" {k}" if v is None else f' {k}="{html.escape(v, quote=True).replace("&amp;", "&")}"')
    return "".join(out)


def outer(node) -> str:
    if isinstance(node, str):
        return node
    if node.tag == "root":
        return inner(node)
    if node.tag in VOID or getattr(node, "selfclose", False):
        return f"<{node.tag}{attrs_html(node.attrs)}>" if node.tag in VOID else f"<{node.tag}{attrs_html(node.attrs)}/>"
    return f"<{node.tag}{attrs_html(node.attrs)}>{inner(node)}</{node.tag}>"


def inner(node) -> str:
    return "".join(outer(c) for c in node.children)


def text_of(node) -> str:
    """Visible words, without placeholders."""
    if isinstance(node, str):
        return TOKEN.sub("", node)
    if node.tag in ("svg", "script", "style", "video", "iframe"):
        return ""
    return "".join(text_of(c) for c in node.children)


def has_text(node) -> bool:
    return bool(html.unescape(text_of(node)).strip())


def inline_only(node) -> bool:
    for c in node.children:
        if isinstance(c, str):
            continue
        if c.tag not in INLINE or not inline_only(c):
            return False
    return True


def decorative(node) -> bool:
    return (isinstance(node, Node) and (
        node.attrs.get("aria-hidden") == "true" or node.attrs.get("data-block") == "html" or node.tag in ("button", "svg", "iframe", "video")
        or not has_text(node)))


# ---------------------------------------------------------------- normalise (shared by both builds)

def normalise(root: Node) -> Node:
    _link_cards(root)
    _wrap_inline(root)
    return root


def _link_cards(node):
    for i, c in enumerate(node.children):
        if not isinstance(c, Node):
            continue
        if c.tag == "a" and not inline_only(c) and not decorative(c):
            link = {k: v for k, v in c.attrs.items() if k in ("href", "target", "rel")}
            heading = _first(c, lambda n: n.tag in HEADINGS)
            if heading is None:
                raise ValueError(f"link card without a heading: {outer(c)[:120]}")
            a = Node("a", {"class": "card-link", **link})
            a.children, heading.children = heading.children, [a]
            div = Node("div", {k: v for k, v in c.attrs.items() if k not in link})
            div.attrs["class"] = " ".join(div.classes() + ["is-linked"])
            div.children = c.children
            node.children[i] = c = div
        _link_cards(c)


def _first(node, test):
    for c in node.children:
        if isinstance(c, Node):
            if test(c):
                return c
            found = _first(c, test)
            if found is not None:
                return found
    return None


def _wrap_inline(node):
    """Text that sits among blocks goes inside a layout-transparent paragraph."""
    if node.tag in ("p", "li", "summary", "cite") or node.tag in HEADINGS:
        return
    kids = [c for c in node.children if not (isinstance(c, str) and not c.strip())]
    if node.tag in CONTAINERS and kids and inline_only(node) and has_text(node) and not decorative(node) and not _single_img(node):
        p = Node("p", {"class": "x-inline"})
        p.children, node.children = node.children, [p]
        return
    if node.tag not in ("ul", "ol"):
        for i, c in enumerate(node.children):
            if isinstance(c, Node) and not decorative(c) and c.tag in INLINE and not (node.tag == "blockquote" and c.tag == "cite"):
                p = Node("p", {"class": "x-inline"})
                p.children = [c]
                node.children[i] = p
    for c in node.children:
        if isinstance(c, Node) and not decorative(c):
            _wrap_inline(c)


def _single_img(node):
    kids = [c for c in node.children if not (isinstance(c, str) and not c.strip())]
    return len(kids) == 1 and isinstance(kids[0], Node) and kids[0].tag == "img"


# ---------------------------------------------------------------- block markup

SHORTCODES = [
    (re.compile(r"\{\{icon:([\w-]+):lg\}\}"), r'[occ_icon name="\1" size="lg"]'),
    (re.compile(r"\{\{icon:([\w-]+)\}\}"), r'[occ_icon name="\1"]'),
    (re.compile(r"\{\{video:([\w-]+):eager\}\}"), r'[occ_video slot="\1" eager="1"]'),
    (re.compile(r"\{\{video:([\w-]+)\}\}"), r'[occ_video slot="\1"]'),
    (re.compile(r"\{\{logo3d:(\w+)\}\}"), r'[occ_logo3d kind="\1"]'),
    (re.compile(r"\{\{form:(\w+)\}\}"), r'[occ_form type="\1"]'),
    (re.compile(r"\{\{ring:([^{}\"\]]+)\}\}"), r'[occ_ring text="\1"]'),
    (re.compile(r"\{\{map\}\}"), "[occ_map]"),
    (re.compile(r"\{\{aurora\}\}"), "[occ_aurora]"),
    (re.compile(r"\{\{wave:top\}\}"), '[occ_wave top="1"]'),
    (re.compile(r"\{\{wave:([\w-]+)\}\}"), r'[occ_wave color="\1"]'),
    (re.compile(r"\{\{wave\}\}"), "[occ_wave]"),
    (re.compile(r"\{\{mark\}\}"), "[occ_mark]"),
    (re.compile(r"\{\{logo\}\}"), "[occ_logo]"),
]


def shortcodes(text: str) -> str:
    for pattern, repl in SHORTCODES:
        text = pattern.sub(repl, text)
    left = [t for t in TOKEN.findall(text) if not t.startswith(("{{url:", "{{asset:"))]
    if left:
        raise ValueError(f"no block form for {left}")
    return text


class Unmappable(ValueError):
    pass


def _comment(name, attrs):
    return f"<!-- wp:{name} {json.dumps(attrs, ensure_ascii=False, separators=(',', ':'))} -->" if attrs else f"<!-- wp:{name} -->"


def _text(node) -> str:
    """Rich text as the editor would save it: no line breaks or indentation from the source."""
    return re.sub(r"\s*\n\s*", " ", inner(node)).strip()


def _cls(node, drop=()):
    return " ".join(c for c in node.classes() if c not in drop)


INLINE_ATTRS = {"class", "href", "target", "rel", "aria-hidden", "aria-label"}


def _check_inline(node):
    for c in node.children:
        if isinstance(c, Node):
            extra = [k for k in c.attrs if k not in INLINE_ATTRS]
            if extra:
                raise Unmappable(f"<{c.tag}> inside text has {extra}: {outer(c)[:140]}")
            _check_inline(c)


def _check_attrs(node, allowed):
    extra = [k for k in node.attrs if k not in allowed and k not in DROPPED_ATTRS and k != "data-block"]
    if extra:
        raise Unmappable(f"<{node.tag}> has {extra}: {outer(node)[:140]}")


def _open(node, base_class, extra=()):
    classes = " ".join(x for x in [base_class, _cls(node)] if x)
    attrs = {}
    if node.attrs.get("id"):
        attrs["id"] = node.attrs["id"]
    if classes:
        attrs["class"] = classes
    for k in extra:
        if k in node.attrs:
            attrs[k] = node.attrs[k]
    return f"<{node.tag}{attrs_html(attrs)}>"


def to_blocks(root: Node) -> str:
    return "\n\n".join(_blocks(root.children))


def _blocks(children) -> list:
    out, html_run = [], []

    def flush():
        if html_run:
            content = "".join(html_run).strip()
            if FORM.fullmatch(content):
                out.append(f"<!-- wp:shortcode -->\n{content}\n<!-- /wp:shortcode -->")
            elif content:
                out.append(f"<!-- wp:html -->\n{content}\n<!-- /wp:html -->")
            html_run.clear()

    for c in children:
        if isinstance(c, str):
            if not c.strip():
                html_run.append(c) if html_run else None
                continue
            if has_text(c):
                raise Unmappable(f"loose text among blocks: {c.strip()[:80]!r}")
            html_run.append(c)  # a placeholder on its own: part of the decorative run
            continue
        if decorative(c) and not (c.tag in CONTAINERS and _single_img(c)):
            node = Node(c.tag, {k: v for k, v in c.attrs.items() if k != "data-block"})
            node.children = c.children
            html_run.append(outer(node))
            continue
        flush()
        out.append(_block(c))
    flush()
    return out


def _block(node) -> str:
    tag = node.tag
    if tag == "p":
        if not inline_only(node):
            raise Unmappable(f"<p> with blocks inside: {outer(node)[:120]}")
        _check_attrs(node, {"class", "id"})
        _check_inline(node)
        attrs = {"className": _cls(node)} if node.classes() else {}
        return f"{_comment('paragraph', attrs)}\n{_open(node, '')}{_text(node)}</p>\n<!-- /wp:paragraph -->"
    if tag in HEADINGS:
        if not inline_only(node):
            raise Unmappable(f"<{tag}> with blocks inside: {outer(node)[:120]}")
        _check_attrs(node, {"class", "id"})
        _check_inline(node)
        attrs = {}
        if tag != "h2":
            attrs["level"] = int(tag[1])
        if node.classes():
            attrs["className"] = _cls(node)
        if node.attrs.get("id"):
            attrs["anchor"] = node.attrs["id"]
        return f"{_comment('heading', attrs)}\n{_open(node, 'wp-block-heading')}{_text(node)}</{tag}>\n<!-- /wp:heading -->"
    if tag in ("ul", "ol"):
        _check_attrs(node, {"class", "id"})
        attrs = {}
        if tag == "ol":
            attrs["ordered"] = True
        if node.classes():
            attrs["className"] = _cls(node)
        items = []
        for li in node.children:
            if isinstance(li, str):
                if li.strip():
                    raise Unmappable(f"text between list items: {li.strip()[:60]!r}")
                continue
            if li.tag != "li" or not inline_only(li):
                raise Unmappable(f"list item that isn't plain text: {outer(li)[:120]}")
            _check_attrs(li, {"class"})
            _check_inline(li)
            la = {"className": _cls(li)} if li.classes() else {}
            items.append(f"{_comment('list-item', la)}\n{_open(li, '')}{_text(li)}</li>\n<!-- /wp:list-item -->")
        return f"{_comment('list', attrs)}\n{_open(node, 'wp-block-list')}{''.join(items)}</{tag}>\n<!-- /wp:list -->"
    if tag == "blockquote":
        _check_attrs(node, {"class", "id"})
        attrs = {"className": _cls(node)} if node.classes() else {}
        parts, cite, loose = [], "", []
        for c in node.children:
            if isinstance(c, Node) and c.tag == "cite":
                cite = _text(c)
            elif isinstance(c, Node) and c.tag == "p":
                parts.append(_block(c))
            elif isinstance(c, str) and not c.strip():
                continue
            else:
                loose.append(outer(c))
        if loose:
            parts.insert(0, f"<!-- wp:paragraph -->\n<p>{''.join(loose).strip()}</p>\n<!-- /wp:paragraph -->")
        cite_html = f"<cite>{cite}</cite>" if cite else ""
        return f"{_comment('quote', attrs)}\n{_open(node, 'wp-block-quote')}{''.join(parts)}{cite_html}</blockquote>\n<!-- /wp:quote -->"
    if tag == "details":
        _check_attrs(node, {"class", "id", "open"})
        summary = next(c for c in node.children if isinstance(c, Node) and c.tag == "summary")
        attrs = {}
        if "open" in node.attrs:
            attrs["showContent"] = True
        if node.classes():
            attrs["className"] = _cls(node)
        rest = [c for c in node.children if c is not summary]
        body = "".join(_blocks(rest))
        return (f"{_comment('details', attrs)}\n{_open(node, 'wp-block-details', ('open',))}<summary>{_text(summary)}</summary>"
                f"{body}</details>\n<!-- /wp:details -->")
    if tag in CONTAINERS and _single_img(node):
        img = next(c for c in node.children if isinstance(c, Node))
        _check_attrs(node, {"class", "aria-hidden"})
        attrs = {"className": _cls(node)} if node.classes() else {}
        src, alt = img.attrs.get("src", ""), img.attrs.get("alt", "")
        return (f"{_comment('image', attrs)}\n<figure class=\"{' '.join(['wp-block-image'] + node.classes())}\">"
                f"<img src=\"{src}\" alt=\"{html.escape(alt)}\"/></figure>\n<!-- /wp:image -->")
    if tag in CONTAINERS:
        _check_attrs(node, {"class", "id"})
        attrs = {} if tag == "div" else {"tagName": tag}
        if node.classes():
            attrs["className"] = _cls(node)
        if node.attrs.get("id"):
            attrs["anchor"] = node.attrs["id"]  # the editor keeps a Group's anchor in its comment too
        body = "\n\n".join(_blocks(node.children))
        return f"{_comment('group', attrs)}\n{_open(node, 'wp-block-group')}{body}</{tag}>\n<!-- /wp:group -->"
    raise Unmappable(f"no block for <{tag}>: {outer(node)[:140]}")


def page_blocks(text: str) -> tuple[str, str]:
    """(normalised HTML for the static build, block markup for WordPress)"""
    root = normalise(parse(text))
    static = outer(root)
    return static, shortcodes(to_blocks(root))
