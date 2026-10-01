"""Dev tool: render placeholder JPGs and docs/image-roadmap.md from demo/images.json.

usage: python demo/build-images.py <out_dir>
Placeholders are written at half the recommended resolution; they exist only until the
original photography set replaces them (same filenames -> same Media Library slots).
"""
import json, os, random, sys
from PIL import Image, ImageDraw, ImageFilter, ImageFont

HERE = os.path.dirname(os.path.abspath(__file__))
SLOTS = json.load(open(os.path.join(HERE, 'images.json'), encoding='utf-8'))
OUT = sys.argv[1]
os.makedirs(OUT, exist_ok=True)

# Palette pairs (top-left -> bottom-right), cycled per slot.
TONES = [('#E9D5CC', '#C99091'), ('#F1E6DF', '#B99A72'), ('#D9B8AE', '#5E4B45'),
         ('#EADBD0', '#9E7B74'), ('#CDA9A0', '#3A2C29'), ('#F3EAE3', '#D2A9A4')]
FONT = 'C:/Windows/Fonts/georgiai.ttf'


def hex2rgb(h):
    return tuple(int(h[i:i + 2], 16) for i in (1, 3, 5))


def render(slot, i):
    w, h = (int(v) // 2 for v in slot['res'].split('x'))
    a, b = (hex2rgb(c) for c in TONES[i % len(TONES)])
    # Diagonal gradient from a small seed image, upscaled smoothly.
    n = 32
    im = Image.new('RGB', (n, n))
    im.putdata([tuple(int(x + (y - x) * (cx + cy) / (2 * n - 2)) for x, y in zip(a, b)) for cy in range(n) for cx in range(n)])
    im = im.resize((w, h), Image.BICUBIC)
    d = ImageDraw.Draw(im, 'RGBA')
    # Soft light orb + arch silhouette hint the composition without pretending to be a photo.
    rnd = random.Random(slot['id'])
    r = int(min(w, h) * rnd.uniform(.35, .55))
    cx, cy = int(w * rnd.uniform(.55, .75)), int(h * rnd.uniform(.3, .6))
    glow = Image.new('L', (w, h), 0)
    ImageDraw.Draw(glow).ellipse((cx - r, cy - r, cx + r, cy + r), fill=110)
    im.paste(Image.new('RGB', (w, h), (255, 250, 245)), mask=glow.filter(ImageFilter.GaussianBlur(r // 2)))
    aw = int(min(w, h) * .32)
    ax, ay = int(w * .14), int(h * .78)
    d.rounded_rectangle((ax, ay - int(aw * 1.3), ax + aw, ay), radius=aw // 2, outline=(255, 255, 255, 90), width=max(1, w // 600))
    # Grain.
    noise = Image.effect_noise((w, h), 14).convert('RGB')
    im = Image.blend(im, noise, .045)
    d = ImageDraw.Draw(im, 'RGBA')
    fs = max(14, w // 48)
    f = ImageFont.truetype(FONT, fs)
    d.text((int(w * .05), int(h - fs * 3.2)), f"Maison Élise  ·  {slot['id']}  ·  {slot['ratio']}", font=f, fill=(255, 255, 255, 200))
    im.save(os.path.join(OUT, f"ens-{slot['id']}.jpg"), quality=78, optimize=True, progressive=True)


def roadmap():
    rows = ['# Image Roadmap — Elite Nail Studio', '',
            'Every image slot in the demo. Generated from `demo/images.json` by `demo/build-images.py` —',
            'edit the JSON, not this file.', '',
            'Delivery: JPG (sRGB, quality 82) at the listed resolution; the site serves WordPress responsive',
            'sizes. Keep the filename `ens-<slot>.jpg` so the Media Library replacement maps 1:1.', '',
            'Global art direction: warm natural light, ivory/nude/rose/espresso tones that sit inside the',
            'Atelier palette, real skin texture, no neon, no glitter overload, no stock-photo smiles at camera',
            '(team portraits excepted). Fictional brand only — no third-party logos.', '',
            f'Total slots: **{len(SLOTS)}**', '',
            '| Slot | Page | Section | Orientation | Ratio | Resolution | Subject | Composition | Mood |',
            '|---|---|---|---|---|---|---|---|---|']
    for s in SLOTS:
        rows.append(f"| `{s['id']}` | {s['page']} | {s['section']} | {s['orient']} | {s['ratio']} | {s['res']} | {s['subject']} | {s['composition']} | {s['mood']} |")
    open(os.path.join(HERE, '..', 'docs', 'image-roadmap.md'), 'w', encoding='utf-8').write('\n'.join(rows) + '\n')


for i, s in enumerate(SLOTS):
    render(s, i)
roadmap()
print(len(SLOTS), 'placeholders ->', OUT)
