(function ($) {
    "use strict";

    var windowOn = $(window);

    // Preloader
    windowOn.on('load', function () {
        $("#loading").fadeOut(500);
    });

    // Smooth scroll wrapper (GSAP ScrollSmoother)
    gsap.registerPlugin(ScrollTrigger, ScrollSmoother, ScrollToPlugin, SplitText);
    if ($('#smooth-wrapper').length && $('#smooth-content').length) {
        ScrollSmoother.create({
            smooth: .5,
            effects: true,
            smoothTouch: .1,
            ignoreMobileResize: true
        });
    }

    var mm = gsap.matchMedia();

    // Mobile menu & offcanvas
    var TPMenu = {
        init: function () {
            this.mobileMenu();
            this.offcanvas();
        },
        mobileMenu: function () {
            var tpMenuWrap = $('.tp-mobile-menu-active > ul').clone();
            var tpSideMenu = $('.tp-offcanvas-menu nav');
            tpSideMenu.append(tpMenuWrap);
            if ($(tpSideMenu).find('.sub-menu, .mega-menu').length !== 0) {
                $(tpSideMenu).find('.sub-menu, .mega-menu').parent().append('<button class="tp-menu-close"><i class="fas fa-chevron-right"></i></button>');
            }
            var sideMenuList = $('.tp-offcanvas-menu nav > ul > li button.tp-menu-close, .tp-offcanvas-menu nav > ul li.has-dropdown > a');
            $(sideMenuList).on('click', function (e) {
                e.preventDefault();
                if (!$(this).parent().hasClass('active')) {
                    $(this).parent().addClass('active');
                    $(this).siblings('.sub-menu, .mega-menu').slideDown();
                } else {
                    $(this).siblings('.sub-menu, .mega-menu').slideUp();
                    $(this).parent().removeClass('active');
                }
            });
        },
        offcanvas: function () {
            $(".tp-offcanvas-toogle").on('click', function () {
                $(".tp-offcanvas").addClass("tp-offcanvas-open");
                $(".tp-offcanvas-overlay").addClass("tp-offcanvas-overlay-open");
            });
            $(".tp-offcanvas-close-toggle, .tp-offcanvas-overlay").on('click', function () {
                $(".tp-offcanvas").removeClass("tp-offcanvas-open");
                $(".tp-offcanvas-overlay").removeClass("tp-offcanvas-overlay-open");
            });
        }
    };

    $(document).ready(function () {
        TPMenu.init();
    });

    // data-background image binding
    $("[data-background]").each(function () {
        $(this).css("background-image", "url(" + $(this).attr("data-background") + ")");
    });

    // data-width binding
    $("[data-width]").each(function () {
        $(this).css("width", $(this).attr("data-width"));
    });

    // Popup image (offcanvas gallery)
    $('.popup-image').magnificPopup({
        type: 'image',
        gallery: { enabled: true }
    });

    // Popup video (click-to-play lightbox)
    $('.popup-video').magnificPopup({
        type: 'iframe',
        mainClass: 'mfp-fade',
        removalDelay: 160,
        preloader: false,
        fixedContentPos: true
    });

    // Nice Select
    $(document).ready(function () {
        $('select').niceSelect();
    });

    // Jarallax parallax
    jarallax(document.querySelectorAll('.jarallax'), {
        speed: 0.2
    });

    // Back to top button
    var backToTopBtn     = $('#back_to_top');
    var backToTopWrapper = $('.back-to-top-wrapper');
    windowOn.on('scroll', function () {
        windowOn.scrollTop() > 300
            ? backToTopWrapper.addClass('back-to-top-btn-show')
            : backToTopWrapper.removeClass('back-to-top-btn-show');
    });
    $(backToTopBtn).on('click', function (e) {
        e.preventDefault();
        $('html, body').animate({ scrollTop: 0 }, '300');
    });

    // Sticky header
    windowOn.on('scroll', function () {
        windowOn.scrollTop() < 20
            ? $('#header-sticky').removeClass('header-sticky')
            : $('#header-sticky').addClass('header-sticky');
    });

    // Odometer counter
    windowOn.on('load', function () {
        $('.odometer').each(function () {
            var $this = $(this);
            $this.waypoint(function (direction) {
                if (direction === 'down' && !$this.hasClass('loaded')) {
                    $this.html($this.data('count'));
                    $this.addClass('loaded');
                }
            }, { offset: '90%' });
        });
    });

    // Brand / clients logo slider
    new Swiper(".tp-brand-active", {
        slidesPerView: 6,
        spaceBetween: 0,
        centeredSlides: true,
        allowTouchMove: false,
        loop: true,
        speed: 4000,
        autoplay: {
            delay: 0,
            disableOnInteraction: true,
        },
        breakpoints: {
            0:    { slidesPerView: 2 },
            568:  { slidesPerView: 3 },
            768:  { slidesPerView: 3 },
            992:  { slidesPerView: 4 },
            1200: { slidesPerView: 6 },
        },
    });

    // Hero slider
    new Swiper(".tp-hero-slider-active", {
        slidesPerView: 1,
        effect: 'fade',
        pagination: {
            el: ".tp-hero-slider-pagination",
            clickable: true,
        },
        loop: true,
        autoplay: { delay: 3000 },
        speed: 1000
    });

    // Services accordion button
    $(".tp-services-button").on("click", function () {
        var target = $(this).closest(".tp-services-item").next();
        $(".tp-services-item").next().not(target).removeClass("active");
        target.addClass("active");
    });

    // Fade-in animations (tp_fade_anim)
    if ($(".tp_fade_anim").length > 0) {
        gsap.utils.toArray(".tp_fade_anim").forEach(function (item) {
            var tp_fade_offset    = item.getAttribute("data-fade-offset")  || 40,
                tp_duration_value = item.getAttribute("data-duration")     || 0.75,
                tp_fade_direction = item.getAttribute("data-fade-from")    || "bottom",
                tp_onscroll_value = item.getAttribute("data-on-scroll")    || 1,
                tp_delay_value    = item.getAttribute("data-delay")        || 0.15,
                tp_ease_value     = item.getAttribute("data-ease")         || "power2.out",
                tp_anim_setting   = {
                    opacity:  0,
                    ease:     tp_ease_value,
                    duration: tp_duration_value,
                    delay:    tp_delay_value,
                    x: (tp_fade_direction === "left"  ? -tp_fade_offset : (tp_fade_direction === "right"  ? tp_fade_offset : 0)),
                    y: (tp_fade_direction === "top"   ? -tp_fade_offset : (tp_fade_direction === "bottom" ? tp_fade_offset : 0)),
                };
            if (tp_onscroll_value == 1) {
                tp_anim_setting.scrollTrigger = {
                    trigger: item,
                    start: 'top 85%',
                };
            }
            gsap.from(item, tp_anim_setting);
        });
    }

    // Reveal line text animation
    if (document.querySelector('.reval-line')) {
        document.querySelectorAll('.reval-line').forEach(function (el) {
            var split = new SplitText(el, {
                type: 'lines,words,chars',
                linesClass: 'split-line'
            });
            gsap.set(split.chars, { opacity: 0.3, x: -7 });
            gsap.to(split.chars, {
                scrollTrigger: {
                    trigger: el,
                    start: el.getAttribute('data-rt-start')  || 'top 80%',
                    end:   el.getAttribute('data-rt-end')    || 'top 20%',
                    toggleActions: 'play none none none',
                    scrub: 1,
                },
                x: 0,
                opacity: 1,
                stagger:  parseFloat(el.getAttribute('data-rt-stagger'))  || 0.2,
                duration: parseFloat(el.getAttribute('data-rt-duration')) || 0.7
            });
        });
    }

    // Zoom-in image animation
    $(".anim-zoomin").each(function () {
        var $this    = $(this);
        var $asiWrap = $this.parents(".anim-zoomin-wrap");
        gsap.timeline({
            scrollTrigger: {
                trigger: $asiWrap,
                start: "top 100%",
            }
        }).from($this, { duration: 2, autoAlpha: 0, scale: 1.2, ease: Power2.easeOut, clearProps: "all" });
    });

    // Portfolio "Selected work" text pin & scale
    gsap.matchMedia().add("(min-width: 993px)", function () {
        var portfolioArea = document.querySelector(".portfolio-area");
        var portfolioText = document.querySelector(".portfolio-text");
        if (portfolioArea && portfolioText) {
            var portfolioline = gsap.timeline({
                scrollTrigger: {
                    trigger: portfolioArea,
                    start: "top center-=300",
                    pin: portfolioText,
                    end: "bottom bottom",
                    pinSpacing: true,
                    anticipatePin: 1,
                    scrub: 3,
                }
            });
            portfolioline.to(portfolioText, { scale: 1,   duration: 1 });
            portfolioline.to(portfolioText, { scale: 1.1, duration: 1 });
            portfolioline.to(portfolioText, { scale: 1.2, duration: 1 }, "+=2");
            gsap.to(portfolioText, {
                scrollTrigger: {
                    trigger: portfolioArea,
                    start: "top center-=300",
                    end:   "bottom bottom",
                    scrub: 3
                },
                opacity: 0
            });
        }
    });

    // Word/char animate (.char-animate)
    gsap.utils.toArray(".char-animate-wrap").forEach(function (section) {
        var tl = gsap.timeline({
            scrollTrigger: {
                trigger: section,
                start: "top 60%",
                toggleActions: "play none none none"
            }
        });
        section.querySelectorAll(".char-animate").forEach(function (chunk) {
            var split = new SplitText(chunk, {
                type: "words",
                charsClass: "char",
                wordsClass: "word++"
            });
            tl.from(split.words, { autoAlpha: 0, duration: 1, stagger: 0.1 });
        });
    });

    new Swiper(".tp-about-slider",{
    loop:true,

    effect:"fade",

    fadeEffect:{
        crossFade:true,
    },

    speed:1800,

    autoplay:{
        delay:3000,
        disableOnInteraction:false,
        pauseOnMouseEnter:true,
    },

    pagination:{
        el:".tp-about-slider .swiper-pagination",
        clickable:true,
        dynamicBullets:true,
    }
});

document.querySelectorAll(".tpportfolio__year").forEach((item) => {

    const area = item.dataset.area;
    const year = item.dataset.year;

    let isArea = true;

    gsap.set(item, {
        transformOrigin: "50% 50%",
        rotationX: 0,
        opacity: 1
    });

    gsap.timeline({
        repeat: -1,
        repeatDelay: 2,
        defaults: {
            ease: "power4.inOut"
        }
    })

    .to(item,{
        duration:0.45,
        rotationX:-90,
        opacity:0
    })

    .call(()=>{
        item.textContent = isArea ? year : area;
        isArea = !isArea;
    })

    .set(item,{
        rotationX:90
    })

    .to(item,{
        duration:0.55,
        rotationX:0,
        opacity:1
    });

});
        var $grid = $('.tp-project-masonry-grid');
        $grid.imagesLoaded(function () {
            $grid.isotope({
                itemSelector: '.grid-item',
                percentPosition: true,
                columnWidth: '.grid-sizer'
            });
        });

})(jQuery);