# Forms: demo, WordPress and static (Cloudflare Pages)

The four ENS form types (booking, quick booking, contact, newsletter) use the same markup in
three deployment situations.

| | A. Maison Élise / EDS showcase | B. Client site on WordPress | C. Client site on Cloudflare Pages (static) |
|---|---|---|---|
| Form posts to | `/api/form` | `/wp-admin/admin-post.php` | `/api/form` |
| Handled by | `cloudflare/functions/api/form.js` | `inc/forms.php` | `cloudflare/functions/api/form.js` |
| Configuration | `FORM_MODE=demo` | Customize → Studio Details → Email | `FORM_MODE=live` + variables below |
| Delivery | **none**: validated, then the demo message | `wp_mail()` to the Studio email | the email provider, to `FORM_TO` |
| Without a recipient | — | demo message, nothing sent (Studio email empty or `.example`) | error, nothing sent |

**Maison Élise is a demonstration site and is deployed with `FORM_MODE=demo`.** Its forms validate
exactly like a live site and show a short demonstration message, but nothing is emailed or
forwarded to anyone, and nothing is stored.

No mode stores submissions, and none falls back to the WordPress admin email or any other mailbox.

## How it works

- **Validation:** required fields, email and date format, field allowlist per form type, length
  limits and control characters, on the server in every mode (the browser checks are only a
  convenience).
- **Abuse protection:**
  - WordPress mode uses a nonce and a honeypot.
  - Static mode uses a honeypot, a minimum fill time of 3 seconds, JSON-only requests, a 16 KB body
    limit, an origin check and POST only.
  - Add a Cloudflare rate-limiting rule (below).
- **Export switch:** during a Simply Static export, `inc/forms.php` rewrites every ENS form to
  `action="/api/form" data-ens-static` and removes the WordPress-only fields (the admin-post action,
  nonce and absolute return URL). It also stops Simply Static from preserving the `wp-admin` form
  action. `assets/js/forms.js` then submits those forms as JSON and shows the widget's own status
  messages inline. Nothing needs editing after an export. Change the path with the
  `ens_static_form_endpoint` filter.

## Deploy (A and C)

Pages Functions are read from a `functions/` folder next to where `wrangler` runs, so deploy from
`cloudflare/`:

```
cd wp-content/themes/elite-nail-studio/cloudflare
npx wrangler pages deploy <path-to-simply-static-export> --project-name <project>
```

## Variables (Pages → Settings → Variables and Secrets)

| Name | A: showcase | C: client | Value |
|---|---|---|---|
| `FORM_MODE` | `demo` | `live` | Anything else, or unset, refuses every submission |
| `FORM_TO` | — | required | the client's mailbox, comma-separated for several; never a developer address |
| `FORM_FROM` | — | required | sender on a domain verified with the provider, e.g. `Salon Name <forms@client-domain>` |
| `EMAIL_PROVIDER` | — | optional | `resend` (default). To use another provider, add a case to `deliver()` |
| `EMAIL_API_KEY` | — | required, **secret** | the provider's API key; set it as a secret, never in Git |
| `ALLOWED_ORIGINS` | optional | recommended | allowed origins, e.g. `https://example.com,https://www.example.com`. Default: the site's own origin |
| `SITE_NAME` | — | optional | email subject prefix, e.g. `Salon Name` |

In live mode, a missing `FORM_TO`, `FORM_FROM` or `EMAIL_API_KEY` makes the endpoint answer with an
error and send nothing. Provider errors are never shown to visitors.

Also add a **rate-limiting rule** for `POST /api/form` (Security → WAF → Rate limiting, for example
5 requests per minute per IP). Pages Functions keep no state of their own.

## Test

- Endpoint logic, with a mock provider and no Cloudflare: `node cloudflare/test/form.test.mjs`
- Exported site with the function, locally: from `cloudflare/`, run
  `npx wrangler pages dev <export> --binding FORM_MODE=demo` and submit each form.
- Client deployment (C): submit each form once and confirm the email arrives at `FORM_TO`.

For a client site, update the Privacy Notice with the providers actually used, and remove the
demonstration paragraph.
