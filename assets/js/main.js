/* KÜS Prüfstelle Blatter – Frontend (ohne jQuery) */
(() => {
	'use strict';
	const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	/* ---------- Navigation ---------- */
	const burger = document.querySelector('.hk-burger');
	const nav = document.getElementById('hk-nav');
	if (burger && nav) {
		burger.addEventListener('click', () => {
			const open = burger.getAttribute('aria-expanded') !== 'true';
			burger.setAttribute('aria-expanded', String(open));
			nav.classList.toggle('is-open', open);
			document.body.style.overflow = open ? 'hidden' : '';
		});
		nav.addEventListener('click', (e) => {
			if (e.target.closest('a') && nav.classList.contains('is-open')) {
				burger.click();
			}
		});
	}
	document.querySelectorAll('.hk-nav__toggle').forEach((btn) => {
		btn.addEventListener('click', () => {
			const li = btn.closest('li');
			const open = !li.classList.contains('is-open');
			li.classList.toggle('is-open', open);
			btn.setAttribute('aria-expanded', String(open));
		});
	});

	/* ---------- Lesefortschritt unter dem Header ---------- */
	const bar = document.querySelector('.hk-progress span');
	if (bar) {
		let ticking = false;
		const update = () => {
			const max = document.documentElement.scrollHeight - window.innerHeight;
			bar.style.setProperty('--p', max > 0 ? Math.min(1, window.scrollY / max) : 0);
			ticking = false;
		};
		window.addEventListener('scroll', () => { if (!ticking) { ticking = true; requestAnimationFrame(update); } }, { passive: true });
		update();
	}

	/* ---------- Einblenden + hochzählende Preise ---------- */
	const countUp = (el) => {
		const target = parseFloat(el.dataset.hkCount);
		if (!isFinite(target) || reduce) { return; }
		const start = performance.now();
		const dur = 2000;
		const step = (t) => {
			const p = Math.min(1, (t - start) / dur);
			el.textContent = Math.round(target * (1 - Math.pow(1 - p, 3)));
			if (p < 1) { requestAnimationFrame(step); }
		};
		el.textContent = '0';
		requestAnimationFrame(step);
	};
	if ('IntersectionObserver' in window) {
		const io = new IntersectionObserver((entries) => {
			entries.forEach((e) => {
				if (!e.isIntersecting) { return; }
				e.target.classList.add('is-visible');
				e.target.querySelectorAll('[data-hk-count]').forEach(countUp);
				io.unobserve(e.target);
			});
		}, { threshold: 0.15 });
		document.querySelectorAll('[data-hk-reveal]').forEach((el) => io.observe(el));
	} else {
		document.querySelectorAll('[data-hk-reveal]').forEach((el) => el.classList.add('is-visible'));
	}

	/* ---------- Startseiten-Slider ---------- */
	document.querySelectorAll('[data-hk-slider]').forEach((root) => {
		const texts = [...root.querySelectorAll('.hk-slider__text')];
		const bgs = [...root.querySelectorAll('.hk-slider__bgimg')];
		const cards = [...root.querySelectorAll('.hk-slider__card')];
		const track = root.querySelector('.hk-slider__track');
		const n = texts.length;
		if (n < 2) { return; }
		let i = 0;
		let timer;
		const go = (to) => {
			i = (to + n) % n;
			texts.forEach((t, k) => { const on = k === i; t.classList.toggle('is-active', on); t.hidden = !on; });
			bgs.forEach((b, k) => b.classList.toggle('is-active', k === i));
			cards.forEach((c, k) => c.classList.toggle('is-active', k === i));
			if (track && cards[i]) { track.style.transform = `translateX(${-cards[i].offsetLeft}px)`; }
		};
		const auto = () => { clearInterval(timer); if (!reduce) { timer = setInterval(() => go(i + 1), 7000); } };
		root.querySelector('.hk-slider__prev')?.addEventListener('click', () => { go(i - 1); auto(); });
		root.querySelector('.hk-slider__next')?.addEventListener('click', () => { go(i + 1); auto(); });
		cards.forEach((c, k) => c.addEventListener('click', () => { go(k); auto(); }));
		root.addEventListener('mouseenter', () => clearInterval(timer));
		root.addEventListener('mouseleave', auto);
		auto();
	});

	/* ---------- Bewertungs-Karussell ---------- */
	document.querySelectorAll('[data-hk-carousel]').forEach((root) => {
		const track = root.querySelector('.hk-carousel__track');
		const items = [...track.children];
		let i = 0;
		let timer;
		const perView = () => (window.innerWidth < 768 ? 1 : 2);
		const go = (to) => {
			const max = Math.max(0, items.length - perView());
			i = to > max ? 0 : (to < 0 ? max : to);
			track.style.transform = `translateX(${-i * (100 / perView())}%)`;
		};
		const auto = () => { clearInterval(timer); if (!reduce) { timer = setInterval(() => go(i + 1), 5000); } };
		root.querySelector('.hk-carousel__prev').addEventListener('click', () => { go(i - 1); auto(); });
		root.querySelector('.hk-carousel__next').addEventListener('click', () => { go(i + 1); auto(); });
		let x0 = null;
		track.addEventListener('touchstart', (e) => { x0 = e.touches[0].clientX; }, { passive: true });
		track.addEventListener('touchend', (e) => {
			if (x0 === null) { return; }
			const dx = e.changedTouches[0].clientX - x0;
			if (Math.abs(dx) > 40) { go(i + (dx < 0 ? 1 : -1)); auto(); }
			x0 = null;
		});
		window.addEventListener('resize', () => go(i));
		auto();
	});

	/* ---------- FAQ: immer nur eine Antwort offen ---------- */
	document.querySelectorAll('.hk-faq').forEach((faq) => {
		faq.querySelectorAll('details').forEach((d) => {
			d.addEventListener('toggle', () => {
				if (d.open) { faq.querySelectorAll('details[open]').forEach((o) => { if (o !== d) { o.open = false; } }); }
			});
		});
	});
})();
