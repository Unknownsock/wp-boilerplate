import { gsap } from 'gsap';
import ScrollTrigger from 'gsap/ScrollTrigger';
import lottie from 'lottie-web';

gsap.registerPlugin(ScrollTrigger);

const defaultConfig = {
    duration: 750,
    delay: 300,
    start: 'top 90%',
    end: 'bottom top',
    disableOnMobile: false,
    playOnce: true,
    mobileBreakpoint: 768,
    detectMethod: 'auto', // 'auto' | 'screensize' | 'touch' | 'useragent'
    animateOnLoadOnly: true,
};

const animationLibrary = {
    fadeIn: { from: { opacity: 0 }, to: { opacity: 1 } },
    fadeOut: { from: { opacity: 1 }, to: { opacity: 0 } },
    zoomIn: { from: { scale: 0.75, opacity: 0 }, to: { scale: 1, opacity: 1 } },
    zoomOut: { from: { scale: 1, opacity: 1 }, to: { scale: 0.5, opacity: 0 } },
    slideIn: { from: { x: '100%', opacity: 0 }, to: { x: '0%', opacity: 1 } },
    slideOut: { from: { x: '0%', opacity: 1 }, to: { x: '-100%', opacity: 0 } },
    slideUp: { from: { y: '8rem', opacity: 0 }, to: { y: '0rem', opacity: 1 } },
    slideUpNoOpacity: { from: { y: '8rem' }, to: { y: '0rem' } },
};

function msToSeconds(...values) {
    return values.map((value) => value * 0.001);
}

function isMobile() {
    const { mobileBreakpoint: breakpoint, detectMethod: method } = defaultConfig;

    const hasTouchScreen = 'ontouchstart' in window || navigator.maxTouchPoints > 0;
    const isSmallScreen = window.innerWidth <= breakpoint;
    const prefersTouchInterface = window.matchMedia('(pointer: coarse)').matches;
    const userAgent = navigator.userAgent.toLowerCase();
    const isMobileUserAgent = ['mobile', 'android', 'iphone', 'ipad', 'ipod', 'blackberry', 'windows phone'].some(
        (keyword) => userAgent.includes(keyword)
    );

    switch (method) {
        case 'screensize':
            return isSmallScreen;
        case 'touch':
            return hasTouchScreen || prefersTouchInterface;
        case 'useragent':
            return isMobileUserAgent;
        default:
            return (hasTouchScreen && isSmallScreen) || prefersTouchInterface || isMobileUserAgent;
    }
}

function isInViewport(element) {
    const windowHeight = window.innerHeight || document.documentElement.clientHeight;
    return element.getBoundingClientRect().top <= windowHeight * 0.9;
}

function applyAnimations() {
    document.querySelectorAll('[data-scroll-animation]').forEach((el) => {
        const animationType = el.getAttribute('data-scroll-animation');
        const animationConfig = animationLibrary[animationType];
        if (!animationConfig) return;

        const start = el.getAttribute('data-scroll-start') || defaultConfig.start;
        const durationMs = parseInt(el.getAttribute('data-scroll-duration'), 10) || defaultConfig.duration;
        const delayMs = parseInt(el.getAttribute('data-scroll-delay'), 10) || defaultConfig.delay;
        const disableOnMobile = el.getAttribute('data-disable-mobile') === 'true' || defaultConfig.disableOnMobile;
        const playOnce = el.getAttribute('data-play-once') === 'true' || defaultConfig.playOnce;
        const animateOnLoadOnly =
            el.getAttribute('data-animate-on-load-only') === 'true' || defaultConfig.animateOnLoadOnly;

        if (disableOnMobile && isMobile()) {
            gsap.set(el, animationConfig.to);
            return;
        }

        const [duration, delay] = msToSeconds(durationMs, delayMs);
        const tween = { ...animationConfig.to, duration, delay, ease: 'power2.out' };

        if (animateOnLoadOnly || isInViewport(el)) {
            gsap.fromTo(el, animationConfig.from, tween);
            return;
        }

        gsap.set(el, animationConfig.from);
        gsap.to(el, {
            ...tween,
            scrollTrigger: {
                trigger: el,
                start,
                end: defaultConfig.end,
                toggleActions: playOnce ? 'play none none none' : 'play none none reverse',
            },
        });
    });
}
applyAnimations();

// Lottie animations - <div data-lottie="/path/to/file.json" data-lottie-loop="false">
function initLottieAnimations() {
    document.querySelectorAll('[data-lottie]').forEach((el) => {
        lottie.loadAnimation({
            container: el,
            renderer: 'svg',
            loop: el.getAttribute('data-lottie-loop') !== 'false',
            autoplay: el.getAttribute('data-lottie-autoplay') !== 'false',
            path: el.getAttribute('data-lottie'),
        });
    });
}
initLottieAnimations();
