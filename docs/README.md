# Elite Nail Studio — Theme Documentation

Luxury nail-atelier template for WordPress + Elementor (free). Demo brand: **Maison Élise**.

| Doc | Contents |
|---|---|
| `design-system.md` | Tokens, type scale, spacing, components, motion and responsive rules |

Production documents (image roadmaps, reference research) and the placeholder generator live in
the source repository only and are not part of the distributed theme.

---

## 1. Architecture

```
Hello Elementor (parent, untouched)
└── elite-nail-studio (child theme)
    ├── functions.php          bootstrap, enqueue, Hello overrides, header-mode detection
    ├── header.php / footer.php global header (PHP, menu + Customizer) / footer (Elementor template)
    ├── template-parts/         archive, single, 404, journal card (overrides Hello's parts)
    ├── inc/options.php         Customizer "Studio Details", icons, badge + template helpers
    ├── inc/forms.php           form handler (booking, quick booking, contact, newsletter)
    ├── inc/elementor.php       widget category, self-hosted font group, widget auto-registration
    ├── inc/widgets/            15 "ENS" Elementor widgets (one class per file)
    ├── assets/css/tokens.css   design tokens — the only file with raw colour values
    ├── assets/css/main.css     all components (sections numbered in the header comment)
    ├── assets/js/motion.js     motion + interaction engine (vanilla, ~9 KB)
    ├── assets/fonts/           Cormorant Garamond + Jost variable woff2 (OFL)
    └── demo/                   seed command, image slot registry, placeholder generator
```

**Requirements:** WordPress 6.5+, PHP 8.0+, Elementor (free) 3.20+ with Flexbox Containers.
No Elementor Pro and no third-party add-ons.

### Who controls what

| Layer | Edited in | Examples |
|---|---|---|
| Page content | Elementor | every heading, paragraph, image, price, artist, FAQ, CTA |
| Global footer | Elementor → Templates → *Global Footer* (`ens-footer`) | newsletter, columns, wordmark |
| 404 page | Elementor → Templates → *404 Page* (`ens-404`) | hero copy, suggested treatments |
| Navigation | Appearance → Menus (*Primary*, *Footer legal*) | |
| Studio details | Appearance → Customize → *Studio Details* | announcement bar, phone, email, address, hours, Instagram, header button |
| Brand name | Settings → General (Site Title / Tagline) or a Custom Logo | wordmark in header |
| Colours & fonts | Elementor → Site Settings → Global Colors / Fonts | flows into tokens.css (see §3) |
| Blog | Posts (block editor) + featured images | Journal archive & single templates |

The header stays in PHP on purpose: its scroll behaviour, transparent-over-hero mode and overlay
menu are theme behaviour, not content. Everything a salon owner changes week to week is in
Elementor or the Customizer.

---

## 2. Widgets (Elementor panel → *Elite Nail Studio*)

| Widget | Use |
|---|---|
| ENS Hero | `home` full-screen cinematic hero, `page` banner, `split` service-detail hero |
| ENS Services Grid | treatment cards; swipe rail on mobile |
| ENS Editorial Split | layered image composition + numbered points (or text-only when the image is removed) |
| ENS Sticky Story | pinned image swapped by scrolling steps |
| ENS Horizontal Lookbook | scroll-driven horizontal gallery |
| ENS Filter Gallery | masonry + category filter + Elementor lightbox |
| ENS Price List | menu rows with dotted leaders |
| ENS Team | artist portraits with overlapping cards |
| ENS Testimonials | editorial quote slider (no autoplay) |
| ENS FAQ | `<details>` accordion + FAQPage schema |
| ENS Form | booking / quick booking / contact / newsletter |
| ENS Journal Posts | latest posts |
| ENS CTA Band | parallax image band |
| ENS Marquee | moving typography |
| ENS Rotating Badge | circular "book" badge |

Native widgets (Heading, Text Editor, Button, Image, Counter, Icon List) are styled by the theme.

### Helper classes (Advanced → CSS Classes)

| On | Class | Effect |
|---|---|---|
| any widget | `ens-reveal` · `ens-mask` · `ens-mask-x` · `ens-split` · `ens-stagger` · `ens-parallax` | motion (design-system §12) |
| Heading | `ens-eyebrow` | tracked label with rule |
| Heading | `ens-display` | hero-size uppercase display |
| Text Editor | `ens-lead` | large light intro paragraph |
| Button | `ens-btn-outline` · `ens-btn-light` | button variants |
| Image | `ens-arch` | arch-shaped crop |
| Container | `ens-sec-dark` · `ens-sec-linen` · `ens-sec-nude` · `ens-sec-porcelain` | section backgrounds |
| Container | `ens-flush` · `ens-tight` · `ens-flush-top` | section padding variants |
| Container | `ens-center` · `ens-narrow` | centred / narrow content |
| Container | `ens-sticky-col` | sticky column (desktop) |
| Container (wrapping an ENS Hero) | `ens-hero-calm` | stronger left/top scrim for busy hero photography |
| Container | `ens-overlap-up` · `ens-first-tablet` | pull a card up over the previous section / move first when stacked |

Top-level containers get section padding (`--ens-section`) and side gutters automatically; set
padding in Elementor to override per section.

---

## 3. Colour architecture

`tokens.css` defines every colour as `--ens-*`, each reading the matching Elementor global first:

```css
--ens-rose: var(--e-global-color-ens_rose, #C99091);
```

The demo seeds those globals into the Elementor Kit, so editing *Dusty Rose* in Site Settings
recolours buttons, eyebrows, header, footer, widgets and motion accents at once. Components only
use semantic aliases (`--ens-text`, `--ens-accent`, `--ens-surface`…), never raw values.

**New palette:** add a `[data-ens-palette="name"] { … }` block in tokens.css and return `name` from
the `ens_palette` filter (sets `data-ens-palette` on `<html>`).

---

## 4. Motion & accessibility

* Entrances use IntersectionObserver plus a scroll-settle sweep (so fast flings never leave content
  hidden). Scroll-linked effects (header, lookbook, parallax) share one passive listener batched in
  `requestAnimationFrame`. Only `transform`, `opacity`, `clip-path` animate.
* Hidden pre-reveal states exist only when JS has run **and** the visitor has not requested reduced
  motion; in the Elementor editor they are disabled so nothing is hidden while editing.
* `prefers-reduced-motion: reduce`: no entrance animations, marquee becomes static wrapped text,
  badge stops, lookbook becomes a swipe rail, sticky story stacks, hero zoom off.
* Overlay menu: `aria-expanded`, Escape closes and returns focus, closes on link click.
* FAQ uses native `<details>`; slider is keyboard-operable (arrow keys); forms have labels,
  `autocomplete` hints and a status region.

---

## 5. Forms

ENS Form posts to `admin-post.php?action=ens_form` (nonce + honeypot + sanitisation), emails the
studio address (Customizer email, falling back to the admin email while the demo `.example`
address is set), then redirects back with a status message shown on the submitting form only.
There is no submissions database — add a CPT store or an SMTP plugin if the studio needs one.

---

## 6. Demo content

```bash
wp ens seed --images=<dir>             # kit globals, media, pages, footer/404 templates, posts, menus
```

`<dir>` holds one `ens-<slot>.jpg` per slot listed in `demo/images.json` (the demo photography pack).

The seed is idempotent (matches by slug / filename) and can be re-run after edits to
`demo/seed.php`; it overwrites the demo pages' Elementor data.

**Replacing placeholders with photography:** upload final images over the `ens-<slot>.jpg`
attachments (same filenames) or swap them in Elementor. Every slot (page, section, ratio,
resolution, subject) is listed in `demo/images.json`.

---

## 7. Team development

The Git repository is the theme folder only — never the WordPress root. Clone it into a local
WordPress install (with Hello Elementor and Elementor present) at:

```
wp-content/themes/elite-nail-studio
```

e.g. `git clone <repo-url> wp-content/themes/elite-nail-studio`, activate the theme, then build the
demo with the commands in §6. WordPress core, uploads, database dumps and `wp-config.php` stay
outside the repo; `.gitignore` also excludes OS/editor files, logs, generated placeholders and QA
artifacts. Placeholder images are regenerated, not committed.

## 8. Development notes

* Asset versions use file mtime, so CSS/JS edits are never served stale.
* Elementor 4 loads per-widget CSS after theme CSS; theme overrides of native widget internals use
  one extra level of specificity (e.g. `body .elementor-widget-counter …`).
* `ponytail:` comments mark deliberate simplifications with their upgrade path.
