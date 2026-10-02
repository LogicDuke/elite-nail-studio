/**
 * POST /api/form — form endpoint for the static (Cloudflare Pages) build of Elite Nail Studio.
 *
 * Receives JSON from assets/js/forms.js, validates it against the same field lists as
 * inc/forms.php (keep them in sync), and delivers it by email through the configured provider.
 * Nothing is stored. Configuration (Pages → Settings → Variables and Secrets), see docs/static-forms.md:
 *
 *   FORM_MODE        "live" to deliver; "demo" validates and answers "demo" without sending. Required.
 *   FORM_TO          recipient address(es), comma-separated. Required for live.
 *   FORM_FROM        sender, e.g. "Maison Élise <forms@example.com>" on a domain verified with the provider. Required for live.
 *   EMAIL_PROVIDER   "resend" (default). Add another case in deliver() to change provider.
 *   EMAIL_API_KEY    secret: the provider API key.
 *   ALLOWED_ORIGINS  comma-separated origins allowed to post (default: the request's own origin).
 *   SITE_NAME        used in the email subject (default "Website").
 *
 * Responses are always {"ok":bool,"status":"sent|demo|invalid|error"}; provider errors are never echoed.
 */

const FIELDS = { name: 'Full name', email: 'Email', phone: 'Phone', service: 'Treatment', artist: 'Preferred artist', date: 'Preferred date', time: 'Preferred time', subject: 'Subject', message: 'Message' };
const MAX = { name: 120, email: 254, phone: 40, service: 120, artist: 120, date: 10, time: 120, subject: 200, message: 4000, page: 300 };
const TYPES = {
	booking: { label: 'Booking', fields: ['name', 'phone', 'email', 'service', 'artist', 'date', 'time', 'message'], required: ['name', 'email', 'service', 'date'] },
	quick: { label: 'Quick booking', fields: ['name', 'phone', 'service', 'date'], required: ['name', 'phone', 'service', 'date'] },
	contact: { label: 'Contact', fields: ['name', 'email', 'phone', 'subject', 'message'], required: ['name', 'email', 'message'] },
	newsletter: { label: 'Newsletter', fields: ['email'], required: ['email'] },
};
const MAX_BODY = 16 * 1024;
const MIN_FILL_MS = 3000; // Faster than a person can fill in a form.
const EMAIL = /^[^\s@<>()",;:]+@[^\s@<>()",;:]+\.[a-z]{2,}$/i;

const reply = (httpStatus, status, headers = {}) =>
	new Response(JSON.stringify({ ok: status === 'sent' || status === 'demo', status }), {
		status: httpStatus,
		headers: { 'Content-Type': 'application/json; charset=utf-8', 'Cache-Control': 'no-store', 'X-Content-Type-Options': 'nosniff', ...headers },
	});

/** Validated, trimmed fields of one submission, or null. */
export function validate(data) {
	if (!data || typeof data !== 'object' || Array.isArray(data)) return null;
	const spec = TYPES[data.type];
	if (!spec) return null;
	const allowed = new Set([...spec.fields, 'type', 'website', 't', 'page']);
	if (Object.keys(data).some((key) => !allowed.has(key))) return null;
	const out = {};
	for (const key of [...spec.fields, 'page']) {
		const raw = data[key] ?? '';
		if (typeof raw !== 'string') return null;
		const value = raw.trim();
		if (value.length > MAX[key]) return null;
		// Control characters are refused; only the message may contain line breaks.
		if (/[\u0000-\u0008\u000B\u000C\u000E-\u001F\u007F]/.test(value) || (key !== 'message' && /[\r\n]/.test(value))) return null;
		out[key] = value;
	}
	if (spec.required.some((key) => !out[key])) return null;
	if (out.email && !EMAIL.test(out.email)) return null;
	if (out.date && !/^\d{4}-\d{2}-\d{2}$/.test(out.date)) return null;
	return { spec, fields: out };
}

/** Send through the configured provider. Returns true on success. */
async function deliver(env, { subject, text, replyTo }) {
	switch (env.EMAIL_PROVIDER || 'resend') {
		case 'resend': {
			if (!env.EMAIL_API_KEY) return false;
			const res = await fetch('https://api.resend.com/emails', {
				method: 'POST',
				headers: { Authorization: `Bearer ${env.EMAIL_API_KEY}`, 'Content-Type': 'application/json' },
				body: JSON.stringify({ from: env.FORM_FROM, to: env.FORM_TO.split(',').map((s) => s.trim()).filter(Boolean), subject, text, ...(replyTo ? { reply_to: replyTo } : {}) }),
			});
			return res.ok;
		}
		default:
			return false;
	}
}

export async function onRequestPost({ request, env }) {
	const origin = request.headers.get('Origin');
	const allowed = (env.ALLOWED_ORIGINS || new URL(request.url).origin).split(',').map((s) => s.trim());
	if (!origin || !allowed.includes(origin)) return reply(403, 'error');
	if (!(request.headers.get('Content-Type') || '').toLowerCase().startsWith('application/json')) return reply(415, 'error');
	if (Number(request.headers.get('Content-Length') || 0) > MAX_BODY) return reply(413, 'error');

	const raw = await request.text();
	if (raw.length > MAX_BODY) return reply(413, 'error');
	let data;
	try { data = JSON.parse(raw); } catch { return reply(400, 'invalid'); }

	// Honeypot filled in: answer as if sent, deliver nothing.
	if (data && typeof data.website === 'string' && data.website !== '') return reply(200, 'sent');
	if (!(Number(data && data.t) >= MIN_FILL_MS)) return reply(400, 'invalid');

	// A well-formed request with invalid fields is a normal visitor outcome: 200 with status "invalid"
	// (browsers log every 4xx as a console error). Malformed and abusive requests keep their 4xx.
	const valid = validate(data);
	if (!valid) return reply(200, 'invalid');

	if (env.FORM_MODE === 'demo') return reply(200, 'demo');
	// Not configured for delivery: refuse rather than guess a recipient.
	if (env.FORM_MODE !== 'live' || !env.FORM_TO || !env.FORM_FROM) return reply(503, 'error');

	const { spec, fields } = valid;
	const lines = spec.fields.map((key) => `${FIELDS[key]}: ${fields[key]}`);
	const sent = await deliver(env, {
		subject: `[${env.SITE_NAME || 'Website'}] ${spec.label}`,
		text: `${lines.join('\n')}\n\nSent from ${origin}${fields.page || '/'}`,
		replyTo: fields.email || '',
	}).catch(() => false);
	return sent ? reply(200, 'sent') : reply(502, 'error');
}

export const onRequest = () => reply(405, 'error', { Allow: 'POST' });
