/**
 * BERBAGI RASA — Client-Side Micro-interactions & 3D Helpers
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Inisialisasi Konfirmasi Aksi (data-confirm)
    initConfirmActions();

    // 2. Preview Unggah Gambar & Drag-Drop
    initImageUploadPreview();

    // 3. Auto-dismiss Flash Alerts
    initFlashAlerts();

    // 4. Efek Interaktif 3D Tilt pada Kartu
    init3DTilt();

    // 5. Quick Demo Account Autofill (Mempermudah Pengujian)
    initDemoAutofill();

    // 6. Toggle Password Visibility (Eye Icon)
    initPasswordToggle();

    // 7. Mobile Touch Dropdowns Navigation (Desktop & General)
    initMobileDropdowns();

    // 8. Mobile Hamburger Drawer System
    initHamburgerDrawer();
});

/**
 * Tangani elemen dengan atribut data-confirm
 */
function initConfirmActions() {
    document.querySelectorAll('[data-confirm]').forEach(el => {
        el.addEventListener('click', (e) => {
            const message = el.getAttribute('data-confirm') || 'Apakah Anda yakin ingin melanjutkan tindakan ini?';
            if (!confirm(message)) {
                e.preventDefault();
                e.stopImmediatePropagation();
                return false;
            }
        });
    });

    document.querySelectorAll('form[data-confirm]').forEach(form => {
        form.addEventListener('submit', (e) => {
            const message = form.getAttribute('data-confirm') || 'Apakah Anda yakin ingin mengirim data ini?';
            if (!confirm(message)) {
                e.preventDefault();
                return false;
            }
        });
    });
}

/**
 * Preview Foto Donasi Sebelum Diunggah
 */
function initImageUploadPreview() {
    const fileInput = document.getElementById('foto-input');
    const previewWrap = document.getElementById('foto-preview-wrap');
    const previewImg = document.getElementById('foto-preview-img');
    const dropzone = document.querySelector('.upload-dropzone-3d');

    if (!fileInput || !previewWrap || !previewImg) return;

    fileInput.addEventListener('change', () => {
        const file = fileInput.files[0];
        if (file) {
            // Validasi ukuran client-side (maks 2MB)
            if (file.size > 2 * 1024 * 1024) {
                alert('Ukuran file foto melebihi 2MB. Silakan pilih foto dengan ukuran lebih kecil.');
                fileInput.value = '';
                previewWrap.style.display = 'none';
                return;
            }

            const reader = new FileReader();
            reader.onload = (e) => {
                previewImg.src = e.target.result;
                previewWrap.style.display = 'block';
                if (dropzone) {
                    dropzone.style.borderColor = '#10b981';
                }
            };
            reader.readAsDataURL(file);
        } else {
            previewWrap.style.display = 'none';
        }
    });
}

/**
 * Auto-dismiss Alert Box setelah beberapa detik
 */
function initFlashAlerts() {
    const alerts = document.querySelectorAll('.alert-box');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(() => alert.remove(), 500);
        }, 6000);
    });
}

/**
 * Efek Interaktif 3D Tilt saat kursor melayang di atas kartu
 */
function init3DTilt() {
    const cards = document.querySelectorAll('.food-card-3d');

    cards.forEach(card => {
        card.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;

            const rotateX = ((y - centerY) / centerY) * -6;
            const rotateY = ((x - centerX) / centerX) * 6;

            card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-6px)`;
        });

        card.addEventListener('mouseleave', () => {
            card.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg) translateY(0)';
        });
    });
}

/**
 * Tombol Cepat Isi Akun Demo untuk Pengujian Cepat
 */
function initDemoAutofill() {
    document.querySelectorAll('.btn-demo-fill').forEach(btn => {
        btn.addEventListener('click', () => {
            const emailInput = document.querySelector('input[name="email"]');
            const passwordInput = document.querySelector('input[name="password"]');
            if (emailInput && passwordInput) {
                emailInput.value = btn.getAttribute('data-email');
                passwordInput.value = btn.getAttribute('data-pass');

                // Efek animasi highlight hijau saat data terisi otomatis
                [emailInput, passwordInput].forEach(inp => {
                    inp.style.borderColor = '#10b981';
                    inp.style.boxShadow = '0 0 0 4px rgba(16, 185, 129, 0.35)';
                    setTimeout(() => {
                        inp.style.borderColor = '';
                        inp.style.boxShadow = '';
                    }, 450);
                });
            }
        });
    });
}

/**
 * Toggle Tampilkan / Sembunyikan Kata Sandi
 */
function initPasswordToggle() {
    document.querySelectorAll('.btn-toggle-password').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const inputGroup = btn.closest('.input-group-3d');
            if (!inputGroup) return;
            const input = inputGroup.querySelector('input');
            const icon = btn.querySelector('i');
            if (!input) return;

            if (input.type === 'password') {
                input.type = 'text';
                if (icon) {
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                    icon.style.color = '#10b981';
                }
            } else {
                input.type = 'password';
                if (icon) {
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                    icon.style.color = '';
                }
            }
        });
    });
}

// 7. Navbar Scroll Background Transition (Shopify Style)
window.addEventListener('scroll', () => {
    const nav = document.querySelector('.header-nav');
    if (!nav) return;
    if (window.scrollY > 40) {
        nav.classList.add('scrolled');
    } else {
        nav.classList.remove('scrolled');
    }
});

/**
 * 8. Mobile Touch Dropdown Navigation Handler
 */
function initMobileDropdowns() {
    document.querySelectorAll('.nav-item-dropdown').forEach(item => {
        const trigger = item.querySelector(':scope > a');
        const menu = item.querySelector('.dropdown-menu-cinematic');
        if (!trigger || !menu) return;

        trigger.addEventListener('click', (e) => {
            if (window.innerWidth <= 768) {
                const isOpen = item.classList.contains('mobile-open');
                // Close other open dropdowns
                document.querySelectorAll('.nav-item-dropdown.mobile-open').forEach(other => {
                    if (other !== item) other.classList.remove('mobile-open');
                });

                if (!isOpen) {
                    e.preventDefault();
                    item.classList.add('mobile-open');
                } else {
                    item.classList.remove('mobile-open');
                }
            }
        });
    });

    // Close when clicking outside
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.nav-item-dropdown')) {
            document.querySelectorAll('.nav-item-dropdown.mobile-open').forEach(openItem => {
                openItem.classList.remove('mobile-open');
            });
        }
    });
}

/**
 * 9. Inisialisasi Hamburger Menu & Mobile Drawer
 */
function initHamburgerDrawer() {
    const hamburgerBtn = document.getElementById('hamburgerBtn');
    const drawer = document.getElementById('mobileNavDrawer');
    const backdrop = document.getElementById('mobileNavBackdrop');
    const closeBtn = document.getElementById('drawerCloseBtn');

    if (!hamburgerBtn || !drawer || !backdrop) return;

    function openDrawer() {
        drawer.classList.add('is-open');
        backdrop.classList.add('is-open');
        hamburgerBtn.classList.add('is-active');
        hamburgerBtn.setAttribute('aria-expanded', 'true');
        drawer.setAttribute('aria-hidden', 'false');
        document.body.classList.add('mobile-drawer-locked');
    }

    function closeDrawer() {
        drawer.classList.remove('is-open');
        backdrop.classList.remove('is-open');
        hamburgerBtn.classList.remove('is-active');
        hamburgerBtn.setAttribute('aria-expanded', 'false');
        drawer.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('mobile-drawer-locked');
    }

    hamburgerBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        if (drawer.classList.contains('is-open')) {
            closeDrawer();
        } else {
            openDrawer();
        }
    });

    if (closeBtn) {
        closeBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            closeDrawer();
        });
    }

    backdrop.addEventListener('click', () => {
        closeDrawer();
    });

    // Close on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && drawer.classList.contains('is-open')) {
            closeDrawer();
        }
    });

    // Auto-close when clicking any link inside the drawer (including anchor links)
    drawer.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => {
            closeDrawer();
        });
    });

    // Handle Accordions inside the drawer (Service & Our Work submenus)
    drawer.querySelectorAll('.drawer-accordion-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            const group = btn.closest('.drawer-nav-group');
            if (group) {
                group.classList.toggle('is-expanded');
            }
        });
    });

    // Close drawer when resized to desktop viewport
    window.addEventListener('resize', () => {
        if (window.innerWidth > 768 && drawer.classList.contains('is-open')) {
            closeDrawer();
        }
    });
}


