// ==============================================================
// PESONA WISATA INDONESIA - ULTRA-LUXURY HERITAGE JAVASCRIPT
// Enhanced Interactivity, Real-time Filters, Stepper, & Responsiveness
// ==============================================================

document.addEventListener('DOMContentLoaded', function () {
  // 1. Sticky Navbar styling on scroll
  const navbar = document.querySelector('.navbar-luxury');
  if (navbar) {
    const handleScroll = () => {
      if (window.scrollY > 30) {
        navbar.classList.add('scrolled');
      } else {
        navbar.classList.remove('scrolled');
      }
    };
    window.addEventListener('scroll', handleScroll, { passive: true });
    handleScroll();
  }

  // 2. Client-side Search, Province, Category, and Sorting on index.php
  const searchInput = document.getElementById('searchWisata');
  const searchClearBtn = document.getElementById('searchClearBtn');
  const provinceSelect = document.getElementById('filterProvinsi');
  const sortSelect = document.getElementById('sortWisata');
  const categoryPills = document.querySelectorAll('.filter-pills .pill-btn');
  const provinceCards = document.querySelectorAll('.province-card-luxury');
  const destinasiGrid = document.getElementById('destinasiGrid');
  const cards = Array.from(document.querySelectorAll('.destinasi-item-col'));
  const visibleCountEl = document.getElementById('visibleCount');
  const totalCountEl = document.getElementById('totalCount');
  const resetFilterBtn = document.getElementById('resetFilterBtn');
  const noResultAlert = document.getElementById('noResultsAlert');

  let activeCategory = 'all';

  if (totalCountEl && cards.length) {
    totalCountEl.textContent = cards.length;
  }

  function filterAndSortCards() {
    const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';
    const selectedProvince = provinceSelect ? provinceSelect.value.toLowerCase() : 'all';
    const sortBy = sortSelect ? sortSelect.value : 'populer';

    // Show/hide clear search button
    if (searchClearBtn) {
      searchClearBtn.style.display = searchTerm.length > 0 ? 'flex' : 'none';
    }

    let visibleCount = 0;

    // Filter
    cards.forEach(card => {
      const title = (card.getAttribute('data-title') || '').toLowerCase();
      const province = (card.getAttribute('data-province') || '').toLowerCase();
      const category = card.getAttribute('data-category') || '';
      const location = (card.getAttribute('data-location') || '').toLowerCase();

      const matchSearch = !searchTerm || title.includes(searchTerm) || location.includes(searchTerm);
      const matchProvince = (selectedProvince === 'all' || selectedProvince === '') || province.includes(selectedProvince);
      const matchCategory = (activeCategory === 'all') || (category === activeCategory);

      if (matchSearch && matchProvince && matchCategory) {
        card.style.display = 'block';
        card.classList.add('fade-in-up');
        visibleCount++;
      } else {
        card.style.display = 'none';
      }
    });

    // Update count badge
    if (visibleCountEl) {
      visibleCountEl.textContent = visibleCount;
    }

    // Toggle no results alert
    if (noResultAlert) {
      if (visibleCount === 0) {
        noResultAlert.classList.remove('d-none');
      } else {
        noResultAlert.classList.add('d-none');
      }
    }

    // Sorting
    if (destinasiGrid) {
      const visibleArray = cards.filter(c => c.style.display !== 'none');
      visibleArray.sort((a, b) => {
        const priceA = parseFloat(a.getAttribute('data-price')) || 0;
        const priceB = parseFloat(b.getAttribute('data-price')) || 0;
        const ratingA = parseFloat(a.getAttribute('data-rating')) || 0;
        const ratingB = parseFloat(b.getAttribute('data-rating')) || 0;
        const titleA = a.getAttribute('data-title') || '';
        const titleB = b.getAttribute('data-title') || '';

        if (sortBy === 'rating') return ratingB - ratingA;
        if (sortBy === 'harga_asc') return priceA - priceB;
        if (sortBy === 'harga_desc') return priceB - priceA;
        if (sortBy === 'nama_asc') return titleA.localeCompare(titleB);
        return 0; // Default/populer
      });

      visibleArray.forEach(card => destinasiGrid.appendChild(card));
    }
  }

  // Event Listeners for Filters
  if (searchInput) {
    searchInput.addEventListener('input', filterAndSortCards);
  }

  if (searchClearBtn) {
    searchClearBtn.addEventListener('click', () => {
      if (searchInput) {
        searchInput.value = '';
        searchInput.focus();
        filterAndSortCards();
      }
    });
  }

  if (provinceSelect) {
    provinceSelect.addEventListener('change', () => {
      const selected = provinceSelect.value;
      // Sync province cards
      provinceCards.forEach(pc => {
        if (pc.getAttribute('data-province') === selected) {
          pc.classList.add('active');
        } else {
          pc.classList.remove('active');
        }
      });
      filterAndSortCards();
    });
  }

  if (sortSelect) {
    sortSelect.addEventListener('change', filterAndSortCards);
  }

  // Category Pills
  categoryPills.forEach(pill => {
    pill.addEventListener('click', function (e) {
      e.preventDefault();
      categoryPills.forEach(p => p.classList.remove('active'));
      this.classList.add('active');
      activeCategory = this.getAttribute('data-category') || 'all';
      filterAndSortCards();
    });
  });

  // Province Cards Click to Filter
  provinceCards.forEach(pCard => {
    pCard.addEventListener('click', function (e) {
      e.preventDefault();
      const provName = this.getAttribute('data-province') || 'all';

      if (this.classList.contains('active')) {
        // Toggle off
        this.classList.remove('active');
        if (provinceSelect) provinceSelect.value = 'all';
      } else {
        provinceCards.forEach(p => p.classList.remove('active'));
        this.classList.add('active');
        if (provinceSelect) provinceSelect.value = provName;
      }
      filterAndSortCards();

      // Smooth scroll towards the grid if clicked from province cards
      const targetSec = document.getElementById('destinasiSection');
      if (targetSec) {
        targetSec.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  });

  // Reset Filter Button
  if (resetFilterBtn) {
    resetFilterBtn.addEventListener('click', () => {
      if (searchInput) searchInput.value = '';
      if (provinceSelect) provinceSelect.value = 'all';
      if (sortSelect) sortSelect.value = 'populer';
      provinceCards.forEach(p => p.classList.remove('active'));
      categoryPills.forEach((p, idx) => {
        if (idx === 0) p.classList.add('active');
        else p.classList.remove('active');
      });
      activeCategory = 'all';
      filterAndSortCards();
    });
  }

  // 3. Quick Ticket Price Calculator & Stepper
  const calcDest = document.getElementById('calcDestinasi');
  const calcQty = document.getElementById('calcJumlah');
  const calcMinus = document.getElementById('calcMinusBtn');
  const calcPlus = document.getElementById('calcPlusBtn');
  const calcTotal = document.getElementById('calcTotalPreview');
  const calcUnit = document.getElementById('calcUnitPricePreview');
  const calcBookingBtn = document.getElementById('calcBookingBtn');
  const calcPresetChips = document.querySelectorAll('.calc-preset-chip');

  function updateQuickCalc() {
    if (!calcDest || !calcQty) return;
    const opt = calcDest.options[calcDest.selectedIndex];
    const price = parseFloat(opt ? opt.getAttribute('data-price') : 0) || 0;
    let qty = parseInt(calcQty.value) || 1;
    if (qty < 1) qty = 1;
    if (qty > 100) qty = 100;
    calcQty.value = qty;

    const total = price * qty;
    if (calcTotal) calcTotal.textContent = 'Rp ' + total.toLocaleString('id-ID');
    if (calcUnit) calcUnit.textContent = 'Rp ' + price.toLocaleString('id-ID');

    // Update preset chips active state
    calcPresetChips.forEach(chip => {
      const chipQty = parseInt(chip.getAttribute('data-qty')) || 0;
      if (chipQty === qty) {
        chip.classList.add('active');
      } else {
        chip.classList.remove('active');
      }
    });

    if (calcBookingBtn && opt) {
      calcBookingBtn.href = 'login.php?destinasi_id=' + opt.value;
    }
  }

  if (calcDest && calcQty) {
    calcDest.addEventListener('change', updateQuickCalc);
    calcQty.addEventListener('input', updateQuickCalc);

    if (calcMinus) {
      calcMinus.addEventListener('click', () => {
        let cur = parseInt(calcQty.value) || 1;
        if (cur > 1) {
          calcQty.value = cur - 1;
          updateQuickCalc();
        }
      });
    }

    if (calcPlus) {
      calcPlus.addEventListener('click', () => {
        let cur = parseInt(calcQty.value) || 1;
        if (cur < 100) {
          calcQty.value = cur + 1;
          updateQuickCalc();
        }
      });
    }

    calcPresetChips.forEach(chip => {
      chip.addEventListener('click', function () {
        const val = parseInt(this.getAttribute('data-qty')) || 1;
        calcQty.value = val;
        updateQuickCalc();
      });
    });

    updateQuickCalc();
  }

  // 4. Dynamic Calculation in Booking Form on pengunjung/pesan_tiket.php
  const ticketInput = document.getElementById('jumlahTiket');
  const unitPriceEl = document.getElementById('unitPrice');
  const totalPriceEl = document.getElementById('totalPricePreview');

  if (ticketInput && unitPriceEl && totalPriceEl) {
    function calculateTotal() {
      const price = parseFloat(unitPriceEl.getAttribute('data-price')) || 0;
      let qty = parseInt(ticketInput.value) || 1;
      if (qty < 1) qty = 1;
      const total = price * qty;
      totalPriceEl.textContent = 'Rp ' + total.toLocaleString('id-ID');
    }

    ticketInput.addEventListener('input', calculateTotal);
    ticketInput.addEventListener('change', calculateTotal);
    calculateTotal();
  }

  // 5. Wishlist Heart Toggle with localStorage & Toast
  const favButtons = document.querySelectorAll('.card-fav-btn');
  let savedFavs = [];
  try {
    savedFavs = JSON.parse(localStorage.getItem('wisata_favs') || '[]');
  } catch (err) {
    savedFavs = [];
  }

  favButtons.forEach(btn => {
    const id = btn.getAttribute('data-id');
    if (savedFavs.includes(id)) {
      btn.classList.add('active');
      const icon = btn.querySelector('i');
      if (icon) {
        icon.classList.remove('far');
        icon.classList.add('fas');
      }
    }

    btn.addEventListener('click', function (e) {
      e.stopPropagation();
      const currentId = this.getAttribute('data-id');
      const title = this.getAttribute('data-title') || 'Destinasi';
      const icon = this.querySelector('i');

      if (this.classList.contains('active')) {
        this.classList.remove('active');
        if (icon) {
          icon.classList.remove('fas');
          icon.classList.add('far');
        }
        savedFavs = savedFavs.filter(item => item !== currentId);
        showLuxuryToast('Dihapus dari Favorit', title + ' dihapus dari daftar impian Anda.');
      } else {
        this.classList.add('active');
        if (icon) {
          icon.classList.remove('far');
          icon.classList.add('fas');
        }
        if (!savedFavs.includes(currentId)) {
          savedFavs.push(currentId);
        }
        showLuxuryToast('Disimpan ke Favorit ❤️', title + ' berhasil ditambahkan ke daftar impian!');
      }
      try {
        localStorage.setItem('wisata_favs', JSON.stringify(savedFavs));
      } catch (err) {}
    });
  });

  // 6. Luxury Toast Notification Helper
  function showLuxuryToast(title, message) {
    let toastContainer = document.getElementById('luxuryToastContainer');
    if (!toastContainer) {
      toastContainer = document.createElement('div');
      toastContainer.id = 'luxuryToastContainer';
      toastContainer.style.cssText = 'position:fixed;bottom:24px;left:24px;z-index:9999;display:flex;flex-direction:column;gap:10px;pointer-events:none;';
      document.body.appendChild(toastContainer);
    }

    const toast = document.createElement('div');
    toast.style.cssText = 'pointer-events:auto;background:#064e3b;color:#fff;padding:12px 18px;border-radius:12px;border:1px solid rgba(245,158,11,0.5);box-shadow:0 12px 28px rgba(0,0,0,0.25);font-size:0.88rem;display:flex;align-items:center;gap:12px;opacity:0;transform:translateY(15px);transition:all 0.3s ease;max-width:340px;';
    toast.innerHTML = `
      <div style="width:34px;height:34px;border-radius:50%;background:#f59e0b;color:#000;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <i class="fas fa-sparkles"></i>
      </div>
      <div>
        <strong style="display:block;font-size:0.9rem;color:#fef08a;">${title}</strong>
        <span style="font-size:0.8rem;color:#e2e8f0;">${message}</span>
      </div>
    `;

    toastContainer.appendChild(toast);
    setTimeout(() => {
      toast.style.opacity = '1';
      toast.style.transform = 'translateY(0)';
    }, 20);

    setTimeout(() => {
      toast.style.opacity = '0';
      toast.style.transform = 'translateY(15px)';
      setTimeout(() => toast.remove(), 350);
    }, 3500);
  }

  // 7. Back to Top Button
  const backToTopBtn = document.getElementById('backToTopBtn');
  if (backToTopBtn) {
    window.addEventListener('scroll', () => {
      if (window.scrollY > 400) {
        backToTopBtn.classList.add('show');
      } else {
        backToTopBtn.classList.remove('show');
      }
    }, { passive: true });

    backToTopBtn.addEventListener('click', () => {
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
    });
  }

  // 8. Mobile Sidebar Toggler
  const sidebarToggleBtn = document.getElementById('sidebarToggleBtn');
  const sidebar = document.querySelector('.dashboard-sidebar');
  if (sidebarToggleBtn && sidebar) {
    sidebarToggleBtn.addEventListener('click', () => {
      sidebar.classList.toggle('show');
    });
  }
});
