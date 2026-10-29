=== One Christian Church ===
Requires at least: 6.5
Tested up to: 7.2
Requires PHP: 7.4
License: GPLv2 or later

Theme for One Christian Church, Greenwood, Indiana. Every page is ordinary block-editor content that
staff can edit in WordPress. Appearance > Site guide (added by the theme) explains each part.

== Before installing on the live site ==

1. Take a full backup (files and database) and check it can be restored. Ask the host (Core Managed)
   for a restore point, or install on their staging copy first.
2. Install there, check every page and send a test message from both forms, then repeat on the live site.

== Installing ==

1. Appearance > Themes > Add New Theme > Upload Theme, choose one-christian-church.zip, Install Now, Activate.
2. Activation:
   * fills the pages Home, Plan a Visit, About, Ministries, Events, Watch & Listen, Give and Contact with the
     design. Existing pages with those addresses are reused (the current front page becomes Home); missing ones
     are created. Their previous content, template and Elementor setting are saved first.
   * creates the "Main menu" and "Search shortcuts" menus and the "Site footer" pattern.
   * adds the three leader photos to the Media Library.
   Other pages and posts are not touched. Old pages at other addresses (for example about-us, contact-us)
   stay published: set them to draft, or redirect them, once the new pages are approved.

== Editing ==

* Pages: Pages > Edit. Click any heading, paragraph, list, FAQ or photo to change it and Save. Coloured
  backgrounds, videos and icons are Custom HTML blocks, labelled "Design element", and [occ_...] codes.
  Each page's Excerpt (editor sidebar) is its search engine description.
  Edit these pages in the WordPress editor, not with "Edit with Elementor".
* Header menu and search shortcuts: Appearance > Menus.
* Promo strip, name next to the logo, "Plan a Visit" button: Appearance > Customize > Header.
* Logo, site title, browser icon: Appearance > Customize > Site Identity.
* Footer: Appearance > Site guide > Footer > Edit (a synced pattern, edited like a page).
* Background videos: upload MP4s in Media, then paste their URLs in Customize > Background videos.
  An empty slot shows the animated colour background. The "Design preview" badge is off by default.
* Back to the original design: Appearance > Site guide > "Reset to the design" for one page
  (the current version stays in the page's revisions).

== Forms ==

The Contact and Plan a Visit forms email the address in Customize > Forms (default onecc.office@gmail.com),
with Reply-To set to the sender, and every message is also kept under Messages in the dashboard.
Spam protection: a hidden field, a minimum time on the page and a limit of five messages per visitor
every ten minutes. If emails don't arrive, the host's mail setup is the usual cause: an SMTP plugin
(for example WP Mail SMTP) fixes it. To use a form plugin instead, replace the Shortcode block
[occ_form type="contact"] or [occ_form type="visit"] with the plugin's form block.

== Search ==

The magnifying glass in the header searches the headings and text of every published page. The index
is rebuilt whenever a page or the footer is saved. Pressing Enter with no match runs the WordPress search.

== Elementor ==

If Elementor stays active, leave these pages in the block editor. With Elementor Pro, a Theme Builder
header or footer set to show on the entire site replaces this theme's header and footer: set those
templates to draft (the Site guide warns when it finds one).

== Switching back ==

Activating another theme puts back each page's previous content, template and Elementor setting, and the
previous front page. Pages this theme created are set to draft. The theme's versions of the pages are kept,
so activating it again restores the edits made with it.
