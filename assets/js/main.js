/**
 * NUSATRIPPER.COM — Landing Page Interactions
 * Bilingual switch (data-en / data-id), drawer mobile, filter paket,
 * FAQ accordion, dan lightbox media.
 */
(function () {
  'use strict';

  var STORAGE_KEY = 'es_lang';
  var currentLang = 'en';

  /* -----------------------------------------------------------------
   * Bilingual: semua teks bilingual dirender server sebagai
   * data-en / data-id (fallback ID kosong sudah dihandle di helper).
   * ----------------------------------------------------------------- */
  function applyLanguage(lang) {
    currentLang = lang === 'id' ? 'id' : 'en';
    document.documentElement.lang = currentLang;

    document.querySelectorAll('[data-en]').forEach(function (el) {
      var value = el.dataset[currentLang];
      if (typeof value === 'string' && value !== '') {
        el.textContent = value;
      }
    });

    var btn = document.getElementById('langToggleBtn');
    if (btn) {
      btn.innerHTML = currentLang === 'en'
        ? '<span class="active-lang">EN</span> | <span>ID</span>'
        : '<span>EN</span> | <span class="active-lang">ID</span>';
    }

    try {
      localStorage.setItem(STORAGE_KEY, currentLang);
    } catch (err) {
      /* localStorage bisa diblokir (mode privat) — abaikan */
    }
  }

  function toggleLanguage() {
    applyLanguage(currentLang === 'en' ? 'id' : 'en');
  }

  /* -----------------------------------------------------------------
   * Drawer navigasi mobile
   * ----------------------------------------------------------------- */
  var mobileBtn = document.getElementById('mobileMenuBtn');
  var navMenu = document.getElementById('navMenu');
  var navBackdrop = null;

  if (navMenu) {
    navBackdrop = document.querySelector('.nav-backdrop');
    if (!navBackdrop) {
      navBackdrop = document.createElement('div');
      navBackdrop.className = 'nav-backdrop';
      document.body.appendChild(navBackdrop);
    }
  }

  function openMobileMenu() {
    if (!navMenu || !mobileBtn) return;
    navMenu.classList.add('show');
    if (navBackdrop) navBackdrop.classList.add('show');
    document.body.style.overflow = 'hidden';
    mobileBtn.innerHTML = '<i class="fa-solid fa-xmark"></i>';
    mobileBtn.setAttribute('aria-expanded', 'true');
  }

  function closeMobileMenu() {
    if (!navMenu || !mobileBtn) return;
    navMenu.classList.remove('show');
    if (navBackdrop) navBackdrop.classList.remove('show');
    document.body.style.overflow = '';
    mobileBtn.innerHTML = '<i class="fa-solid fa-bars"></i>';
    mobileBtn.setAttribute('aria-expanded', 'false');
  }

  function isMobileMenuOpen() {
    return !!(navMenu && navMenu.classList.contains('show'));
  }

  /* -----------------------------------------------------------------
   * Lightbox media
   * ----------------------------------------------------------------- */
  var lightbox = document.getElementById('lightboxModal');

  if (!lightbox) {
    lightbox = document.createElement('div');
    lightbox.id = 'lightboxModal';
    lightbox.className = 'lightbox-modal';
    lightbox.setAttribute('role', 'dialog');
    lightbox.setAttribute('aria-modal', 'true');
    lightbox.innerHTML =
      '<div class="lightbox-content-box">' +
        '<button type="button" class="lightbox-close-btn" id="lightboxCloseBtn" aria-label="Close preview">&times;</button>' +
        '<div class="lightbox-media-container" id="lightboxMediaContainer"></div>' +
        '<div class="lightbox-caption" id="lightboxCaption"></div>' +
      '</div>';
    document.body.appendChild(lightbox);
  }

  var mediaContainer = document.getElementById('lightboxMediaContainer');
  var captionEl = document.getElementById('lightboxCaption');

  function openMediaLightbox(type, src, caption) {
    if (!src || !mediaContainer) return;

    mediaContainer.innerHTML = '';

    if (type === 'video') {
      var video = document.createElement('video');
      video.src = src;
      video.controls = true;
      video.autoplay = true;
      video.playsInline = true;
      mediaContainer.appendChild(video);
    } else {
      var img = document.createElement('img');
      img.src = src;
      img.alt = caption || '';
      mediaContainer.appendChild(img);
    }

    if (captionEl) captionEl.textContent = caption || '';
    lightbox.classList.add('active');
    document.body.style.overflow = 'hidden';
  }

  function closeMediaLightbox() {
    if (!lightbox.classList.contains('active')) return;
    lightbox.classList.remove('active');
    if (mediaContainer) mediaContainer.innerHTML = '';
    if (captionEl) captionEl.textContent = '';

    // Jangan buka scroll body kalau drawer sedang terbuka
    if (!isMobileMenuOpen()) document.body.style.overflow = '';
  }

  lightbox.addEventListener('click', function (e) {
    if (e.target === lightbox || e.target.classList.contains('lightbox-content-box')) {
      closeMediaLightbox();
    }
  });

  var lightboxCloseBtn = document.getElementById('lightboxCloseBtn');
  if (lightboxCloseBtn) {
    lightboxCloseBtn.addEventListener('click', closeMediaLightbox);
  }

  // Delegasi klik: pakai metadata data-media/data-src bila ada,
  // jika tidak fallback ke <video>/<img> di dalam elemen media.
  document.addEventListener('click', function (e) {
    var trigger = e.target.closest('[data-src], .gallery-item, .card-img-wrap, .featured-img-col, .polaroid-card');
    if (!trigger) return;

    // Jangan buka lightbox dari elemen yang bukan media (mis. tombol CTA di kartu)
    if (e.target.closest('a, button:not(.lightbox-close-btn)')) return;

    var src = trigger.getAttribute('data-src');
    var type = trigger.getAttribute('data-media');
    var caption = trigger.getAttribute('data-caption');

    if (!src) {
      var video = trigger.querySelector('video');
      var img = trigger.querySelector('img');

      if (video) {
        var source = video.querySelector('source');
        src = source ? source.src : video.currentSrc || video.src;
        type = 'video';
      } else if (img) {
        src = img.currentSrc || img.src;
        type = 'image';
      } else {
        return;
      }
    }

    if (!caption) {
      var captionNode = trigger.querySelector('.card-title, .gallery-name, .polaroid-caption, h4, strong');
      caption = captionNode ? captionNode.textContent.trim() : '';
    }

    openMediaLightbox(type === 'video' ? 'video' : 'image', src, caption);
  });

  /* -----------------------------------------------------------------
   * Navbar shadow saat scroll
   * ----------------------------------------------------------------- */
  var navbar = document.getElementById('navbar');
  var ticking = false;

  function updateNavbar() {
    if (navbar) navbar.classList.toggle('scrolled', window.scrollY > 40);
    ticking = false;
  }

  window.addEventListener('scroll', function () {
    if (!ticking) {
      ticking = true;
      window.requestAnimationFrame(updateNavbar);
    }
  }, { passive: true });

  /* -----------------------------------------------------------------
   * Filter paket wisata
   * ----------------------------------------------------------------- */
  var filterBtns = Array.prototype.slice.call(document.querySelectorAll('.filter-btn'));
  var packageCards = Array.prototype.slice.call(document.querySelectorAll('.package-card'));

  filterBtns.forEach(function (btn) {
    btn.addEventListener('click', function () {
      filterBtns.forEach(function (b) {
        b.classList.remove('active');
        b.setAttribute('aria-pressed', 'false');
      });
      btn.classList.add('active');
      btn.setAttribute('aria-pressed', 'true');

      var filterVal = btn.getAttribute('data-filter');

      packageCards.forEach(function (card) {
        var match = filterVal === 'all' || card.getAttribute('data-category') === filterVal;
        card.classList.toggle('hidden', !match);
      });
    });
  });

  /* -----------------------------------------------------------------
   * FAQ accordion
   * ----------------------------------------------------------------- */
  var faqItems = Array.prototype.slice.call(document.querySelectorAll('.faq-item'));

  faqItems.forEach(function (item) {
    var header = item.querySelector('.faq-header');
    if (!header) return;

    header.addEventListener('click', function () {
      var isActive = item.classList.contains('active');

      faqItems.forEach(function (other) {
        other.classList.remove('active');
        var otherHeader = other.querySelector('.faq-header');
        if (otherHeader) otherHeader.setAttribute('aria-expanded', 'false');
      });

      if (!isActive) {
        item.classList.add('active');
        header.setAttribute('aria-expanded', 'true');
      }
    });
  });

  /* -----------------------------------------------------------------
   * Init
   * ----------------------------------------------------------------- */
  document.addEventListener('DOMContentLoaded', function () {
    var langBtn = document.getElementById('langToggleBtn');
    if (langBtn) langBtn.addEventListener('click', toggleLanguage);

    if (mobileBtn && navMenu) {
      mobileBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        if (isMobileMenuOpen()) {
          closeMobileMenu();
        } else {
          openMobileMenu();
        }
      });

      if (navBackdrop) navBackdrop.addEventListener('click', closeMobileMenu);

      navMenu.querySelectorAll('a').forEach(function (link) {
        link.addEventListener('click', closeMobileMenu);
      });
    }

    var saved = 'en';
    try {
      saved = localStorage.getItem(STORAGE_KEY) || 'en';
    } catch (err) {
      saved = 'en';
    }
    applyLanguage(saved);

    updateNavbar();
  });

  /* Escape menutup lightbox / drawer, resize besar menutup drawer */
  document.addEventListener('keydown', function (e) {
    if (e.key !== 'Escape') return;
    if (lightbox.classList.contains('active')) {
      closeMediaLightbox();
    } else if (isMobileMenuOpen()) {
      closeMobileMenu();
    }
  });

  window.addEventListener('resize', function () {
    if (window.innerWidth >= 900 && isMobileMenuOpen()) closeMobileMenu();
  });
})();
