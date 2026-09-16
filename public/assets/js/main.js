/**
 * MyBDSMS - Main JavaScript File
 * All interactive functionality for the website
 */

(function ($) {
    "use strict";

    // ===============================================
    // Document Ready
    // ===============================================
    $(document).ready(function () {
        // Preloader
        handlePreloader();

        // Navbar scroll effect
        initNavbarScroll();

        // Mobile menu toggle
        initMobileMenu();

        // Smooth scroll for navigation links
        initSmoothScroll();

        // Back to top button
        initBackToTop();

        // Testimonials slider
        initTestimonials();

        // FAQ accordion
        initFAQ();

        // Pricing tabs
        initPricingTabs();

        // Scroll animations
        initScrollAnimations();

        // Form submission
        initContactForm();

        // Counter animation
        initCounterAnimation();
    });

    // ===============================================
    // Preloader Handler
    // ===============================================
    function handlePreloader() {
        setTimeout(function () {
            $("#preloader").addClass("hidden");
            setTimeout(function () {
                $("#preloader").remove();
            }, 500);
        }, 1500);
    }

    // ===============================================
    // Navbar Scroll Effect
    // ===============================================
    function initNavbarScroll() {
        const header = $("#header");
        const navbar = $(".navbar");

        $(window).on("scroll", function () {
            if ($(this).scrollTop() > 50) {
                header.addClass("header-scrolled");
                navbar.addClass("navbar-scrolled");
            } else {
                header.removeClass("header-scrolled");
                navbar.removeClass("navbar-scrolled");
            }
        });

        // Active nav link on scroll
        const sections = $("section");
        const navLinks = $(".nav-link");

        $(window).on("scroll", function () {
            const current = $(this).scrollTop();

            sections.each(function () {
                const sectionTop = $(this).offset().top - 100;
                const sectionId = $(this).attr("id");

                if (current >= sectionTop) {
                    navLinks.removeClass("active");
                    $('.nav-link[href="#' + sectionId + '"]').addClass(
                        "active",
                    );
                }
            });
        });
    }

    // ===============================================
    // Mobile Menu Toggle
    // ===============================================
    function initMobileMenu() {
        const navbarToggler = $(".navbar-toggler");
        const navbarCollapse = $(".navbar-collapse");

        navbarToggler.on("click", function () {
            $(this).toggleClass("active");
            navbarCollapse.toggleClass("show");
        });

        // Close menu on link click
        $(".nav-link").on("click", function () {
            navbarToggler.removeClass("active");
            navbarCollapse.removeClass("show");
        });

        // Close menu on outside click
        $(document).on("click", function (e) {
            if (!$(e.target).closest(".navbar").length) {
                navbarToggler.removeClass("active");
                navbarCollapse.removeClass("show");
            }
        });
    }

    // ===============================================
    // Smooth Scroll
    // ===============================================
    function initSmoothScroll() {
        $('a[href^="#"]').on("click", function (e) {
            const target = $(this.getAttribute("href"));

            if (target.length) {
                e.preventDefault();

                const offset = 80;
                const targetPos = target.offset().top - offset;

                $("html, body").animate(
                    {
                        scrollTop: targetPos,
                    },
                    800,
                );
            }
        });
    }

    // ===============================================
    // Back to Top Button
    // ===============================================
    function initBackToTop() {
        const backToTop = $("#back-to-top");

        $(window).on("scroll", function () {
            if ($(this).scrollTop() > 300) {
                backToTop.addClass("visible");
            } else {
                backToTop.removeClass("visible");
            }
        });

        backToTop.on("click", function (e) {
            e.preventDefault();
            $("html, body").animate(
                {
                    scrollTop: 0,
                },
                800,
            );
        });
    }

    // ===============================================
    // Testimonials Slider - Multi-Card Layout
    // ===============================================
    function initTestimonials() {
        setTimeout(function() {
            if (typeof Swiper === 'undefined') {
                console.error('Swiper is not loaded!');
                return;
            }

            const testimonialsSlider = document.querySelector('.testimonials-slider .swiper');
            if (!testimonialsSlider) {
                console.log('Testimonials slider not found');
                return;
            }

            const slides = testimonialsSlider.querySelectorAll('.swiper-slide');
            const slideCount = slides.length;

            // console.log('Testimonials slider found with', slideCount, 'slides');

            if (slideCount === 0) {
                return;
            }

            // Calculate slides per view based on screen size
            const getSlidesPerView = function() {
                const width = window.innerWidth;
                if (width >= 1024) {
                    // Desktop: show 3 slides
                    return 3;
                } else if (width >= 768) {
                    // Tablet: show 2 slides
                    return 2;
                }
                // Mobile: show 1 slide
                return 1;
            };

            // Calculate space between based on screen size
            const getSpaceBetween = function() {
                const width = window.innerWidth;
                if (width >= 1024) {
                    return 30;
                } else if (width >= 768) {
                    return 25;
                }
                return 20;
            };

            const config = {
                slidesPerView: getSlidesPerView(),
                spaceBetween: getSpaceBetween(),
                speed: 500,
                grabCursor: true,
                autoplay: {
                    delay: 4000,
                    disableOnInteraction: false,
                    pauseOnMouseEnter: true,
                },
                loop: true,
                loopFillGroupWithBlank: true,
                pagination: {
                    el: '.testimonials-slider .swiper-pagination',
                    clickable: true,
                },
                on: {
                    init: function () {
                        // console.log('✓ Testimonials slider initialized');
                        // console.log('  - Slides:', slideCount);
                        // console.log('  - Per view:', this.params.slidesPerView);
                        // console.log('  - Space between:', this.params.spaceBetween);
                        // console.log('  - Loop enabled:', this.params.loop);
                        // console.log('  - Autoplay enabled:', !!this.params.autoplay);
                    },
                    slideChange: function () {
                        // console.log('Slide:', this.activeIndex, '/', this.slides.length);
                    }
                }
            };

            const swiper = new Swiper('.testimonials-slider .swiper', config);

            // Handle resize
            let resizeTimer;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    const newSlidesPerView = getSlidesPerView();
                    const newSpaceBetween = getSpaceBetween();
                    swiper.params.slidesPerView = newSlidesPerView;
                    swiper.params.spaceBetween = newSpaceBetween;
                    swiper.update();
                    console.log('Updated - slidesPerView:', newSlidesPerView, 'spaceBetween:', newSpaceBetween);
                }, 250);
            });

        }, 500);
    }

    // ===============================================
    // FAQ Accordion
    // ===============================================
    function initFAQ() {
        $(".faq-question").on("click", function () {
            const faqItem = $(this).closest(".faq-item");
            const isActive = faqItem.hasClass("active");

            // Close all FAQ items
            $(".faq-item").removeClass("active");
            $(".faq-answer").css("max-height", 0);

            // Open clicked item if it wasn't active
            if (!isActive) {
                faqItem.addClass("active");
                const answer = faqItem.find(".faq-answer");
                answer.css("max-height", answer[0].scrollHeight + "px");
            }
        });
    }

    // ===============================================
    // Pricing Tabs
    // ===============================================
     function initPricingTabs() {
        // Tab click handler
        $(".pricing-tab").on("click", function () {
            const type = $(this).data("type");
            // console.log("Tab clicked:", type);

            // Update active tab
            $(".pricing-tab").removeClass("active");
            $(this).addClass("active");

            filterPricingCards(type);
        });

        // Filter pricing cards by type
        function filterPricingCards(type) {
            const allCards = $(".pricing-card");
            // console.log("Total cards found:", allCards.length);

            // Show/hide pricing cards
            if (type === "all") {
                allCards.fadeIn(300);
            } else {
                // Hide all first
                allCards.hide();

                // Show matching cards
                const matchingCards = $('.pricing-card[data-type="' + type + '"]');
                // console.log("Matching cards for type '" + type + "':", matchingCards.length);

                // Debug: show all data-type values
                // allCards.each(function() {
                //     console.log("Card data-type:", $(this).data("type"));
                // });

                matchingCards.fadeIn(300);
            }
        }

        // Trigger filter on page load for active tab
        const activeTab = $(".pricing-tab.active").first();
        if (activeTab.length) {
            const initialType = activeTab.data("type");
            // console.log("Initial filter on page load:", initialType);
            filterPricingCards(initialType);
        }
    }
    // function initPricingTabs() {
    //     $(".pricing-tab").on("click", function () {
    //         const type = $(this).data("type");

    //         // Update active tab
    //         $(".pricing-tab").removeClass("active");
    //         $(this).addClass("active");

    //         // Show/hide pricing cards
    //         if (type === "all") {
    //             $(".pricing-card").fadeIn(300);
    //         } else {
    //             $(".pricing-card").hide();
    //             $('.pricing-card[data-type="' + type + '"]').fadeIn(300);
    //         }
    //     });
    // }

    // ===============================================
    // Scroll Animations
    // ===============================================
    function initScrollAnimations() {
        const animateElements = $(
            ".fade-in, .slide-in-left, .slide-in-right, .zoom-in",
        );

        function checkVisibility() {
            const windowHeight = $(window).height();

            animateElements.each(function () {
                const elementTop = $(this).offset().top;

                if (elementTop < windowHeight + $(window).scrollTop() - 50) {
                    $(this).addClass("visible");
                }
            });
        }

        $(window).on("scroll", checkVisibility);
        checkVisibility(); // Check on page load
    }

    // ===============================================
    // Contact Form Handler
    // ===============================================
    function initContactForm() {
        $("#contactForm").on("submit", function (e) {
            e.preventDefault();

            const form = $(this);
            const formData = new FormData(this);
            const submitBtn = form.find('button[type="submit"]');
            const originalText = submitBtn.html();

            // Remove any existing alerts
            form.find(".alert").remove();

            // Show loading state
            submitBtn
                .html('<i class="fas fa-spinner fa-spin"></i> Sending...')
                .prop("disabled", true);

            // Convert FormData to URL-encoded string
            const formDataObj = {};
            formData.forEach((value, key) => {
                formDataObj[key] = value;
            });
            formDataObj._token = $('meta[name="csrf-token"]').attr("content");

            // Send AJAX request
            $.ajax({
                url: form.attr("action") || "/contact",
                type: "POST",
                data: formDataObj,
                dataType: "json",
                success: function (response) {
                    // Show success message
                    form.prepend(
                        '<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                            '<i class="fas fa-check-circle me-2"></i>' +
                            "<strong>Success!</strong> " +
                            (response.message ||
                                "Thank you for your message! We will get back to you soon.") +
                            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                            "</div>",
                    );

                    // Reset form
                    form[0].reset();

                    // Reset button
                    submitBtn.html(originalText).prop("disabled", false);

                    // Auto-hide success message
                    setTimeout(function () {
                        form.find(".alert-success").fadeOut(500, function () {
                            $(this).remove();
                        });
                    }, 5000);
                },
                error: function (xhr) {
                    let errorMsg = "An error occurred. Please try again.";
                    let errors = [];

                    if (xhr.status === 422) {
                        // Validation errors
                        const response = xhr.responseJSON;
                        if (response.errors) {
                            $.each(response.errors, function (key, messages) {
                                errors.push(messages.join(" "));
                            });
                            errorMsg = errors.join("<br>");
                        }
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }

                    // Show error message
                    form.prepend(
                        '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                            '<i class="fas fa-exclamation-circle me-2"></i>' +
                            "<strong>Error!</strong> " +
                            errorMsg +
                            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                            "</div>",
                    );

                    // Reset button
                    submitBtn.html(originalText).prop("disabled", false);

                    // Auto-hide error message
                    setTimeout(function () {
                        form.find(".alert-danger").fadeOut(500, function () {
                            $(this).remove();
                        });
                    }, 8000);
                },
            });
        });
    }

    // ===============================================
    // Counter Animation
    // ===============================================
    function initCounterAnimation() {
        const counters = $(".stat-box h4, #stats.stats-section .stat-item h4");

        function animateCounter(element) {
            const target = parseInt(element.data("target"));
            const duration = 2000;
            const step = target / (duration / 16);
            let current = 0;

            const timer = setInterval(function () {
                current += step;
                if (current >= target) {
                    element.text(target + (element.data("suffix") || ""));
                    clearInterval(timer);
                } else {
                    element.text(
                        Math.floor(current) + (element.data("suffix") || ""),
                    );
                }
            }, 16);
        }

        // Start animation when element is visible
        $(window).on("scroll", function () {
            counters.each(function () {
                const element = $(this);
                const elementTop = element.offset().top;

                if (
                    elementTop <
                    $(window).height() + $(window).scrollTop() - 100
                ) {
                    if (!element.hasClass("counted")) {
                        animateCounter(element);
                        element.addClass("counted");
                    }
                }
            });
        });
    }

    // ===============================================
    // Dynamic Year in Footer
    // ===============================================
    $("#currentYear").text(new Date().getFullYear());

    // ===============================================
    // Typing Effect for Hero
    // ===============================================
    function typeWriter(element, text, speed = 100) {
        let i = 0;
        element.html("");

        function type() {
            if (i < text.length) {
                element.html(element.html() + text.charAt(i));
                i++;
                setTimeout(type, speed);
            }
        }

        type();
    }

    // ===============================================
    // Parallax Effect for Hero
    // ===============================================
    $(window).on("scroll", function () {
        const scrolled = $(this).scrollTop();
        const hero = $("#hero");

        if (hero.length) {
            hero.find(".hero-mockup").css({
                transform: "translateY(" + scrolled * 0.1 + "px)",
            });
        }
    });

    // ===============================================
    // Service Card Hover Effect
    // ===============================================
    $(".service-card")
        .on("mouseenter", function () {
            $(this).find(".service-icon").addClass("fa-spin");
        })
        .on("mouseleave", function () {
            $(this).find(".service-icon").removeClass("fa-spin");
        });

    // ===============================================
    // Pricing Card Toggle
    // ===============================================
    $(".pricing-toggle").on("click", function () {
        const monthly = $(".monthly-price");
        const yearly = $(".yearly-price");

        if ($(this).hasClass("active")) {
            monthly.show();
            yearly.hide();
            $(this).removeClass("active").text("Switch to Yearly");
        } else {
            monthly.hide();
            yearly.show();
            $(this).addClass("active").text("Switch to Monthly");
        }
    });

    // ===============================================
    // WhatsApp Float Button
    // ===============================================
    const whatsappBtn = `
        <a href="https://wa.me/+8809611778371" target="_blank"
           class="whatsapp-float"
           style="position: fixed;
                  bottom: 90px;
                  right: 30px;
                  width: 55px;
                  height: 55px;
                  background: #25D366;
                  color: white;
                  border-radius: 50%;
                  display: flex;
                  align-items: center;
                  justify-content: center;
                  font-size: 28px;
                  z-index: 998;
                  box-shadow: 0 4px 12px rgba(37, 211, 102, 0.4);
                  transition: all 0.3s ease;">
            <i class="fab fa-whatsapp"></i>
        </a>
    `;

    $("body").append(whatsappBtn);

    $(".whatsapp-float")
        .on("mouseenter", function () {
            $(this).css("transform", "scale(1.1)");
        })
        .on("mouseleave", function () {
            $(this).css("transform", "scale(1)");
        });

    // ===============================================
    // Image Lazy Loading
    // ===============================================
    const images = $("img[data-src]");

    function loadImage(img) {
        const src = img.data("src");
        img.attr("src", src)
            .on("load", function () {
                img.addClass("loaded");
            })
            .removeAttr("data-src");
    }

    if ("IntersectionObserver" in window) {
        const imageObserver = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    loadImage($(entry.target));
                    imageObserver.unobserve(entry.target);
                }
            });
        });

        images.each(function () {
            imageObserver.observe(this);
        });
    } else {
        images.each(function () {
            loadImage($(this));
        });
    }

    // ===============================================
    // Service Worker Registration (for PWA)
    // ===============================================
    if ("serviceWorker" in navigator) {
        window.addEventListener("load", function () {
            // navigator.serviceWorker.register('/sw.js')
            //     .then(function(registration) {
            //         console.log('Service Worker registered:', registration);
            //     })
            //     .catch(function(error) {
            //         console.log('Service Worker registration failed:', error);
            //     });
        });
    }

    // ===============================================
    // Console Branding
    // ===============================================
    // console.log('%c MyBDSMS ', 'background: linear-gradient(135deg, #0066cc 0%, #00c4cc 100%); color: white; font-size: 24px; font-weight: bold; padding: 10px 20px; border-radius: 5px;');
    // console.log('%c Premium SMS Service Provider in Bangladesh ', 'color: #0066cc; font-size: 14px;');
    // console.log('%c Developed with ❤️ by Freelancer Digital Expert ', 'color: #6c757d; font-size: 12px;');
})(jQuery);

// ===============================================
// Window Load Events
// ===============================================
$(window).on("load", function () {
    // Remove preloader if still present
    $("#preloader").fadeOut(500, function () {
        $(this).remove();
    });
});

// ===============================================
// Window Resize Events
// ===============================================
$(window)
    .on("resize", function () {
        // Recalculate any size-dependent elements
        const windowHeight = $(window).height();

        // Adjust hero section height on mobile
        if ($(window).width() < 768) {
            $("#hero").css("min-height", windowHeight - 80);
        } else {
            $("#hero").css("min-height", "auto");
        }
    })
    .trigger("resize");
