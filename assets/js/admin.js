/* =====================================================================
   Admin panel - interaksi ringan
   ===================================================================== */

(function () {
	'use strict';

	var sidebar = document.getElementById('adminSidebar');
	var backdrop = document.getElementById('adminBackdrop');
	var toggle = document.getElementById('menuToggle');

	function setSidebar(open) {
		if (!sidebar) return;

		sidebar.classList.toggle('is-open', open);

		if (backdrop) {
			backdrop.hidden = !open;
			backdrop.classList.toggle('is-open', open);
		}

		if (toggle) {
			toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
		}

		document.body.style.overflow = open ? 'hidden' : '';
	}

	if (toggle) {
		toggle.addEventListener('click', function () {
			setSidebar(!sidebar.classList.contains('is-open'));
		});
	}

	if (backdrop) {
		backdrop.addEventListener('click', function () {
			setSidebar(false);
		});
	}

	document.addEventListener('keydown', function (event) {
		if (event.key === 'Escape') setSidebar(false);
	});

	// Tutup drawer setelah memilih menu (hanya di layar kecil).
	if (sidebar) {
		sidebar.addEventListener('click', function (event) {
			var link = event.target.closest('a.sidebar-link');
			if (link && window.innerWidth < 900) setSidebar(false);
		});
	}

	// Konfirmasi untuk aksi destruktif.
	document.querySelectorAll('[data-confirm]').forEach(function (el) {
		el.addEventListener('click', function (event) {
			var message = el.getAttribute('data-confirm') || 'Yakin ingin melanjutkan?';
			if (!window.confirm(message)) {
				event.preventDefault();
			}
		});
	});

	// Preview media sebelum diunggah.
	document.querySelectorAll('input[type="file"][data-preview]').forEach(function (input) {
		input.addEventListener('change', function () {
			var target = document.querySelector(input.getAttribute('data-preview'));
			var file = input.files && input.files[0];

			if (!target || !file) return;

			target.src = URL.createObjectURL(file);
			target.hidden = false;

			if (target.tagName === 'VIDEO') target.load();

			var empty = document.querySelector(input.getAttribute('data-empty'));
			if (empty) empty.hidden = true;
		});
	});

	// Fallback media: kalau file gagal dimuat (path salah / file hilang),
	// sembunyikan elemen rusak dan tampilkan kotak pengganti agar tidak blank.
	function showFallback(el, selector) {
		el.hidden = true;

		var fallback = selector ? document.querySelector(selector) : null;
		if (fallback) fallback.hidden = false;
	}

	// Thumbnail di tabel daftar: <img data-fallback="next"> -> elemen setelahnya.
	document.querySelectorAll('[data-fallback]').forEach(function (el) {
		el.addEventListener('error', function () {
			var mode = el.getAttribute('data-fallback');
			if (mode === 'next') {
				var sibling = el.nextElementSibling;
				if (sibling) sibling.classList.remove('is-hidden');
				el.classList.add('is-hidden');
				return;
			}
			showFallback(el, mode);
		});
	});

	// Preview di form: <img|video data-empty="#empty-xxx">.
	document.querySelectorAll('.media-preview[data-empty]').forEach(function (el) {
		el.addEventListener('error', function () {
			showFallback(el, el.getAttribute('data-empty'));
		});

		// Video tanpa sumber sama sekali tidak memicu "error"; pantau juga
		// kegagalan metadata.
		if (el.tagName === 'VIDEO') {
			el.addEventListener('stalled', function () {
				if (!el.getAttribute('src')) showFallback(el, el.getAttribute('data-empty'));
			});
		}
	});
})();
