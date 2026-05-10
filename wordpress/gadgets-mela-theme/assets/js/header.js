(function () {
  const toggle = document.querySelector('.gm-mobile-toggle');
  const panel = document.querySelector('#gm-mobile-menu');

  if (!toggle || !panel) {
    return;
  }

  toggle.addEventListener('click', function () {
    const isOpen = toggle.getAttribute('aria-expanded') === 'true';
    toggle.setAttribute('aria-expanded', String(!isOpen));
    panel.classList.toggle('is-open', !isOpen);
  });

  panel.addEventListener('click', function (event) {
    if (!(event.target instanceof HTMLAnchorElement)) {
      return;
    }

    toggle.setAttribute('aria-expanded', 'false');
    panel.classList.remove('is-open');
  });
})();
