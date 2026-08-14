<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TravelEase - Travel Booking & Holiday Packages</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/all.min.css">
    <link rel="stylesheet" href="css/fonts.css">
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> -->
    <!-- <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Montserrat:wght@400;500;600;700&display=swap"> -->
 
<link rel="icon" href="travelEASEonly.png" type="image/png">


</head>
<body>
    <!-- Navigation Bar -->
    <?php include '.\includes\navbar.php'; ?>

    <!-- Hero Section -->
    <section class="hero">
    <video autoplay muted loop playsinline class="hero-video">
        <source src="trip.mp4" type="video/mp4">
    </video>

    <!-- Dark Overlay -->
    <div class="hero-overlay"></div>

    <div class="container">
        <div class="hero-content">
            <h1>Travel The World With <span class="highlight">Ease</span></h1>
            <p class="hero-subtitle">
                Book flights, hotels, trains, buses, cruises & holiday packages all in one place
            </p>
        </div>
    </div>
    </section>

 <!-- Services Section -->
    <section class="services" id="services-section">
        <div class="container">
            <h2 class="section-title">Our <span class="highlight">Services</span></h2>
            <p class="section-subtitle">Everything you need for a perfect journey</p>
            
           <div class="services-grid">
    <a href="flightbook.php" class="service-card">
        <div class="service-icon">
            <i class="fas fa-plane"></i>
        </div>
        <h3>Flight Booking</h3>
        <p>Domestic and international flights with best prices and flexible options</p>
        <span class="service-link-text">Book Now <i class="fas fa-arrow-right"></i></span>
    </a>
    
    <a href="hotelbook.php" class="service-card">
        <div class="service-icon">
            <i class="fas fa-hotel"></i>
        </div>
        <h3>Hotel Reservation</h3>
        <p>Luxury to budget hotels with verified reviews and instant confirmation</p>
        <span class="service-link-text">Book Now <i class="fas fa-arrow-right"></i></span>
    </a>
    
    <a href="trainbook.php" class="service-card">
        <div class="service-icon">
            <i class="fas fa-train"></i>
        </div>
        <h3>Train Tickets</h3>
        <p>Book train tickets across India with easy cancellation and PNR status</p>
        <span class="service-link-text">Book Now <i class="fas fa-arrow-right"></i></span>
    </a>
    
    <a href="busbook.php" class="service-card">
        <div class="service-icon">
            <i class="fas fa-bus"></i>
        </div>
        <h3>Bus Tickets</h3>
        <p>Intercity bus tickets with seat selection and live tracking features</p>
        <span class="service-link-text">Book Now <i class="fas fa-arrow-right"></i></span>
    </a>
    
    <a href="cruisebook.php" class="service-card">
        <div class="service-icon">
            <i class="fas fa-ship"></i>
        </div>
        <h3>Cruise Packages</h3>
        <p>Luxury cruise packages with all-inclusive amenities and exotic itineraries</p>
        <span class="service-link-text">Book Now <i class="fas fa-arrow-right"></i></span>
    </a>
    
    <a href="holidaybook.php" class="service-card" id="holiday-package-card">
        <div class="service-icon">
            <i class="fas fa-suitcase-rolling"></i>
        </div>
        <h3>Holiday Packages</h3>
        <p>Customized holiday packages with flights, hotels, transfers & sightseeing</p>
        <span class="service-link-text">Book Now <i class="fas fa-arrow-right"></i></span>
    </a>
</div>
            </div>
      
    </section>

    <!-- Popular Destinations -->
    <section class="destinations">
    <div class="container">
        <h2 class="section-title">Popular <span class="highlight">Destinations</span></h2>
        <p class="section-subtitle">Explore the most sought-after travel destinations</p>
        
        <div class="destinations-grid">
            <!-- Goa -->
            <div class="destination-card" data-destination="goa">
                <div class="destination-image">
                    <img src=".\tour_images\goahome.jpg" alt="Goa">
                    <div class="destination-overlay">
                        <h3>Goa</h3>
                        <p>Beaches • Nightlife • Adventure</p>
                        <div class="destination-price">
                            <span class="old-price">₹12,499</span>
                            <span class="new-price">₹8,999</span>
                            <span class="discount-badge">-28%</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tokyo -->
            <div class="destination-card" data-destination="tokyo">
                <div class="destination-image">
                    <img src=".\tour_images\japanhome.jpg" alt="Tokyo">
                    <div class="destination-overlay">
                        <h3>Tokyo</h3>
                        <p>Culture • Technology • Cuisine</p>
                        <div class="destination-price">
                            <span class="old-price">₹499,000</span>
                            <span class="new-price">₹349,000</span>
                            <span class="discount-badge">-30%</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Rajasthan -->
            <div class="destination-card" data-destination="rajasthan">
                <div class="destination-image">
                    <img src=".\tour_images\rajasthanhome.jpg" alt="Rajasthan">
                    <div class="destination-overlay">
                        <h3>Rajasthan</h3>
                        <p>Palaces • Deserts • Culture</p>
                        <div class="destination-price">
                            <span class="old-price">₹22,499</span>
                            <span class="new-price">₹15,999</span>
                            <span class="discount-badge">-29%</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Himachal -->
            <div class="destination-card" data-destination="himachal">
                <div class="destination-image">
                    <img src=".\tour_images\himachalhome.jpg" alt="Himachal">
                    <div class="destination-overlay">
                        <h3>Himachal</h3>
                        <p>Mountains • Trekking • Snow</p>
                        <div class="destination-price">
                            <span class="old-price">₹14,999</span>
                            <span class="new-price">₹9,999</span>
                            <span class="discount-badge">-33%</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Paris -->
            <div class="destination-card" data-destination="Paris">
                <div class="destination-image">
                    <img src=".\tour_images\parishome.jpg" alt="Paris">
                    <div class="destination-overlay">
                        <h3>Paris</h3>
                        <p>Art • Culture • Cityscape</p>
                        <div class="destination-price">
                            <span class="old-price">₹350,000</span>
                            <span class="new-price">₹249,000</span>
                            <span class="discount-badge">-20%</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Dubai -->
            <div class="destination-card" data-destination="dubai">
                <div class="destination-image">
                    <img src=".\tour_images\dubaihome.jpg" alt="Dubai">
                    <div class="destination-overlay">
                        <h3>Dubai</h3>
                        <p>Skyscrapers • Shopping • Luxury</p>
                        <div class="destination-price">
                            <span class="old-price">₹499,000</span>
                            <span class="new-price">₹349,000</span>
                            <span class="discount-badge">-30%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


 <!-- ===== Testimonials Section ===== -->
<section class="testimonials">
  <div class="container">
    <h2 class="section-title">What Our <span class="highlight">Travelers Say</span></h2>
    <p class="section-subtitle">Real stories from happy travelers around the world</p>

    <div class="testimonial-wrapper">
  <button class="arrow left" id="leftArrow">&#10094;</button>

      <div class="testimonial-carousel" id="testimonialCarousel">

        <!-- 1 -->
        <div class="testimonial-card">
          <div class="quote-icon">“</div>
          <p class="testimonial-text">Smooth booking, great support, and the best hotel deals I’ve ever seen!</p>
          <div class="traveler-info">
            <img src="./images/pr1.jpeg" class="traveler-avatar">
            <div>
              <h4>Priya Sharma</h4>
              <p>Mumbai</p>
              <div class="stars">★★★★★</div>
            </div>
          </div>
        </div>

        <!-- 2 -->
        <div class="testimonial-card">
          <div class="quote-icon">“</div>
          <p class="testimonial-text">Cancelled my train ticket last minute and got refund in 2 hours.</p>
          <div class="traveler-info">
            <img src="./images/pr2.jpeg" class="traveler-avatar">
            <div>
              <h4>Rahul Singh</h4>
              <p>Delhi</p>
              <div class="stars">★★★★☆</div>
            </div>
          </div>
        </div>

        <!-- 3 -->
        <div class="testimonial-card">
          <div class="quote-icon">“</div>
          <p class="testimonial-text">The cruise package was perfectly planned. Loved every moment!</p>
          <div class="traveler-info">
            <img src="./images/pr3.jpeg" class="traveler-avatar">
            <div>
              <h4>Anjali Mehta</h4>
              <p>Bangalore</p>
              <div class="stars">★★★★★</div>
            </div>
          </div>
        </div>

        <!-- 4 -->
        <div class="testimonial-card">
          <div class="quote-icon">“</div>
          <p class="testimonial-text">Flight booking was super easy and prices were cheaper than others.</p>
          <div class="traveler-info">
            <img src="./images/pr4.jpeg" class="traveler-avatar">
            <div>
              <h4>Amit Joshi</h4>
              <p>Pune</p>
              <div class="stars">★★★★☆</div>
            </div>
          </div>
        </div>

        <!-- 5 -->
        <div class="testimonial-card">
          <div class="quote-icon">“</div>
          <p class="testimonial-text">Hotel + cab combo saved me a lot of money. Highly recommended!</p>
          <div class="traveler-info">
            <img src="./images/pr5.jpeg" class="traveler-avatar">
            <div>
              <h4>Neha Kapoor</h4>
              <p>Chandigarh</p>
              <div class="stars">★★★★★</div>
            </div>
          </div>
        </div>

        <!-- 6 -->
        <div class="testimonial-card">
          <div class="quote-icon">“</div>
          <p class="testimonial-text">Customer support is fast and helpful. Issue resolved in minutes.</p>
          <div class="traveler-info">
            <img src="./images/pr6.jpeg" class="traveler-avatar">
            <div>
              <h4>Mahendra Singh</h4>
              <p>Jaipur</p>
              <div class="stars">★★★★☆</div>
            </div>
          </div>
        </div>

        <!-- 7 -->
        <div class="testimonial-card">
          <div class="quote-icon">“</div>
          <p class="testimonial-text">Booked honeymoon package and everything was perfectly managed.</p>
          <div class="traveler-info">
            <img src="./images/pr7.jpeg" class="traveler-avatar">
            <div>
              <h4>Sneha Patel</h4>
              <p>Ahmedabad</p>
              <div class="stars">★★★★★</div>
            </div>
          </div>
        </div>

        <!-- 8 -->
        <div class="testimonial-card">
          <div class="quote-icon">“</div>
          <p class="testimonial-text">Train booking UI is simple and smooth. Even my parents loved it.</p>
          <div class="traveler-info">
            <img src="./images/pr8.jpeg" class="traveler-avatar">
            <div>
              <h4>Vikas Rao</h4>
              <p>Hyderabad</p>
              <div class="stars">★★★★☆</div>
            </div>
          </div>
        </div>

        <!-- 9 -->
        <div class="testimonial-card">
          <div class="quote-icon">“</div>
          <p class="testimonial-text">Best travel site for budget travelers. Trustworthy and fast.</p>
          <div class="traveler-info">
            <img src="./images/pr9.jpeg" class="traveler-avatar">
            <div>
              <h4>Pooja Nair</h4>
              <p>Kolkata</p>
              <div class="stars">★★★★★</div>
            </div>
          </div>
        </div>

      </div>

      <button class="arrow right" id="rightArrow">&#10095;</button>
      </div>
  </div>
</section>

<!-- ===== Why Choose Us Section ===== -->
<section class="why-choose">
  <div class="container">
    <h2 class="section-title">Why Choose <span class="highlight">TravelEase?</span></h2>
    <p class="section-subtitle">We make travel simple, secure, and affordable</p>
    <div class="usp-grid">
      <div class="usp-card">
        <div class="usp-icon-wrapper">
          <span class="usp-icon">🏷️</span>
        </div>
        <h3 class="usp-title">Best Price Guarantee</h3>
        <p class="usp-desc">We match any lower price you find within 24 hours of booking.</p>
      </div>
      
      <div class="usp-card">
        <div class="usp-icon-wrapper">
          <span class="usp-icon">🔄</span>
        </div>
        <h3 class="usp-title">Easy Cancellation</h3>
        <p class="usp-desc">Cancel or reschedule anytime with minimal fees and instant refunds.</p>
      </div>
      
      <div class="usp-card">
        <div class="usp-icon-wrapper">
          <span class="usp-icon">🌐</span>
        </div>
        <h3 class="usp-title">24/7 Customer Support</h3>
        <p class="usp-desc">Round-the-clock assistance via chat, phone, or email. We're always here.</p>
      </div>
      
      <div class="usp-card">
        <div class="usp-icon-wrapper">
          <span class="usp-icon">🔒</span>
        </div>
        <h3 class="usp-title">Secure Booking</h3>
        <p class="usp-desc">Your data and payments are 100% protected with bank-level security.</p>
      </div>
    </div>
  </div>
</section>




    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section" id="footer-about">
                    <div class="footer-brand">
                        <img src="travelEASEonly.png" alt="TravelEase Logo" class="nav-logo">
                        <span>Travel<span class="brand-highlight">Ease</span></span>
                    </div>
                    <p> Since <strong>1999</strong>, TravelEase has been delivering trusted travel
                    solutions — flights, hotels, trains, buses, cruises, and holiday packages —
                    all in one place.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                    <a href="javascript:void(0)" class="footer-readmore" onclick="toggleAbout()">
    Read our story →
</a>

<div id="footer-about-expanded" class="footer-about-expanded">
    <h4>About TravelEase</h4>
    <p>
        We turn journeys into stories. From weekend getaways to once-in-a-lifetime
        adventures, we’ve been your compass since 1999.
    </p>

    <ul class="footer-points">
        <li>🏆 Award-winning service</li>
        <li>🌍 Global partners in 90+ countries</li>
        <li>📞 24/7 human support</li>
        <li>💰 Fair-price guarantee</li>
    </ul>

    <p><strong>Our Promise:</strong> Transparent pricing, zero hidden fees, and
    itineraries that fit how you live.</p>

    <div class="footer-stats">
        <div><strong>25+</strong><span>Years</span></div>
        <div><strong>1M+</strong><span>Trips</span></div>
        <div><strong>4.9★</strong><span>Rating</span></div>
    </div>
</div>

                </div>
                
                <div class="footer-section">
    <h3>Quick Links</h3>
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="flightbook.php">Flights</a></li>
        <li><a href="hotelbook.php">Hotels</a></li>
        <li><a href="trainbook.php">Trains</a></li>
        <li><a href="busbook.php">Buses</a></li>
        <li><a href="cruisebook.php">Cruises</a></li> <!-- NEW -->
        <li><a href="holidaybook.php">Holiday Packages</a></li> <!-- CHANGED -->
    </ul>
</div>


                <div class="footer-section" id="footer-contact">
                    <h3>Contact Us</h3>
                    <ul class="contact-info">
                        <li><i class="fas fa-map-marker-alt"></i> 123 Travel Street, Mumbai, India</li>
                        <li>
  <i class="fas fa-phone"></i>
  <a href="tel:+917201807642">+91 72018 07642</a>
</li>

<li>
  <i class="fas fa-phone"></i>
  <a href="tel:+917043958364">+91 70439 58364</a>
</li>

                        <li>
  <i class="fas fa-envelope"></i>
  <a href="mailto:support@travelease.com
?subject=Travel%20Inquiry
&body=Hello%20TravelEase%20Team,
&cc=admin@travelease.com
">support@travelease.com</a>
</li>
                        <li><i class="fas fa-clock"></i> 24/7 Customer Support</li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2026 TravelEase. All rights reserved. | <a href="legal.html">Privacy Policy</a> | <a href="legal.html">Terms & Conditions</a></p>
            </div>
        </div>
    </footer>

    <script src="js/script.js"></script>
    <script>
function toggleAbout() {
    const about = document.getElementById("footer-about-expanded");
    about.style.display = about.style.display === "block" ? "none" : "block";
}




const carousel = document.getElementById("testimonialCarousel");
const leftArrow = document.getElementById("leftArrow");
const rightArrow = document.getElementById("rightArrow");

let currentIndex = 0;
let autoScrollInterval;

function cardsPerView() {
  return window.innerWidth <= 768 ? 1 : 3;
}

function updateCarousel() {
  const card = carousel.children[0];
  const gap = 20;
  const cardWidth = card.offsetWidth + gap;

  const maxIndex = carousel.children.length - cardsPerView();

  currentIndex = Math.max(0, Math.min(currentIndex, maxIndex));

  carousel.style.transform = `translateX(-${currentIndex * cardWidth}px)`;

  // Arrow visibility
  leftArrow.classList.toggle("hidden", currentIndex === 0);
  rightArrow.classList.toggle("hidden", currentIndex === maxIndex);
}

function nextSlide() {
  currentIndex++;
  if (currentIndex > carousel.children.length - cardsPerView()) {
    currentIndex = 0; // loop back to start
  }
  updateCarousel();
}

function startAutoScroll() {
  autoScrollInterval = setInterval(nextSlide, 3000); // every 3s
}

function stopAutoScroll() {
  clearInterval(autoScrollInterval);
}

// Arrow controls
rightArrow.addEventListener("click", () => {
  nextSlide();
  stopAutoScroll();
  startAutoScroll();
});

leftArrow.addEventListener("click", () => {
  currentIndex--;
  updateCarousel();
  stopAutoScroll();
  startAutoScroll();
});

// Pause on hover
carousel.addEventListener("mouseenter", stopAutoScroll);
carousel.addEventListener("mouseleave", startAutoScroll);

window.addEventListener("resize", updateCarousel);

// Initial state
updateCarousel();
startAutoScroll();

</script>

</body>
</html>