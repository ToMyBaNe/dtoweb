// Mobile Menu Toggle
const navMenuToggle = document.getElementById('navMenuToggle');
const navMenu = document.getElementById('navMenu');

if (navMenuToggle) {
    navMenuToggle.addEventListener('click', function() {
        navMenu.classList.toggle('active');
    });

    // Close menu when clicking on a link
    document.querySelectorAll('.nav-menu a').forEach(link => {
        link.addEventListener('click', function() {
            navMenu.classList.remove('active');
        });
    });

    // Close menu when clicking outside
    document.addEventListener('click', function(e) {
        if (!navMenuToggle.contains(e.target) && !navMenu.contains(e.target)) {
            navMenu.classList.remove('active');
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    if (window.lucide) {
        lucide.createIcons();
    }

    // Header color change on scroll
    const nav = document.querySelector('nav');
    const hero = document.querySelector('.hero');
    
    if (hero && nav) {
        function updateNavColor() {
            const heroRect = hero.getBoundingClientRect();
            // If hero section is visible (navBar is over hero), use light style
            if (heroRect.bottom > 0) {
                nav.classList.add('nav-light');
                nav.classList.remove('nav-dark');
            } else {
                nav.classList.remove('nav-light');
                nav.classList.add('nav-dark');
            }
        }
        
        window.addEventListener('scroll', updateNavColor);
        updateNavColor(); // Call on load
    }

    // Featured carousel
    const swiperContainer = document.querySelector('.carousel-container');
    if (swiperContainer) {
        const swiper = new Swiper('.carousel-container', {
            loop: true,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            effect: 'fade',
            fadeEffect: {
                crossFade: true,
            },
        });
    }

    // Calendar events carousel
    const calendarCarouselContainer = document.querySelector('.calendar-carousel');
    if (calendarCarouselContainer) {
        const calendarCarousel = new Swiper('.calendar-carousel', {
            slidesPerView: 1,
            spaceBetween: 30,
            loop: true,
            mousewheel: true,
            keyboard: {
                enabled: true,
                onlyInViewport: true,
            },
            pagination: {
                el: '.calendar-carousel .swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.calendar-carousel .swiper-button-next',
                prevEl: '.calendar-carousel .swiper-button-prev',
            },
            breakpoints: {
                640: {
                    slidesPerView: 2,
                    spaceBetween: 20,
                },
                1024: {
                    slidesPerView: 3,
                    spaceBetween: 30,
                },
                1440: {
                    slidesPerView: 4,
                    spaceBetween: 30,
                },
            },
        });
    }
});

// Modal Functions
function openModal(itemJson, type) {
    try {
        const item = JSON.parse(itemJson);
        const modal = document.getElementById('contentModal');
        const modalTag = document.getElementById('modalTag');
        const modalTitle = document.getElementById('modalTitle');
        const modalDate = document.getElementById('modalDate');
        const modalTime = document.getElementById('modalTime');
        const modalLocation = document.getElementById('modalLocation');
        const modalDescription = document.getElementById('modalDescription');
        const modalImage = document.getElementById('modalImage');
        const timeContainer = document.getElementById('timeContainer');
        const locationContainer = document.getElementById('locationContainer');

        // Set header image
        if (item.image) {
            const imageUrl = type === 'announcement' ? '/assets/uploads/announcements/' + item.image : '/assets/uploads/news/' + item.image;
            modalImage.style.backgroundImage = 'url(' + imageUrl + ')';
        } else {
            modalImage.style.backgroundImage = 'url(/assets/DTO-hero.jpg)';
        }

        // Set content
        modalTag.textContent = type.charAt(0).toUpperCase() + type.slice(1);
        modalTag.innerHTML = (type === 'announcement' ? '<i data-lucide="megaphone" style="width: 16px; height: 16px;"></i>' : '<i data-lucide="newspaper" style="width: 16px; height: 16px;"></i>') + ' ' + modalTag.textContent;
        modalTitle.textContent = item.title;

        // Set date
        const dateField = type === 'announcement' ? 'date_created' : 'date_published';
        const dateValue = item[dateField] || item.date;
        modalDate.textContent = new Date(dateValue).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });

        // Set time if available
        if (item.start_time) {
            const startTime = new Date('1970-01-01 ' + item.start_time).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
            const endTime = new Date('1970-01-01 ' + item.end_time).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
            modalTime.textContent = startTime + ' - ' + endTime;
            timeContainer.style.display = 'flex';
        } else {
            timeContainer.style.display = 'none';
        }

        // Set location if available
        if (item.location) {
            modalLocation.textContent = item.location;
            locationContainer.style.display = 'flex';
        } else {
            locationContainer.style.display = 'none';
        }

        // Set description
        modalDescription.textContent = item.content || item.description || '';

        // Show modal
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
        lucide.createIcons();
    } catch (e) {
        console.error('Error opening modal:', e);
    }
}

function closeModal() {
    const modal = document.getElementById('contentModal');
    modal.classList.remove('active');
    document.body.style.overflow = 'auto';
}

document.addEventListener('DOMContentLoaded', function() {
    const contentModal = document.getElementById('contentModal');
    if (contentModal) {
        contentModal.addEventListener('click', function(e) {
            if (e.target.id === 'contentModal') {
                closeModal();
            }
        });
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
    }
});
