(function () {
  const root = document.documentElement;
  const stored = localStorage.getItem('nx-theme');
  if (stored) root.setAttribute('data-theme', stored);

  const themeBtn = document.getElementById('nxThemeToggle');
  if (themeBtn) {
    themeBtn.addEventListener('click', function () {
      const current = root.getAttribute('data-theme') === 'dark' ? 'dark' : 'light';
      const next = current === 'dark' ? 'light' : 'dark';
      root.setAttribute('data-theme', next);
      localStorage.setItem('nx-theme', next);
      themeBtn.querySelector('i').className = next === 'dark' ? 'bi bi-sun' : 'bi bi-moon-stars';
    });
    themeBtn.querySelector('i').className =
      root.getAttribute('data-theme') === 'dark' ? 'bi bi-sun' : 'bi bi-moon-stars';
  }

  const sidebarToggle = document.getElementById('nxSidebarToggle');
  const sidebar = document.querySelector('.nx-sidebar');
  const backdrop = document.querySelector('.nx-sidebar-backdrop');
  if (sidebarToggle && sidebar && backdrop) {
    sidebarToggle.addEventListener('click', function () {
      sidebar.classList.toggle('show');
      backdrop.classList.toggle('show');
    });
    backdrop.addEventListener('click', function () {
      sidebar.classList.remove('show');
      backdrop.classList.remove('show');
    });
  }

  document.querySelectorAll('[data-confirm]').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      if (!window.confirm(form.getAttribute('data-confirm'))) {
        e.preventDefault();
      }
    });
  });
})();
