<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="footer-content">

            <!-- ABOUT -->
            <div class="footer-section" id="footer-about">
                <div class="footer-brand">
                    <img src="travelEASEonly.png" alt="TravelEase Logo" class="nav-logo">
                    <span>Travel<span class="brand-highlight">Ease</span></span>
                </div>

                <p>
                    Since <strong>1999</strong>, TravelEase has been delivering trusted travel
                    solutions — flights, hotels, trains, buses, cruises, and holiday packages —
                    all in one place.
                </p>

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

                    <p>
                        <strong>Our Promise:</strong> Transparent pricing, zero hidden fees,
                        and itineraries that fit how you live.
                    </p>

                    <div class="footer-stats">
                        <div><strong>25+</strong><span>Years</span></div>
                        <div><strong>1M+</strong><span>Trips</span></div>
                        <div><strong>4.9★</strong><span>Rating</span></div>
                    </div>
                </div>
            </div>

            <!-- QUICK LINKS -->
            <div class="footer-section">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="flightbook.php">Flights</a></li>
                    <li><a href="hotelbook.php">Hotels</a></li>
                    <li><a href="trainbook.php">Trains</a></li>
                    <li><a href="busbook.php">Buses</a></li>
                    <li><a href="cruisebook.php">Cruises</a></li>
                    <li><a href="holidaybook.php">Holiday Packages</a></li>
                </ul>
            </div>

            <!-- CONTACT -->
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
                        <a href="mailto:support@travelease.com?subject=Travel%20Inquiry&body=Hello%20TravelEase%20Team,&cc=admin@travelease.com">
                            support@travelease.com
                        </a>
                    </li>

                    <li><i class="fas fa-clock"></i> 24/7 Customer Support</li>
                </ul>
            </div>

        </div>

        <!-- BOTTOM -->
        <div class="footer-bottom">
            <p>
                &copy; 2026 TravelEase. All rights reserved. |
                <a href="legal.html">Privacy Policy</a> |
                <a href="legal.html">Terms & Conditions</a>
            </p>
        </div>
    </div>
</footer>

<script>
function toggleAbout() {
    const about = document.getElementById("footer-about-expanded");
    about.style.display = about.style.display === "block" ? "none" : "block";
}
</script>

