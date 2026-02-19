// functions for login_page ==================================================================

function login_submit(e) {
    const uname = document.getElementById('username');
    const pwd   = document.getElementById('password');

    reset_input(uname);
    reset_input(pwd);

    let nameValid = uname.value ? checkUserName(uname.value) : false;
    let passValid = pwd.value   ? checkPassword(pwd.value)   : false;

    if (!uname.value) error_input_red(uname, "Username is required");
    else if (!nameValid) error_input_red(uname, "Username must be 3–16 characters");

    if (!pwd.value) error_input_red(pwd, "Password is required");
    else if (!passValid) error_input_red(pwd, "Password needs 8+ chars, upper, lower, number & special character");

    if (!nameValid || !passValid) {
        e.preventDefault();
    }
}

// functions for SignUp_page ==================================================================

function signUp_submit(e) {
    const uname = document.getElementById('username');
    const pwd   = document.getElementById('password');
    const mail  = document.getElementById('email');
    const cpass = document.getElementById('cpassword');

    reset_input(uname);
    reset_input(pwd);
    reset_input(mail);
    reset_input(cpass);

    let nameValid  = uname.value ? checkUserName(uname.value)            : false;
    let passValid  = pwd.value   ? checkPassword(pwd.value)              : false;
    let mailValid  = mail.value  ? checkEmail(mail.value)                : false;
    let cpassValid = cpass.value ? confirmPassword(pwd.value, cpass.value) : false;

    if (!uname.value) error_input_red(uname, "Username is required");
    else if (!nameValid) error_input_red(uname, "Username must be 3–16 characters");

    if (!pwd.value) error_input_red(pwd, "Password is required");
    else if (!passValid) error_input_red(pwd, "Password needs 8+ chars, upper, lower, number & special character");

    if (!mail.value) error_input_red(mail, "Email is required");
    else if (!mailValid) error_input_red(mail, "Invalid email format");

    if (!cpass.value) error_input_red(cpass, "Please confirm your password");
    else if (!cpassValid) error_input_red(cpass, "Passwords must match!");

    if (!nameValid || !passValid || !mailValid || !cpassValid) {
        e.preventDefault();
    }
}

// Common FUNCTIONS DOWN HERE =================================================================

// CHECK INPUTS ==>>

function checkUserName(name) {
    const regex = /^[a-zA-Z0-9_-]{3,16}$/;
    return regex.test(name);
}

function checkPassword(pass) {
    const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
    return regex.test(pass);
}

function checkEmail(email) {
    const regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    return regex.test(email);
}

function confirmPassword(pass, cpass) {
    return cpass === pass;
}

// CHANGE COLORS FOR INPUT ==>>

function error_input_red(field, message = "Invalid input.") {
    field.classList.add("error_border");

    const error_field = field.parentElement.nextElementSibling;
    error_field.textContent = message;
    error_field.classList.remove("error_hidden");
    error_field.classList.add("error_visible");
}

function reset_input(field) {
    field.classList.remove("error_border");

    const error_field = field.parentElement.nextElementSibling;
    error_field.textContent = "\u00A0";
    error_field.classList.remove("error_visible");
    error_field.classList.add("error_hidden");
}

// blur handlers ==============================================================================
function user_blur(e) {
    const self = e.target;
    if (!self.value) { error_input_red(self, "This field must not be empty"); return; }
    if (!checkUserName(self.value)) {
        error_input_red(self, "Username must be 3–16 characters");
    } else {
        reset_input(self);
    }
}

function pass_blur(e) {
    const self = e.target;
    if (!self.value) { error_input_red(self, "This field must not be empty"); return; }
    if (!checkPassword(self.value)) {
        error_input_red(self, "Password needs 8+ chars, upper, lower, number & special character");
    } else {
        reset_input(self);
    }
}

function mail_blur(e) {
    const self = e.target;
    if (!self.value) { error_input_red(self, "This field must not be empty"); return; }
    if (!checkEmail(self.value)) {
        error_input_red(self, "Invalid email format");
    } else {
        reset_input(self);
    }
}
function cpass_blur(e) {
    const self = e.target;
    if (!self.value) return;
    const pwd = document.getElementById('password');
    if (!confirmPassword(pwd.value, self.value)) {
        error_input_red(self, "Passwords must match!");
    } else {
        reset_input(self);
    }
}