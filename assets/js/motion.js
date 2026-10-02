/**
 * Elite Nail Studio — motion & interaction.
 * Entrances: IntersectionObserver adds `.is-in`. Scroll-linked effects (header, lookbook,
 * parallax) share one passive scroll listener batched through requestAnimationFrame.
 * Only transform / opacity / clip-path are animated. Reduced motion: CSS shows final states;
 * scroll-linked effects are not started. See docs/design-system.md §12.
 */
(() => {
	const root = document.documentElement;
	const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
	const desktop = matchMedia('(min-width: 1025px)');
	const $$ = (sel, ctx = document) => [...ctx.querySelectorAll(sel)];
	const clamp = (v, min, max) => Math.min(max, Math.max(min, v));

	/* ---------- Brand name: never machine-translated ("Maison Élise" ≠ "Huis Élise") ---------- */
	// Runs before GTranslate can start (its scripts load only after consent, from the network).
	// Neighbouring spaces and a closing punctuation mark go inside the span: translation trims them
	// from the text around it.
	const brand = root.dataset.ensBrand;
	if (brand) {
		const hits = [];
		const tw = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT);
		while (tw.nextNode()) {
			const n = tw.currentNode;
			if (n.data.includes(brand) && !n.parentElement.closest('script, style, .notranslate')) hits.push(n);
		}
		const re = new RegExp(`(\\s*${brand.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')}[.,;:!?]?\\s*)`);
		hits.forEach((n) => {
			const svg = n.parentElement.closest('svg'); // e.g. the circular badge text: needs <tspan>.
			const parts = n.data.split(re).map((part, i) => {
				if (i % 2 === 0) return part;
				const name = svg ? document.createElementNS(svg.namespaceURI, 'tspan') : document.createElement('span');
				name.setAttribute('class', 'notranslate');
				name.setAttribute('translate', 'no');
				name.textContent = part;
				return name;
			});
			if (!svg && /flex|grid/.test(getComputedStyle(n.parentElement).display)) {
				const item = document.createElement('span'); // One flex/grid item, as the text was (e.g. eyebrow gap).
				item.append(...parts);
				n.replaceWith(item);
			} else {
				n.replaceWith(...parts);
			}
		});
	}

	/* ---------- Split text: wrap words (keeps <em>/<br>/links) ---------- */
	const splitWords = (el) => {
		let i = 0;
		const walk = (node) => {
			[...node.childNodes].forEach((child) => {
				if (child.nodeType === 3) {
					const frag = document.createDocumentFragment();
					child.textContent.split(/(\s+)/).forEach((part) => {
						if (!part) return;
						if (/^\s+$/.test(part)) return frag.append(part);
						const w = document.createElement('span');
						w.className = 'ens-w';
						w.style.setProperty('--i', i++);
						const inner = document.createElement('span');
						inner.textContent = part;
						w.append(inner);
						frag.append(w);
					});
					child.replaceWith(frag);
				} else if (child.nodeType === 1 && child.tagName !== 'BR') {
					walk(child);
				}
			});
		};
		walk(el);
	};

	/* ---------- Entrances ---------- */
	const revealSel = '.ens-reveal, .ens-mask, .ens-mask-x, .ens-split, .ens-stagger, .ens-lines, .ens-hero';
	const pending = new Set();
	const reveal = (el) => {
		el.classList.add('is-in');
		io.unobserve(el);
		pending.delete(el);
	};
	const io = new IntersectionObserver((entries) => {
		entries.forEach((e) => { if (e.isIntersecting) reveal(e.target); });
	}, { rootMargin: '0px 0px -12% 0px' });
	// A fast fling can carry a target from below to above the viewport within one frame, which
	// IntersectionObserver never reports. When scrolling settles, reveal anything already passed.
	let settleTimer;
	const sweep = () => pending.forEach((el) => { if (el.getBoundingClientRect().top < innerHeight) reveal(el); });

	const initReveals = () => {
		$$(revealSel).forEach((el) => {
			if (el.dataset.ensInit) return;
			el.dataset.ensInit = '1';
			if (el.classList.contains('ens-split') && !reduce) splitWords(el);
			if (el.classList.contains('ens-stagger')) {
				// Elementor containers wrap children in .e-con-inner when boxed.
				const host = el.querySelector(':scope > .e-con-inner') || el;
				[...host.children].forEach((c, n) => c.style.setProperty('--i', n));
			}
			pending.add(el);
			io.observe(el);
		});
	};

	/* ---------- Header ---------- */
	const header = document.querySelector('[data-ens-header]');
	let lastY = scrollY;
	const updateHeader = (y) => {
		if (!header) return;
		header.classList.toggle('is-scrolled', y > 24);
		const down = y > lastY && y > 600 && !root.classList.contains('ens-menu-open');
		header.classList.toggle('is-hidden', down && !header.contains(document.activeElement));
		lastY = y;
	};

	/* ---------- Overlay menu ---------- */
	const toggle = document.querySelector('[data-ens-menu-toggle]');
	const menu = document.querySelector('[data-ens-menu]');
	const setMenu = (open) => {
		if (!toggle || !menu) return;
		toggle.setAttribute('aria-expanded', String(open));
		root.classList.toggle('ens-menu-open', open);
		if (open) {
			menu.hidden = false;
			requestAnimationFrame(() => menu.classList.add('is-open'));
			menu.querySelector('a')?.focus({ preventScroll: true });
		} else {
			menu.classList.remove('is-open');
			const done = () => { if (!menu.classList.contains('is-open')) menu.hidden = true; };
			reduce ? done() : setTimeout(done, 500);
		}
	};
	toggle?.addEventListener('click', () => setMenu(toggle.getAttribute('aria-expanded') !== 'true'));
	menu?.addEventListener('click', (e) => { if (e.target.closest('a')) setMenu(false); });
	document.addEventListener('keydown', (e) => {
		if (e.key === 'Escape' && root.classList.contains('ens-menu-open')) {
			setMenu(false);
			toggle.focus();
		}
	});
	desktop.addEventListener('change', (e) => { if (e.matches) setMenu(false); });

	/* ---------- Horizontal lookbook (desktop, motion allowed) ---------- */
	const books = $$('[data-ens-lookbook]').map((el) => ({
		el,
		track: el.querySelector('[data-ens-lookbook-track]'),
		bar: el.querySelector('[data-ens-lookbook-bar]'),
		dist: 0,
	}));
	const measureBooks = () => {
		const on = desktop.matches && !reduce;
		books.forEach((b) => {
			b.el.classList.toggle('is-pinned', on);
			b.track.style.transform = '';
			b.el.style.height = '';
			if (!on) return;
			b.dist = Math.max(0, b.track.scrollWidth - b.track.parentElement.clientWidth);
			b.el.style.height = `${innerHeight + b.dist}px`;
		});
	};
	const updateBooks = () => {
		books.forEach((b) => {
			if (!b.el.classList.contains('is-pinned')) return;
			const r = b.el.getBoundingClientRect();
			if (r.bottom < 0 || r.top > innerHeight) return;
			const p = clamp(-r.top / (b.dist || 1), 0, 1);
			b.track.style.transform = `translate3d(${-p * b.dist}px,0,0)`;
			if (b.bar) b.bar.style.transform = `scaleX(${p})`;
		});
	};

	/* ---------- Parallax (visible elements only) ---------- */
	const visible = new Set();
	const pio = new IntersectionObserver((entries) => entries.forEach((e) => (e.isIntersecting ? visible.add(e.target) : visible.delete(e.target))));
	if (!reduce) $$('.ens-parallax').forEach((el) => pio.observe(el));
	const updateParallax = () => {
		visible.forEach((el) => {
			const r = el.getBoundingClientRect();
			const p = clamp((r.top + r.height / 2 - innerHeight / 2) / (innerHeight + r.height), -0.5, 0.5);
			el.style.setProperty('--ens-shift', `${(-p * 12).toFixed(2)}%`);
		});
	};

	/* ---------- One scroll loop ---------- */
	let ticking = false;
	const onScroll = () => {
		clearTimeout(settleTimer);
		if (pending.size) settleTimer = setTimeout(sweep, 160);
		if (ticking) return;
		ticking = true;
		requestAnimationFrame(() => {
			updateHeader(scrollY);
			updateBooks();
			updateParallax();
			ticking = false;
		});
	};
	addEventListener('scroll', onScroll, { passive: true });
	let resizeTimer;
	addEventListener('resize', () => {
		clearTimeout(resizeTimer);
		resizeTimer = setTimeout(() => { measureBooks(); onScroll(); }, 150);
	});

	/* ---------- Sticky story ---------- */
	$$('[data-ens-story]').forEach((story) => {
		const frames = $$('.ens-story__frame', story);
		const steps = $$('[data-ens-step]', story);
		const index = story.querySelector('[data-ens-story-index]');
		const sio = new IntersectionObserver((entries) => {
			entries.forEach((e) => {
				if (!e.isIntersecting) return;
				const n = +e.target.dataset.ensStep;
				steps.forEach((s, k) => s.classList.toggle('is-active', k === n));
				frames.forEach((f, k) => f.classList.toggle('is-active', k === n));
				if (index) index.textContent = String(n + 1).padStart(2, '0');
			});
		}, { rootMargin: '-45% 0px -45% 0px' });
		steps.forEach((s) => sio.observe(s));
	});

	/* ---------- Quote slider ---------- */
	$$('[data-ens-slider]').forEach((slider) => {
		const track = slider.querySelector('[data-ens-slider-track]');
		const index = slider.querySelector('[data-ens-slider-index]');
		const go = (dir) => track.scrollBy({ left: dir * track.clientWidth, behavior: reduce ? 'auto' : 'smooth' });
		slider.querySelector('[data-ens-slider-prev]')?.addEventListener('click', () => go(-1));
		slider.querySelector('[data-ens-slider-next]')?.addEventListener('click', () => go(1));
		track.addEventListener('keydown', (e) => {
			if (e.key === 'ArrowRight' || e.key === 'ArrowLeft') { e.preventDefault(); go(e.key === 'ArrowRight' ? 1 : -1); }
		});
		let t = false;
		track.addEventListener('scroll', () => {
			if (t || !index) return;
			t = true;
			requestAnimationFrame(() => {
				index.textContent = String(Math.round(track.scrollLeft / track.clientWidth) + 1).padStart(2, '0');
				t = false;
			});
		}, { passive: true });
	});

	/* ---------- Gallery filter ---------- */
	$$('[data-ens-gallery]').forEach((g) => {
		const chips = $$('[data-ens-filter]', g);
		const items = $$('[data-ens-cat]', g);
		chips.forEach((chip) => chip.addEventListener('click', () => {
			const f = chip.dataset.ensFilter;
			chips.forEach((c) => {
				c.classList.toggle('is-active', c === chip);
				c.setAttribute('aria-pressed', String(c === chip));
			});
			items.forEach((it) => { it.hidden = f !== '*' && it.dataset.ensCat !== f; });
		}));
	});

	/* ---------- Share links ---------- */
	// A relative static export turns the permalink into a bare path; share the address actually shown.
	$$('.ens-share a').forEach((a) => {
		a.href = a.href.replace(/([?&](?:url|u|body)=)[^&]*/, (m, k) => k + encodeURIComponent(location.origin + location.pathname));
	});

	initReveals();
	measureBooks();
	onScroll();
	root.classList.add('ens-ready');
})();
