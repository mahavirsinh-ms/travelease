// DOM Elements
const mobileToggle = document.getElementById('mobileToggle');
const navMenu = document.getElementById('navMenu');
const tabs = document.querySelectorAll('.tab');
const forms = document.querySelectorAll('.search-form');
const dateInputs = document.querySelectorAll('input[type="date"]');

// Set today's date as minimum for date inputs
const today = new Date().toISOString().split('T')[0];
dateInputs.forEach(input => {
    input.min = today;
    
    // Set default values
    if (input.previousElementSibling && 
        input.previousElementSibling.textContent.includes('Departure')) {
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        input.value = tomorrow.toISOString().split('T')[0];
    }
    
    if (input.previousElementSibling && 
        input.previousElementSibling.textContent.includes('Check-in')) {
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        input.value = tomorrow.toISOString().split('T')[0];
    }
    
    if (input.previousElementSibling && 
        input.previousElementSibling.textContent.includes('Check-out')) {
        const dayAfter = new Date();
        dayAfter.setDate(dayAfter.getDate() + 2);
        input.value = dayAfter.toISOString().split('T')[0];
    }
});

// Mobile Menu Toggle
mobileToggle.addEventListener('click', () => {
    navMenu.classList.toggle('active');
    mobileToggle.querySelector('i').classList.toggle('fa-bars');
    mobileToggle.querySelector('i').classList.toggle('fa-times');
});

// Close mobile menu when clicking outside
document.addEventListener('click', (e) => {
    if (!navMenu.contains(e.target) && !mobileToggle.contains(e.target)) {
        navMenu.classList.remove('active');
        mobileToggle.querySelector('i').classList.remove('fa-times');
        mobileToggle.querySelector('i').classList.add('fa-bars');
    }
});

// Search Tab Switching
tabs.forEach(tab => {
    tab.addEventListener('click', () => {
        const type = tab.dataset.type;
        
        // Remove active class from all tabs and forms
        tabs.forEach(t => t.classList.remove('active'));
        forms.forEach(form => form.classList.remove('active'));
        
        // Add active class to clicked tab and corresponding form
        tab.classList.add('active');
        document.getElementById(`${type}-form`).classList.add('active');
    });
});

// Form Validation and Submission
// Forms now submit directly to search-results.php, so we just add loading state
forms.forEach(form => {
    form.addEventListener('submit', (e) => {
        // Show loading state
        const submitBtn = form.querySelector('.btn-search');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Searching...';
        submitBtn.disabled = true;
        
        // Form will submit normally to search-results.php
    });
});

// Smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const targetId = this.getAttribute('href');
        if (targetId === '#') return;
        
        const targetElement = document.querySelector(targetId);
        if (targetElement) {
            window.scrollTo({
                top: targetElement.offsetTop - 80,
                behavior: 'smooth'
            });
            
            // Close mobile menu if open
            navMenu.classList.remove('active');
            mobileToggle.querySelector('i').classList.remove('fa-times');
            mobileToggle.querySelector('i').classList.add('fa-bars');
        }
    });
});

// Add animation on scroll
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('animate');
        }
    });
}, observerOptions);

// Observe elements for animation
document.querySelectorAll('.feature-card, .destination-card, .service-card').forEach(el => {
    observer.observe(el);
});


// Set today's date as minimum for date inputs

dateInputs.forEach(input => {
    input.min = today;
    
    // Set default values
    if (input.previousElementSibling && 
        input.previousElementSibling.textContent.includes('Departure')) {
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        input.value = tomorrow.toISOString().split('T')[0];
    }
    
    if (input.previousElementSibling && 
        input.previousElementSibling.textContent.includes('Check-in')) {
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        input.value = tomorrow.toISOString().split('T')[0];
    }
    
    if (input.previousElementSibling && 
        input.previousElementSibling.textContent.includes('Check-out')) {
        const dayAfter = new Date();
        dayAfter.setDate(dayAfter.getDate() + 2);
        input.value = dayAfter.toISOString().split('T')[0];
    }
    
    if (input.previousElementSibling && 
        input.previousElementSibling.textContent.includes('Cruise')) {
        const nextWeek = new Date();
        nextWeek.setDate(nextWeek.getDate() + 7);
        input.value = nextWeek.toISOString().split('T')[0];
    }
});


// Profile Dropdown Toggle
document.addEventListener("DOMContentLoaded", function () {
    const profileBtn = document.getElementById("profileButton");
    const profileMenu = document.getElementById("profileMenu");

    profileBtn.addEventListener("click", function (e) {
        e.preventDefault();
        profileMenu.style.display =
            profileMenu.style.display === "flex" ? "none" : "flex";
    });

    // Close dropdown when clicking outside
    document.addEventListener("click", function (event) {
        if (!profileBtn.contains(event.target) && !profileMenu.contains(event.target)) {
            profileMenu.style.display = "none";
        }
    });
});

// Destination Card Click Handler - Scroll to Services and Glow Holiday Package Card
document.addEventListener("DOMContentLoaded", function () {
    const destinationCards = document.querySelectorAll('.destination-card');
    const servicesSection = document.getElementById('services-section');
    const holidayPackageCard = document.getElementById('holiday-package-card');
    const navHolidayPackages = document.getElementById('nav-holiday-packages');

    if (destinationCards.length > 0 && servicesSection && holidayPackageCard) {
        destinationCards.forEach(card => {
            card.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Remove any existing glow classes
                holidayPackageCard.classList.remove('holiday-glow');
                if (navHolidayPackages) {
                    navHolidayPackages.classList.remove('nav-holiday-glow');
                }
                
                // Calculate scroll position (services section position minus some offset)
                const scrollPosition = servicesSection.offsetTop + 200;
                
                // Smooth scroll to services section
                window.scrollTo({
                    top: scrollPosition,
                    behavior: 'smooth'
                });
                
                // Add glow effect after a short delay to sync with scroll
                setTimeout(() => {
                    holidayPackageCard.classList.add('holiday-glow');
                    if (navHolidayPackages) {
                        navHolidayPackages.classList.add('nav-holiday-glow');
                    }
                    
                    // Remove glow effect after 4 seconds
                    setTimeout(() => {
                        holidayPackageCard.classList.remove('holiday-glow');
                        if (navHolidayPackages) {
                            navHolidayPackages.classList.remove('nav-holiday-glow');
                        }
                    }, 4000);
                }, 500);
            });
        });
    }
});