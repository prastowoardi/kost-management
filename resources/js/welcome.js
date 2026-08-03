import 'spotlight.js/dist/spotlight.bundle.js';

const navbar = document.getElementById('navbar');
const bg = document.getElementById('navbar-bg');
const logo = document.getElementById('logo-text');
const menu = document.getElementById('menu-text');

function handleNavbarScroll() {
    if (window.scrollY > 50) {
        navbar.classList.add('pt-6');
        bg.classList.add('bg-white/80', 'backdrop-blur-xl', 'shadow-lg', 'border', 'border-white/20');
        bg.classList.remove('max-w-6xl');
        bg.classList.add('max-w-4xl');

        logo.classList.replace('text-white', 'text-slate-900');
        menu.classList.replace('text-white', 'text-slate-900');
    } else {
        navbar.classList.remove('pt-6');
        bg.classList.remove('bg-white/80', 'backdrop-blur-xl', 'shadow-lg', 'border', 'border-white/20', 'max-w-4xl');
        bg.classList.add('max-w-6xl');

        logo.classList.replace('text-slate-900', 'text-white');
        menu.classList.replace('text-slate-900', 'text-white');
    }
}

if (navbar && bg && logo && menu) {
    window.addEventListener('scroll', handleNavbarScroll);
    handleNavbarScroll();
}
