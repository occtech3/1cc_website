// One Christian Church: header, menu, search, background videos, scroll effects, preview mock forms.
document.documentElement.classList.add("js");

const body = document.body;
const reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
const store = {
  get(k) { try { return localStorage.getItem(k); } catch (e) { return null; } },
  set(k, v) { try { localStorage.setItem(k, v); } catch (e) { /* storage blocked */ } },
};

/* Header: solid after scrolling, hides on the way down, returns on the way up */
const header = document.querySelector("[data-header]");
let lastY = window.scrollY;
const onScroll = () => {
  const y = window.scrollY;
  if (header) {
    header.classList.toggle("is-scrolled", y > 30);
    header.classList.toggle("is-hidden", y > 400 && y > lastY && !body.classList.contains("menu-open"));
  }
  lastY = y;
};
window.addEventListener("scroll", onScroll, { passive: true });
onScroll();

/* Mobile menu */
const toggle = document.querySelector("[data-menu-toggle]");
if (toggle) {
  const menuIcon = toggle.innerHTML;
  const closeIcon = '<svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg>';
  const setOpen = (open) => {
    body.classList.toggle("menu-open", open);
    body.style.overflow = open ? "hidden" : "";
    toggle.setAttribute("aria-expanded", String(open));
    toggle.setAttribute("aria-label", open ? "Close menu" : "Open menu");
    toggle.innerHTML = open ? closeIcon : menuIcon;
  };
  toggle.addEventListener("click", () => setOpen(!body.classList.contains("menu-open")));
  document.addEventListener("keydown", (e) => { if (e.key === "Escape") setOpen(false); });
  document.querySelectorAll(".primary-nav a").forEach((a) => a.addEventListener("click", () => setOpen(false)));
}

/* Site search: every page's headings and text, fetched the first time search opens (built by build.py for the
   preview; WordPress builds it from the live pages, and each entry carries its page's address in "u").
   Enter goes to the top result; in WordPress, Enter with no match falls through to the normal site search. */
const search = document.querySelector("[data-search]");
if (search && typeof search.showModal === "function") {
  const form = search.querySelector("form");
  const input = search.querySelector("input");
  const quick = search.querySelector("[data-search-quick]");
  const status = search.querySelector("[data-search-status]");
  const list = search.querySelector("[data-search-results]");
  const pages = JSON.parse(search.dataset.searchPages || "{}");
  const native = "native" in search.dataset;
  let entries = null;
  let loading = null;
  const esc = (s) => s.replace(/[&<>"]/g, (c) => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;" })[c]);
  const fold = (s) => s.toLowerCase().replace(/[‘’]/g, "'");
  const load = () => loading || (loading = fetch(search.dataset.searchIndex)
    .then((r) => (r.ok ? r.json() : []))
    .catch(() => [])
    .then((data) => {
      entries = (Array.isArray(data) ? data : []).map((e) => ({ ...e, fh: fold(e.h || ""), fp: fold(e.p || ""), ft: fold(e.t || "") }));
      render();
    }));
  const find = (terms) => entries.map((e) => {
    let score = 0;
    for (const w of terms) {
      const s = (e.fh.includes(w) ? 6 : 0) + (e.fp.includes(w) ? 2 : 0) + (e.ft.includes(w) ? 1 : 0);
      if (!s) return null;
      score += s + (e.fh.startsWith(w) ? 2 : 0);
    }
    return { e, score };
  }).filter(Boolean).sort((a, b) => b.score - a.score).map((r) => r.e);
  const highlight = (s, re) => s.split(re).map((part, i) => (i % 2 ? `<mark>${esc(part)}</mark>` : esc(part))).join("");
  const snippet = (e, terms) => {
    const hits = terms.map((w) => e.ft.indexOf(w)).filter((n) => n >= 0);
    const start = hits.length ? Math.max(0, Math.min(...hits) - 50) : 0;
    return (start ? "…" : "") + e.t.slice(start, start + 150) + (start + 150 < e.t.length ? "…" : "");
  };
  const render = () => {
    const q = input.value.trim();
    quick.hidden = Boolean(q);
    list.innerHTML = "";
    if (!q) { status.textContent = ""; return; }
    if (!entries) { status.textContent = "Searching…"; return; }
    const terms = [...new Set(fold(q).split(/\s+/))];
    const found = find(terms);
    const re = new RegExp(`(${terms.map((w) => w.replace(/[.*+?^${}()|[\]\\]/g, "\\$&")).join("|")})`, "i");
    status.textContent = found.length
      ? `${found.length} result${found.length === 1 ? "" : "s"}`
      : `No matches for “${q}”.${native ? " Press Enter to search the whole site." : " Try “Sunday”, “kids” or “youth”."}`;
    list.innerHTML = found.slice(0, 8).map((e) => `<li><a href="${esc((e.u || pages[e.s] || "") + (e.a ? `#${e.a}` : ""))}">`
      + `<span class="sr-page">${esc(e.p)}</span><span class="sr-title">${highlight(e.h, re)}</span>`
      + (e.t ? `<span class="sr-snip">${highlight(snippet(e, terms), re)}</span>` : "") + "</a></li>").join("");
  };
  const open = () => {
    if (search.open) return;
    search.showModal();
    body.style.overflow = "hidden";
    input.select();
    load();
    render();
  };
  document.querySelectorAll("[data-search-open]").forEach((b) => b.addEventListener("click", open));
  document.addEventListener("keydown", (e) => {
    const typing = e.target.closest && e.target.closest("input, textarea, select, [contenteditable]");
    if ((e.key === "/" && !typing) || (e.key.toLowerCase() === "k" && (e.metaKey || e.ctrlKey))) { e.preventDefault(); open(); }
  });
  search.addEventListener("close", () => { body.style.overflow = ""; });
  search.addEventListener("click", (e) => {
    if (e.target === search || e.target.closest("[data-search-close], a")) search.close();
  });
  input.addEventListener("input", render);
  form.addEventListener("submit", (e) => {
    const first = list.querySelector("a");
    if (first) { e.preventDefault(); search.close(); window.location.href = first.href; }
    else if (!native || !input.value.trim()) e.preventDefault();
  });
  search.addEventListener("keydown", (e) => {
    if (e.key === "Escape") { e.preventDefault(); search.close(); return; }  // a search field would only clear itself
    if (e.key !== "ArrowDown" && e.key !== "ArrowUp") return;
    const links = [...search.querySelectorAll(".search-results a, .search-quick a")].filter((a) => a.offsetParent);
    if (!links.length) return;
    e.preventDefault();
    const i = links.indexOf(document.activeElement) + (e.key === "ArrowDown" ? 1 : -1);
    (i < 0 ? input : links[Math.min(i, links.length - 1)]).focus();
  });
}

/* Background videos: load when near the viewport, play only while visible, fade in once frames arrive.
   Story videos are driven by the story controller below. */
let videosPaused = reduceMotion || store.get("occ-video-paused") === "1";
const videos = [...document.querySelectorAll("video.bg-video")];
const load = (v) => {
  if (v.dataset.loaded) return;
  v.querySelectorAll("source[data-src]").forEach((s) => { s.src = s.dataset.src; });
  v.dataset.loaded = "1";
  v.load();
};
const tryPlay = (v) => {
  if (videosPaused) return;
  load(v);
  const p = v.play();
  if (p && p.catch) p.catch(() => {});
};
videos.forEach((v) => {
  v.muted = true;
  v.addEventListener("playing", () => v.classList.add("is-playing"));
  v.addEventListener("loadeddata", () => { if (videosPaused) v.classList.add("is-playing"); });
});

/* Every video spot cycles through all the clips: it starts on its own clip, then plays the others in turn */
const clips = (() => {
  try { return JSON.parse(document.getElementById("occ-videos").textContent).map((u) => new URL(u, location.href).href); } catch (e) { return []; }
})();
const shouldPlay = (v) => !videosPaused && v.dataset.visible && (!v.closest(".story") || v.classList.contains("is-active"));
videos.forEach((v) => {
  const source = v.querySelector("source");
  if (!source) return;
  const own = new URL(source.dataset.src || source.getAttribute("src"), location.href).href;
  const at = clips.indexOf(own);
  const list = at < 0 ? [own, ...clips] : [...clips.slice(at), ...clips.slice(0, at)];
  if (list.length < 2) return;
  let i = 0;
  v.loop = false;
  v.addEventListener("ended", () => {
    i = (i + 1) % list.length;
    v.classList.remove("is-playing");
    setTimeout(() => {
      source.src = list[i];
      v.dataset.loaded = "1";
      v.load();
      if (shouldPlay(v)) tryPlay(v);
    }, 700);
  });
});

const vio = new IntersectionObserver((entries) => entries.forEach((en) => {
  const v = en.target;
  v.dataset.visible = en.isIntersecting ? "1" : "";
  if (v.closest(".story")) return;
  if (en.isIntersecting) tryPlay(v); else v.pause();
}), { rootMargin: "200px 0px" });
videos.forEach((v) => vio.observe(v));

const toggles = document.querySelectorAll("[data-video-toggle]");
const playIcon = '<svg class="ico" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8 5v14l11-7z"/></svg>';
const pauseIcon = '<svg class="ico" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M7 5h4v14H7zM13 5h4v14h-4z"/></svg>';
const syncToggles = () => toggles.forEach((b) => {
  b.innerHTML = (videosPaused ? playIcon : pauseIcon) + `<span>${videosPaused ? "Play video" : "Pause video"}</span>`;
  b.setAttribute("aria-pressed", String(videosPaused));
});
toggles.forEach((b) => b.addEventListener("click", () => {
  videosPaused = !videosPaused;
  store.set("occ-video-paused", videosPaused ? "1" : "0");
  videos.forEach((v) => {
    if (videosPaused) v.pause();
    else if (v.dataset.visible && (!v.closest(".story") || v.classList.contains("is-active"))) tryPlay(v);
  });
  syncToggles();
}));
syncToggles();
if (videosPaused) videos.forEach(load);

/* Hero rotating words */
document.querySelectorAll(".rotator").forEach((el) => {
  const items = [...el.children];
  if (items.length < 2 || reduceMotion) return;
  let i = 0;
  setInterval(() => {
    const cur = items[i];
    cur.classList.remove("is-on");
    cur.classList.add("is-out");
    setTimeout(() => cur.classList.remove("is-out"), 700);
    i = (i + 1) % items.length;
    items[i].classList.add("is-on");
  }, 2600);
});

/* Reveal on scroll */
const rio = new IntersectionObserver((entries) => entries.forEach((en) => {
  if (en.isIntersecting) { en.target.classList.add("is-in"); rio.unobserve(en.target); }
}), { rootMargin: "0px 0px -10% 0px" });
// Text the editor keeps in a <p class="x-inline"> counts as a child of the box around it
const kids = (box) => [...box.children].flatMap((c) => (c.classList.contains("x-inline") ? [...c.children] : [c]));
document.querySelectorAll(".reveal").forEach((el) => {
  const box = el.parentElement && el.parentElement.classList.contains("x-inline") ? el.parentElement.parentElement : el.parentElement;
  const group = box ? kids(box).filter((c) => c.classList.contains("reveal")) : [];
  const idx = group.indexOf(el);
  if (idx > 0) el.style.transitionDelay = `${Math.min(idx, 5) * 90}ms`;
  rio.observe(el);
});

/* Statement: words light up as it scrolls through the viewport */
const fills = [...document.querySelectorAll(".statement")].map((el) => {
  const walk = (node) => {
    [...node.childNodes].forEach((n) => {
      if (n.nodeType === 3) {
        const frag = document.createDocumentFragment();
        n.textContent.split(/(\s+)/).forEach((part) => {
          if (!part) return;
          if (/^\s+$/.test(part)) { frag.appendChild(document.createTextNode(part)); return; }
          const s = document.createElement("span");
          s.className = "w";
          s.textContent = part;
          frag.appendChild(s);
        });
        n.replaceWith(frag);
      } else if (n.nodeType === 1) walk(n);
    });
  };
  walk(el);
  return { el, words: [...el.querySelectorAll(".w")] };
});

/* Story: a pinned section whose video and words change with scroll position */
const stories = [...document.querySelectorAll(".story")].map((el) => ({
  el,
  vids: [...el.querySelectorAll(".story-media video")],
  panels: [...el.querySelectorAll(".story-panel")],
  bars: [...el.querySelectorAll(".story-progress i")],
  active: -1,
}));
stories.forEach((s) => s.el.style.setProperty("--panels", String(s.panels.length || 1)));  // one screen of scrolling per panel
const setStory = (s, idx) => {
  if (idx === s.active) return;
  s.active = idx;
  s.el.dataset.active = String(idx);
  s.panels.forEach((p, i) => p.classList.toggle("is-active", i === idx));
  s.vids.forEach((v, i) => {
    v.classList.toggle("is-active", i === idx);
    if (i === idx) tryPlay(v);
    else if (Math.abs(i - idx) === 1) load(v);
    else v.pause();
  });
  s.vids.forEach((v, i) => { if (i !== idx) setTimeout(() => { if (s.active !== i) v.pause(); }, 1000); });
};

/* Parallax */
const parallax = [...document.querySelectorAll("[data-parallax]")];

let ticking = false;
const frame = () => {
  ticking = false;
  const vh = window.innerHeight;
  fills.forEach(({ el, words }) => {
    const r = el.getBoundingClientRect();
    const p = Math.min(1, Math.max(0, (vh * 0.85 - r.top) / (r.height + vh * 0.35)));
    const n = reduceMotion ? words.length : Math.round(p * words.length);
    words.forEach((w, i) => w.classList.toggle("on", i < n));
  });
  stories.forEach((s) => {
    const r = s.el.getBoundingClientRect();
    const total = s.el.offsetHeight - vh;
    const p = Math.min(0.9999, Math.max(0, -r.top / total));
    const n = s.panels.length;
    setStory(s, Math.floor(p * n));
    s.bars.forEach((b, i) => b.style.setProperty("--p", `${Math.min(100, Math.max(0, (p * n - i) * 100))}%`));
  });
  if (!reduceMotion) parallax.forEach((el) => {
    const r = el.parentElement.getBoundingClientRect();
    const speed = parseFloat(el.dataset.parallax) || 0.15;
    el.style.transform = `translate3d(0, ${((r.top + r.height / 2) - vh / 2) * -speed}px, 0) scale(1.12)`;
  });
};
const request = () => { if (!ticking) { ticking = true; requestAnimationFrame(frame); } };
window.addEventListener("scroll", request, { passive: true });
window.addEventListener("resize", request);
frame();

/* 3D logos: lean toward the pointer, and stop animating while off screen.
   The colour shimmer runs in the shared SVG gradients, so it pauses whenever no big logo is in view. */
const defs = document.querySelector(".occ-defs");
const shimmer = new Set();
const syncShimmer = () => {
  if (!defs || !defs.pauseAnimations) return;
  if (reduceMotion || !shimmer.size) defs.pauseAnimations(); else defs.unpauseAnimations();
};
const lio = new IntersectionObserver((entries) => entries.forEach((en) => {
  en.target.classList.toggle("is-off", !en.isIntersecting);
  if (en.target.matches(".logo3d:not(.logo3d-badge)")) {
    if (en.isIntersecting) shimmer.add(en.target); else shimmer.delete(en.target);
  }
  syncShimmer();
}), { rootMargin: "120px 0px" });
document.querySelectorAll(".logo3d, .drift").forEach((el) => lio.observe(el));
syncShimmer();
const tilters = [...document.querySelectorAll("[data-tilt]")];
if (tilters.length && !reduceMotion && window.matchMedia("(pointer: fine)").matches) {
  window.addEventListener("pointermove", (e) => {
    const x = e.clientX / window.innerWidth - 0.5;
    const y = e.clientY / window.innerHeight - 0.5;
    tilters.forEach((el) => {
      el.style.setProperty("--ry", `${(x * 34).toFixed(1)}deg`);
      el.style.setProperty("--rx", `${(y * -22).toFixed(1)}deg`);
    });
  }, { passive: true });
}

/* Carousels */
document.querySelectorAll(".carousel").forEach((c) => {
  const track = c.querySelector(".carousel-track");
  const step = () => (track.firstElementChild ? track.firstElementChild.getBoundingClientRect().width + 22 : 320);
  c.querySelectorAll("[data-dir]").forEach((b) => b.addEventListener("click", () => {
    track.scrollBy({ left: step() * Number(b.dataset.dir), behavior: reduceMotion ? "auto" : "smooth" });
  }));
});

/* Mock forms (real forms come from a form plugin in WordPress) */
document.querySelectorAll("[data-mock-form]").forEach((form) => {
  form.addEventListener("submit", (e) => {
    e.preventDefault();
    const ok = form.querySelector(".form-ok");
    if (ok) ok.hidden = false;
    form.reset();
  });
});

/* Preview pill */
const pill = document.querySelector("[data-preview-pill]");
if (pill) {
  if (store.get("occ-pill-closed") === "1") pill.remove();
  else pill.querySelector("button").addEventListener("click", () => { store.set("occ-pill-closed", "1"); pill.remove(); });
}
