(function () {
    "use strict";

    var reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

    // Dark/light theme toggle. The inline <head> script (layout_header.php)
    // already applied any stored explicit choice before first paint, to
    // avoid a flash of the wrong theme — this only handles the click and
    // keeps the button's aria state in sync, including when no explicit
    // choice has ever been stored (falls back to the OS preference).
    var themeToggle = document.getElementById("theme-toggle");
    if (themeToggle) {
        var prefersDark = window.matchMedia("(prefers-color-scheme: dark)");
        function effectiveTheme() {
            var explicit = document.documentElement.getAttribute("data-theme");
            if (explicit === "dark" || explicit === "light") return explicit;
            return prefersDark.matches ? "dark" : "light";
        }
        function syncToggleUi() {
            var isDark = effectiveTheme() === "dark";
            themeToggle.setAttribute("aria-pressed", isDark ? "true" : "false");
            themeToggle.setAttribute("aria-label", isDark ? "Switch to light theme" : "Switch to dark theme");
        }
        themeToggle.addEventListener("click", function () {
            var next = effectiveTheme() === "dark" ? "light" : "dark";
            document.documentElement.setAttribute("data-theme", next);
            try {
                localStorage.setItem("gu-theme", next);
            } catch (e) {}
            syncToggleUi();
        });
        // If the OS-level preference changes while no explicit choice is
        // stored, keep the button's label/state honest rather than stale.
        prefersDark.addEventListener("change", function () {
            if (!document.documentElement.getAttribute("data-theme")) syncToggleUi();
        });
        syncToggleUi();
    }

    // Skip-link: handled in JS, not via plain browser navigation of its
    // href="#main-content" — with <base href> set (layout_header.php),
    // a bare-fragment href resolves against the base URL's path, not the
    // current page, which would send "Skip to content" to the homepage
    // from every other page. preventDefault() sidesteps that entirely by
    // never letting the browser resolve the href at all.
    var skipLink = document.querySelector(".skip-link");
    if (skipLink) {
        skipLink.addEventListener("click", function (e) {
            var target = document.getElementById("main-content");
            if (target) {
                e.preventDefault();
                target.focus();
                target.scrollIntoView();
            }
        });
    }

    // Mobile nav toggle — accessible (aria-expanded), no dependency.
    var toggle = document.querySelector(".nav-toggle");
    var nav = document.querySelector(".primary-nav");
    if (toggle && nav) {
        toggle.addEventListener("click", function () {
            var isOpen = nav.classList.toggle("is-open");
            toggle.setAttribute("aria-expanded", isOpen ? "true" : "false");
        });
        nav.querySelectorAll("a").forEach(function (link) {
            link.addEventListener("click", function () {
                nav.classList.remove("is-open");
                toggle.setAttribute("aria-expanded", "false");
            });
        });
    }

    // Mega-menu dropdowns. Desktop (>860px): pure CSS :hover/:focus-within
    // reveals the panel (see style.css) — the parent tab is a real link, so
    // a click just navigates normally, exactly like a tab with no sub-tabs.
    // Mobile (<=860px, no hover to rely on): tapping the parent tab must
    // expand the accordion instead of navigating away immediately, so JS
    // intercepts the click there with preventDefault(). Checked live via
    // matchMedia on every click, not cached, so a resize/orientation change
    // between clicks can't leave the wrong mode wired in.
    var dropdowns = document.querySelectorAll(".nav-item-dropdown");
    var isMobileNav = function () { return window.matchMedia("(max-width: 860px)").matches; };
    function closeAllDropdowns(except) {
        dropdowns.forEach(function (dd) {
            if (dd === except) return;
            dd.classList.remove("is-open");
            var btn = dd.querySelector(".nav-dropdown-toggle");
            if (btn) btn.setAttribute("aria-expanded", "false");
        });
    }
    dropdowns.forEach(function (dd) {
        var btn = dd.querySelector(".nav-dropdown-toggle");
        if (!btn) return;
        btn.addEventListener("click", function (e) {
            if (!isMobileNav()) {
                return; // desktop: let it navigate, hover already revealed the panel
            }
            e.preventDefault();
            e.stopPropagation();
            var willOpen = !dd.classList.contains("is-open");
            closeAllDropdowns(willOpen ? dd : null);
            dd.classList.toggle("is-open", willOpen);
            btn.setAttribute("aria-expanded", willOpen ? "true" : "false");
        });
    });
    document.addEventListener("click", function () { closeAllDropdowns(); });
    document.addEventListener("keydown", function (e) {
        if (e.key === "Escape") closeAllDropdowns();
    });

    // Sticky header shadow once the page has scrolled past the top.
    var header = document.querySelector(".site-header");
    var backToTop = document.querySelector(".back-to-top");
    if (header || backToTop) {
        var onScroll = function () {
            if (header) header.classList.toggle("is-scrolled", window.scrollY > 8);
            if (backToTop) backToTop.classList.toggle("is-visible", window.scrollY > 500);
        };
        window.addEventListener("scroll", onScroll, { passive: true });
        onScroll();
    }

    if (backToTop) {
        backToTop.addEventListener("click", function () {
            window.scrollTo({ top: 0, behavior: reduceMotion ? "auto" : "smooth" });
        });
    }

    // Homepage slider — alternating text/illustration slides (index.php
    // only; guarded by element existence so this is a no-op elsewhere).
    // Translate-based track, always-on autoplay (no dedicated pause/play
    // button by design) with a visible progress bar as the "this is
    // auto-advancing, here's when it'll change" indication, and a real
    // pause mechanism for anyone who needs one: hovering or keyboard-
    // focusing anywhere in the slider pauses it for as long as the
    // pointer/focus stays there, resuming the moment it leaves — reduced-
    // motion users get it pre-paused (manual prev/next/dot still work for
    // everyone). Manual navigation resets the timer rather than killing
    // autoplay permanently, since there's no button left to revive it.
    var slider = document.getElementById("home-slider");
    if (slider) {
        var track = slider.querySelector(".home-slider__track");
        var slides = Array.prototype.slice.call(slider.querySelectorAll(".slide"));
        var dotsWrap = slider.querySelector(".slider-dots");
        var prevBtn = slider.querySelector(".slider-prev");
        var nextBtn = slider.querySelector(".slider-next");
        var progressBar = slider.querySelector(".slider-progress__bar");
        var index = 0;
        var timer = null;
        var slideDuration = 6000;
        var isHeld = reduceMotion; // reduced-motion users get it pre-paused; manual nav still works

        slides.forEach(function (s, i) {
            var dot = document.createElement("button");
            dot.type = "button";
            dot.className = "slider-dot" + (i === 0 ? " is-active" : "");
            dot.setAttribute("aria-label", "Go to slide " + (i + 1) + " of " + slides.length);
            dot.addEventListener("click", function () {
                goTo(i);
                restart();
            });
            dotsWrap.appendChild(dot);
        });
        var dots = Array.prototype.slice.call(dotsWrap.querySelectorAll(".slider-dot"));

        function render() {
            track.style.transform = "translateX(-" + index * 100 + "%)";
            slides.forEach(function (s, i) {
                s.setAttribute("aria-hidden", i === index ? "false" : "true");
            });
            dots.forEach(function (d, i) {
                d.classList.toggle("is-active", i === index);
            });
        }
        function goTo(i) {
            index = (i + slides.length) % slides.length;
            render();
        }
        function next() { goTo(index + 1); }
        function prev() { goTo(index - 1); }
        function runProgressBar() {
            if (!progressBar) return;
            progressBar.style.transition = "none";
            progressBar.style.width = "0%";
            if (isHeld) return;
            // Force layout so the width:0% above actually paints before the
            // transition to 100% starts — otherwise the browser can coalesce
            // both style writes into one frame and the bar never animates.
            void progressBar.offsetWidth;
            progressBar.style.transition = "width " + slideDuration + "ms linear";
            progressBar.style.width = "100%";
        }
        function start() {
            if (isHeld) return;
            if (timer) window.clearInterval(timer);
            timer = window.setInterval(next, slideDuration);
            runProgressBar();
        }
        function pause() {
            if (timer) {
                window.clearInterval(timer);
                timer = null;
            }
            if (progressBar) progressBar.style.transition = "none"; // freeze wherever it is, not reset to 0
        }
        function restart() {
            pause();
            if (!isHeld) start();
        }

        if (prevBtn) prevBtn.addEventListener("click", function () { prev(); restart(); });
        if (nextBtn) nextBtn.addEventListener("click", function () { next(); restart(); });
        slider.addEventListener("mouseenter", pause);
        slider.addEventListener("mouseleave", function () { isHeld = reduceMotion; start(); });
        slider.addEventListener("focusin", pause);
        slider.addEventListener("focusout", function () { isHeld = reduceMotion; start(); });
        slider.addEventListener("keydown", function (e) {
            if (e.key === "ArrowRight") { next(); restart(); }
            if (e.key === "ArrowLeft") { prev(); restart(); }
        });

        render();
        start();
    }

    // Scroll-reveal for .reveal elements. Reduced-motion users see content
    // immediately — the effect is decorative, never load-bearing for content
    // visibility (requirements.md Section 9 / CLAUDE.md Rule 6).
    var revealEls = document.querySelectorAll(".reveal");
    if (revealEls.length) {
        if (reduceMotion || !("IntersectionObserver" in window)) {
            revealEls.forEach(function (el) { el.classList.add("is-visible"); });
        } else {
            var observer = new IntersectionObserver(
                function (entries) {
                    entries.forEach(function (entry) {
                        if (entry.isIntersecting) {
                            entry.target.classList.add("is-visible");
                            observer.unobserve(entry.target);
                        }
                    });
                },
                { threshold: 0.12, rootMargin: "0px 0px -40px 0px" }
            );
            revealEls.forEach(function (el) { observer.observe(el); });
        }
    }
})();
