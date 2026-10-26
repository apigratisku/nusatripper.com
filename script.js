/**
 * NUSATRIPPER.COM — LOMBOK TOUR & EXPEDITION
 * Interactive Logic: Bilingual Translation (EN/ID), Package Filter, FAQ Accordion, Sticky Nav
 */

// Bilingual Dictionary
const translations = {
  en: {
    nav_home: "Home",
    nav_packages: "Tour Packages",
    nav_whaleshark: "Whale Shark Expedition",
    nav_whyus: "Why Us",
    nav_gallery: "NTB Gallery",
    nav_faq: "FAQ",
    btn_book_wa: "Book via WhatsApp",
    
    hero_badge: "Lombok's #1 Marine & Adventure Specialist",
    hero_title: "Unforgettable Lombok Tours, Diving & Whale Shark Expeditions",
    hero_subtitle: "Discover untouched nature, swim alongside gentle giants, and create memories that last a lifetime with certified local guides and premium service.",
    hero_btn_explore: "Explore Packages",
    hero_btn_chat: "Chat on WhatsApp",
    
    trust_1_title: "Licensed Guides",
    trust_1_sub: "Certified Diving Instructors",
    trust_2_title: "Safe & Comfortable",
    trust_2_sub: "Standardized Safety Gear",
    trust_3_title: "24/7 Fast Response",
    trust_3_sub: "Friendly Tour Consultants",
    
    featured_badge: "Most Popular Bucket List",
    featured_tag: "SIGNATURE EXPEDITION",
    featured_title: "Whale Shark & Giant Manta Ray Expedition",
    featured_desc: "Experience the thrilling once-in-a-lifetime opportunity to swim alongside the gentle giants of the ocean in their natural sanctuary. Our experienced captains know the seasonal feeding locations to guarantee an ethical, respectful, and safe marine encounter.",
    featured_perk_1: "Free GoPro 4K Underwater Photos & Videos",
    featured_perk_2: "Fresh Meals, Mineral Water & Tropical Fruits",
    featured_perk_3: "Complete Life Jacket & Snorkel Gear Provided",
    featured_perk_4: "Return Air-Conditioned Land & Boat Transfer",
    btn_book_expedition: "Book Expedition Now",
    
    packages_tag: "HANDCRAFTED EXPERIENCES",
    packages_title: "Explore Our Curated Lombok Tour Packages",
    packages_subtitle: "Choose from thrilling underwater adventures to scenic mountain waterfalls and cultural heritage tours.",
    
    filter_all: "All Packages",
    filter_diving: "Diving & Snorkeling",
    filter_island: "Island & Overland",
    filter_transport: "VIP Transport",
    
    pkg1_title: "Whale Shark & Giant Manta Expedition (Sumbawa)",
    pkg1_desc: "Get up close with magnificent whale sharks and gliding manta rays in their untouched sanctuary in Saleh Bay, Sumbawa with certified divemasters. Diving IDR 5.500.000/pax (min 2 pax) | Snorkeling IDR 3.500.000/pax.",
    pkg1_inc1: "Snorkeling IDR 3.5M / Diving IDR 5.5M (Min. 2 PAX)",
    pkg1_inc2: "Comfortable Hotel/Resort Accommodation",
    pkg1_inc3: "All Meals & Refreshments Included",
    pkg1_inc4: "Free High-Res Underwater Photos & Video",
    
    pkg2_title: "North Lombok & 3 Gili Discovery + Rinjani Waterfalls",
    pkg2_desc: "Explore the iconic underwater statues of Gili Meno, swim with turtles at Gili Trawangan, followed by lush Sendang Gile & Tiu Kelep waterfalls.",
    pkg2_inc1: "4x Guided Dives or 3 Gili Snorkeling Tour",
    pkg2_inc2: "Sendang Gile & Tiu Kelep Waterfall Trek",
    pkg2_inc3: "AC Transport, Hotel, & Entry Tickets",
    pkg2_inc4: "Full Snorkel/Scuba Gear Provided",
    
    pkg3_title: "Snorkeling & Traditional Fishing Adventure (3 Gili)",
    pkg3_desc: "Charter a private glass-bottom boat to Gili Trawangan, Meno, and Air. Snorkel vibrant coral reefs and experience traditional line fishing.",
    pkg3_inc1: "Private Glass-Bottom Boat & Local Captain",
    pkg3_inc2: "Snorkeling in Turtle Point & Coral Garden",
    pkg3_inc3: "Fishing Equipment & Fresh Bait",
    pkg3_inc4: "Free GoPro Photos Documentation",
    
    pkg4_title: "Complete Lombok Overland Tour (Custom Itinerary)",
    pkg4_desc: "Immerse yourself in South & Central Lombok. Discover Kuta Mandalika beach, Merese Hill panorama, traditional Sasak Sade Village, and weaving center Sukarara.",
    pkg4_inc1: "Kuta Mandalika, Tanjung Aan & Merese Hill",
    pkg4_inc2: "Sade Indigenous Village & Sukarara Weaving",
    pkg4_inc3: "Islamic Center Hubbul Wathan Mataram",
    pkg4_inc4: "Private AC MPV Vehicle + Fuel + Local Guide",
    
    pkg5_title: "Private VIP Airport Transfer & Car Rental",
    pkg5_desc: "Punctual, comfortable, and sanitized private transfers to/from Lombok International Airport (LOP), Bangsal Harbor, Senggigi, and Kuta.",
    pkg5_inc1: "Prompt Meet & Greet with Name Signboard",
    pkg5_inc2: "Clean, Cold AC MPV / SUV Vehicles",
    pkg5_inc3: "Experienced, English-Speaking Drivers",
    pkg5_inc4: "Includes Fuel, Parking & Toll Fees",
    
    card_from: "Starting from",
    btn_book_now: "Book Now",
    
    why_tag: "THE NUSATRIPPER.COM ADVANTAGE",
    why_title: "Why Travel With NusaTripper.com?",
    why_subtitle: "We bring together authentic local wisdom, strict safety standards, and personalized hospitality for your dream trip.",
    
    why1_title: "Professional & Certified Team",
    why1_desc: "All our dive masters, boat captains, and tour guides are fully licensed with extensive regional knowledge and first-aid safety certifications.",
    why2_title: "Safety & Comfort Priority",
    why2_desc: "We operate well-maintained vessels, top-tier Cressi/Mares snorkeling and scuba equipment, and clean air-conditioned vehicles for all transfers.",
    why3_title: "Best Service & 100% Satisfaction",
    why3_desc: "Flexible private itineraries tailored to your schedule, dietary preferences, and travel style without hidden surcharges.",
    why4_title: "Unforgettable Documentation",
    why4_desc: "Receive complimentary 4K underwater GoPro photos and drone/aerial video footage to share memories that last forever.",
    
    gallery_tag: "DISCOVER LOMBOK & NTB",
    gallery_title: "Spectacular Destinations of West Nusa Tenggara",
    gallery_subtitle: "From dramatic volcanic waterfalls and white sand atolls to mystical underwater sculptures.",
    
    testi_tag: "TRAVELER EXPERIENCES",
    testi_title: "Loved by Travelers Worldwide",
    
    faq_tag: "FREQUENTLY ASKED QUESTIONS",
    faq_title: "Got Questions? We Have Answers",
    faq_subtitle: "Find helpful details regarding our tours, booking policies, and marine safety instructions.",
    
    faq_q1: "When is the best season to swim with Whale Sharks in Lombok/Sumbawa?",
    faq_a1: "Whale Sharks can be seen year-round in Saleh Bay because they inhabit the calm bay waters and feed near the traditional fisherman platforms (bagan). However, the best ocean conditions with optimal underwater visibility are generally from April to November.",
    
    faq_q2: "What should I bring on the snorkeling or diving tour?",
    faq_a2: "We provide all high-quality snorkeling gear, life vests, and scuba equipment. We recommend bringing your swimsuit/rashguard, reef-safe sunscreen, a dry change of clothes, sunglasses, a towel, and personal medications if you are prone to seasickness.",
    
    faq_q3: "How do I book and what is the payment/deposit policy?",
    faq_a3: "Booking is simple via WhatsApp or email. A 30% deposit is required to secure your boat charter, certified guide, and date reservation. The remaining balance can be settled upon arrival via Bank Transfer, Wise, Credit Card, or Cash (IDR/USD).",
    
    faq_q4: "What happens if the weather is bad? What is your cancellation policy?",
    faq_a4: "Your safety is our #1 priority. If the harbor authority restricts sailing due to extreme weather or sea conditions, we offer a free reschedule or a full refund of your deposit. Free cancellations are accepted up to 48 hours prior to the scheduled departure.",
    
    faq_q5: "Can non-swimmers or beginners join the 3 Gili Snorkeling Tour?",
    faq_a5: "Yes, absolutely! We provide certified flotation life vests and personal guide assistance. Our guides will swim alongside beginners and hold a safety buoy to ensure you can comfortably admire the turtles and underwater statues without fear.",
    
    cta_heading: "Ready for an Epic Lombok & Whale Shark Adventure?",
    cta_text: "Contact our local travel specialists today and receive a tailored itinerary with exclusive WhatsApp direct booking discounts.",
    btn_wa_chat: "Chat Directly via WhatsApp",
    
    footer_bio: "Certified tour operator in Lombok, West Nusa Tenggara. Specializing in ethical Whale Shark expeditions, 3 Gili snorkeling & diving, Mount Rinjani waterfalls, and VIP transport.",
    footer_h_packages: "Tour Packages",
    footer_h_destinations: "NTB Highlights",
    footer_h_contact: "Contact Us",
    wa_tooltip: "Chat with us!"
  },
  id: {
    nav_home: "Beranda",
    nav_packages: "Paket Wisata",
    nav_whaleshark: "Ekspedisi Hiu Paus",
    nav_whyus: "Keunggulan",
    nav_gallery: "Galeri NTB",
    nav_faq: "FAQ",
    btn_book_wa: "Pesan via WhatsApp",
    
    hero_badge: "Spesialis Wisata Bahari & Petualangan #1 di Lombok",
    hero_title: "Tur Lombok, Diving & Ekspedisi Hiu Paus Tak Terlupakan",
    hero_subtitle: "Jelajahi keindahan alam asri, berenang bersama raksasa laut yang ramah, dan abadikan momen berharga bersama pemandu lokal berlisensi dan armada premium.",
    hero_btn_explore: "Lihat Paket Wisata",
    hero_btn_chat: "Chat WhatsApp",
    
    trust_1_title: "Pemandu Berlisensi",
    trust_1_sub: "Instruktur Diving Bersertifikat",
    trust_2_title: "Aman & Nyaman",
    trust_2_sub: "Standar Keselamatan Internasional",
    trust_3_title: "Respon Cepat 24/7",
    trust_3_sub: "Konsultan Tur Ramah & Sigap",
    
    featured_badge: "Paket Impian Paling Favorit",
    featured_tag: "EKSPEDISI UNGGULAN",
    featured_title: "Ekspedisi Hiu Paus & Pari Manta Raksasa",
    featured_desc: "Rasakan sensasi sekali seumur hidup berenang dekat dengan hiu paus di habitat aslinya. Kapten berpengalaman kami mengetahui titik temu musiman terbaik untuk memastikan pengalaman bahari yang etis, ramah lingkungan, dan aman.",
    featured_perk_1: "Gratis Dokumentasi Foto & Video 4K GoPro Underwater",
    featured_perk_2: "Makan Segar, Air Mineral & Buah-buahan Tropis",
    featured_perk_3: "Disediakan Pelampung & Alat Snorkeling Lengkap",
    featured_perk_4: "Antar-Jemput Darat & Kapal Ber-AC Pulang Pergi",
    btn_book_expedition: "Pesan Ekspedisi Sekarang",
    
    packages_tag: "PENGALAMAN EKSKLUSIF",
    packages_title: "Pilihan Paket Wisata Terbaik Lombok",
    packages_subtitle: "Mulai dari eksplorasi bawah laut memukau hingga air terjun pegunungan dan wisata budaya tradisional Sasak.",
    
    filter_all: "Semua Paket",
    filter_diving: "Diving & Snorkeling",
    filter_island: "Keliling Pulau & Budaya",
    filter_transport: "Transportasi VIP",
    
    pkg1_title: "Ekspedisi Hiu Paus & Pari Manta Raksasa (Sumbawa)",
    pkg1_desc: "Interaksi dekat dengan Whale Shark dan pari manta di perairan Teluk Saleh Sumbawa dipandu divemaster bersertifikat. Diving Rp 5.500.000/org (min 2 pax) | Snorkeling Rp 3.500.000/org.",
    pkg1_inc1: "Snorkeling Rp 3.5 JT / Diving Rp 5.5 JT (Min. 2 PAX)",
    pkg1_inc2: "Akomodasi Hotel/Resort Nyaman",
    pkg1_inc3: "Termasuk Semua Makan & Refreshment",
    pkg1_inc4: "Gratis Dokumentasi Foto & Video Resolusi Tinggi",
    
    pkg2_title: "Lombok Utara & Jelajah 3 Gili + Air Terjun Rinjani",
    pkg2_desc: "Snorkeling patung ikonik Gili Meno, penyu Gili Trawangan, dilanjutkan tracking air terjun Sendang Gile & Tiu Kelep di kaki Gunung Rinjani.",
    pkg2_inc1: "4x Dives Terpandu atau Tur Snorkeling 3 Gili",
    pkg2_inc2: "Trekking Air Terjun Sendang Gile & Tiu Kelep",
    pkg2_inc3: "Mobil AC, Hotel & Tiket Masuk Wisata",
    pkg2_inc4: "Peralatan Lengkap Snorkel/Diving",
    
    pkg3_title: "Petualangan Snorkeling & Mancing Tradisional (3 Gili)",
    pkg3_desc: "Sewa kapal private glass-bottom mengitari Gili Trawangan, Meno, dan Air. Nikmati terumbu karang indah serta memancing tradisional bersama kapten lokal.",
    pkg3_inc1: "Kapal Private Glass-Bottom & Kapten Lokal",
    pkg3_inc2: "Snorkeling di Turtle Point & Coral Garden",
    pkg3_inc3: "Peralatan Memancing & Umpan Segar",
    pkg3_inc4: "Gratis Dokumentasi Foto GoPro",
    
    pkg4_title: "Tur Keliling Lombok Lengkap (Itinerary Kustom)",
    pkg4_desc: "Jelajahi pesona Lombok Selatan & Tengah: Pantai Kuta Mandalika, Bukit Merese, Desa Adat Sade, sentra tenun Sukarara & Islamic Center.",
    pkg4_inc1: "Kuta Mandalika, Tanjung Aan & Bukit Merese",
    pkg4_inc2: "Desa Adat Sasak Sade & Tenun Sukarara",
    pkg4_inc3: "Islamic Center Hubbul Wathan Mataram",
    pkg4_inc4: "Mobil MPV Pribadi Ber-AC + BBM + Pemandu Lokal",
    
    pkg5_title: "Antar-Jemput Bandara VIP & Rental Mobil",
    pkg5_desc: "Layanan penjemputan tepat waktu, higienis, dan nyaman dari/ke Bandara Internasional Lombok (LOP), Pelabuhan Bangsal, Senggigi, dan Mandalika.",
    pkg5_inc1: "Penyambutan Tepat Waktu dengan Papan Nama",
    pkg5_inc2: "Armada Mobil MPV/SUV Bersih & AC Dingin",
    pkg5_inc3: "Driver Berpengalaman & Menguasai Rute Wisata",
    pkg5_inc4: "Sudah Termasuk BBM, Parkir & Tol",
    
    card_from: "Mulai dari",
    btn_book_now: "Pesan Sekarang",
    
    why_tag: "KEUNGGULAN NUSATRIPPER.COM",
    why_title: "Mengapa Memilih NusaTripper.com?",
    why_subtitle: "Kami menggabungkan wawasan lokal autentik, standar keamanan ketat, dan keramahan personal untuk liburan impian Anda.",
    
    why1_title: "Tim Profesional & Bersertifikat",
    why1_desc: "Seluruh dive master, nakhoda kapal, dan pemandu wisata kami berlisensi resmi dengan pelatihan keselamatan & pertolongan pertama.",
    why2_title: "Prioritas Keselamatan & Kenyamanan",
    why2_desc: "Armada kapal terawat, alat snorkeling & scuba standar internasional (Cressi/Mares), serta kendaraan darat AC prima.",
    why3_title: "Pelayanan Terbaik & Jaminan Kepuasan",
    why3_desc: "Jadwal tur fleksibel yang disesuaikan dengan ritme perjalanan dan preferensi Anda tanpa biaya tersembunyi.",
    why4_title: "Dokumentasi Tak Terlupakan",
    why4_desc: "Bonus dokumentasi foto & video bawah air 4K GoPro serta drone footage untuk mengabadikan momen berharga Anda.",
    
    gallery_tag: "EKSPLORASI LOMBOK & NTB",
    gallery_title: "Destinasi Spektakuler Nusa Tenggara Barat",
    gallery_subtitle: "Dari kemegahan air terjun pegunungan dan atol pasir putih hingga karya seni patung bawah laut mistis.",
    
    testi_tag: "ULASAN WISATAWAN",
    testi_title: "Dipercaya Wisatawan Domestik & Mancanegara",
    
    faq_tag: "PERTANYAAN UMUM",
    faq_title: "Punya Pertanyaan? Kami Siap Menjawab",
    faq_subtitle: "Temukan informasi lengkap terkait paket perjalanan, kebijakan pemesanan, dan keselamatan bahari.",
    
    faq_q1: "Kapan waktu terbaik untuk melihat Hiu Paus di Lombok/Sumbawa?",
    faq_a1: "Hiu Paus dapat dijumpai sepanjang tahun di Teluk Saleh karena menetap dan mencari makan di sekitar bagan nelayan tradisional. Namun kondisi perairan paling tenang dengan jarak pandang (visibility) terbaik adalah dari bulan April hingga November.",
    
    faq_q2: "Perlengkapan apa saja yang perlu disiapkan oleh peserta tur?",
    faq_a2: "Kami sudah menyediakan perlengkapan snorkeling berkualitas, pelampung, dan alat selam. Anda cukup membawa baju renang/rashguard, sunscreen aman untuk terumbu karang (reef-safe), baju ganti kering, kacamata hitam, handuk, dan obat pribadi.",
    
    faq_q3: "Bagaimana cara pemesanan dan ketentuan pembayaran deposit?",
    faq_a3: "Pemesanan sangat mudah via WhatsApp. Uang muka (DP) sebesar 30% digunakan untuk mengunci slot kapal, pemandu, dan tanggal tur. Sisa pembayaran dapat dilunasi saat kedatangan via transfer bank, kartu kredit, atau tunai (IDR/USD).",
    
    faq_q4: "Bagaimana jika cuaca buruk? Bagaimana kebijakan pembatalannya?",
    faq_a4: "Keselamatan tamu adalah prioritas utama. Jika syahbandar melarang pelayaran karena cuaca ekstrem, kami memberikan opsi penjadwalan ulang gratis atau pengembalian uang muka 100%. Pembatalan tanpa biaya berlaku hingga 48 jam sebelum jadwal keberangkatan.",
    
    faq_q5: "Apakah peserta yang belum bisa berenang bisa ikut Snorkeling 3 Gili?",
    faq_a5: "Tentu saja sangat bisa! Kami menyediakan life vest pelampung standar dan pemandu pribadi yang akan mendampingi sambil memegang ring buoy penyelamat, sehingga Anda tetap aman dan rileks melihat patung dan penyu.",
    
    cta_heading: "Siap Merasakan Petualangan Epik di Lombok?",
    cta_text: "Hubungi konsultan wisata lokal kami hari ini dan dapatkan itinerary terbaik serta penawaran diskon khusus via WhatsApp.",
    btn_wa_chat: "Chat Langsung via WhatsApp",
    
    footer_bio: "Operator tur resmi dan berlisensi di Lombok, Nusa Tenggara Barat. Spesialis ekspedisi hiu paus ramah lingkungan, diving 3 Gili, trekking air terjun Rinjani, dan layanan rental mobil VIP.",
    footer_h_packages: "Paket Tur",
    footer_h_destinations: "Destinasi NTB",
    footer_h_contact: "Hubungi Kami",
    wa_tooltip: "Chat kami sekarang!"
  }
};

let currentLang = 'en';

function setLanguage(lang) {
  currentLang = lang;
  document.documentElement.lang = lang;
  
  // Update texts
  const elements = document.querySelectorAll('[data-i18n]');
  elements.forEach(el => {
    const key = el.getAttribute('data-i18n');
    if (translations[lang] && translations[lang][key]) {
      el.textContent = translations[lang][key];
    }
  });

  // Update button active state indicator
  const btn = document.getElementById('langToggleBtn');
  if (btn) {
    if (lang === 'en') {
      btn.innerHTML = '<span class="active-lang">EN</span> | <span>ID</span>';
    } else {
      btn.innerHTML = '<span>EN</span> | <span class="active-lang">ID</span>';
    }
  }
  
  localStorage.setItem('es_lang', lang);
}

function toggleLanguage() {
  const nextLang = currentLang === 'en' ? 'id' : 'en';
  setLanguage(nextLang);
}

// Mobile Menu Toggle & Drawer Backdrop
const mobileBtn = document.getElementById('mobileMenuBtn');
const navMenu = document.getElementById('navMenu');

// Create backdrop element dynamically if not exists
let navBackdrop = document.querySelector('.nav-backdrop');
if (!navBackdrop) {
  navBackdrop = document.createElement('div');
  navBackdrop.className = 'nav-backdrop';
  document.body.appendChild(navBackdrop);
}

function openMobileMenu() {
  if (navMenu && mobileBtn) {
    navMenu.classList.add('show');
    navBackdrop.classList.add('show');
    document.body.style.overflow = 'hidden';
    mobileBtn.innerHTML = '<i class="fa-solid fa-xmark"></i>';
  }
}

function closeMobileMenu() {
  if (navMenu && mobileBtn) {
    navMenu.classList.remove('show');
    navBackdrop.classList.remove('show');
    document.body.style.overflow = '';
    mobileBtn.innerHTML = '<i class="fa-solid fa-bars"></i>';
  }
}

if (mobileBtn && navMenu) {
  mobileBtn.addEventListener('click', (e) => {
    e.stopPropagation();
    if (navMenu.classList.contains('show')) {
      closeMobileMenu();
    } else {
      openMobileMenu();
    }
  });

  navBackdrop.addEventListener('click', () => {
    closeMobileMenu();
  });

  // Close menu when clicking nav links or CTA
  navMenu.querySelectorAll('.nav-link, .nav-cta').forEach(link => {
    link.addEventListener('click', () => {
      closeMobileMenu();
    });
  });
}

// Navbar scroll shadow
window.addEventListener('scroll', () => {
  const navbar = document.getElementById('navbar');
  if (window.scrollY > 40) {
    navbar.classList.add('scrolled');
  } else {
    navbar.classList.remove('scrolled');
  }
});

// Category Filter for Tour Packages
const filterBtns = document.querySelectorAll('.filter-btn');
const packageCards = document.querySelectorAll('.package-card');

filterBtns.forEach(btn => {
  btn.addEventListener('click', () => {
    filterBtns.forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    const filterVal = btn.getAttribute('data-filter');

    packageCards.forEach(card => {
      const cardCategory = card.getAttribute('data-category');
      if (filterVal === 'all' || cardCategory === filterVal) {
        card.classList.remove('hidden');
      } else {
        card.classList.add('hidden');
      }
    });
  });
});

// FAQ Accordion
const faqItems = document.querySelectorAll('.faq-item');

faqItems.forEach(item => {
  const header = item.querySelector('.faq-header');
  header.addEventListener('click', () => {
    const isActive = item.classList.contains('active');
    
    // Close other open items
    faqItems.forEach(otherItem => {
      otherItem.classList.remove('active');
      otherItem.querySelector('.faq-header').setAttribute('aria-expanded', 'false');
    });

    if (!isActive) {
      item.classList.add('active');
      header.setAttribute('aria-expanded', 'true');
    }
  });
});

// Initialize on DOMContentLoaded
document.addEventListener('DOMContentLoaded', () => {
  const savedLang = localStorage.getItem('es_lang') || 'en';
  setLanguage(savedLang);
});

/* ==========================================================================
   Content Protection: Disable Right Click, DevTools Shortcuts, Selection, & View Source
   ========================================================================== */

// 1. Disable Context Menu (Right Click)
document.addEventListener('contextmenu', (e) => {
  e.preventDefault();
  return false;
});

// 2. Disable Key Shortcuts (F12, Ctrl+U, Ctrl+S, Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+Shift+C)
document.addEventListener('keydown', (e) => {
  // F12
  if (e.key === 'F12' || e.keyCode === 123) {
    e.preventDefault();
    return false;
  }

  // Ctrl + U (View Source)
  if ((e.ctrlKey || e.metaKey) && (e.key === 'u' || e.key === 'U' || e.keyCode === 85)) {
    e.preventDefault();
    return false;
  }

  // Ctrl + S (Save Page)
  if ((e.ctrlKey || e.metaKey) && (e.key === 's' || e.key === 'S' || e.keyCode === 83)) {
    e.preventDefault();
    return false;
  }

  // Ctrl + Shift + I (Inspect Element)
  if ((e.ctrlKey || e.metaKey) && e.shiftKey && (e.key === 'i' || e.key === 'I' || e.keyCode === 73)) {
    e.preventDefault();
    return false;
  }

  // Ctrl + Shift + J (Console)
  if ((e.ctrlKey || e.metaKey) && e.shiftKey && (e.key === 'j' || e.key === 'J' || e.keyCode === 74)) {
    e.preventDefault();
    return false;
  }

  // Ctrl + Shift + C (Element Selector)
  if ((e.ctrlKey || e.metaKey) && e.shiftKey && (e.key === 'c' || e.key === 'C' || e.keyCode === 67)) {
    e.preventDefault();
    return false;
  }
});

// 3. Disable Dragging of Images / Elements
document.addEventListener('dragstart', (e) => {
  e.preventDefault();
  return false;
});

/* ==========================================================================
   Automatic Image Compression on Display & Full-Resolution Lightbox Viewer
   ========================================================================== */

// 1. Client-side Auto Image Compression (Lossy canvas scaling for thumbnail performance)
function compressImageElement(img) {
  // Only compress content images with high native resolution
  if (!img.complete || img.naturalWidth <= 640 || img.dataset.compressed === 'true') return;
  
  try {
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    
    // Scale down dimensions for fast rendering
    const maxDim = 720;
    let width = img.naturalWidth;
    let height = img.naturalHeight;

    if (width > height) {
      if (width > maxDim) {
        height = Math.round((height * maxDim) / width);
        width = maxDim;
      }
    } else {
      if (height > maxDim) {
        width = Math.round((width * maxDim) / height);
        height = maxDim;
      }
    }

    canvas.width = width;
    canvas.height = height;
    ctx.drawImage(img, 0, 0, width, height);

    // Save full original resolution link in dataset before compression
    img.dataset.fullSrc = img.src;
    
    // Export compressed JPEG thumbnail (~70% quality for lightning-fast page speed)
    const compressedUrl = canvas.toDataURL('image/jpeg', 0.72);
    img.src = compressedUrl;
    img.dataset.compressed = 'true';
  } catch (err) {
    // Fallback gracefully (e.g. cross-origin images retain original src)
    img.dataset.fullSrc = img.src;
  }
}

// Automatically process all images after load
window.addEventListener('load', () => {
  document.querySelectorAll('img').forEach(img => {
    img.dataset.fullSrc = img.src;
    if (img.complete) {
      compressImageElement(img);
    } else {
      img.addEventListener('load', () => compressImageElement(img), { once: true });
    }
  });
});

// 2. Full-Resolution Lightbox Modal Handler
let lightboxModal = document.getElementById('lightboxModal');
if (!lightboxModal) {
  lightboxModal = document.createElement('div');
  lightboxModal.id = 'lightboxModal';
  lightboxModal.className = 'lightbox-modal';
  lightboxModal.innerHTML = `
    <div class="lightbox-content-box">
      <button class="lightbox-close-btn" id="lightboxCloseBtn" aria-label="Close Preview">&times;</button>
      <div class="lightbox-media-container" id="lightboxMediaContainer"></div>
      <div class="lightbox-caption" id="lightboxCaption"></div>
    </div>
  `;
  document.body.appendChild(lightboxModal);
}

const mediaContainer = document.getElementById('lightboxMediaContainer');
const captionEl = document.getElementById('lightboxCaption');
const closeBtn = document.getElementById('lightboxCloseBtn');

function openMediaLightbox(mediaType, src, captionText = '') {
  mediaContainer.innerHTML = '';
  if (mediaType === 'video') {
    const video = document.createElement('video');
    video.src = src;
    video.controls = true;
    video.autoplay = true;
    video.playsInline = true;
    mediaContainer.appendChild(video);
  } else {
    const img = document.createElement('img');
    img.src = src; // Full resolution source
    img.alt = captionText;
    mediaContainer.appendChild(img);
  }
  
  captionEl.textContent = captionText;
  lightboxModal.classList.add('active');
  document.body.style.overflow = 'hidden';
}

function closeMediaLightbox() {
  lightboxModal.classList.remove('active');
  mediaContainer.innerHTML = '';
  document.body.style.overflow = '';
}

if (closeBtn) {
  closeBtn.addEventListener('click', closeMediaLightbox);
}

lightboxModal.addEventListener('click', (e) => {
  if (e.target === lightboxModal || e.target.classList.contains('lightbox-content-box')) {
    closeMediaLightbox();
  }
});

// Click delegation on gallery items, package cards, videos to trigger full resolution lightbox
document.addEventListener('click', (e) => {
  const targetMedia = e.target.closest('.gallery-item, .card-img-wrap, .featured-img-col, .polaroid-card');
  if (!targetMedia) return;

  const video = targetMedia.querySelector('video');
  const img = targetMedia.querySelector('img');
  
  if (video) {
    const videoSource = video.querySelector('source') ? video.querySelector('source').src : video.src;
    openMediaLightbox('video', videoSource, 'NusaTripper.com Whale Shark Expedition Video');
  } else if (img) {
    // Pull the original full-res source (stored before compression)
    const fullRes = img.dataset.fullSrc || img.src;
    const caption = targetMedia.querySelector('h4, .card-title, .polaroid-caption, strong')?.textContent || img.alt || 'NusaTripper.com High Resolution View';
    openMediaLightbox('image', fullRes, caption);
  }
});
