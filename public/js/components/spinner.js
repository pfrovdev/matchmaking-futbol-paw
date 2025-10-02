document.querySelectorAll(".form-desafiar").forEach(form => {
    form.addEventListener("submit", function () {
        const button = this.querySelector("button");
        activarSpinner(button);
    });
});
document.querySelectorAll(".form-calificar").forEach(form => {
    form.addEventListener("submit", function () {
        const button = this.querySelector("button");
        activarSpinner(button);
    });
});

document.querySelectorAll(".form-team").forEach(form => {
    form.addEventListener("submit", function () {
        const button = this.querySelector("button");
        activarSpinner(button);
    });
});

document.querySelectorAll(".btn").forEach(form => {
    form.addEventListener("submit", function () {
        const button = this.querySelector("button");
        activarSpinner(button);
    });
});

document.querySelectorAll(".btn-desafiar").forEach(link => {
    link.addEventListener("click", function (e) {
        if (!e.ctrlKey && !e.metaKey && !e.shiftKey) {
            activarSpinner(this);
        }
    });
});

document.addEventListener("click", function (e) {
  const target = e.target.closest(".btn-cancelar");
  if (target && !e.ctrlKey && !e.metaKey && !e.shiftKey) {
    activarSpinner(target);
  }
});

function activarSpinner(elemento) {
    const spinner = elemento.querySelector(".spinner");
    const btnText = elemento.querySelector(".btn-text");

    spinner.style.display = "inline-block";
    btnText.style.display = "none";
}


