// Select elements
const mobilePanel = document.querySelector('.mobile-panel');
const toggle = document.querySelector('.tiny-toggle');
const menu = document.querySelector('#nav-bar');
const overlay = document.querySelector('.mobile-overlay');

// Function to control .mobile-panel visibility based on window width
function handleMobilePanelVisibility() {
    if (window.innerWidth <= 600) {
        mobilePanel.classList.add('active'); // Show at 600px or less
    } else {
        // Hide above 600px and remove .active classes to reset mobile menu state
        mobilePanel.classList.remove('active');
        toggle.classList.remove('active');
        overlay.classList.remove('active');
        menu.classList.remove('active');
        document.body.style.overflow = ''; // Re-enable scrolling
    }
}

// Initial setup
handleMobilePanelVisibility();

// Update on window resize
window.addEventListener('resize', handleMobilePanelVisibility);

// Hamburger menu and slide effect
toggle.addEventListener('click', () => {
    const isActive = menu.classList.toggle('active');
    toggle.classList.toggle('active');
    overlay.classList.toggle('active');

    // Accessibility: Focus management
    if (isActive) {
        menu.querySelector('a').focus();
        document.body.style.overflow = 'hidden'; // Disable scrolling
    } else {
        toggle.focus();
        document.body.style.overflow = ''; // Re-enable scrolling
    }
});

// Overlay closes the menu
overlay.addEventListener('click', () => {
    menu.classList.remove('active');
    toggle.classList.remove('active');
    overlay.classList.remove('active');
    document.body.style.overflow = '';
    toggle.focus();
});

// Expand/collapse submenus
const submenuItems = document.querySelectorAll('#nav-bar .menu-item-has-children');
submenuItems.forEach(item => {
    const link = item.querySelector('a');
    link.addEventListener('click', (e) => {
        e.preventDefault();
        const isActive = item.classList.toggle('active');
        submenuItems.forEach(otherItem => {
            if (otherItem !== item) {
                otherItem.classList.remove('active');
            }
        });
        if (isActive) {
            item.querySelector('ul a').focus();
        }
    });
});

// Close on click outside the menu
document.addEventListener('click', (e) => {
    if (!menu.contains(e.target) && !toggle.contains(e.target) && menu.classList.contains('active')) {
        menu.classList.remove('active');
        toggle.classList.remove('active');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
        toggle.focus();
    }
});

// Accessibility: Close menu with ESC key
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && menu.classList.contains('active')) {
        menu.classList.remove('active');
        toggle.classList.remove('active');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
        toggle.focus();
    }
});