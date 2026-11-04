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

    // Segarkan caption + detail galeri aktif agar ikut bahasa terpilih.
    if (typeof renderLightboxMedia === 'function' && lightbox.classList.contains('active')) {
      renderLightboxMedia();
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
        '<div class="lightbox-head" id="lightboxHead">' +
          '<span class="lightbox-title" id="lightboxTitle"></span>' +
          '<span class="lightbox-counter" id="lightboxCounter"></span>' +
        '</div>' +
        '<div class="lightbox-media-container" id="lightboxMediaContainer"></div>' +
        '<button type="button" class="lightbox-nav lightbox-prev" id="lightboxPrevBtn" aria-label="Previous"><i class="fa-solid fa-angle-left"></i></button>' +
        '<button type="button" class="lightbox-nav lightbox-next" id="lightboxNextBtn" aria-label="Next"><i class="fa-solid fa-angle-right"></i></button>' +
        '<div class="lightbox-caption" id="lightboxCaption"></div>' +
        '<div class="lightbox-thumbs" id="lightboxThumbs"></div>' +
        '<button type="button" class="lightbox-detail-btn" id="lightboxDetailBtn"><i class="fa-solid fa-circle-info"></i> <span id="lightboxDetailLabel">Detail</span></button>' +
        '<div class="lightbox-detail-panel" id="lightboxDetailPanel" hidden>' +
          '<div class="lightbox-detail-meta"><span class="lightbox-detail-cat" id="lightboxDetailCat"></span></div>' +
          '<p class="lightbox-detail-desc" id="lightboxDetailDesc"></p>' +
        '</div>' +
      '</div>';
    document.body.appendChild(lightbox);
  }

  var mediaContainer = document.getElementById('lightboxMediaContainer');
  var captionEl = document.getElementById('lightboxCaption');
  var titleEl = document.getElementById('lightboxTitle');
  var counterEl = document.getElementById('lightboxCounter');
  var thumbsEl = document.getElementById('lightboxThumbs');
  var prevBtn = document.getElementById('lightboxPrevBtn');
  var nextBtn = document.getElementById('lightboxNextBtn');
  var detailBtn = document.getElementById('lightboxDetailBtn');
  var detailPanel = document.getElementById('lightboxDetailPanel');
  var detailLabel = document.getElementById('lightboxDetailLabel');
  var detailCat = document.getElementById('lightboxDetailCat');
  var detailDesc = document.getElementById('lightboxDetailDesc');

  // State galeri aktif: daftar media + indeks saat ini.
  var galleryItems = [];
  var galleryIndex = 0;

  function renderLightboxMedia() {
    if (!mediaContainer) return;

    var item = galleryItems[galleryIndex];
    if (!item) {
      mediaContainer.innerHTML = '';
      return;
    }

    mediaContainer.innerHTML = '';

    // Kalau file media gagal dimuat, tampilkan pesan — bukan area kosong.
    function showMediaError() {
      mediaContainer.innerHTML = '<div class="lightbox-media-error">' +
        '<i class="fa-solid fa-triangle-exclamation"></i>' +
        '<span>Media tidak dapat dimuat</span></div>';
    }

    if (item.type === 'video') {
      var video = document.createElement('video');
      video.src = item.src;
      video.controls = true;
      video.autoplay = true;
      video.playsInline = true;
      video.addEventListener('error', showMediaError);
      mediaContainer.appendChild(video);
    } else {
      var img = document.createElement('img');
      img.src = item.src;
      img.alt = item.caption || '';
      img.addEventListener('error', showMediaError);
      mediaContainer.appendChild(img);
    }

    var caption = currentLang === 'id'
      ? (item.caption_id || item.caption_en || '')
      : (item.caption_en || item.caption_id || '');

    if (captionEl) captionEl.textContent = caption;

    // Panel detail: kategori + deskripsi media aktif.
    if (detailCat) detailCat.textContent = currentLang === 'id'
      ? (item.category_id || item.category_en || '')
      : (item.category_en || item.category_id || '');

    if (detailDesc) detailDesc.textContent = currentLang === 'id'
      ? (item.description_id || item.description_en || '')
      : (item.description_en || item.description_id || '');

    var hasDetail = !!(
      (currentLang === 'id'
        ? (item.description_id || item.description_en || '')
        : (item.description_en || item.description_id || '')) ||
      (currentLang === 'id'
        ? (item.category_id || item.category_en || '')
        : (item.category_en || item.category_id || ''))
    );

    if (detailBtn) detailBtn.style.display = hasDetail ? 'inline-flex' : 'none';

    // Kalau item baru tidak punya detail, tutup panel yang masih terbuka.
    if (!hasDetail && detailPanel) detailPanel.hidden = true;

    if (counterEl) {
      counterEl.textContent = galleryItems.length > 1
        ? (galleryIndex + 1) + ' / ' + galleryItems.length
        : '';
    }

    var multi = galleryItems.length > 1;
    if (prevBtn) prevBtn.style.display = multi ? 'flex' : 'none';
    if (nextBtn) nextBtn.style.display = multi ? 'flex' : 'none';

    if (thumbsEl) {
      thumbsEl.innerHTML = '';
      if (multi) {
        galleryItems.forEach(function (thumb, idx) {
          var btn = document.createElement('button');
          btn.type = 'button';
          btn.className = 'lightbox-thumb' + (idx === galleryIndex ? ' active' : '');
          btn.setAttribute('aria-label', 'Media ' + (idx + 1));

          // Thumbnail: pakai poster (foto) bila ada. Video tanpa poster
          // cukup ikon play supaya tidak muncul gambar rusak.
          var poster = thumb.thumb || '';
          if (thumb.type === 'video' && poster === '') {
            btn.innerHTML = '<i class="fa-solid fa-circle-play"></i>';
          } else {
            var thumbImg = document.createElement('img');
            thumbImg.src = poster || thumb.src;
            thumbImg.alt = '';
            thumbImg.loading = 'lazy';
            btn.appendChild(thumbImg);

            // Foto poster gagal dimuat: ganti dengan ikon supaya tidak blank.
            thumbImg.addEventListener('error', function () {
              btn.innerHTML = thumb.type === 'video'
                ? '<i class="fa-solid fa-circle-play"></i>'
                : '<i class="fa-regular fa-image"></i>';
            });

            if (thumb.type === 'video') {
              var badge = document.createElement('i');
              badge.className = 'fa-solid fa-circle-play lightbox-thumb-play';
              btn.appendChild(badge);
            }
          }

          btn.addEventListener('click', function (e) {
            e.stopPropagation();
            galleryIndex = idx;
            renderLightboxMedia();
          });

          thumbsEl.appendChild(btn);
        });
      }
    }
  }

  function showLightboxAt(index) {
    if (!galleryItems.length) return;

    galleryIndex = (index + galleryItems.length) % galleryItems.length;
    renderLightboxMedia();
    lightbox.classList.add('active');
    document.body.style.overflow = 'hidden';
  }

  function openMediaLightbox(type, src, caption) {
    if (!src) return;

    galleryItems = [{ type: type, src: src, caption_en: caption || '', caption_id: '' }];
    galleryIndex = 0;

    if (titleEl) titleEl.textContent = '';

    renderLightboxMedia();
    lightbox.classList.add('active');
    document.body.style.overflow = 'hidden';
  }

  function openPackageGallery(packageId, title, startIndex) {
    var galleries = window.PACKAGE_GALLERIES || {};
    var items = galleries[packageId] || [];

    if (!items.length) return;

    galleryItems = items;
    galleryIndex = (typeof startIndex === 'number' && startIndex >= 0 && startIndex < items.length)
      ? startIndex
      : 0;

    if (titleEl) titleEl.textContent = title || '';
    if (detailPanel) detailPanel.hidden = true;

    renderLightboxMedia();
    lightbox.classList.add('active');
    document.body.style.overflow = 'hidden';
  }

  function closeMediaLightbox() {
    if (!lightbox.classList.contains('active')) return;
    lightbox.classList.remove('active');
    if (mediaContainer) mediaContainer.innerHTML = '';
    if (captionEl) captionEl.textContent = '';
    if (counterEl) counterEl.textContent = '';
    if (titleEl) titleEl.textContent = '';
    if (thumbsEl) thumbsEl.innerHTML = '';
    if (detailPanel) detailPanel.hidden = true;
    if (detailBtn) detailBtn.classList.remove('active');
    galleryItems = [];

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

  if (prevBtn) {
    prevBtn.addEventListener('click', function (e) {
      e.stopPropagation();
      showLightboxAt(galleryIndex - 1);
    });
  }

  if (nextBtn) {
    nextBtn.addEventListener('click', function (e) {
      e.stopPropagation();
      showLightboxAt(galleryIndex + 1);
    });
  }

  // Tombol "Detail" di dalam lightbox: tampilkan / sembunyikan panel detail media.
  if (detailBtn) {
    detailBtn.addEventListener('click', function (e) {
      e.stopPropagation();
      if (!detailPanel) return;

      detailPanel.hidden = !detailPanel.hidden;

      if (!detailPanel.hidden) {
        detailBtn.classList.add('active');
      } else {
        detailBtn.classList.remove('active');
      }
    });
  }

  // Delegasi klik: pakai metadata data-media/data-src bila ada,
  // jika tidak fallback ke <video>/<img> di dalam elemen media.
  document.addEventListener('click', function (e) {
    // Klik satu thumbnail galeri di kartu paket: buka modal langsung di item itu.
    var thumb = e.target.closest('.card-gallery [data-gallery-index]');

    if (thumb) {
      e.preventDefault();
      e.stopPropagation();

      var strip = thumb.closest('.card-gallery');

      if (strip) {
        openPackageGallery(
          parseInt(strip.getAttribute('data-gallery-trigger'), 10) || 0,
          strip.getAttribute('data-gallery-title'),
          parseInt(thumb.getAttribute('data-gallery-index'), 10) || 0
        );
      }

      return;
    }

    // Klik gambar paket tour (atau tombol Detail Galeri): buka seluruh galeri paket tersebut.
    var galleryTrigger = e.target.closest('[data-gallery-trigger]');

    if (galleryTrigger) {
      e.preventDefault();
      openPackageGallery(
        parseInt(galleryTrigger.getAttribute('data-gallery-trigger'), 10) || 0,
        galleryTrigger.getAttribute('data-gallery-title')
      );
      return;
    }

    var trigger = e.target.closest('[data-src], .gallery-item, .card-img-wrap, .featured-img-col, .polaroid-card');
    if (!trigger) return;

    // Jangan buka lightbox dari elemen yang bukan media (mis. tombol CTA di kartu).
    // Kartu galeri (.card-gallery) sudah ditangani di atas, jadi tombol di
    // dalamnya tetap membuka modal.
    if (e.target.closest('a, button:not(.lightbox-close-btn)') && !e.target.closest('.card-gallery')) return;

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

  // Keyboard: navigasi galeri saat lightbox terbuka
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      if (lightbox.classList.contains('active')) {
        closeMediaLightbox();
      }
      return;
    }

    if (!lightbox.classList.contains('active') || galleryItems.length < 2) return;

    if (e.key === 'ArrowLeft') {
      e.preventDefault();
      showLightboxAt(galleryIndex - 1);
    } else if (e.key === 'ArrowRight') {
      e.preventDefault();
      showLightboxAt(galleryIndex + 1);
    }
  });

  // Aksesibilitas: elemen galeri dengan role="button" bisa diaktifkan Enter/Space.
  document.addEventListener('keydown', function (e) {
    if (e.key !== 'Enter' && e.key !== ' ') return;

    var target = e.target.closest('.card-gallery');
    if (!target) return;

    e.preventDefault();
    target.click();
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
