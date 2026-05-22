document.addEventListener("DOMContentLoaded", function () {

    const form = document.getElementById("addUserForm");
    if (!form) return;

    const fields = {
        userid: form.querySelector("[name='userid']"),
        username: form.querySelector("[name='username']"),
        email: form.querySelector("[name='email']"),
        password: form.querySelector("[name='userpass']"),
        repassword: form.querySelector("[name='re_password']"),
        deptid: form.querySelector("[name='deptid']")
    };

    function setError(field, msg) {
        if (!field) return;

        let error = field.parentNode.querySelector("small");

        if (!error) {
            error = document.createElement("small");
            error.className = "text-danger d-block mt-1";
            field.parentNode.appendChild(error);
        }

        error.innerText = msg;
    }

    function clearError(field) {
        if (!field) return;

        const error = field.parentNode.querySelector("small");
        if (error) error.innerText = "";
    }

    function validate() {

        let valid = true;

        if (fields.userid && !fields.userid.value.trim()) {
            setError(fields.userid, "User ID required");
            valid = false;
        } else clearError(fields.userid);

        if (fields.username && !fields.username.value.trim()) {
            setError(fields.username, "User Name required");
            valid = false;
        } else clearError(fields.username);

        const email = fields.email ? fields.email.value.trim() : "";
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (fields.email && (!email || !emailPattern.test(email))) {
            setError(fields.email, "Valid email required");
            valid = false;
        } else clearError(fields.email);

        if (fields.password && (!fields.password.value || fields.password.value.length < 6)) {
            setError(fields.password, "Min 6 characters required");
            valid = false;
        } else clearError(fields.password);

        if (fields.password && fields.repassword &&
            fields.password.value !== fields.repassword.value) {
            setError(fields.repassword, "Passwords do not match");
            valid = false;
        } else clearError(fields.repassword);

        if (fields.deptid && !fields.deptid.value) {
            setError(fields.deptid, "Select department");
            valid = false;
        } else clearError(fields.deptid);

        return valid;
    }

    Object.values(fields).forEach(field => {
        if (!field) return;

        field.addEventListener("input", validate);
        field.addEventListener("change", validate);
    });

    form.addEventListener("submit", function (e) {

        if (!validate()) {
            e.preventDefault();
            alert("⚠️ Please fix validation errors before submitting.");
            return;
        }

        if (!confirm("Are you sure you want to create this user?")) {
            e.preventDefault();
        }
    });

});