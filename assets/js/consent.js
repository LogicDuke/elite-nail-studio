/* Elite Nail Studio — consent preferences (inc/consent.php). Vanilla, no requests, static-safe.
 * Record (localStorage, key from ensConsentConfig): {"v":1,"policy":"1","ts":<unix s>,"cats":{"preferences":bool}}.
 * Asked again after maxAgeDays or when the policy version changes.
 * API (EDS Consent contract): edsConsent.allowed(cat) · get() · set(cats) · acceptAll() · rejectAll() · open();
 * event `eds:consent` on document, detail { categories, previous, source }. */
(() => {
	const cfg = window.ensConsentConfig;
	if (!cfg || window.edsConsent) return;

	const banner = document.querySelector('[data-ens-consent-banner]');
	const panel = document.querySelector('[data-ens-consent-panel]');
	const toggle = panel && panel.querySelector('[data-eds-consent-category="preferences"]');
	let returnFocus = null;

	const read = () => {
		try {
			const r = JSON.parse(localStorage.getItem(cfg.key));
			if (r && r.v === 1 && r.policy === cfg.version && Date.now() / 1000 - r.ts < cfg.maxAgeDays * 86400) return r;
		} catch (e) { /* storage blocked or corrupt: undecided */ }
		return null;
	};
	let record = read();
	const allowed = (cat) => cat === 'necessary' || !!(record && record.cats[cat]);

	// Translation storage left from before a decision, or after a withdrawal, goes.
	const cleanup = () => {
		try { cfg.cleanup.localStorage.forEach((k) => localStorage.removeItem(k)); } catch (e) { /* blocked */ }
		const host = location.hostname;
		cfg.cleanup.cookies.forEach((name) => ['', host, '.' + host].forEach((d) => {
			document.cookie = name + '=; Max-Age=0; Path=/' + (d ? '; Domain=' + d : '');
		}));
	};

	// Start inert scripts whose category is now allowed (same attributes, real src).
	const activate = () => {
		document.querySelectorAll('script[type="text/plain"][data-eds-consent]').forEach((old) => {
			if (!old.dataset.edsConsent.split(' ').some(allowed)) return;
			document.querySelectorAll('[data-ens-lang-placeholder]').forEach((el) => el.remove());
			const s = document.createElement('script');
			[...old.attributes].forEach((a) => { if (!['type', 'data-src', 'data-eds-consent'].includes(a.name)) s.setAttribute(a.name, a.value); });
			s.src = old.dataset.src;
			// Allowed from the language placeholder: hand focus to the real selector once it exists.
			if (returnFocus && returnFocus.matches('[data-ens-lang-placeholder]')) {
				s.addEventListener('load', () => { const sel = document.querySelector('.menu-item-gtranslate select'); if (sel) sel.focus(); }, { once: true });
			}
			old.replaceWith(s);
		});
	};

	// Until translation is allowed, the language control is a button that explains and asks.
	const placeholders = () => {
		if (allowed('preferences')) return;
		document.querySelectorAll('.menu-item-gtranslate').forEach((li) => {
			if (li.querySelector('[data-ens-lang-placeholder]')) return;
			const b = document.createElement('button');
			b.type = 'button';
			b.className = 'ens-consent-lang';
			b.dataset.ensLangPlaceholder = '';
			b.dataset.edsConsentOpen = '';
			b.textContent = cfg.labels.language;
			b.setAttribute('aria-label', cfg.labels.languageHint);
			li.append(b);
		});
	};

	const set = (cats, source) => {
		const previous = record ? { ...record.cats } : null;
		record = { v: 1, policy: cfg.version, ts: Math.floor(Date.now() / 1000), cats: { preferences: !!cats.preferences } };
		try { localStorage.setItem(cfg.key, JSON.stringify(record)); } catch (e) { /* this page only */ }
		if (banner) banner.hidden = true;
		document.dispatchEvent(new CustomEvent('eds:consent', { detail: { categories: { necessary: true, ...record.cats }, previous, source } }));
		if (previous && previous.preferences && !record.cats.preferences) {
			cleanup();
			location.reload(); // A script that already ran cannot be unloaded.
			return;
		}
		if (!record.cats.preferences) cleanup();
		activate();
	};

	const open = () => {
		if (!panel || panel.open) return;
		returnFocus = document.activeElement;
		toggle.checked = allowed('preferences');
		panel.showModal();
	};
	const close = () => {
		if (!panel || !panel.open) return;
		panel.close();
	};
	panel && panel.addEventListener('close', () => {
		const target = returnFocus && returnFocus.isConnected ? returnFocus : document.querySelector('.menu-item-gtranslate select, .menu-item-gtranslate button, [href$="#eds-consent"]');
		if (target) target.focus();
	});

	const act = (action) => {
		const fromPanel = panel && panel.open;
		if (action === 'manage') return open();
		if (action === 'close') return close();
		const cats = action === 'accept' ? { preferences: true } : action === 'reject' ? { preferences: false } : { preferences: toggle.checked };
		set(cats, (fromPanel ? 'panel-' : 'banner-') + action);
		close();
	};

	document.addEventListener('click', (e) => {
		const a = e.target.closest('[data-eds-consent-action]');
		if (a) return act(a.dataset.edsConsentAction);
		const o = e.target.closest('[data-eds-consent-open], a[href$="#eds-consent"]');
		if (o) { e.preventDefault(); open(); }
	});
	// Clicking the backdrop (outside the panel box) closes without changes.
	panel && panel.addEventListener('click', (e) => { if (e.target === panel) close(); });
	// Links to #eds-consent act as buttons.
	document.querySelectorAll('a[href$="#eds-consent"]').forEach((a) => a.setAttribute('role', 'button'));

	window.edsConsent = {
		allowed,
		get: () => ({ decided: !!record, categories: { necessary: true, preferences: allowed('preferences') }, version: cfg.version }),
		set: (cats) => set(cats || {}, 'api'),
		acceptAll: () => set({ preferences: true }, 'api'),
		rejectAll: () => set({ preferences: false }, 'api'),
		open,
	};

	if (!allowed('preferences')) cleanup();
	placeholders();
	activate();
	if (!record && banner) banner.hidden = false;
	if (location.hash === '#eds-consent') open();
})();
