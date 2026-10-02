// node cloudflare/test/form.test.mjs — checks /api/form (cloudflare/functions/api/form.js) without Cloudflare.
import assert from 'node:assert/strict';
import { onRequestPost, onRequest } from '../functions/api/form.js';

const ORIGIN = 'https://demo.example';
const sent = [];
globalThis.fetch = async (url, init) => { sent.push({ url, body: JSON.parse(init.body), auth: init.headers.Authorization }); return new Response('{}', { status: 200 }); };

const call = async (payload, { env = {}, origin = ORIGIN, type = 'application/json', raw } = {}) => {
	const request = new Request(`${ORIGIN}/api/form`, { method: 'POST', headers: { 'Content-Type': type, ...(origin ? { Origin: origin } : {}) }, body: raw ?? JSON.stringify(payload) });
	const res = await onRequestPost({ request, env });
	return { code: res.status, ...(await res.json()) };
};
const live = { FORM_MODE: 'live', FORM_TO: 'studio@demo.example', FORM_FROM: 'Site <forms@demo.example>', EMAIL_API_KEY: 'test-key', SITE_NAME: 'Maison Élise' };
const forms = {
	booking: { name: 'Ada', phone: '+1 555', email: 'ada@demo.example', service: 'Gel Couture', artist: 'No preference', date: '2026-11-03', time: 'Morning', message: 'Hello\nthere' },
	quick: { name: 'Ada', phone: '+1 555', service: 'Gel Couture', date: '2026-11-03' },
	contact: { name: 'Ada', email: 'ada@demo.example', phone: '', subject: 'Hi', message: 'A question' },
	newsletter: { email: 'ada@demo.example' },
};
const ok = (type, extra = {}) => ({ type, t: 5000, website: '', page: '/contact/', ...forms[type], ...extra });

for (const type of Object.keys(forms)) {
	assert.deepEqual(await call(ok(type), { env: live }), { code: 200, ok: true, status: 'sent' }, `${type} live`);
	assert.deepEqual(await call(ok(type), { env: { FORM_MODE: 'demo' } }), { code: 200, ok: true, status: 'demo' }, `${type} demo`);
}
assert.equal(sent.length, 4, 'one email per live submission, none in demo mode');
assert.deepEqual(sent[0].body.to, ['studio@demo.example']);
assert.equal(sent[0].body.reply_to, 'ada@demo.example');
assert.equal(sent[0].body.subject, '[Maison Élise] Booking');
assert.equal(sent[0].auth, 'Bearer test-key');

// Not configured: refuse, never guess a recipient.
assert.equal((await call(ok('contact'))).code, 503);
assert.equal((await call(ok('contact'), { env: { FORM_MODE: 'live', FORM_FROM: 'x@demo.example', EMAIL_API_KEY: 'k' } })).code, 503);
// Origin, method, content type, size.
assert.equal((await call(ok('contact'), { env: live, origin: 'https://evil.example' })).code, 403);
assert.equal((await call(ok('contact'), { env: live, origin: null })).code, 403);
assert.equal((await call(ok('contact'), { env: { ...live, ALLOWED_ORIGINS: 'https://other.example, https://demo.example' } })).code, 200);
assert.equal((await call(null, { env: live, type: 'application/x-www-form-urlencoded', raw: 'type=contact' })).code, 415);
assert.equal((await call(null, { env: live, raw: JSON.stringify({ ...ok('contact'), message: 'x'.repeat(20000) }) })).code, 413);
assert.equal((await onRequest()).status, 405);
// Validation and abuse.
const n = sent.length;
for (const [label, payload] of [
	['unknown type', ok('contact', { type: 'admin' })],
	['missing required', ok('contact', { message: '' })],
	['bad email', ok('newsletter', { email: 'not-an-email' })],
	['unexpected field', ok('newsletter', { cc: 'x@y.example' })],
	['header injection', ok('contact', { subject: 'Hi\nBcc: x@y.example' })],
	['non-string value', ok('contact', { name: { $ne: 1 } })],
	['too long', ok('contact', { name: 'x'.repeat(121) })],
	['bad date', ok('quick', { date: 'tomorrow' })],
]) assert.deepEqual(await call(payload, { env: live }), { code: 200, ok: false, status: 'invalid' }, label);
for (const [label, payload] of [['too fast (bot)', ok('contact', { t: 400 })], ['no timing (no JS)', ok('contact', { t: undefined })]]) {
	assert.deepEqual(await call(payload, { env: live }), { code: 400, ok: false, status: 'invalid' }, label);
}
assert.equal((await call(null, { env: live, raw: '{oops' })).status, 'invalid');
// Honeypot: answer "sent", deliver nothing.
assert.deepEqual(await call(ok('contact', { website: 'http://spam.example' }), { env: live }), { code: 200, ok: true, status: 'sent' });
assert.equal(sent.length, n, 'nothing delivered for invalid or honeypot submissions');
// Provider failure: controlled error, no provider details.
globalThis.fetch = async () => new Response('{"message":"secret upstream detail"}', { status: 500 });
assert.deepEqual(await call(ok('contact'), { env: live }), { code: 502, ok: false, status: 'error' });

console.log('form endpoint: all checks passed');
