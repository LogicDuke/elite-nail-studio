# SEO, social metadata and the static export

The theme provides its own lightweight SEO layer (`inc/seo.php`); no SEO plugin is needed.

## What each page outputs

| Tag | Source |
|---|---|
| `<title>` | WordPress: page title – site name (front page: site name – tagline) |
| `meta description`, `og:description`, `twitter:description` | the page/post **excerpt** (page editor → Excerpt). Empty excerpt = no description. Categories use their description, else a short generated line |
| `link rel="canonical"` | WordPress for single pages/posts; the theme adds it for the Journal and category pages |
| `og:type` | `article` for Journal posts (plus `article:published_time`, `modified_time`, `section`), else `website` |
| `og:image`, `twitter:image` | the **featured image** (1536 px JPEG size), else the front page's featured image. Alt text from the image |
| `og:site_name` | Settings → General → Site Title (`Maison Élise`) |
| `twitter:card` | `summary_large_image` (no tracking scripts) |
| Site icon | Settings → General → Site Icon (WordPress core: 32/180/192/270 px links) |
| Structured data | FAQ widget only (`FAQPage`, built from the visible questions; one block per FAQ widget). No LocalBusiness, ratings or opening-hours data: Maison Élise is fictional |

404 and search views carry no social metadata. Author archives return 404 and authors are left out of
the WordPress sitemap: they expose login names and add nothing to a salon site. Head links to
WordPress-only endpoints (XML-RPC/RSD, REST API, oEmbed, shortlink, RSS feeds) are not printed.

**Demo content:** `wp ens seo` (also run by `wp ens seed`) sets the page descriptions, each page's
social image (its own hero image) and the site icon (`assets/img/site-icon.png`, the wordmark initial in
Cormorant Garamond, ivory on espresso).

**Brand name and translation:** the site name is printed as `<html data-ens-brand>`; `assets/js/motion.js`
marks every occurrence in the page `translate="no"` before GTranslate starts, so automatic translation
(browser language, after Preferences consent) translates the content but never the business name.

## Static export (Cloudflare Pages)

A relative Simply Static export turns every URL root-relative. Canonical, `og:url`, `og:image`,
`twitter:image`, `sitemap.xml` and `robots.txt` need absolute URLs, so they are completed for the public
host in one step after the export. Nothing in the theme or database names a production domain.

1. **WordPress** (before exporting): Settings → Reading → *Search engine visibility* **unchecked**
   (otherwise every page is exported with `noindex`). Simply Static: Relative Path `/`, local directory,
   **Generate 404 page: on** (writes `404.html` from the theme's 404 template, so unknown URLs answer
   404 instead of the homepage).
2. Export.
3. Finalize for the public origin:

   ```
   node cloudflare/finalize.mjs <export-dir> https://<project>.pages.dev
   ```

   Makes the four URL tags absolute, writes `sitemap.xml` (every exported page except `404.html` and
   `noindex` pages), `robots.txt` (`Allow: /` + sitemap) and `favicon.ico` (the 32 px site icon).
   It refuses development origins and stops if a `.local`/`localhost` host is left in any page.
4. Deploy (`docs/static-forms.md`).

**Custom domain:** run step 3 again on the same export with the final origin, then redeploy.

Test: `node cloudflare/test/finalize.test.mjs`.
