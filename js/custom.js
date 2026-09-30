/**
 * The Vastra Mahal (द वस्त्र महल) - Custom JavaScript
 * Handles Carousel auto-play, interaction, navigation & UI enhancements
 */

(function () {
    "use strict";

    document.addEventListener("DOMContentLoaded", function () {
        // ----------------------------------------------------
        // 1. Hero Carousel Initialization & Robust Control
        // ----------------------------------------------------
        const heroCarouselEl = document.getElementById("vastraHeroCarousel");
        
        if (heroCarouselEl && typeof bootstrap !== "undefined" && bootstrap.Carousel) {
            // Get or create Bootstrap carousel instance
            const heroCarousel = bootstrap.Carousel.getOrCreateInstance(heroCarouselEl, {
                interval: 4500,
                ride: "carousel",
                pause: "hover",
                wrap: true,
                touch: true,
                keyboard: true
            });

            // Start cycling immediately
            heroCarousel.cycle();

            // Prev control click handler
            const prevBtn = heroCarouselEl.querySelector(".carousel-control-prev");
            if (prevBtn) {
                prevBtn.addEventListener("click", function (e) {
                    e.preventDefault();
                    heroCarousel.prev();
                });
            }

            // Next control click handler
            const nextBtn = heroCarouselEl.querySelector(".carousel-control-next");
            if (nextBtn) {
                nextBtn.addEventListener("click", function (e) {
                    e.preventDefault();
                    heroCarousel.next();
                });
            }

            // Indicator buttons click handlers
            const indicatorButtons = heroCarouselEl.querySelectorAll(".carousel-indicators button");
            indicatorButtons.forEach(function (btn) {
                btn.addEventListener("click", function (e) {
                    e.preventDefault();
                    const slideIndex = parseInt(btn.getAttribute("data-bs-slide-to"), 10);
                    if (!isNaN(slideIndex)) {
                        heroCarousel.to(slideIndex);
                    }
                });
            });

            // Resume cycle when mouse leaves the carousel
            heroCarouselEl.addEventListener("mouseleave", function () {
                heroCarousel.cycle();
            });
        }

        // ----------------------------------------------------
        // 2. Initialize Bootstrap Tooltips / Popovers if present
        // ----------------------------------------------------
        if (typeof bootstrap !== "undefined" && bootstrap.Tooltip) {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }

        // ----------------------------------------------------
        // 3. Smooth scroll for anchor links
        // ----------------------------------------------------
        document.querySelectorAll('a[href^="#"]:not([href="#"]):not([data-bs-toggle])').forEach(function (anchor) {
            anchor.addEventListener("click", function (e) {
                const target = document.querySelector(this.getAttribute("href"));
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({
                        behavior: "smooth"
                    });
                }
            });
        });
    });
})();
