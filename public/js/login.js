/*
|==================================================
| AUTO HIDE ALERTS
|==================================================
*/
setTimeout(() => {

    const alerts =
        document.querySelectorAll('.alert.alert-success');

    alerts.forEach(alert => {

        const bsAlert =
            bootstrap.Alert.getOrCreateInstance(alert);

        bsAlert.close();
    });

}, 8000);



/*
|==================================================
| GENERATE OTP FORM SUBMIT
|==================================================
*/
const loginForm =
    document.getElementById('loginForm');

if (loginForm)
{
    loginForm.addEventListener(
        'submit',
        function ()
        {
            const btn =
                document.getElementById('generateOtpBtn');

            if (btn)
            {
                btn.disabled = true;

                btn.innerHTML =
                    'Generating OTP...';
            }
        }
    );
}



/*
|==================================================
| VERIFY OTP FORM SUBMIT
|==================================================
*/
const otpForm =
    document.querySelector(
        'form[action*="verifyOtp"]'
    );

if (otpForm)
{
    otpForm.addEventListener(
        'submit',
        function ()
        {
            const btn =
                document.getElementById('verifyOtpBtn');

            if (btn)
            {
                btn.disabled = true;

                btn.innerHTML =
                    'Verifying OTP...';
            }
        }
    );
}



/*
|==================================================
| PASSWORD TOGGLE
|==================================================
*/
const togglePassword =
    document.getElementById('togglePassword');

const password =
    document.getElementById('password');

if (
    togglePassword &&
    password
)
{
    togglePassword.addEventListener(
        'click',
        function ()
        {
            const type =
                password.getAttribute('type') === 'password'
                ? 'text'
                : 'password';

            password.setAttribute(
                'type',
                type
            );

            this.innerHTML =
                type === 'password'
                ? '<i class="bi bi-eye"></i>'
                : '<i class="bi bi-eye-slash"></i>';
        }
    );
}