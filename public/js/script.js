const menuIcon = document.querySelector
('hamburger-menu');
const navbar = document.querySelector('#navbar-menu');

menuIcon.addEventListener('click', () => {
    navbar.classList.toggle("change");
});