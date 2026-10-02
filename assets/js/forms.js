/* Elite Nail Studio — forms on a static export (inc/forms.php rewrites them to data-ens-static).
 * Submits as JSON to the form's action (Cloudflare Pages Function, cloudflare/functions/api/form.js)
 * and shows the widget's own status messages inline. In WordPress the forms post normally. */
(() => {
	const forms = document.querySelectorAll('form[data-ens-static]');
	if (!forms.length) return;
	const shownAt = performance.now();

	forms.forEach((form) => {
		const box = form.closest('.ens-form');
		let messages = {};
		try { messages = JSON.parse(box.dataset.ensMessages || '{}'); } catch (e) { /* defaults below */ }
		// An empty live region from the start, so the message is announced when it is filled.
		const status = document.createElement('p');
		status.className = 'ens-form__status';
		status.setAttribute('role', 'status');
		status.hidden = true;
		box.insertBefore(status, form);

		const show = (state) => {
			status.className = 'ens-form__status ens-form__status--' + state;
			status.textContent = messages[state] || messages.error || '';
			status.hidden = false;
		};

		form.addEventListener('submit', async (e) => {
			e.preventDefault();
			if (form.getAttribute('aria-busy') === 'true') return;
			const data = new FormData(form);
			const payload = { type: data.get('ens_type'), t: Math.round(performance.now() - shownAt), website: data.get('ens_website') || '', page: location.pathname };
			for (const [key, value] of data) {
				if (!key.startsWith('ens_')) payload[key] = String(value);
			}
			const button = form.querySelector('[type="submit"]');
			form.setAttribute('aria-busy', 'true');
			if (button) button.setAttribute('aria-disabled', 'true'); // Not `disabled`: focus stays on the button.
			let state = 'error';
			try {
				const res = await fetch(form.getAttribute('action'), {
					method: 'POST',
					headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
					body: JSON.stringify(payload),
				});
				const body = await res.json();
				if (['sent', 'demo', 'invalid', 'error'].includes(body.status)) state = body.status;
			} catch (err) { /* network or invalid response: generic error */ }
			show(state);
			if (state === 'sent' || state === 'demo') form.reset();
			form.removeAttribute('aria-busy');
			if (button) button.removeAttribute('aria-disabled');
		});
	});
})();
