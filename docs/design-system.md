# Elite Nail Studio — Design System

Demo brand: **Maison Élise — Luxury Nail Atelier**.
Direction: luxury nail atelier × editorial fashion magazine × modern beauty studio.
Warm, quiet, confident. Photography carries colour; the UI stays calm.

Source of truth in code: `assets/css/tokens.css` (all tokens) and `assets/css/main.css`
(components). No component hard-codes a colour — everything resolves to a token.

---

## 1. Principles

1. **Image first, UI second.** Large photography, generous margins, few boxes.
2. **Editorial type.** Big, light serif display; small, tracked sans labels. Italic serif for accents.
3. **Square edges, one signature curve.** Cards and images are square-cornered; the **arch**
   (`border-radius: 999px 999px 0 0`) is reserved for a few hero/portrait images. Buttons are pills.
4. **Motion has hierarchy.** One major reveal per section, supporting elements follow. Nothing bounces.
5. **Every colour from a token.** Palettes and section tones re-point tokens, never per selector.

---

## 2. Colour

### Palette — “Atelier” (default)

| Token | Hex | Role |
|---|---|---|
| `--ens-ivory` | `#F8F4F0` | page background |
| `--ens-porcelain` | `#FFFDFB` | raised surfaces, cards, inputs |
| `--ens-linen` | `#F1E6DF` | quiet alternate band |
| `--ens-nude` | `#E9D5CC` | soft panels, offset blocks, image placeholders |
| `--ens-rose` | `#C99091` | accent fills, rules, numerals, hover wash |
| `--ens-rose-ink` | `#8F585A` | small accent text on light (5.2 : 1 on ivory) |
| `--ens-champagne` | `#B99A72` | hairlines, stars, fine detail, accent on dark |
| `--ens-champagne-ink` | `#85683F` | champagne text on light (4.7 : 1) |
| `--ens-espresso` | `#271D1B` | primary text, dark bands, primary buttons |
| `--ens-cocoa` | `#5E4B45` | secondary text (7.5 : 1 on ivory) |
| `--ens-taupe` | `#6F5C56` | muted/meta text (5.7 : 1 on ivory) |

Global (tokens.css): `--ens-dark` (espresso), `--ens-overlay` (espresso @ 45 %), `--ens-on-dark`
(ivory), `--ens-on-dark-muted` (ivory @ 72 %), `--ens-line-on-dark` (ivory @ 22 %) — photography and
dark chrome (hero, CTA band, overlay menu, footer legal row). Helper slots: `--ens-on-rose` (label on
rose fills) and `--ens-rose-glow` (rose as text on dark).

### Palettes

Atelier is the fixed default and one of 44 EDS palettes (Appearance → Customize → *Maison Élise
Colours*). The other 43 come from `bin/palette-sources.json` (ink / depth / metal / mist / paper per
palette) and are derived into the slots above by `bin/palette-build.php`, which writes
`inc/palette-registry.php`. Atelier's slots are never derived.

Layers, lowest to highest: tokens.css fallbacks (Atelier) → saved palette, printed inline on `:root`
as `--ens-c-*` → demo preview (EDS Demo Palette Switcher re-points `--ens-c-*` on
`html[data-eds-demo-palette]`) → section tone (role tokens re-pointed on the section).

### Role tokens (what components use)

`--ens-bg`, `--ens-surface`, `--ens-surface-alt`, `--ens-soft`, `--ens-text`, `--ens-heading`,
`--ens-text-muted`, `--ens-text-subtle`, `--ens-accent` (fill / button wash), `--ens-on-accent`,
`--ens-accent-ink` (emphasis, links), `--ens-eyebrow`, `--ens-detail`, `--ens-strong` /
`--ens-on-strong` (buttons, active chips), `--ens-focus`, `--ens-line`, `--ens-line-strong`.
Each tone (light / dark / accent) maps them in `ens_tones()` (inc/palette.php); light is `:root`.

### Section tones and patterns

* `ens-sec-dark` and `ens-tone-dark` → dark tone; `ens-tone-light`, `ens-tone-accent` → the others.
* A section pattern (Original, Dark/Light, Light/Dark, Dark/Light/Accent, Light/Dark/Accent, Mostly
  light, Mostly dark) gives every top-level page container a tone by position. Photographic
  sections (ENS Hero, ENS CTA) are skipped; a container with an `ens-tone-*` class keeps its own.
  Original adds nothing. Palettes whose Accent fails the gate skip Accent in the cycle.

### Elementor

The theme never reads Elementor Global Colours. `tokens.css` re-points the kit's `ens_*` and system
colours to the theme tokens on the front end, and saving the Customizer writes the saved palette into
the kit, so the editor's swatches match. Edit colours in the Customizer, not in Site Settings.

### Gates

```
php bin/palette-build.php          rebuild the registry
php bin/palette-build.php --check  exit 1 if the registry is stale or a palette fails
php bin/contrast-gate.php [slug]   every tone of every palette (text 4.5:1, UI 3:1)
```

### Contrast rules

* Body text: `--ens-text` or `--ens-text-muted` only.
* Rose/champagne **fills** never carry ivory text — use espresso (6.2 : 1).
* Small accent text uses the `*-ink` variants.
* Focus ring: champagne-ink on light (≥ 3 : 1), champagne on dark.

---

## 3. Typography

Self-hosted (OFL) — no Google Fonts requests.

| Family | Use | Weights |
|---|---|---|
| **Cormorant Garamond** | display, headings, quotes, prices, numerals | 300, 400, 500 + italic 300/400 |
| **Jost** | body, UI, labels, nav, buttons | 300–600 (variable) |

Replaces the reference’s Cantata One / Heebo / Yesteryear trio. The script eyebrow is replaced by
a tracked sans label, and italic Cormorant provides the “handwritten” warmth inside headlines
(`<em>` in a heading renders italic, rose-ink).

### Fluid scale (`clamp`, 390 → 1440)

| Token | Size | LH | Tracking | Notes |
|---|---|---|---|---|
| `--ens-fs-display` | 3.25 → 9 rem | 0.9 | −0.02em | hero, uppercase, weight 300 |
| `--ens-fs-h1` | 2.75 → 5.5 rem | 0.98 | −0.015em | page heroes |
| `--ens-fs-h2` | 2.25 → 4 rem | 1.04 | −0.01em | section titles |
| `--ens-fs-h3` | 1.6 → 2.25 rem | 1.12 | 0 | card / block titles |
| `--ens-fs-h4` | 1.3 → 1.55 rem | 1.2 | 0 | list titles |
| `--ens-fs-lead` | 1.1 → 1.3 rem | 1.6 | 0 | intro paragraphs (Jost 300) |
| `--ens-fs-body` | 1 → 1.0625 rem | 1.7 | 0 | body |
| `--ens-fs-small` | 0.875 rem | 1.6 | 0 | meta |
| `--ens-fs-label` | 0.72 rem | 1.4 | 0.22em | eyebrows, nav, buttons — uppercase Jost 500 |

Measure: body copy max 62ch; lead max 52ch.

---

## 4. Spacing

4 px base, named steps:

| Token | px |
|---|---|
| `--ens-s-1` … `--ens-s-10` | 4, 8, 12, 16, 24, 32, 48, 64, 96, 128 |
| `--ens-section` | `clamp(72px, 5vw + 52px, 152px)` — vertical section padding |
| `--ens-section-sm` | `clamp(48px, 3vw + 36px, 96px)` |
| `--ens-gutter` | `clamp(20px, 4vw, 56px)` — page side padding |
| `--ens-gap` | `clamp(16px, 2vw, 32px)` — grid gap |

Vertical rhythm: eyebrow → 16 → heading → 24 → text → 40 → actions.

## 5. Containers

| Token | Value | Use |
|---|---|---|
| `--ens-container` | 1320 px | default boxed content |
| `--ens-container-wide` | 1560 px | galleries, lookbook |
| `--ens-container-narrow` | 760 px | centred copy, articles |

Elementor Kit “Content Width” is set to 1320 so native boxed containers match.

## 6. Radius

| Token | Value | Use |
|---|---|---|
| `--ens-radius-0` | 0 | images, cards, panels (default) |
| `--ens-radius-sm` | 2 px | inputs |
| `--ens-radius-pill` | 999 px | buttons, tags, filter chips |
| `--ens-radius-arch` | `999px 999px 0 0` | signature arch images (≤ 1 per section) |

## 7. Shadows

Used sparingly — luxury reads as flat + layered, not floating.

| Token | Value |
|---|---|
| `--ens-shadow-sm` | `0 1px 2px rgb(39 29 27 / .06)` |
| `--ens-shadow-card` | `0 24px 60px -32px rgb(39 29 27 / .28)` — overlapping cards only |
| `--ens-shadow-float` | `0 40px 80px -40px rgb(39 29 27 / .35)` — booking card, header on scroll |

## 8. Buttons

| Variant | Class | Look | Hover |
|---|---|---|---|
| Primary | `.ens-btn` | espresso pill, ivory label | rose wash rises from bottom, label → espresso |
| Outline | `.ens-btn--outline` | 1 px espresso border | fills espresso, label → ivory |
| Light | `.ens-btn--light` | ivory pill on dark/photo | rose wash |
| Link | `.ens-link` | label + 32 px rule | rule extends to 56 px |

Height 52 px (48 px mobile), padding 0 28 px, Jost 500 label size, tracking 0.18em.
Focus: 2 px champagne outline, 3 px offset. Native Elementor Button widgets get the same look
through the Kit button settings + `main.css`.

## 9. Cards & components

* **Service card** — 4:5 image, number (`01`) in Cormorant italic, title, one-line description,
  price “from”, link. Image zooms 1.06 on hover, `--ens-ease`, 1.2 s.
* **Overlap card** — porcelain panel, `--ens-shadow-card`, overlaps its image by 48–96 px.
* **Price row** — name … dotted leader … price; description + duration below. Serif price.
* **Team card** — 3:4 portrait; on hover the portrait desaturates the surround, zooms 1.04, and the
  info panel slides up 8 px; Instagram handle revealed.
* **Testimonial** — big italic Cormorant quote, name + treatment label; manual snap slider.
* **FAQ** — native `<details>`; hairline separators, + / − icon rotates.

## 10. Image ratios

| Slot type | Ratio |
|---|---|
| Home hero | 16:9 desktop / 4:5 crop on mobile (object-position controlled) |
| Page hero | 21:9 (min-height 62vh desktop, 58vh mobile) |
| Service card / sticky story | 4:5 |
| Portrait (team) | 3:4 |
| Lookbook | mixed 3:4 and 4:5 |
| Blog card | 3:2 |
| Gallery grid | 1:1 and 4:5 (masonry rhythm) |

## 11. Page system

Home · About · Services · Service detail (×6) · Pricing · Lookbook (gallery) · Artists (team) ·
Booking · FAQ · Journal (blog) · Single post · Contact · 404. Header and footer are global.

## 12. Motion

Engine: `assets/js/motion.js` — vanilla, IntersectionObserver for entrances, one passive
`scroll` listener batched through `requestAnimationFrame` for scroll-linked effects. Only
`transform`, `opacity` and `clip-path` are animated.

| Token | Value |
|---|---|
| `--ens-ease` | `cubic-bezier(.22, 1, .36, 1)` (expo-out) |
| `--ens-ease-in-out` | `cubic-bezier(.65, 0, .35, 1)` |
| `--ens-dur-1` | 0.35 s — hovers |
| `--ens-dur-2` | 0.9 s — fades / rises |
| `--ens-dur-3` | 1.4 s — mask reveals, split lines |
| `--ens-stagger` | 90 ms |

Classes (usable on any Elementor widget/container via *Advanced → CSS Classes*):

| Class | Effect |
|---|---|
| `ens-reveal` | fade + 24 px rise |
| `ens-mask` | image reveals bottom → top via clip-path, inner image settles from 1.12 scale |
| `ens-mask-x` | same, left → right |
| `ens-split` | heading lines rise from a mask, line by line |
| `ens-stagger` | direct children reveal sequentially |
| `ens-parallax` | image drifts ±6 % against scroll |

Widget-specific motion: hero cinematic zoom (CSS, 12 s), marquee (CSS), rotating badge (CSS,
24 s/turn), horizontal lookbook (scroll-linked transform), sticky story (IntersectionObserver
swaps active step), pricing rows stagger.

**Reduced motion:** with `prefers-reduced-motion: reduce` every class above resolves to its final
state with no transition; marquee and badge stop; lookbook becomes a native horizontal scroller;
sticky story stacks normally. Content never depends on JS: the `ens-js` class is added to `<html>`
by an inline script, and the hidden pre-reveal states only apply under `.ens-js`.

## 13. Responsive rules

Breakpoints match Elementor: **mobile ≤ 767**, **tablet ≤ 1024**, plus **≤ 480** for fine tuning.

* Header: full nav ≥ 1025; burger + overlay menu ≤ 1024. CTA button stays visible down to 480.
* Two-column splits stack at ≤ 1024 when either side has an image taller than 600 px; otherwise ≤ 767.
* Service grids: 3 → 2 (tablet) → horizontal snap rail (mobile) — no 3 000 px stacks.
* Lookbook: scroll-linked ≥ 1025; native swipe rail below.
* Sticky story: sticky ≥ 1025; image-above-text stack below.
* Display type never exceeds 3 lines on mobile; minimum tap target 44 px.
* No horizontal page scroll at any width (`overflow-x: clip` on `body` as a safety net, never
  as a fix for a known overflow).
