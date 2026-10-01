# Reference Audit — Nayaka (Nail Salon & Beauty Care Elementor Template Kit)

Audited 2026-10-01 from the public demo
(`point.moxcreative.com/nayaka/template-kit/…`, loaded inside the ThemeForest preview frame)
using headless/headed Chrome over CDP. Measurements are rendered/computed values at
1440 / 1024 / 768 / 390 px viewports.

Used as a **visual and behavioural specification only**. No source files, images,
Elementor JSON, logo or copy were taken. Everything in Elite Nail Studio is rebuilt
independently.

---

## 1. Page / navigation map

| Destination | Reference path | Notes |
|---|---|---|
| Home | `/template-kit/homepage/` | 12 sections, 9 054 px tall @1440 |
| Services | `/template-kit/services/` | services grid reused from Home |
| Pricing plan | `/template-kit/pricing/` | under *Services* dropdown |
| Booking | `/template-kit/booking/` | under *Services* dropdown |
| Contact Us | `/template-kit/contact-us/` | form + Google Map + info cards |
| About Us | `/template-kit/about-us/` | under *Pages* dropdown |
| Team | `/template-kit/team/` | under *Pages* |
| Gallery | `/template-kit/gallery/` | filterable grid |
| FAQ | `/template-kit/faq/` | categories + accordion |
| Blog (archive) | `?elementor_library=nayaka-archive-blog` | Theme Builder archive |
| Single Post | `/2022/05/07/spring-makeup-tutorial…/` | Theme Builder single |
| Error 404 | `?elementor_library=nayaka-error-404` | Theme Builder 404 |

**Not present in the reference:** an individual service-detail page. Elite Nail Studio adds one
(six instances) — see design-system.md §11.

**Primary nav (desktop):** `HOME · SERVICES ▾ (Pricing plan, Booking) · CONTACT US · PAGES ▾
(About Us, Team, Gallery, FAQ, Blog, Single Post, Error 404)` — left-aligned, logo centred,
`BOOK NOW` button right.

Plugin dependencies observed: Elementor **Pro** (nav-menu, form, call-to-action, price-list,
price-table, testimonial-carousel, posts, theme builder) and **ElementsKit** (accordion).
Elite Nail Studio must not depend on either.

---

## 2. Global layout

| Token | Reference value |
|---|---|
| Main container | 1280 px inner (boxed) |
| Narrow container | 840 px (hero copy, testimonials), 720 px (CTA bands, page hero) |
| Section vertical padding | 112 px typical; 48–80 px on secondary bands |
| Grid gaps | ~20 px (service cards), 35 px (gallery), 2 px (decorative mosaics) |
| Card style | white, square corners, very soft `0 0 30px rgba(0,0,0,.05)` shadow |
| Corners | 0 everywhere (buttons, images, cards, inputs) |
| Overflow-x | none at any tested width |

### Header
* Two rows: utility bar (promo text left · phone + email right, 12–13 px) and main bar
  (105 px total). Transparent, white text, overlaid on the hero image.
* **Not sticky** — scrolls away (top = −1500 after scrolling 1500 px).
* Nav links: Heebo 500 12 px, uppercase, 2 px tracking; hover = 1 px underline that slides in
  (`0.3s cubic-bezier(.58,.3,.005,1)`). Dropdown: white panel, 30 px soft shadow, items 12 px
  uppercase, `13px 20px` padding.
* Mobile (≤767): utility bar keeps only the promo text, hamburger left, logo right, Book button
  hidden. Menu opens as a full-width white panel under the header with collapsible submenus.
* Tablet (768–1024): hamburger + centred logo + Book button.

### Footer
* Newsletter band (light grey `#F8F8F8`, decorative leaf shapes) — heading, name + email inputs,
  full-width submit.
* Footer bar: legal links left · logo centre · social icons right; copyright line below a hairline.
* A 6 px rose strip at the very bottom.

---

## 3. Typography

| Role | Family | Size @1440 / 1024 / 390 | Weight | Line-height | Tracking |
|---|---|---|---|---|---|
| H1 hero | Cantata One (serif) | 72 / – / – | 500 | 1.0 | −2 px |
| H2 section | Cantata One | 48 / 36 / 28 | 500 | 1.1 | 0 |
| H3 | Cantata One | 36 | 500 | 1.2 | 0 |
| H4 card title | Cantata One | 24 | 500 | 1.3 | 0 |
| Eyebrow | **Yesteryear** (script) | 24 / 21 / 18 | 400 | 1.5 | 0 |
| Body | Heebo (sans) | 16 | 400 | 1.5 | 0 |
| Small | Heebo | 14 / 13 | 400 | 1.6 | 0 |
| Label / nav | Heebo | 12 | 500 | – | 2 px, uppercase |
| Decorative numeral | Cantata One | 200 | 500 | 1.0 | −2 px |

Pattern: **serif display + neutral sans body + script eyebrow**. Heading sizes step down only at
the tablet breakpoint and again at mobile; body copy stays 16 px.

Weaknesses to improve: the script eyebrow reads dated; heading scale is timid (72 px hero on a
1440 canvas); body grey `#8E8987` on white is ~3.4:1 (fails WCAG AA for body text).

---

## 4. Colour (extracted, not reused)

| Global | Value | Use |
|---|---|---|
| primary | `#8E8987` | body text grey |
| secondary | `#5A6C70` | headings, dark slate overlay |
| accent pink | `#FDB1AA` | buttons, eyebrows, numerals |
| blush | `#FBF2EE` | icon discs, panels, button hover bg |
| light | `#F8F8F8` | newsletter band |
| hero overlay | slate @ ~50 % | over photography |

Character: cool slate + candy pink. Elite Nail Studio replaces this with a warm
ivory / nude / dusty-rose / espresso / champagne palette (design-system.md §2).

---

## 5. Page compositions (1440 px)

Heights in px; values in parentheses are 1024 / 768 / 390.

### Home (9 054 → 11 761 @390)
1. **Hero** 832 (680/680/630) — full-bleed photo, slate overlay, centred: script eyebrow →
   3-line H1 (72 px) → 1-line body → filled button. Copy column 840 px.
2. **Intro + booking card** 649 — left: eyebrow, H2, text, two counters (7K+ / 15+). Centre: tall
   portrait image (307×550, 0.56 ratio). Right: white **booking card overlapping the hero** by
   ~110 px (3-step form) with a decorative plant cut-out below. Strongest composition idea on the page.
3. **Two promo cards** 619 — 2-up image cards with overlay, eyebrow, H3, small button. Background
   zooms 1.2× and pans on hover (`.6s`).
4. **Services grid** 1 546 — centred intro, 3×2 cards: image (≈1.15 ratio) + small uppercase
   category + serif title + outlined “Book Now ↗” button. Cards stagger in (fadeInUp + delay 240/480 ms).
5. **Discount band** 542 — full-bleed photo, copy block right-aligned on the right half.
6. **Why choose us** 936 — left: eyebrow, H2, 3 icon-boxes (icon disc + title + text); right:
   large image (734×800) bleeding to the viewport edge, offset blush rectangle behind it,
   floating counter card overlapping its top-left corner.
7. **Testimonials** 632 — centred, serif quote text, small avatar + name, arrows + bullets,
   autoplay 5 s, loop.
8. **CTA band** 596 — full-bleed photo, centred copy (720 px).
9. **Pricing tables** 1 203 — 3 packages, middle highlighted.
10. **About mosaic** 874 — 4-up image strip with a white card overlapping its bottom edge holding
    centred eyebrow + H2 + text + button.
11. **Newsletter + footer** 536.

### Inner pages — shared pattern
* **Page hero** 535–578 (352 @ tablet/mobile): full-bleed photo + slate overlay, centred serif H1
  (72 px) and a serif subtitle (≈32 px). Copy column 720 px.
* Body sections reuse homepage modules; newsletter + footer close every page.

| Page | Sections after hero |
|---|---|
| About | split intro (H2 left / 2 text columns right) → value band with **two cards overlapping the band’s bottom edge** → Why-choose-us → image mosaic + overlapping card |
| Services | intro split (copy left, two tall image cards right) → services grid → why-choose-us → testimonials → discount band |
| Pricing | 2 promo cards → **price list** (dotted leader between name and price, small description under each; image right) → two price lists side by side → discount band → pricing tables → CTA band |
| Gallery | filter tabs (ALL / categories, uppercase 12 px, underline on active) → 4-col grid 4:3, 35 px gap, lightbox → CTA band → latest posts (4-up full-width) |
| Team | centred intro → 2×2 **portrait + overlapping white info card** (portrait 354×472, 3:4; card overlaps ~35 px and drops 60 px) → founder message (portrait left over blush panel, signature) → promo cards → FAQ |
| Booking | split: copy left / form card right (2-col fields: name, phone, date, time, email, technician, message) → discount band → FAQ (heading left, accordion right) |
| FAQ | categories list left / accordion right → latest posts |
| Contact | image left + form card right → full-width map (450) → 3-up info card (location / call / email) |
| Blog archive | 3-col cards: square image, serif title, date + comments meta, excerpt |
| Single post | hero with eyebrow category + title + author · date → **content card overlapping the hero** (840 px) → share buttons → author box → comments → related posts |
| 404 | full hero with framed box: “Page not found”, short text, back button |

---

## 6. Interaction & motion

| Element | Behaviour |
|---|---|
| Buttons | `transition: .3s`; hover swaps pink → blush bg, slate text |
| Nav | underline pointer slide, `.3s cubic-bezier(.58,.3,.005,1)` |
| Promo/CTA cards | bg image pre-scaled 1.2, translates on hover; overlay `.6s` |
| Entrance | Elementor animate.css: fadeInUp / Left / Right / Down, zoomIn, bounceIn, bounceInRight — **1.25 s ease**; per-card delays 240 / 480 ms |
| Testimonials | Swiper, 500 ms slide, autoplay 5 s, loop, pause on hover |
| Gallery | filter tabs, fade-in overlay on hover, lightbox |
| Accordion | ElementsKit; first item open on blush background, others grey panels, chevron |
| Counters | count-up on enter |
| Sticky / parallax | none |
| Reduced motion | not handled — `prefers-reduced-motion` ignored |

Observed weaknesses: bounce animations feel cheap; everything uses the same fade; no motion
hierarchy; hero is static.

---

## 7. Responsive behaviour

| Width | Behaviour |
|---|---|
| 1024 | Same layouts compressed; H2 48 → 36; hero 832 → 680; service grid stays 3-col; booking card still overlaps |
| 768 | Hamburger; utility bar loses contact items; most 2-col splits remain 2-col; 3-col service grid kept |
| 390 | Everything single column; counters stay 2-up; service cards full width (very long page: 11 761 px); H2 28 px; icon-boxes switch to centred stacked layout; promo cards become tall (~375 px) |

No horizontal overflow at any width. Mobile simply stacks — the page becomes long and repetitive
(six full-width service cards ≈ 3 000 px). Elite Nail Studio uses horizontal snap rails and
condensed lists on mobile instead (design-system.md §10).

---

## 8. What we keep vs. improve

**Keep (composition ideas):**
* Image-led hero with centred editorial headline.
* Booking card overlapping the hero edge.
* Large image bleeding to the viewport edge with offset colour block + floating stat card.
* Portrait + overlapping info card for team members.
* Price list with dotted leaders.
* Content card overlapping the single-post hero.
* Image mosaic with an overlapping centred copy card.
* Newsletter closing every page.

**Improve:**
* Warm luxury palette instead of slate + candy pink; AA-compliant text contrast.
* Bigger, lighter editorial display type; italic serif accents replace the script font.
* Sticky, condensing header with an overlay mobile menu.
* Restrained, hierarchical motion (mask reveals, split lines, stagger) with full
  reduced-motion support — no bounces.
* Mobile-specific patterns (snap rails, compact lists) rather than raw stacking.
* No Pro / third-party widget dependencies: everything editable in Elementor free.
