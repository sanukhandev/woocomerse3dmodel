// public-script.js

document.addEventListener("DOMContentLoaded", function() {
    // Open the 3D viewer modal
    var buttons = document.querySelectorAll(".db-3d-modal-btn");
    for (let i = 0; i < buttons.length; i++) {
        buttons[i].addEventListener("click", function(e) {
            e.preventDefault();

            var modalId = this.getAttribute("data-modal");
            var modal = document.getElementById(modalId);

            if (modal) {
                modal.style.display = "block";
            }
        });
    }

    // Close the 3D viewer modal
    var closeButtons = document.querySelectorAll(".db-3d-modal-close");
    for (let i = 0; i < closeButtons.length; i++) {
        closeButtons[i].addEventListener("click", function() {
            var modal = this.closest(".db-3d-modal");
            if (modal) {
                modal.style.display = "none";
            }
        });
    }

    // Close the modal if the overlay is clicked
    var modals = document.querySelectorAll(".db-3d-modal");
    for (let i = 0; i < modals.length; i++) {
        modals[i].addEventListener("click", function(e) {
            if (e.target === this) {
                this.style.display = "none";
            }
        });
    }
});

