document.addEventListener("DOMContentLoaded", () => {
  const filtersBtn = document.getElementById("openFiltersBtn");
  const orderBtn = document.getElementById("openOrderBtn");
  const filtersModal = document.getElementById("filtersModal");
  const orderModal = document.getElementById("orderModal");
  const closeButtons = document.querySelectorAll(".close-modal");
  const overlay = document.getElementById("modalOverlayInfo");

  const openModal = (modal) => {
    modal.classList.add("show");
    overlay.classList.add("show");
  };

  const closeModal = () => {
    filtersModal?.classList.remove("show");
    orderModal?.classList.remove("show");
    overlay.classList.remove("show");
  };

  const closeFiltersModal = () => {
    const filtersModal = document.getElementById("filtersModal");
    const overlay = document.getElementById("modalOverlayInfo");
    const orderModal = document.getElementById("orderModal");
    
    filtersModal?.classList.remove("show");
    orderModal?.classList.remove("show");
    overlay?.classList.remove("show");
  };

  filtersBtn?.addEventListener("click", () => openModal(filtersModal));
  orderBtn?.addEventListener("click", () => openModal(orderModal));

  closeButtons.forEach((btn) => btn.addEventListener("click", closeModal));
  overlay?.addEventListener("click", closeModal);

  [filtersModal, orderModal].forEach((modal) => {
    modal?.addEventListener("click", (e) => {
      if (e.target === modal) closeModal();
    });
  });

  window.closeFiltersModal = closeFiltersModal;
});
