function ajustarSidebar() {
    const header = document.querySelector('header');
    const footer = document.querySelector('footer');
    const sidebar = document.querySelector('.side-navbar');
    if (!header || !sidebar) return;
    const altoHeader = header.getBoundingClientRect().height;
    const scrolled = window.scrollY;
    const nuevoTop = Math.max(altoHeader - scrolled, 0);
    sidebar.style.top = nuevoTop + 'px';

    let bottomOffset = 0;
    if (footer) {
      const rectFooter = footer.getBoundingClientRect();
      if (rectFooter.top < window.innerHeight) {
        bottomOffset = window.innerHeight - rectFooter.top;
      }
    }
    sidebar.style.bottom = bottomOffset + 'px';
  }

  window.addEventListener('DOMContentLoaded', ajustarSidebar);
  window.addEventListener('resize', ajustarSidebar);
  window.addEventListener('scroll', ajustarSidebar);