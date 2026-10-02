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
	let shieldHeader = () => false; // set by the mobile pinned chapters below
	const updateHeader = (y) => {
		if (!header) return;
		header.classList.toggle('is-scrolled', y > 24);
		const menuOpen = root.classList.contains('ens-menu-open');
		const down = y > lastY && y > 600 && !menuOpen;
		header.classList.toggle('is-hidden', (down || (!menuOpen && shieldHeader(y))) && !header.contains(document.activeElement));
		lastY = y;
	};

	/* ---------- Overlay menu ---------- */
	const toggle = document.querySelector('[data-ens-menu-toggle]');
	const menu = document.querySelector('[data-ens-menu]');

	// Sections with children become accordions: a toggle beside the parent link (which stays a
	// link), one section open at a time.
	const sections = $$('.ens-menu__list > .menu-item-has-children').map((li, k) => {
		const link = li.querySelector(':scope > a');
		const sub = li.querySelector(':scope > .sub-menu');
		const btn = document.createElement('button');
		sub.id ||= `ens-menu-sub-${k + 1}`;
		btn.type = 'button';
		btn.className = 'ens-menu__toggle';
		btn.setAttribute('aria-expanded', 'false');
		btn.setAttribute('aria-controls', sub.id);
		btn.innerHTML = '<span class="screen-reader-text"></span>';
		btn.firstChild.textContent = link.textContent;
		link.after(btn);
		li.classList.add('ens-menu__acc');
		return { li, btn };
	});
	const expand = (target) => sections.forEach(({ li, btn }) => {
		li.classList.toggle('is-expanded', li === target);
		btn.setAttribute('aria-expanded', String(li === target));
	});
	sections.forEach(({ li, btn }) => btn.addEventListener('click', () => expand(btn.getAttribute('aria-expanded') === 'true' ? null : li)));
	$$('.ens-menu__list > li, .ens-menu__foot').forEach((el, i) => el.style.setProperty('--i', i));

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
			const done = () => { if (!menu.classList.contains('is-open')) { menu.hidden = true; expand(null); } };
			reduce ? done() : setTimeout(done, 500);
		}
	};
	toggle?.addEventListener('click', () => setMenu(toggle.getAttribute('aria-expanded') !== 'true'));
	menu?.addEventListener('click', (e) => { if (e.target.closest('a')) setMenu(false); });

	/* ---------- Desktop dropdowns (CSS hover/focus panels) ---------- */
	// Stagger index; images load on a panel's first opening; Escape closes the open panel.
	// Touch screens without hover (tablets ≥1025px): the first tap opens the panel, the second follows the link.
	const dropdowns = $$('.ens-nav__list > .menu-item-has-children');
	const noHover = matchMedia('(hover: none)');
	const touchOpen = (target) => dropdowns.forEach((li) => li.classList.toggle('is-touch-open', li === target));
	dropdowns.forEach((li) => {
		$$(':scope > .sub-menu > li', li).forEach((item, i) => item.style.setProperty('--i', i));
		const prime = () => li.classList.add('is-primed');
		li.addEventListener('pointerenter', prime, { once: true });
		li.addEventListener('focusin', prime, { once: true });
		li.addEventListener('mouseleave', () => li.classList.remove('is-dismissed'));
		li.addEventListener('focusout', (e) => { if (!li.contains(e.relatedTarget)) li.classList.remove('is-dismissed'); });
		li.querySelector(':scope > a').addEventListener('click', (e) => {
			if (!noHover.matches || li.classList.contains('is-touch-open')) return;
			e.preventDefault();
			prime();
			touchOpen(li);
		});
	});
	document.addEventListener('click', (e) => { if (!e.target.closest('.ens-nav__list > .menu-item-has-children')) touchOpen(null); });

	document.addEventListener('keydown', (e) => {
		if (e.key !== 'Escape') return;
		if (root.classList.contains('ens-menu-open')) {
			setMenu(false);
			toggle.focus();
			return;
		}
		const open = dropdowns.find((li) => li.matches(':hover, :focus-within, .is-touch-open'));
		if (open) {
			touchOpen(null);
			open.classList.add('is-dismissed');
			if (open.contains(document.activeElement)) open.querySelector('a').focus();
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

	/* ---------- Pinned horizontal chapters (mobile ≤767px, motion allowed) ---------- */
	// Treatments, Journal and Lookbook rails: when the section reaches the viewport it pins (its inner
	// block turns sticky) and further vertical scrolling moves the rail through its overflow; the
	// section releases once the last item is in view, and the same in reverse. Horizontal position is
	// derived from scroll progress only (linear, exactly reversible). The section is lengthened by a
	// spacer of `overflow × PIN_RATIO`, measured from real geometry. DOM order is untouched; while
	// pinned the rail is moved programmatically (manual horizontal swipe off, so nothing drifts), and
	// keyboard focus scrolls the page to the focused card's position. Artists and other rails stay native.
	const mobile = matchMedia('(max-width: 767px)');
	const PIN_RATIO = 0.7; // vertical px per horizontal px: deliberate, never tiring (Lookbook ≈ 1200 px at 390 wide)
	const chapters = $$('.ens-services.ens-rail, .ens-cards.ens-rail, .ens-lookbook__viewport').map((rail) => {
		const book = rail.closest('.ens-lookbook');
		let host = book;
		if (!host) { // the page's top-level Elementor section holding the heading and the rail
			host = rail.closest('.e-con');
			while (host && host.parentElement.closest('.e-con')) host = host.parentElement.closest('.e-con');
		}
		const pin = book ? book.querySelector('.ens-lookbook__stage') : host?.querySelector(':scope > .e-con-inner');
		return pin ? { rail, host, pin, start: 0, dist: 0, max: 0, on: false, railH: 0, railTop: 0, headH: 0, tooTall: false } : null;
	}).filter(Boolean);
	const chaptersOn = () => mobile.matches && !reduce;
	const measureChapters = () => {
		const on = chaptersOn();
		chapters.forEach((c) => {
			c.max = on ? c.rail.scrollWidth - c.rail.clientWidth : 0;
			c.on = on && c.max > 0;
			[c.host, c.pin, c.rail].forEach((el, i) => el.classList.toggle(['ens-chapter', 'ens-chapter__pin', 'ens-chapter__rail'][i], c.on));
			if (!c.on) {
				c.host.style.removeProperty('--ens-pin-d');
				c.host.style.removeProperty('--ens-pin-top');
				return;
			}
			const vh = innerHeight;
			const h = c.pin.offsetHeight;
			// The whole section centred when it fits; otherwise the rail itself centred (within the
			// section's own edges), so the cards are fully in view however tall the section is.
			const railOffset = c.rail.getBoundingClientRect().top - c.pin.getBoundingClientRect().top;
			// A rail taller than the screen (e.g. long translated cards on a short phone) keeps its top edge.
			let top = h <= vh ? (vh - h) / 2 : clamp(Math.max(0, (vh - c.rail.offsetHeight) / 2) - railOffset, vh - h, 0);
			// Fixed header (as shown when scrolling up): keep the pinned rail below it when there is room;
			// when there is not (short phones), the header stays out of the way while the rail crosses it.
			const headH = header?.querySelector('.ens-header__main')?.offsetHeight || 0;
			c.railH = c.rail.offsetHeight;
			c.tooTall = c.railH + headH > vh;
			if (!c.tooTall && top + railOffset < headH) top += headH - (top + railOffset);
			c.railTop = top + railOffset; // viewport y of the rail's top while pinned
			c.headH = headH;
			c.dist = Math.round(c.max * PIN_RATIO);
			c.host.style.setProperty('--ens-pin-d', `${c.dist}px`);
			c.host.style.setProperty('--ens-pin-top', `${Math.round(top)}px`);
			const hostTop = c.host.getBoundingClientRect().top + scrollY;
			c.start = hostTop + parseFloat(getComputedStyle(c.host).paddingTop) - Math.round(top); // scrollY at which it pins
		});
	};
	// True while a chapter rail crosses the header's band: a released rail sliding back in from above
	// (re-entering from below), or a rail too tall to pin below the header. The header holds back
	// instead of covering the cards, and returns once the rail is clear. Never on ≥768px.
	shieldHeader = (y) => chapters.some((c) => {
		if (!c.on) return false;
		const railTop = c.railTop + Math.max(0, c.start - y) - Math.max(0, y - c.start - c.dist);
		return railTop < c.headH && railTop + c.railH > c.headH;
	});
	const updateChapters = () => {
		chapters.forEach((c) => {
			if (!c.on) return;
			const x = clamp((scrollY - c.start) / c.dist, 0, 1) * c.max;
			if (Math.abs(c.rail.scrollLeft - x) >= 0.5) c.rail.scrollLeft = x;
		});
	};
	if (chapters.length) {
		// Keyboard: bring a focused card into view by scrolling the page to its point in the chapter.
		chapters.forEach((c) => c.rail.addEventListener('focusin', (e) => {
			if (!c.on) return;
			const item = [...c.rail.querySelectorAll(':scope > *, .ens-look')].find((el) => el.contains(e.target) && !el.matches('ul'));
			if (!item) return;
			const pad = parseFloat(getComputedStyle(c.rail).paddingLeft) || parseFloat(getComputedStyle(item.parentElement).paddingLeft) || 0;
			const x = clamp(item.offsetLeft - pad, 0, c.max);
			scrollTo({ top: c.start + (x / c.max) * c.dist, behavior: 'instant' });
			updateChapters();
		}));
		let queued = 0;
		const remeasure = () => { cancelAnimationFrame(queued); queued = requestAnimationFrame(() => { measureChapters(); updateChapters(); }); };
		const ro = new ResizeObserver(remeasure); // fonts, translation, late images, orientation
		chapters.forEach((c) => { ro.observe(c.pin); ro.observe(c.rail); });
		ro.observe(document.body);
		mobile.addEventListener('change', remeasure);
		measureChapters();
	}

	/* ---------- One scroll loop ---------- */
	let ticking = false;
	const onScroll = () => {
		clearTimeout(settleTimer);
		if (pending.size) settleTimer = setTimeout(sweep, 160);
		if (ticking) return;
		ticking = true;
		requestAnimationFrame(() => {
			updateChapters();
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
		resizeTimer = setTimeout(() => { measureBooks(); measureChapters(); onScroll(); }, 150);
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
