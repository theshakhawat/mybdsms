@extends('layouts.app')

@section('title', 'Home - MyBDSMS')

@section('content')
    <!-- ===============================================
         Hero Section
         =============================================== -->
    <section id="hero">
        <div class="hero-pattern"></div>
        <div class="hero-shapes">
            <div class="hero-shape hero-shape-1"></div>
            <div class="hero-shape hero-shape-2"></div>
            <div class="hero-shape hero-shape-3"></div>
            <div class="hero-shape hero-shape-4"></div>
        </div>
        <div class="hero-decoration hero-decoration-1"></div>
        <div class="hero-decoration hero-decoration-2"></div>
        <div class="hero-dots">
            <div class="hero-dot"></div>
            <div class="hero-dot"></div>
            <div class="hero-dot"></div>
            <div class="hero-dot"></div>
            <div class="hero-dot"></div>
            <div class="hero-dot"></div>
        </div>
        <div class="container">
            @if($banners && $banners->count() > 0)
                @php $banner = $banners->first(); @endphp
                <div class="row align-items-center">
                    <div class="col-lg-7">
                        <div class="hero-content fade-in">
                            @if($banner->badge)<span class="hero-badge"><i class="fas fa-check-circle"></i>{{ $banner->badge }}</span>@endif

                            @if($banner->title)
                                <h1 class="hero-title">
                                    @if(str_contains($banner->title, '<span>')){!! $banner->title !!}
                                    @elseif(str_contains($banner->title, '|')){!! str_replace('|', '<span>', $banner->title) . '</span>' !!}
                                    @else{!! $banner->title !!}@endif
                                </h1>
                            @else
                                <h1 class="hero-title">Connect with Customers Through <span>Powerful SMS</span></h1>
                            @endif

                            @if($banner->subtitle)<p class="hero-subtitle">{{ $banner->subtitle }}</p>@endif

                            <div class="hero-buttons">
                                @if($banner->button_text)<a href="{{ $banner->button_link ?: '/#contact' }}" class="btn-hero-primary"><i class="fas fa-rocket"></i>{{ $banner->button_text }}</a>@endif
                                <a href="#services" class="btn-hero-secondary"><i class="fas fa-info-circle"></i>Our Services</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="hero-image fade-in">
                            <div class="hero-image-wrapper">
                                @if($banner->image)<img src="{{ asset($banner->image) }}" alt="MyBDSMS SMS Services" class="hero-main-image">
                                @else<img src="{{ asset('assets/images/banner_img.png') }}" alt="MyBDSMS SMS Services" class="hero-main-image">@endif
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="row align-items-center">
                    <div class="col-lg-7">
                        <div class="hero-content fade-in">
                            <span class="hero-badge"><i class="fas fa-check-circle"></i>Bangladesh's Trusted SMS Partner</span>
                            <h1 class="hero-title">Connect with Customers Through <span>Powerful SMS</span></h1>
                            <p class="hero-subtitle">MyBDSMS is Bangladesh's trusted SMS service provider. We deliver reliable bulk SMS solutions including Non-Masking, Transactional, OTP, and Promotional SMS to help your business connect with customers instantly.</p>
                            <div class="hero-buttons">
                                <a href="/#contact" class="btn-hero-primary"><i class="fas fa-rocket"></i>Get Started</a>
                                <a href="#services" class="btn-hero-secondary"><i class="fas fa-info-circle"></i>Our Services</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="hero-image fade-in">
                            <div class="hero-image-wrapper">
                                <img src="{{ asset('assets/images/banner_img.png') }}" alt="MyBDSMS SMS Services" class="hero-main-image">
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>

    <!-- ===============================================
         About Section
         =============================================== -->
    <section id="about">
        <div class="container">
            <div class="section-header fade-in">
                @if($about->badge)<span class="section-badge">{{ $about->badge }}</span>@endif
                @if($about->title)<h2 class="section-title">{!! $about->title !!}</h2>@endif
                @if($about->subtitle)<p class="section-subtitle">{{ $about->subtitle }}</p>@endif
            </div>

            <div class="about-content">
                <div class="about-text slide-in-left">
                    @if($about->description)
                        <p>{{ $about->description }}</p>
                    @else
                        <h3>Empowering Businesses Through SMS</h3>
                        <p>MyBDSMS, a venture of Freelancer Digital Expert, is Bangladesh's leading SMS service provider. We understand that in today's fast-paced digital world, effective communication is key to business success.</p>
                        <p>Our SMS solutions are designed to help you connect with your customers instantly and reliably. Whether you need to send promotional messages, transactional alerts, or OTP verification codes, we've got you covered.</p>
                    @endif

                    <ul class="about-features">
                        @if($about->features && is_iterable($about->features) && count($about->features) > 0)
                            @foreach($about->features as $feature)
                                <li><i class="fas fa-check"></i>{{ $feature }}</li>
                            @endforeach
                        @else
                            <li><i class="fas fa-check"></i>Direct connections with all mobile operators</li>
                            <li><i class="fas fa-check"></i>99.9% delivery guarantee</li>
                            <li><i class="fas fa-check"></i>Real-time delivery reports</li>
                            <li><i class="fas fa-check"></i>24/7 customer support</li>
                            <li><i class="fas fa-check"></i>Competitive pricing in Bangladesh</li>
                        @endif
                    </ul>
                </div>

                <div class="about-image slide-in-right">
                    @if($about->image)<img src="{{ asset($about->image) }}" alt="About Us" class="img-fluid">
                    @else<img src="https://images.unsplash.com/photo-1556761175-5973dc0f32e7?w=600&h=500&fit=crop" alt="Team Collaboration" class="img-fluid">@endif
                </div>
            </div>
        </div>
    </section>

    <!-- ===============================================
         Stats Section
         =============================================== -->
    <section id="stats" class="stats-section">
        <div class="stats-overlay"></div>
        <div class="container position-relative">
            <div class="row g-4">
                <div class="col-md-3 col-6">
                    <div class="stat-item">
                        <div class="stat-icon"><i class="fas fa-users"></i></div>
                        <h4 class="counter" data-target="{{ $stats->clients_count }}">{{ $stats->clients_count }}</h4>
                        <p>{{ $stats->clients_label }}</p>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-item">
                        <div class="stat-icon"><i class="fas fa-sms"></i></div>
                        <h4 class="counter" data-target="{{ $stats->sms_count }}">{{ $stats->sms_count }}</h4>
                        <p>{{ $stats->sms_label }}</p>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-item">
                        <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
                        <h4 class="counter" data-target="{{ $stats->delivery_count }}">{{ $stats->delivery_count }}</h4>
                        <p>{{ $stats->delivery_label }}</p>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-item">
                        <div class="stat-icon"><i class="fas fa-award"></i></div>
                        <h4 class="counter" data-target="{{ $stats->years_count }}">{{ $stats->years_count }}</h4>
                        <p>{{ $stats->years_label }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===============================================
         Services Section
         =============================================== -->
    <section id="services">
        <div class="container">
            <div class="section-header fade-in">
                <span class="section-badge">Our Services</span>
                <h2 class="section-title">What We Offer</h2>
                <p class="section-subtitle">
                    Comprehensive SMS solutions tailored to meet all your business communication needs.
                </p>
            </div>

            <div class="service-grid">
                @forelse($services as $service)
                    <div class="service-card fade-in">
                        <div class="service-icon">
                            <i class="{{ $service->icon }}"></i>
                        </div>
                        <h3>{{ $service->title }}</h3>
                        <p>{{ $service->description }}</p>
                        <a href="#contact" class="service-link">
                            Get Started <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                @empty
                    <div class="col-12 text-center">
                        <p class="text-muted">No services available at the moment.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- ===============================================
         Features Section
         =============================================== -->
    <section id="features">
        <div class="container">
            <div class="section-header fade-in">
                <span class="section-badge">Features</span>
                <h2 class="section-title">Why We're Different</h2>
                <p class="section-subtitle">
                    Advanced features that set us apart from other SMS service providers in Bangladesh.
                </p>
            </div>

            <div class="feature-grid">
                @forelse($features as $feature)
                    <div class="feature-item fade-in">
                        <div class="feature-number">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</div>
                        <div class="feature-content">
                            <h4>{{ $feature->title }}</h4>
                            <p>{{ $feature->description }}</p>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center">
                        <p class="text-muted">No features available at the moment.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- ===============================================
         Pricing Section
         =============================================== -->
    <section id="pricing">
        <div class="container">
            <div class="section-header fade-in">
                <span class="section-badge">Pricing</span>
                <h2 class="section-title">Affordable Packages</h2>
                <p class="section-subtitle">
                    Choose the package that fits your business needs. No hidden fees, transparent pricing.
                </p>
            </div>

            <div class="pricing-tabs">
                {{-- <button class="pricing-tab active" data-type="all">All Packages</button> --}}
                <button class="pricing-tab active" data-type="non-masking">Non-Masking</button>
                <button class="pricing-tab" data-type="masking">Masking</button>
                
            </div>

            <div class="pricing-grid">
                @forelse($pricingPlans as $plan)
                    <div class="pricing-card fade-in {{ $plan->is_featured ? 'featured' : '' }}" data-type="{{ strtolower($plan->plan_type) }}">
                        @if($plan->is_featured)
                            <div class="pricing-badge">Popular</div>
                        @endif

                        @if($plan->icon)
                            <div class="pricing-icon">
                                <i class="{{ $plan->icon }}"></i>
                            </div>
                        @endif

                        <h3>{{ $plan->name }}</h3>
                        <div class="pricing-price">
                            {{ $plan->price }} <span>/ SMS</span>
                        </div>
                        @if($plan->description)
                            <p class="pricing-desc">{{ $plan->description }}</p>
                        @endif

                        @if($plan->features && is_array($plan->features) && count($plan->features) > 0)
                            <ul class="pricing-features">
                                @foreach($plan->features as $feature)
                                    <li><i class="fas fa-check-circle"></i> {{ $feature }}</li>
                                @endforeach
                            </ul>
                        @endif

                        <a href="{{ $plan->button_link ?: '#contact' }}" class="btn-primary-custom">Choose Plan</a>
                    </div>
                @empty
                    <div class="col-12 text-center">
                        <p class="text-muted">No pricing plans available at the moment.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- ===============================================
         Testimonials Section
         =============================================== -->
    <section id="testimonials">
        <div class="container">
            <div class="section-header fade-in">
                <span class="section-badge">Testimonials</span>
                <h2 class="section-title">What Our Clients Say</h2>
                <p class="section-subtitle">
                    Don't just take our word for it. Here's what our valued clients have to say about our services.
                </p>
            </div>

            <!-- Testimonials Slider -->
            <div class="testimonials-slider fade-in">
                <div class="swiper">
                    <div class="swiper-wrapper">
                        @forelse($testimonials ?? [] as $testimonial)
                            <div class="swiper-slide">
                                <div class="testimonial-card">
                                    <div class="testimonial-quote">
                                        <i class="fas fa-quote-left"></i>
                                    </div>
                                    <div class="testimonial-stars">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <p class="testimonial-text">
                                        "{{ $testimonial->message }}"
                                    </p>
                                    <div class="testimonial-author">
                                        @if($testimonial->client_image)
                                            <img src="{{ asset($testimonial->client_image) }}" alt="{{ $testimonial->client_name }}">
                                        @else
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($testimonial->client_name) }}&background=34BD93&color=fff&size=150" alt="{{ $testimonial->client_name }}">
                                        @endif
                                        <div class="author-info">
                                            <h5>{{ $testimonial->client_name }}</h5>
                                            @if($testimonial->client_position)
                                                <span class="author-position">{{ $testimonial->client_position }}</span>
                                            @endif
                                            @if($testimonial->client_company)
                                                <span class="author-company">{{ $testimonial->client_company }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="swiper-slide">
                                <div class="text-center py-5">
                                    <p class="text-muted">No testimonials available at the moment.</p>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    <div class="swiper-pagination"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===============================================
         FAQ Section
         =============================================== -->
    <section id="faq">
        <div class="container">
            <div class="section-header fade-in">
                <span class="section-badge">FAQ</span>
                <h2 class="section-title">Frequently Asked Questions</h2>
                <p class="section-subtitle">
                    Find answers to common questions about our SMS services.
                </p>
            </div>

            <div class="faq-container">
                @forelse($faqs as $faq)
                    <div class="faq-item fade-in">
                        <button class="faq-question">
                            <h4>{{ $faq->question }}</h4>
                            <div class="faq-icon">
                                <i class="fas fa-chevron-down"></i>
                            </div>
                        </button>
                        <div class="faq-answer">
                            <p>{{ $faq->answer }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <i class="fas fa-question-circle fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No FAQs available at the moment.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- ===============================================
         Contact Section
         =============================================== -->
    <section id="contact">
        <div class="container">
            <div class="section-header fade-in">
                <span class="section-badge">Contact Us</span>
                <h2 class="section-title">Get In Touch</h2>
                <p class="section-subtitle">
                    Have questions? We're here to help. Reach out to us and we'll respond as soon as possible.
                </p>
            </div>

            <div class="contact-wrapper">
                <div class="contact-info slide-in-left">
                    <h3>Let's Start a Conversation</h3>
                    <p>
                        Ready to transform your business communication? Get in touch with us today and discover how our SMS solutions can help you reach more customers effectively.
                    </p>

                    <ul class="contact-details">
                        @if($settings->company_address ?? false)
                            <li>
                                <i class="fas fa-map-marker-alt"></i>
                                <div>
                                    <h5>Address</h5>
                                    <p>{{ $settings->company_address }}</p>
                                </div>
                            </li>
                        @endif
                        @if($settings->company_phone ?? false)
                            <li>
                                <i class="fas fa-phone-alt"></i>
                                <div>
                                    <h5>Phone</h5>
                                    <p>{{ $settings->company_phone }}</p>
                                </div>
                            </li>
                        @endif
                        @if($settings->company_email ?? false)
                            <li>
                                <i class="fas fa-envelope"></i>
                                <div>
                                    <h5>Email</h5>
                                    <p>{{ $settings->company_email }}</p>
                                </div>
                            </li>
                        @endif
                        @if($settings->company_hours ?? false)
                            <li>
                                <i class="fas fa-clock"></i>
                                <div>
                                    <h5>Working Hours</h5>
                                    <p>{{ $settings->company_hours }}</p>
                                </div>
                            </li>
                        @endif
                    </ul>

                    <div class="contact-social">
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

                <div class="contact-form slide-in-right">
                    <form id="contactForm" action="{{ route('contact.submit') }}" method="POST">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Full Name</label>
                                    <input type="text" class="form-control" id="name" name="name" required placeholder="Enter your name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email" required placeholder="Enter your email">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone">Phone Number <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" id="phone" name="phone" required placeholder="Enter your phone">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="service">Interested Service <span class="text-danger">*</span></label>
                                    <select class="form-control" id="service" name="service" required>
                                        <option value="">Select a service</option>
                                        <option value="non-masking">Non-Masking SMS</option>
                                        <option value="otp">OTP Verification</option>
                                        <option value="api">API Integration</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="message">Message</label>
                            <textarea class="form-control" id="message" name="message" rows="5" required placeholder="How can we help you?"></textarea>
                        </div>

                        <button type="submit" class="btn-primary-custom w-100">
                            <i class="fas fa-paper-plane"></i>
                            Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
