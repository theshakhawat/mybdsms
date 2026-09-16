{{-- Footer --}}
<footer id="footer">
    <div class="container">
        <div class="footer-top">
            <div class="footer-about">
                <img src="{{ $settings->company_logo ?? asset('assets/images/logo.jpeg') }}" alt="MyBDSMS Logo" style="border-radius: 10px; background: white; padding: 5px;">
                <p>
                    MyBDSMS, a venture of Freelancer Digital Expert, is Bangladesh's trusted SMS service provider. We deliver reliable, cost-effective bulk messaging solutions for businesses of all sizes.
                </p>
                <div class="contact-social mt-3">
                    @if($settings->social_facebook ?? false)
                        <a href="{{ $settings->social_facebook }}" target="_blank" aria-label="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                    @endif
                    @if($settings->social_twitter ?? false)
                        <a href="{{ $settings->social_twitter }}" target="_blank" aria-label="Twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                    @endif
                    @if($settings->social_linkedin ?? false)
                        <a href="{{ $settings->social_linkedin }}" target="_blank" aria-label="LinkedIn">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    @endif
                    @if($settings->social_youtube ?? false)
                        <a href="{{ $settings->social_youtube }}" target="_blank" aria-label="YouTube">
                            <i class="fab fa-youtube"></i>
                        </a>
                    @endif
                </div>
            </div>

            <div class="footer-links">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="#hero">Home</a></li>
                    <li><a href="#about">About Us</a></li>
                    <li><a href="#services">Services</a></li>
                    <li><a href="#pricing">Pricing</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
            </div>

            <div class="footer-links">
                <h4>Services</h4>
                <ul>
                    <li><a href="#services">Non-Masking SMS</a></li>
                    <li><a href="#services">Transactional SMS</a></li>
                    <li><a href="#services">OTP Verification</a></li>
                    <li><a href="#services">Promotional SMS</a></li>
                    <li><a href="#services">API Integration</a></li>
                </ul>
            </div>

            <div class="footer-contact">
                <h4>Contact Info</h4>
                <ul>
                    @if($settings->company_address ?? false)
                        <li>
                            <i class="fas fa-map-marker-alt"></i>
                            <span>{{ $settings->company_address }}</span>
                        </li>
                    @endif
                    @if($settings->company_phone ?? false)
                        <li>
                            <i class="fas fa-phone-alt"></i>
                            <span>{{ $settings->company_phone }}</span>
                        </li>
                    @endif
                    @if($settings->company_email ?? false)
                        <li>
                            <i class="fas fa-envelope"></i>
                            <span>{{ $settings->company_email }}</span>
                        </li>
                    @endif
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; <span id="currentYear"></span> MyBDSMS. All rights reserved.</p>
            <p>A product of <a href="https://www.freelancer.com.de/" target="_blank"><strong>Freelancer Digital Expert</strong></a></p>
        </div>
    </div>
</footer>
