// One Christian Church design preview: menu, sticky header, reveal-on-scroll, mock forms.
document.documentElement.classList.add("js");

const body = document.body;
const toggle = document.querySelector("[data-menu-toggle]");
if (toggle) {
  const setOpen = (open) => {
    body.classList.toggle("menu-open", open);
    toggle.setAttribute("aria-expanded", String(open));
    toggle.setAttribute("aria-label", open ? "Close menu" : "Open menu");
    toggle.innerHTML = open
      ? '<svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M6 6l12 12M18 6 6 18"/></svg>'
      : '<svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M4 7h16M4 12h16M4 17h16"/></svg>';
  };
  toggle.addEventListener("click", () => setOpen(!body.classList.contains("menu-open")));
  document.addEventListener("keydown", (e) => e.key === "Escape" && setOpen(false));
  document.addEventListener("click", (e) => {
    if (body.classList.contains("menu-open") && !e.target.closest("#primary-nav, [data-menu-toggle]")) setOpen(false);
  });
}

const header = document.querySelector("[data-header]");
const onScroll = () => header && header.classList.toggle("is-scrolled", window.scrollY > 8);
window.addEventListener("scroll", onScroll, { passive: true });
onScroll();

const io = "IntersectionObserver" in window
  ? new IntersectionObserver((entries) => entries.forEach((en) => {
      if (en.isIntersecting) { en.target.classList.add("is-in"); io.unobserve(en.target); }
    }), { rootMargin: "0px 0px -8% 0px" })
  : null;
document.querySelectorAll(".reveal").forEach((el, i) => {
  el.style.transitionDelay = `${(i % 4) * 70}ms`;
  io ? io.observe(el) : el.classList.add("is-in");
});

document.querySelectorAll("[data-mock-form]").forEach((form) => {
  form.addEventListener("submit", (e) => {
    e.preventDefault();
    const ok = form.querySelector(".form-ok");
    if (ok) ok.hidden = false;
    form.reset();
  });
});
