document.addEventListener('DOMContentLoaded', function () {
    const overlay = document.getElementById('success-modal-overlay');
    if (!overlay) return;
  
    const btnClose = document.getElementById('success-modal-close');
    const btnOk = document.getElementById('success-modal-ok');
  
    function hide() {
      overlay.classList.add('hidden');
      overlay.setAttribute('aria-hidden', 'true');
    }
    function show() {
      overlay.classList.remove('hidden');
      overlay.setAttribute('aria-hidden', 'false');
      btnOk && btnOk.focus();
    }
  
    // if (!overlay.classList.contains('hidden')) {
    //   show();
    //   setTimeout(hide, 4000);
    // }
  
    btnClose && btnClose.addEventListener('click', hide);
    btnOk && btnOk.addEventListener('click', hide);
    overlay.addEventListener('click', function (e) { if (e.target === overlay) hide(); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') hide(); });
  });