    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <h3>About DTO</h3>
                    <p style="color: rgba(255,255,255,0.8); line-height: 1.6;">Digital Transformation Office - Empowering innovation through seamless communication and cutting-edge solutions.</p>
                </div>
                <div class="footer-col">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="/"><i data-lucide="home" style="width: 16px; height: 16px;"></i> Home</a></li>
                        <li><a href="/?section=announcements"><i data-lucide="megaphone" style="width: 16px; height: 16px;"></i> Announcements</a></li>
                        <li><a href="/?section=news"><i data-lucide="newspaper" style="width: 16px; height: 16px;"></i> News</a></li>
                        <li><a href="/?section=systems"><i data-lucide="box" style="width: 16px; height: 16px;"></i> Systems</a></li>
                        <li><a href="/?section=developer"><i data-lucide="code" style="width: 16px; height: 16px;"></i> Developer</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h3>Contact</h3>
                    <ul>
                        <li><a href="mailto:info@dto.com"><i data-lucide="mail" style="width: 16px; height: 16px;"></i> dto@basc.edu.ph</a></li>
                        <li><a href="tel:+1234567890"><i data-lucide="phone" style="width: 16px; height: 16px;"></i> (044) 931 8660 </a></li>
                        <li><i data-lucide="map-pin" style="width: 16px; height: 16px; display: inline; margin-right: 0.5rem;"></i>Pinaod, San Ildefonso Bulacan</li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h3>Follow Us</h3>
                    <ul>
                        <li><a href="https://www.facebook.com/profile.php?id=61587088024158"><i data-lucide="facebook" style="width: 16px; height: 16px;"></i> Facebook</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 Digital Transformation Office. All rights reserved. | Version 1.0.0</p>
            </div>
        </div>
    </footer>

    <!-- Modal -->
    <div class="modal" id="contentModal">
        <div class="modal-content">
            <button class="modal-close" onclick="closeModal();">
                <i data-lucide="x" style="width: 20px; height: 20px;"></i>
            </button>
            <div class="modal-header" id="modalImage">
                <div class="modal-header-content">
                    <div class="modal-tag" id="modalTag"></div>
                    <h2 class="modal-title" id="modalTitle"></h2>
                </div>
            </div>
            <div class="modal-body">
                <div class="modal-info">
                    <div class="modal-info-item">
                        <i data-lucide="calendar" style="width: 18px; height: 18px; color: var(--primary);"></i>
                        <span id="modalDate"></span>
                    </div>
                    <div class="modal-info-item" id="timeContainer" style="display: none;">
                        <i data-lucide="clock" style="width: 18px; height: 18px; color: var(--primary);"></i>
                        <span id="modalTime"></span>
                    </div>
                    <div class="modal-info-item" id="locationContainer" style="display: none;">
                        <i data-lucide="map-pin" style="width: 18px; height: 18px; color: var(--primary);"></i>
                        <span id="modalLocation"></span>
                    </div>
                </div>
                <div class="modal-description" id="modalDescription"></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="/assets/js/script.js"></script>
</body>
</html>
