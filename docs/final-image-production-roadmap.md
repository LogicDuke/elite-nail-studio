# Final Image Production Roadmap — Maison Élise

Production plan for all **52** image slots of the Elite Nail Studio demo.
Source of truth for slot list, ratios and subjects: `docs/image-roadmap.md` (generated from
`demo/images.json`). This document adds art direction, continuity and per-slot production specs.

Goal: the whole website must read as **one real atelier, photographed over two or three days by
one team** — the same rooms, the same people, the same light, the same nail looks recurring.

Contents
1. The visual world of Maison Élise
2. Cast (recurring people)
3. Sets (recurring rooms / surfaces)
4. Lighting setups
5. Nail look library
6. Global rules — delivery, must-not-appear, AI/retouch QA
7. Production batches (overview)
8. Sharing matrix — model / station / room / lighting
9. Per-slot specifications (52)
10. Slots needing special attention

---

## 1. The visual world of Maison Élise

A small, appointment-only nail atelier in an old arcade. Parisian-calm, architectural, warm.
It should feel like a beautiful room where precise work happens — not a spa cliché, not a
beauty-supply shop.

| Element | Specification |
|---|---|
| **Walls** | Limewash plaster. Main colour *Linen* (≈ #EFE6DF, warm off-white). One feature wall in *Rose Plaster* (≈ #E3CFC6) behind the colour wall and the portrait area. Soft, cloudy limewash texture must be visible — never flat paint. |
| **Architecture** | Arched plaster niches and one tall arched mirror (the arch echoes the website’s arch motif). Tall window wall with sheer raw-linen curtains. Pale oak herringbone floor. Plain plaster ceiling. |
| **Manicure tables** | Custom: honed **travertine** top (cream with soft beige veining, matte, rounded 10 mm edge) on a fluted **oiled walnut** base. Each table holds: one brass task lamp, one ivory ceramic dappen dish, one linen hand cushion in oatmeal, a small brass tray of tools. Nothing else. |
| **Chairs** | Guest: ivory **bouclé** tub chair with walnut legs. Artist: walnut stool with oatmeal linen seat. Pedicure / ritual: deep reclining bouclé lounge chair with travertine footbath basin. |
| **Lighting fixtures** | Brushed-brass articulated **task lamps** with opal-glass shades (one per station). Opal **globe pendants** in a row over the stations. Plaster wall **sconces** (half-moon). No visible track lights, no ring lights, no fluorescent tubes. |
| **Nail lamp** | Minimal ivory/white curved LED lamp, no visible brand, no purple glow (lamp switched off or shot with warm LEDs only). |
| **Flowers / decor** | Base: dried pampas and bleached ruscus in tall ivory ceramic vessels. Hero moments: blush **peonies** or garden roses (single variety, single colour). Accents: stacked linen-bound art books, ivory ceramics, brass trays, one sculptural travertine bowl. Never mixed multicolour bouquets, never artificial flowers. |
| **Textiles** | Raw linen in oatmeal and ivory: towels, hand cushions, napkins. Tone-on-tone **“ME” monogram** embroidered on towels (small, rarely legible). |
| **Uniform** | Artists: collarless wrap tunic in **oatmeal linen** (≈ #E9D5CC), three-quarter sleeves, worn over an ivory crew-neck. Optional espresso linen apron for pedicure/ritual. Hair tied back, minimal brushed-gold jewellery (one ring max on hands, no rings on working fingers). Artists’ own nails: short, clean, sheer or milky. |
| **Polish display** | The **colour wall**: ~140 identical **house bottles** — square glass, ivory caps, no labels — on slim brushed-brass ledges against the Rose Plaster wall, arranged as a gradient (ivory → nude → rose → berry → espresso). |
| **Tools** | Glass files, orangewood pushers, stainless instruments with brushed-brass details in sealed sterilisation pouches, fine art brushes with walnut handles, glass droppers of golden cuticle oil. |
| **Hospitality** | Espresso in ivory ceramic cups; still water in clear glass; tea in an ivory teapot. |
| **Branding treatment** | Quiet. “MAISON ÉLISE” appears only as: brushed-brass lettering above the facade door, blind-embossed on the reception card holder, tone-on-tone on towels. **No other legible text anywhere.** Paper (menu cards, appointment book, magazine) is blank or out of focus. |

### Palette in photography

Images should sit inside the site tokens: ivory `#F8F4F0`, linen `#F1E6DF`, nude `#E9D5CC`,
dusty rose `#C99091`, champagne `#B99A72`, espresso `#271D1B`. Colour comes from skin, polish and
flowers; environments stay neutral-warm. Saturated colour (lookbook only) must still harmonise —
deep berry, olive, burgundy, chrome — never primary red/blue/green.

---

## 2. Cast

| Code | Person | Description | Appears in |
|---|---|---|---|
| **M-ÉLISE** | Élise Marchand — founder & creative director | Woman, early–mid 40s, warm olive/light-medium skin, dark brown hair in a low chignon, calm direct gaze. Portrait: ivory tailored blazer over ivory knit. At work: oatmeal tunic. Single brushed-gold ring (left middle finger). | team-1, founder, service-manicure (technician hands) |
| **M-INÈS** | Inès Laurent — senior nail artist | Woman, late 20s, fair skin with freckles, auburn hair in a low bun, fine-line tattoo-free. Oatmeal tunic. Thin gold band on right ring finger (non-working). Holds a walnut fine-art brush in portrait. | team-2, team-hero, service-gel / service-art / story-1 / story-2 (technician hands) |
| **M-MAYA** | Maya Okafor — extensions specialist | Woman, early 30s, deep brown skin, short natural coils, small gold hoops. Oatmeal tunic. | team-3, team-hero, service-extensions (technician hands) |
| **M-SOFIA** | Sofia Reyes — pedicure & wellness | Woman, mid 30s, light-medium warm skin, dark wavy hair in a low ponytail, espresso linen apron over tunic. | team-4, service-pedicure, service-ritual (technician hands) |
| **H1 / G1** | “Camille” — the house guest | Woman, early 30s, light-medium skin with golden undertone, slender hands, medium nail beds suited to almond shape. **She is the face of the brand’s hands.** Wardrobe: ivory or oatmeal cashmere knit, a fine gold chain bracelet (right wrist), no rings except where specified. | home-hero, home-hero-detail, story-1/2/3, service-manicure (client), service-gel (client), service-ritual (client), lookbook-1, -5, -7, post-2, post-4 (left hand), journal-hero, gallery-hero (one hand), booking-side |
| **H2** | Hand model 2 | Woman, deep brown skin with neutral undertone, long fingers, strong nail beds (ideal for length and art). Wardrobe: espresso or camel knit, brushed-gold signet ring (left little finger). | service-art (client), service-extensions (client), lookbook-2, -4, -8, -11, post-4 (right hand), post-5, gallery-hero (one hand) |
| **H3** | Hand model 3 | Woman, fair skin with cool-pink undertone, graceful hands. Wardrobe: ivory silk, pearl stud jewellery, slim engagement ring (bridal only). | lookbook-3, -6, -10, post-3, gallery-hero (one hand) |
| **F1** | Feet model | Same person as H1 / Camille where scheduling allows (keeps skin tone continuity). | service-pedicure, lookbook-9 |

Casting rules: all hands photographed with **real, healthy skin texture**, moisturised, no
redness at cuticles, no visible veins from cold, no tattoos, no hand jewellery beyond what is
listed. Keep each model’s jewellery identical across all their images.

---

## 3. Sets

| Code | Set | Built from | Used by |
|---|---|---|---|
| **S1** | **Atelier floor** — window wall with three manicure stations, pendant row, linen curtains | §1 tables, bouclé chairs, pampas vessels | home-intro, services-hero, team-hero, about-studio (mirror corner), cta-band (at dusk) |
| **S2** | **Signature station** — the front station, closest to the window; the hero table | One travertine/walnut table, brass lamp, hand cushion, dappen dish, peonies (hero days only) | home-hero, home-hero-detail, story-1, story-2, story-3, service-manicure, service-gel, service-art, service-extensions, post-2, post-4, gallery-hero |
| **S3** | **Ritual lounge** — curtained alcove with reclining bouclé chair and travertine footbath | Plaster sconce, linen curtains, tray of oils | service-pedicure, service-ritual, booking-side, post-6 |
| **S4** | **Reception & colour wall** — fluted walnut reception desk, Rose Plaster wall with brass bottle ledges | 140 house bottles, appointment book (blank), peonies | booking-hero, pricing-hero, faq-hero |
| **S5** | **Arcade exterior** — ivory canvas awning, espresso-painted timber shopfront, brass lettering, stone arcade | Exterior location (or façade build) | about-hero, contact-hero, contact-side |
| **S6** | **Still-life corner** — travertine slab, raw linen, ivory ceramic, espresso velvet; shot in the atelier’s back room by the same window | Surfaces, house bottles, tools, flowers | home-intro-detail, about-detail, pricing-side, journal-hero, post-1, post-5, notfound, lookbook-12, lookbook-11 |
| **S7** | **Portrait wall** — section of Rose Plaster limewash wall with an arched niche edge, 2 m deep from subject | Wall + floor bounce | team-1, team-2, team-3, team-4, founder (variant: seated at S2) |
| **S8** | **Lookbook surfaces** — modular backgrounds used only for lookbook hands: ivory ceramic, travertine, espresso velvet, ivory satin, linen sheets | Portable surfaces lit like S6 | lookbook-1 … -10 |

---

## 4. Lighting setups

| Code | Name | Recipe | Used by |
|---|---|---|---|
| **L1** | Atelier daylight | Large soft window light from camera-left (north-facing feel), ~5200 K, negative fill opposite, practicals off or barely on. Gentle falloff, open shadows. | Interiors S1/S4, exterior day S5, booking-hero, services-hero, team-hero, about-hero, contact-side, home-intro, about-studio |
| **L2** | Golden hour | Low warm sun ~3800 K raking through linen curtains, soft long shadows, warm bounce from travertine. Campaign mood. | home-hero, home-hero-detail, story-3, gallery-hero, journal-hero, lookbook-1/3/6 |
| **L3** | Atelier evening | Blue-hour ambient through windows + all practicals on at 2700 K (task lamps, sconces, pendants). | cta-band, contact-hero |
| **L4** | Macro soft | Large diffused softbox overhead-left, white bounce right, black flag shaping **one clean rectangular specular highlight per nail**. ~5000 K. | All nail macros, flat lays, service close-ups, lookbook-2/4/5/7/8/10/11/12, posts |
| **L5** | Portrait | Window key at 45°, background ½ stop under, subtle warm rim. Same white balance for all four artists. | team-1–4, founder |

Rule: **never mix colour temperatures within a frame** except L3 (intentional dusk). One white
balance per batch; grade all batches to the same LUT (warm-neutral, lifted blacks to espresso, no
teal shadows).

---

## 5. Nail look library

| Code | Look | Shape / length | Finish | Used by |
|---|---|---|---|---|
| **N1** | **Milk & Champagne** (house signature) | Medium almond | Milky sheer nude, fine champagne-chrome tip line | home-hero, story-3, lookbook-1 variant (French), post-2 |
| **N2** | Rose-nude glass | Medium almond | Sheer rose-nude, glass-like gloss | home-hero-detail, service-manicure (finish), post-4 (left) |
| **N3** | Deep rose / black cherry | Medium oval | Rich deep-rose gel, mirror gloss | service-gel, post-1 swatch |
| **N4** | Gold line | Short-medium almond | Nude base, single hand-painted brushed-gold line | service-art, post-5 (dot variant) |
| **N5** | Sculpted milky pink | Long almond | Milky pink builder gel, thin free edge | service-extensions, post-4 (right) |
| **N6** | Nude pedicure | Short square | Sheer nude | service-pedicure, lookbook-9 |
| **N7** | Bare / prepared | Natural | Clean, oiled, no colour | story-1, story-2, service-ritual |

Lookbook-specific looks are defined per slot (§9 Batch 6). All looks: perfect cuticle line,
symmetrical shapes across all five fingers, no flooding, no bubbles, no lint.

---

## 6. Global rules

### Delivery
* Filename **exactly** `ens-<slot>.jpg` (drop-in replacement for the Media Library attachment).
* sRGB, 8-bit JPEG, quality 82, progressive. Keep layered masters (TIFF/PSD) separately.
* Strip GPS/location EXIF. No watermarks, no signatures, no borders.
* Deliver at the final dimensions listed (§9). Where this plan raises resolution above
  `image-roadmap.md`, the **ratio is unchanged** — only pixel count increases.

### Must not appear (every image)
* Any third-party brand, logo or legible text (bottles, lamps, cups, magazines, phones, clothing).
* Neon or saturated primary colours; rainbow sets; glitter overload; oversized rhinestones; charms.
* Press-on / plastic tips, visibly fake nails, chipped, lifted or flooded polish.
* Red, damaged or over-cut cuticles; nail dust, filings, cotton fibres, cords, cables, packaging.
* Blue/purple nitrile gloves, face masks, clinical paper towels, plastic cups, plastic bottles.
* Purple UV glow, ring-light reflections, phone screens, cluttered stations.
* Stock-style smiling at camera (exception: team portraits — warm, closed-mouth or soft smile).
* Real shops, real street signs, real addresses, recognisable landmarks.

### AI generation / retouch QA (mandatory per image)
* Count fingers and nails (5 per hand), check knuckles, joints and thumb orientation.
* Nails must be the same shape and length across the hand; no fused nails or extra cuticles.
* Bottles: identical house shape, caps aligned, no warped glass, no pseudo-text.
* Faces (team/guests): symmetrical eyes, natural teeth, consistent identity across slots.
* Recurring set check: travertine veining, walnut tone, brass finish match the reference stills.
* Lock reference frames on day 1 (one per set, one per cast member, one per nail look) and compare
  every subsequent image against them.

---

## 7. Production batches

| # | Batch | Slots | Count | Main sets | Main light |
|---|---|---|---|---|---|
| 1 | Hero / campaign | home-hero, home-hero-detail, gallery-hero, cta-band | 4 | S2, S1 | L2, L3 |
| 2 | Salon interior | home-intro, about-studio, services-hero, team-hero, booking-hero, pricing-hero, about-hero, contact-hero, contact-side | 9 | S1, S4, S5 | L1, L3 |
| 3 | Treatments | service-manicure, service-gel, service-art, service-extensions, service-pedicure, service-ritual, story-1, story-2 | 8 | S2, S3 | L4 + L1 |
| 4 | Nail macro / still life | home-intro-detail, about-detail, pricing-side, faq-hero | 4 | S6, S4 | L4 |
| 5 | Artists / team | team-1, team-2, team-3, team-4, founder | 5 | S7, S2 | L5 |
| 6 | Lookbook | lookbook-1 … lookbook-12 | 12 | S8, S6 | L2, L4 |
| 7 | Editorial / lifestyle | story-3, booking-side, journal-hero | 3 | S2, S3, S6 | L2 |
| 8 | Blog / supporting | post-1 … post-6, notfound | 7 | S6, S2, S3 | L4, L2 |
| | **Total** | | **52** | | |

Suggested shoot order (minimises resets): **Day 1** S5 exterior + S1/S4 interiors (Batch 2,
cta-band at dusk) → **Day 2** S2 station: treatments, story, hero, editorial (Batches 1, 3, 7) →
**Day 3** S7 portraits + S8/S6 lookbook, still life, blog (Batches 4, 5, 6, 8).

---

## 8. Sharing matrix

### Same model
| Model | Slots |
|---|---|
| H1 / G1 Camille | home-hero, home-hero-detail, story-1, story-2, story-3, service-manicure, service-gel, service-ritual, lookbook-1, lookbook-5, lookbook-7, post-2, post-4 (L), journal-hero, gallery-hero, booking-side (+ F1: service-pedicure, lookbook-9) |
| H2 | service-art, service-extensions, lookbook-2, lookbook-4, lookbook-8, lookbook-11, post-4 (R), post-5, gallery-hero |
| H3 | lookbook-3, lookbook-6, lookbook-10, post-3, gallery-hero |
| M-ÉLISE | team-1, founder, service-manicure (hands) |
| M-INÈS | team-2, team-hero, story-1, story-2, service-gel, service-art (hands) |
| M-MAYA | team-3, team-hero, service-extensions (hands) |
| M-SOFIA | team-4, service-pedicure, service-ritual (hands) |

### Same manicure station (S2)
home-hero, home-hero-detail, story-1, story-2, story-3, service-manicure, service-gel, service-art,
service-extensions, gallery-hero, post-2, post-4 — and founder (seated at S2).

### Same room
* **S1 atelier floor:** home-intro, services-hero, team-hero, about-studio, cta-band
* **S3 ritual lounge:** service-pedicure, service-ritual, booking-side, post-6
* **S4 reception & colour wall:** booking-hero, pricing-hero, faq-hero (+ swatch ring in story-1)
* **S5 exterior:** about-hero, contact-hero, contact-side
* **S6 still-life corner:** home-intro-detail, about-detail, pricing-side, journal-hero, post-1, post-5, notfound, lookbook-11, lookbook-12
* **S7 portrait wall:** team-1 … team-4

### Same lighting setup
* **L1:** home-intro, about-studio, services-hero, team-hero, booking-hero, pricing-hero, about-hero, contact-side, booking-side
* **L2:** home-hero, home-hero-detail, gallery-hero, story-3, journal-hero, lookbook-1, -3, -6, post-3
* **L3:** cta-band, contact-hero
* **L4:** all of Batch 3 close-ups, Batch 4, lookbook-2, -4, -5, -7, -8, -9, -10, -11, -12, post-1, -2, -4, -5, -6, notfound
* **L5:** team-1 … team-4, founder

---

## 9. Per-slot specifications

How the site uses each image (overlays, crops) is stated under **Layout**. “Safe zone” = where
the key subject must sit so it survives every crop. Percentages are of image width (x) and height
(y), origin top-left.

Shared layout facts:
* **Page heroes** (`*-hero` except home, plus journal-hero, notfound) render full-bleed ≈ 21:9 on
  desktop with copy **bottom-left** (x 4–50 %, y 45–92 %) over a dark gradient; on a 390 px phone
  only the **central ~32 % of the width** is visible (x 34–66 %). → Key subject at **x 45–66 %,
  y 15–50 %**; left-bottom quadrant calm and mid-dark.
* **Service images** render as 4:5 cards (ivory numeral “01” top-left) **and** as the arch-topped
  split hero on their treatment page (arch removes the top corners — top 25 % is cut to a
  semicircle). → Subject in **x 20–80 %, y 30–90 %**, top-left 18 % mid-tone for the ivory numeral.
* **Lookbook 1–6** render twice: in the home rail at **alternating 3:4 / 4:5 crops** (rail item 1
  and 4 are forced to 3:4, items 2, 3, 5, 6 to 4:5) and in the Lookbook gallery at their natural
  ratio. → Keep ≥ 7 % calm margin top and bottom.
* **Posts** render as 3:2 cards **and** as the full-bleed single-post hero (≈ 2.3:1 desktop crop,
  ≈ 0.75:1 phone crop) with the title bottom-centre and the article card overlapping the bottom.
  → Subject **x 35–65 %, y 15–50 %**; bottom 40 % calm.

---

### Batch 1 — Hero / campaign

#### `ens-home-hero.jpg` — Home · Hero
* **Final dimensions / ratio:** 2880 × 1620 · 16:9 · landscape · Category: campaign hero
* **Subject:** Camille’s (H1) hand resting on the S2 travertine table edge, N1 Milk & Champagne nails, cashmere sleeve cuff visible.
* **Camera:** 50 mm-equivalent, eye-level with the table plane, slight 10° downward tilt; shallow depth (f/2.8), nails pin-sharp.
* **Composition:** Hand enters from the right edge; **nails cluster at x 50–68 %, y 60–80 %**. Left 45 % is calm travertine and soft window bokeh.
* **Lighting:** L2 golden hour from camera-left, warm bounce under the hand; one soft specular per nail.
* **Palette:** travertine cream, ivory knit, warm skin, champagne glints, espresso shadows at frame edges.
* **Continuity:** S2 table (travertine veining must match), brass lamp base just out of focus top-right, blush peony petal on table optional.
* **Model / wardrobe:** H1, ivory cashmere knit, gold chain bracelet.
* **Nails:** N1.
* **Negative space:** Headline + text + buttons occupy **x 4–45 %, y 25–90 %** — keep this area low-detail and mid-dark after the site gradient. The arch inset covers **x 66–83 %, y 30–64 %** (desktop) and the rotating badge **x 88–97 %, y 78–95 %** — keep nails out of both.
* **Mobile crop:** Phone shows x ≈ 49–75 % (focal point set at 62 %): nails at x 50–68 % stay in frame.
* **Must not appear:** rings, lamp glare, visible table edge horizon tilt, text.

#### `ens-home-hero-detail.jpg` — Home · Hero arch inset
* **Final dimensions / ratio:** 1200 × 1500 · 4:5 · portrait · Category: campaign macro
* **Subject:** Single N2 rose-nude nail touching a faceted crystal perfume stopper (unbranded, no bottle label).
* **Camera:** 100 mm macro, 30° above, f/4.
* **Composition:** Fingertip and stopper centred at x 40–60 %, **y 40–75 %** (top 30 % disappears into the arch curve).
* **Lighting:** L2 warm key, crystal catching a small warm caustic.
* **Palette:** rose-nude, champagne, ivory background blur.
* **Continuity:** Same hand and session as home-hero (H1, S2 window light).
* **Negative space:** Top corners are cut by the arch — keep empty.
* **Mobile crop:** Hidden on ≤ 1024 px.
* **Must not appear:** perfume branding, fingerprints on crystal.

#### `ens-gallery-hero.jpg` — Lookbook · Page hero
* **Final dimensions / ratio:** 2880 × 1234 · 21:9 · landscape · Category: campaign
* **Subject:** Three hands stacked gently, one above the other — H3 (pearl bridal, lookbook-6 look), H1 (Milk & Honey French, lookbook-1 look), H2 (glazed pearl chrome, lookbook-2 look).
* **Camera:** 85 mm, frontal, slightly above; f/5.6 so all three sets are sharp.
* **Composition:** Hands stack at **x 46–66 %, y 18–55 %**; wide calm margins; ivory satin and linen.
* **Lighting:** L2, soft.
* **Palette:** ivory satin, three skin tones, pearl, chrome, milky nude.
* **Continuity:** Nail sets must be identical to lookbook-1, -2, -6.
* **Model / wardrobe:** H1, H2, H3 with their standard jewellery; sleeves ivory / camel / ivory silk.
* **Negative space:** Page-hero rule (bottom-left copy).
* **Mobile crop:** Central 32 % — the stack must fit in x 34–66 %.
* **Must not appear:** more than three hands, overlapping fingers that read as extra digits.

#### `ens-cta-band.jpg` — Home / About / Treatments / Pricing / Lookbook / Artists / FAQ / Service pages · Booking CTA band
* **Final dimensions / ratio:** 2880 × 1234 · 21:9 · landscape · Category: campaign atmosphere
* **Subject:** The atelier at dusk: S1/S2 table with the brass lamp on, tools aligned, empty bouclé chair, peonies, window blue hour beyond.
* **Camera:** 35 mm, seated eye-level, f/2.8, softly focused (atmosphere, not detail).
* **Composition:** **Centre must be calm** (headline, text and button sit dead-centre). Lamp glow and chair at **x 55–70 %**, table edge across the lower third, window blue on the left.
* **Lighting:** L3 atelier evening.
* **Palette:** espresso, warm amber lamp pools, dusty blue-grey window, ivory bouclé.
* **Continuity:** Same S1 furniture as home-intro and services-hero.
* **Negative space:** Centre x 30–70 %, y 30–75 % low-contrast; overlaid at 45 % espresso.
* **Mobile crop:** Image is rendered at ≈ 0.45:1 on phones (parallax + tall band) — only **x 40–60 %** remains. The lamp glow should sit near x 55 % so phones still get one warm light source.
* **Must not appear:** people, readable objects, harsh lamp hotspots.

---

### Batch 2 — Salon interior

#### `ens-home-intro.jpg` — Home + Treatments · Editorial split (main)
* **Final dimensions / ratio:** 1600 × 2000 · 4:5 · portrait · Category: interior
* **Subject:** S1 atelier: two bouclé chairs at a walnut/travertine table, linen curtains, pampas vessel, pendant above.
* **Camera:** 35 mm, eye-level, one-point perspective, verticals perfectly straight.
* **Composition:** Table in the lower third, centred **x 30–70 %**, pendant top-centre.
* **Lighting:** L1.
* **Palette:** linen walls, ivory bouclé, walnut, travertine, brass.
* **Continuity:** Master reference for S1.
* **Negative space / overlays:** Used **mirrored**: Home places a stat card over **top-right (x 75–100 %, y 9–22 %)** and a detail image over **bottom-right (x 68–100 %, y 70–100 %)** with an arch-cropped top; Treatments places them **top-left / bottom-left** with a square top. → Corners must be quiet; arch-safe top 25 %.
* **Mobile crop:** Full 4:5 at ~85 % width.
* **Must not appear:** people, clutter on table, visible cables.

#### `ens-about-studio.jpg` — About · Story split (main, arch)
* **Final dimensions / ratio:** 1600 × 2000 · 4:5 · Category: interior detail
* **Subject:** S1 corner: tall arched mirror on limewash wall, linen curtain edge, dried florals in ivory vessel on a travertine plinth.
* **Camera:** 50 mm, frontal, symmetrical.
* **Composition:** Mirror arch centred, its top in **y 25–35 %** (aligns with the website arch crop).
* **Lighting:** L1, soft raking light across limewash texture.
* **Palette:** linen, rose plaster reflection in mirror, ivory, sand.
* **Continuity:** Same arched mirror seen nowhere else — keep it unique to About.
* **Negative space:** Detail image overlaps bottom-right (x 68–100 %, y 70–100 %).
* **Must not appear:** photographer reflection in mirror.

#### `ens-services-hero.jpg` — Treatments · Page hero
* **Final dimensions / ratio:** 2880 × 1234 · 21:9 · Category: interior
* **Subject:** The row of three S1 stations in morning light, receding to the right.
* **Camera:** 24–28 mm, standing eye-level, slight angle, verticals corrected.
* **Composition:** Nearest station and pendant at **x 45–66 %**; perspective leads right; left third is curtained window glow (calm for copy).
* **Lighting:** L1.
* **Continuity:** Same stations as home-intro / team-hero.
* **Negative space / mobile:** Page-hero rule.
* **Must not appear:** people, empty-looking “closed” mood (keep peonies and lamps tidy).

#### `ens-team-hero.jpg` — Artists · Page hero
* **Final dimensions / ratio:** 2880 × 1234 · 21:9 · Category: interior with people
* **Subject:** M-INÈS and M-MAYA working at two adjacent S1 stations, faces turned to their work and softly out of focus; guests’ hands at the tables.
* **Camera:** 50 mm, from behind the guest chairs at seated height, f/2.8.
* **Composition:** The two artists at **x 45–75 %**, upper half; foreground chair backs soft.
* **Lighting:** L1.
* **Continuity:** Uniforms, hair and jewellery must match team-2 / team-3 exactly.
* **Negative space / mobile:** Page-hero rule; on phones Inès (x ≈ 50 %) must be the visible artist.
* **Must not appear:** direct eye contact, masks, gloves.

#### `ens-booking-hero.jpg` — Booking · Page hero
* **Final dimensions / ratio:** 2880 × 1234 · 21:9 · Category: interior
* **Subject:** S4 reception: fluted walnut desk, blank linen-bound appointment book open, brass pen, peonies in ivory vessel, colour wall softly behind.
* **Camera:** 35 mm, standing eye-level, frontal.
* **Composition:** Book and peonies at **x 46–66 %, y 25–55 %**; desk runs across lower third.
* **Lighting:** L1.
* **Continuity:** Same desk and colour wall as pricing-hero / faq-hero.
* **Negative space / mobile:** Page-hero rule.
* **Must not appear:** legible writing in the book, computers, card terminals.

#### `ens-pricing-hero.jpg` — Pricing · Page hero
* **Final dimensions / ratio:** 2880 × 1234 · 21:9 · Category: interior detail
* **Subject:** The S4 colour wall: house bottles on brass ledges in a nude-to-rose gradient.
* **Camera:** 70 mm, frontal, perfectly level.
* **Composition:** Rhythmic rows; the **rose-to-berry** transition sits at **x 45–66 %** so phones see the richest colour; left third nude/ivory bottles (calm for copy).
* **Lighting:** L1, even, no glare on glass.
* **Continuity:** Same bottles as faq-hero, post-1, notfound.
* **Must not appear:** labels, mismatched caps, gaps in rows.

#### `ens-about-hero.jpg` — About · Page hero
* **Final dimensions / ratio:** 2880 × 1234 · 21:9 · Category: exterior
* **Subject:** S5 facade: ivory awning, espresso timber shopfront, brushed-brass “MAISON ÉLISE” lettering above the door, stone arcade.
* **Camera:** 35 mm, eye-level across the arcade, verticals corrected.
* **Composition:** Door and lettering at **x 46–66 %, y 15–50 %**; arcade columns frame left/right.
* **Lighting:** L1 overcast daylight, interior lamps faintly glowing.
* **Continuity:** Same shopfront as contact-hero and contact-side.
* **Must not appear:** real street names, other shop signs, cars, passers-by faces.

#### `ens-contact-hero.jpg` — Contact · Page hero
* **Final dimensions / ratio:** 2880 × 1234 · 21:9 · Category: exterior / threshold
* **Subject:** S5 door ajar at blue hour, warm interior light spilling onto arcade stone.
* **Camera:** 35 mm, eye-level, slight angle.
* **Composition:** Door opening at **x 46–64 %**; light spill falls toward bottom-right (away from copy).
* **Lighting:** L3.
* **Continuity:** Same door hardware and awning as about-hero.
* **Must not appear:** people, street signage.

#### `ens-contact-side.jpg` — Contact · Visit-us image
* **Final dimensions / ratio:** 1600 × 1200 · 4:3 · landscape · Category: exterior
* **Subject:** Street-corner view of the atelier window: stations and peonies visible through glass.
* **Camera:** 50 mm, eye-level, frontal to the window.
* **Composition:** Window centred; full image shown (no overlay), mask reveal from bottom.
* **Lighting:** L1 daylight, minimal reflections (polariser).
* **Continuity:** Interior visible through glass must match S1.
* **Must not appear:** reflections of the street/photographer, real neighbouring shops.

---

### Batch 3 — Treatments

All service images: **1600 × 2000 · 4:5 portrait** (raised from 1200 × 1500 — they also render as
the ~620 px-wide split hero on retina screens). Safe zone **x 20–80 %, y 30–90 %**; top-left 18 %
mid-tone for the ivory numeral; top corners empty for the arch crop. Set S2 unless stated.

#### `ens-service-manicure.jpg` — Signature Manicure
* **Subject:** M-ÉLISE’s hands shaping Camille’s (H1) nails with a glass file; hand on oatmeal cushion.
* **Camera:** 85 mm, 45° above, f/4. **Light:** L4 + window fill.
* **Composition:** Client hand centre (x 30–70 %, y 45–80 %); artist’s hand enters from top-right.
* **Nails:** client N2 in finishing state (two coats, glossy); artist short natural nails.
* **Continuity:** Élise’s ring and tunic sleeve per M-ÉLISE.
* **Must not appear:** filing dust, gloves.

#### `ens-service-gel.jpg` — Gel Couture
* **Subject:** Camille’s (H1) hand angled 30° beside the ivory LED lamp (off/warm), freshly cured N3 deep-rose gloss.
* **Camera:** 100 mm macro, f/5.6. **Light:** L4, deliberate long glossy highlights.
* **Composition:** Nails at x 35–70 %, y 40–75 %; lamp curve soft in background right.
* **Continuity:** Inès’s brush hand may rest at frame edge (sleeve only).
* **Must not appear:** purple UV glow, lamp branding.

#### `ens-service-art.jpg` — Nail Art Atelier
* **Subject:** M-INÈS painting a brushed-gold line (N4) on H2’s nude nail with a walnut fine-liner brush.
* **Camera:** 100 mm macro, f/5.6, brush tip at golden-ratio point (~x 62 %, y 58 %). **Light:** L4.
* **Palette:** nude, gold, deep skin, walnut.
* **Must not appear:** paint palettes with saturated colours, stencils, stickers.

#### `ens-service-extensions.jpg` — Sculpted Extensions
* **Subject:** H2’s hand vertical, fingers gently fanned, long N5 milky-pink sculpted almonds against ivory silk folds; M-MAYA’s hand steadying the wrist (sleeve visible).
* **Camera:** 85 mm, frontal, f/5.6. **Light:** L4.
* **Composition:** Fingertips at y 30–55 %, hand centred — fingertips must stay below the arch curve.
* **Must not appear:** forms or tips visible, extreme stiletto shape.

#### `ens-service-pedicure.jpg` — Spa Pedicure · S3
* **Subject:** F1’s feet resting in the travertine basin with milk and a few blush petals, N6 nude toes.
* **Camera:** top-down, 50 mm, f/5.6. **Light:** L1 from window + L4 fill.
* **Composition:** Basin edge across lower third; feet x 30–70 %, y 40–85 %.
* **Continuity:** M-SOFIA’s apron hem may appear at the edge.
* **Must not appear:** calluses, hair, plastic basin liner edges.

#### `ens-service-ritual.jpg` — Hand Ritual · S3
* **Subject:** Camille’s (H1) hands wrapped in warm oatmeal linen after paraffin, M-SOFIA’s hands pressing gently; tray of glass oil droppers.
* **Camera:** 85 mm, 45° above. **Light:** L1 soft + warm sconce.
* **Nails:** N7 bare.
* **Composition:** Wrapped hands centre (y 40–80 %), linen texture filling lower half.
* **Must not appear:** visible wax drips, plastic wrap.

#### `ens-story-1.jpg` — Home · Sticky story “Consultation”
* **Final dimensions / ratio:** 1600 × 2000 · 4:5
* **Subject:** Over-the-shoulder of Camille (G1) and M-INÈS choosing shades on a swatch ring of house colours (N1, N2, N3 visible).
* **Camera:** 50 mm, over Camille’s shoulder, f/2.8, swatch ring sharp. **Light:** L1.
* **Composition:** Swatch ring x 35–65 %, y 30–60 %; faces out of frame or fully soft.
* **Negative space:** Counter “01 / 03” sits **bottom-left (x 3–20 %, y 92–98 %)** in ivory — keep that corner mid-dark.
* **Nails:** client N7 bare.

#### `ens-story-2.jpg` — Home · Sticky story “Preparation”
* **Final dimensions / ratio:** 1600 × 2000 · 4:5
* **Subject:** Macro of an orangewood pusher gently working one cuticle of Camille’s (H1) ring finger.
* **Camera:** 100 mm macro, f/5.6. **Light:** L4.
* **Composition:** Single finger x 35–65 %, y 35–70 %. Bottom-left corner mid-dark (counter).
* **Nails:** N7, oiled.
* **Must not appear:** blood, redness, cutting tools near skin.

---

### Batch 4 — Nail macro / still life (S6, L4)

#### `ens-home-intro-detail.jpg` — Home + Treatments · Editorial split (detail)
* **Final dimensions / ratio:** 1200 × 1200 · 1:1 · Category: still life
* **Subject:** Top-down flat lay: five house bottles (ivory, nude, rose, berry, espresso) diagonally on raw linen with a glass file.
* **Composition:** Diagonal across centre, ≥ 20 % breathing room; reads at 260 px (displayed small inside a 10 px ivory frame).
* **Must not appear:** labels, more than five bottles.

#### `ens-about-detail.jpg` — About · Story split (detail)
* **Final dimensions / ratio:** 1200 × 1200 · 1:1
* **Subject:** Brushed-brass instruments in an opened sterilisation pouch on a travertine tray.
* **Composition:** Centred, simple silhouettes readable small.
* **Must not appear:** medical-looking blue pouches (use kraft/white pouches).

#### `ens-pricing-side.jpg` — Pricing · Price-list image (arch, sticky)
* **Final dimensions / ratio:** 1600 × 2000 · 4:5
* **Subject:** Camille’s (H1) hand holding a **blank** ivory letterpress menu card on raw linen, N1 nails.
* **Camera:** top-down 50 mm. **Light:** L4 + window.
* **Composition:** Card and hand at x 25–75 %, **y 35–85 %** (arch removes the top corners).
* **Must not appear:** any printed text, prices, logos.

#### `ens-faq-hero.jpg` — FAQ · Page hero
* **Final dimensions / ratio:** 2880 × 1234 · 21:9
* **Subject:** House bottles (from the colour wall) in soft focus on a travertine ledge, low angle, creamy bokeh.
* **Camera:** 85 mm, f/2, ledge-level. **Light:** L1 window backlight + L4 fill.
* **Composition:** Sharpest bottle at **x 50–62 %, y 20–50 %**; left third pure bokeh.
* **Must not appear:** labels, dust on glass.

---

### Batch 5 — Artists / team (S7, L5)

All team portraits: **1200 × 1600 · 3:4 portrait**, waist-up, Rose Plaster wall, same distance
(2 m to wall), same lens (85 mm), same white balance. Expression: warm, composed, soft smile or
neutral. **Layout:** an info card overlaps the **bottom 13 %** and 16 px in from each side → keep
hands/face within **y 8–82 %**; head-room 8–12 %. Swipe rail on phones shows the full card.

#### `ens-team-1.jpg` — Élise Marchand, founder
* **Pose:** Three-quarter, arms loosely crossed, ivory blazer. Off-centre to the left (x 35–60 %), hands visible.
* **Must not appear:** blazer branding, heavy jewellery.

#### `ens-team-2.jpg` — Inès Laurent, senior nail artist
* **Pose:** Facing camera, holding a walnut fine-art brush near the collarbone, oatmeal tunic.

#### `ens-team-3.jpg` — Maya Okafor, extensions specialist
* **Pose:** Slight turn, one hand resting on the opposite forearm (her own nails short milky sheer), gold hoops.

#### `ens-team-4.jpg` — Sofia Reyes, pedicure & wellness
* **Pose:** Facing camera, espresso apron over tunic, hands folded low (within y < 80 %).

#### `ens-founder.jpg` — About + Artists · Founder note
* **Final dimensions / ratio:** 1600 × 2000 · 4:5
* **Subject:** M-ÉLISE seated at the S2 station mid-conversation, three-quarter view, oatmeal tunic (working, not blazer), espresso cup at hand.
* **Camera:** 50 mm, seated eye-level, f/2.8. **Light:** L1 (matches S2 day).
* **Composition:** Face at x 35–60 %, y 20–45 %; hands on table lower third.
* **Layout:** On Artists a stat card overlaps **top-right (x 75–100 %, y 9–22 %)**; on About it is shown clean with an offset colour block → keep top-right calm.
* **Continuity:** Same face/hair/ring as team-1.

---

### Batch 6 — Lookbook (S8 surfaces)

Lookbook 1–6: home rail + gallery (see shared facts — **≥ 7 % calm top/bottom margin**).
Lookbook 7–12: gallery masonry + lightbox at natural ratio (full image visible, full resolution).
Captions appear **below** the image, never on it. Filter categories: Minimal, Couture, Art, Bridal.

| Slot | Final dims · ratio | Model · surface · light | Nail look | Composition / notes |
|---|---|---|---|---|
| `ens-lookbook-1.jpg` | 1200 × 1600 · 3:4 | H1 · ivory ceramic · L2 | Milk & honey French: milky sheer base, fine honey-champagne smile line, short square | Hand resting on a ceramic bowl rim, nails x 30–70 %, y 30–70 %. *Minimal.* |
| `ens-lookbook-2.jpg` | 1200 × 1500 · 4:5 | H2 · espresso velvet · L4 | Glazed pearl chrome over milky base, medium almond | Fingers relaxed, chrome reading as soft pearl, not mirror silver. *Couture.* |
| `ens-lookbook-3.jpg` | 1200 × 1600 · 3:4 | H3 · ivory satin + blush peony · L2 | Dusty-rose ombré (nude cuticle → rose tip), medium almond | Peony fills upper half — keep bloom inside y 10–55 % (rail crops this item to 4:5). *Couture.* |
| `ens-lookbook-4.jpg` | 1200 × 1500 · 4:5 | H2 · travertine, hard shadow · L4 (hard key) | Espresso-brown gloss + single gold micro-stud on ring finger | Graphic shadow diagonal; rail crops this item to 3:4 → hand centred. *Art.* |
| `ens-lookbook-5.jpg` | 1200 × 1600 · 3:4 | H1 · linen · L4 | Negative-space crescent line art on bare nail | Macro, two fingers, x 30–70 %. *Art.* |
| `ens-lookbook-6.jpg` | 1200 × 1500 · 4:5 | H3 · ivory satin · L2 | **Pearl bridal**: sheer pink-white + tiny pearl at cuticle (ring finger only) | Engagement ring visible; same set reappears in post-3 and gallery-hero. *Bridal.* |
| `ens-lookbook-7.jpg` | 1400 × 1400 · 1:1 | H1 · linen + linen-bound book · L4 | Matte nude, short square | Top-down, hand on closed book (no title). *Minimal.* |
| `ens-lookbook-8.jpg` | 1200 × 1500 · 4:5 | H2 · ivory · L4 | Champagne cat-eye gel (soft magnetic shimmer) | Hand angled toward light so the cat-eye line reads. *Couture.* |
| `ens-lookbook-9.jpg` | 1400 × 1400 · 1:1 | F1 · linen sheets · L1 | N6 nude pedicure | Feet crossed on a diagonal, ankles to toes. *Minimal.* |
| `ens-lookbook-10.jpg` | 1200 × 1500 · 4:5 | H3 · espresso satin opera glove on the other hand · L4 | Burgundy velvet (deep berry, matte-velvet top coat), medium oval | Bare hand resting on gloved hand — texture contrast. *Couture.* |
| `ens-lookbook-11.jpg` | 1400 × 1400 · 1:1 | H2 · S6 travertine · L4 | Abstract rose-and-champagne swirl art | Macro, three nails. *Art.* |
| `ens-lookbook-12.jpg` | 1200 × 1500 · 4:5 | — · S6 · L4 side light | House bottles as a colour story (ivory → espresso) | Still life, bottles in a soft arc. *Bridal* category in the demo (colour story for bridal consultations). |

Lookbook must-not-appear: identical poses twice in a row, rings on lookbook-1–5, saturated
backgrounds, more than one accent element per image.

---

### Batch 7 — Editorial / lifestyle

#### `ens-story-3.jpg` — Home · Sticky story “Finish”
* **Final dimensions / ratio:** 1600 × 2000 · 4:5
* **Subject:** Camille’s (H1) finished N1 set resting beside an ivory espresso cup and folded linen napkin at S2.
* **Camera:** 50 mm, 40° above. **Light:** L2.
* **Composition:** Hand lower-left (x 20–55 %, y 50–80 %), cup upper-right; bottom-left corner mid-dark for the counter.
* **Continuity:** Same nails as home-hero — the story ends on the house look.

#### `ens-booking-side.jpg` — Booking · Form image ⚠ not currently placed
* **Final dimensions / ratio:** 1600 × 2000 · 4:5
* **Subject:** Camille (G1) relaxing in the S3 lounge with tea during a Hand Ritual, face in soft profile.
* **Camera:** 50 mm, side view. **Light:** L1 + warm sconce.
* **Composition:** Figure x 30–70 %; teacup sharp.
* **Note:** Registered in `images.json` but **not used on any page** in the current demo. Kept in
  production for a planned Booking-page refinement (image column beside the form).

#### `ens-journal-hero.jpg` — Journal + all archives · Page hero
* **Final dimensions / ratio:** 2880 × 1234 · 21:9
* **Subject:** Top-down: open magazine with **blank / illegible** pages, Camille’s (H1) N1 hand turning a page, espresso cup, peony.
* **Camera:** top-down 35 mm. **Light:** L2.
* **Composition:** Hand and cup at **x 46–66 %, y 15–50 %**; magazine spread runs left (calm for copy).
* **Must not appear:** readable headlines, real magazine layouts.

---

### Batch 8 — Blog / supporting

All post images: **2880 × 1920 · 3:2** (raised from 1800 × 1200 — they are also the full-bleed
single-post hero). Post-hero rule: subject **x 35–65 %, y 15–50 %**, bottom 40 % calm.

#### `ens-post-1.jpg` — “The autumn edit”
* **Subject:** S6 flat lay: five house bottles in milky mocha, black cherry (N3), olive smoke, champagne chrome, espresso, with three fallen leaves (golden/rust) on linen. **Light:** L4.
* **Must not appear:** pumpkins, cinnamon-stick clichés, labels.

#### `ens-post-2.jpg` — “Cuticle care, properly”
* **Subject:** Macro: glass dropper releasing one golden drop of oil onto Camille’s (H1) cuticle, N1 nails. S2. **Light:** L4.

#### `ens-post-3.jpg` — “Bridal nails”
* **Subject:** H3’s hands holding a small ivory peony/ranunculus bouquet, lookbook-6 pearl bridal set, engagement ring, ivory silk sleeve. **Light:** L2.
* **Must not appear:** wedding dress branding, faces.

#### `ens-post-4.jpg` — “Gel or builder gel?”
* **Subject:** Two hands side by side on S2 travertine: left H1 with N2 gel colour, right H2 with N5-short milky builder gel. Symmetrical. **Light:** L4.
* **Must not appear:** captions or arrows.

#### `ens-post-5.jpg` — “The case for minimal nail art”
* **Subject:** Macro of H2’s nude nails with single brushed-gold micro-dots (N4 dot variant) on S6 linen. **Light:** L4.

#### `ens-post-6.jpg` — “Inside the hand ritual”
* **Subject:** Top-down S3 tray: folded hot towel, three glass oil droppers, small bowl of rose clay, sprig of dried lavender (muted). **Light:** L1.

#### `ens-notfound.jpg` — 404 · Hero
* **Final dimensions / ratio:** 2880 × 1620 · 16:9 (renders cropped to ≈ 21:9 — keep the action in the middle 70 % of the height)
* **Subject:** A single house bottle lying on its side on travertine, a small, elegant pool of nude lacquer spreading — wry, not messy.
* **Camera:** 85 mm, low angle. **Light:** L4.
* **Composition:** Bottle at **x 50–66 %, y 30–55 %**; spill flows right.
* **Negative space / mobile:** Page-hero rule.
* **Must not appear:** splatter, broken glass, cap lost.

---

## 10. Slots needing special attention

| Slot(s) | Why | Requirement |
|---|---|---|
| **home-hero** | Three overlays (headline left, arch inset right, badge bottom-right) + 62 % mobile focal point | Nails strictly in x 50–68 %, y 60–80 % |
| **cta-band** | Used on ~13 pages; centred text; on phones only ~20 % of width survives | Atmospheric, centre calm, one warm light near x 55 %. Consider a second CTA image later to reduce repetition (would need a content change) |
| **All page heroes** (about, services, pricing, gallery, team, booking, faq, contact, journal, notfound) | Phones show only the central third | Subject in x 45–66 %, upper half |
| **lookbook-1 … -6** | Home rail forces alternating 3:4 / 4:5 crops that don’t match their native ratios (items 3 and 4 in particular) | ≥ 7 % calm top/bottom margin, centred subject |
| **service-*** (6) | Double use: 4:5 card with ivory numeral **and** arch-topped split hero | 1600 × 2000; top corners empty; top-left mid-tone |
| **post-1 … -6** | Double use: card and full-bleed post hero | 2880 × 1920; subject upper-middle; bottom 40 % calm |
| **home-intro, founder** | Same image used with mirrored overlays on two pages | All four corners quiet |
| **team-hero, gallery-hero, post-4** | Multiple people/hands in one frame | Strict anatomy QA; identities must match single-person slots |
| **about-hero, contact-hero, contact-side** | Fictional facade lettering | Only “MAISON ÉLISE”; nothing resembling a real shop or street |
| **pricing-side, journal-hero, booking-hero** | Paper props | Absolutely no legible text |
| **booking-side** | Not placed on any page yet | In production; reserved for the Booking-page refinement |
| **notfound** | Spill must feel intentional and premium | Small, glossy, controlled pool |

### Resolution changes (accepted, ratio unchanged)
* service-manicure, -gel, -art, -extensions, -pedicure, -ritual: 1200 × 1500 → **1600 × 2000**
* post-1 … post-6: 1800 × 1200 → **2880 × 1920**

Applied to `demo/images.json` and `image-roadmap.md`; registry and both roadmaps agree.

---

## Post-import photo follow-ups

1. **`ens-about-hero.jpg` and `ens-contact-hero.jpg`**
   * Retouch or alternate crop for tablet/desktop so the facade MAISON ÉLISE lettering does not visually duplicate the global header wordmark.
   * Preserve the same facade, lighting, architecture and branding.
2. **`ens-home-hero.jpg`**
   * Produce a dedicated portrait/mobile-safe variant or retouch so at 390px the nails sit clearly away from the hero paragraph and CTA while preserving the same hand, nail look N1, S2 station, lighting and campaign identity.
