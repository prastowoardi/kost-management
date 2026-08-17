import 'spotlight.js/dist/spotlight.bundle.js';

const navbar = document.getElementById('navbar');
const links = document.querySelectorAll('#navbar a[href^="#"]');
const sections = document.querySelectorAll('main section[id]');

function handleNavbarScroll() {
    navbar.classList.toggle('is-scrolled', window.scrollY > 40);
}

function highlightActiveLink() {
    const pos = window.scrollY + 140;
    let current = '';
    sections.forEach(section => {
        if (pos >= section.getBoundingClientRect().top + window.scrollY) {
            current = section.id;
        }
    });
    links.forEach(link => link.classList.toggle('active', link.getAttribute('href') === '#' + current));
}

function onScroll() {
    handleNavbarScroll();
    highlightActiveLink();
}

if (navbar) {
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
}