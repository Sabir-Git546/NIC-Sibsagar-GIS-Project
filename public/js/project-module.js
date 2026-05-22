// =========================
// PREVENT DOUBLE SUBMIT
// =========================
document.addEventListener('DOMContentLoaded', function () {

    // =========================
    // PROJECT FORM
    // =========================
    const projectForm = document.getElementById('projectForm');

    if (projectForm) {

        projectForm.addEventListener('submit', function () {

            const btn = document.getElementById('submitBtn');

            if (btn) {

                btn.disabled = true;

                btn.innerHTML = 'Saving...';
            }
        });
    }

    // =========================
    // EDIT PROJECT FORM
    // =========================
    const editProjectForm = document.getElementById('editProjectForm');

    if (editProjectForm) {

        editProjectForm.addEventListener('submit', function () {

            const btn = document.getElementById('submitBtn');

            if (btn) {

                btn.disabled = true;

                if (btn.dataset.role === 'admin') {

                    btn.innerHTML = 'Updating...';

                } else {

                    btn.innerHTML = 'Sending Request...';
                }
            }
        });
    }

});

// =========================
// CONFIRM UPDATE
// =========================
function confirmUpdate(role) {

    if (role === 'admin') {

        return confirm(
            'Update this project directly?'
        );

    } else {

        return confirm(
            'Send project update request for admin approval?'
        );
    }
}

// =========================
// CONFIRM DELETE
// =========================
function confirmDelete(role) {

    if (role === 'admin') {

        return confirm(
            'Delete this project permanently?'
        );

    } else {

        return confirm(
            'Send delete request for admin approval?'
        );
    }
}