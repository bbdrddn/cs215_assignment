// functions for login_page ==================================================================

function login_submit(e) {
    const uname = document.getElementById('username');
    const pwd   = document.getElementById('password');

    reset_input(uname);
    reset_input(pwd);

    let nameValid = uname.value ? checkUserName(uname.value) : false;
    let passValid = pwd.value   ? checkPassword(pwd.value)   : false;

    if (!uname.value) error_input_red(uname, "Username is required");
    else if (!nameValid) error_input_red(uname, "Username must contain only 3-50 letters, digits, underscores");

    if (!pwd.value) error_input_red(pwd, "Password is required");
    else if (!passValid) error_input_red(pwd, "Password must be at least 6 characters and contain no spaces");

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
    else if (!nameValid) error_input_red(uname, "Username must contain only letters, digits, underscores");

    if (!pwd.value) error_input_red(pwd, "Password is required");
    else if (!passValid) error_input_red(pwd, "Password must be at least 6 characters and at least 1 non letter character ");

    if (!mail.value) error_input_red(mail, "Email is required");
    else if (!mailValid) error_input_red(mail, "Invalid email format");

    if (!cpass.value) error_input_red(cpass, "Please confirm your password");
    else if (!cpassValid) error_input_red(cpass, "Passwords must match!");

    if (!nameValid || !passValid || !mailValid || !cpassValid) {
        e.preventDefault();
    }
}
//======= dashboard handler====>
function trigger_upload(){
    document.getElementById("file-upload-input").click();
}

function handleCancelFile(e) {
    const error_field = document.querySelector(".profile-photo-buttons").nextElementSibling;
    error_field.textContent = "Upload was Cancelled, No Update Made!";
    error_field.classList.remove("error_hidden");
    error_field.classList.add("error_visible");
}

function handleChangeFile(e){
    const val = e.target;
    const profile = document.getElementById("profile-photo-frame");
    if(val.files && val.files.length > 0){
        let urlImage = URL.createObjectURL(val.files[0]);
        profile.setAttribute("src", urlImage);
        const error_field = document.querySelector(".profile-photo-buttons").nextElementSibling;
        error_field.textContent = "\u00A0";
        error_field.classList.remove("error_visible");
        error_field.classList.add("error_hidden");
    } else {
        const error_field = document.querySelector(".profile-photo-buttons").nextElementSibling;
        error_field.textContent = "File Upload Error, retry!";
        error_field.classList.remove("error_hidden");
        error_field.classList.add("error_visible");
    }
}

function defaultProfile(){
    const profile = document.getElementById("profile-photo-frame");
    profile.setAttribute("src", "images/egg.jpg");
}
// Common FUNCTIONS DOWN HERE =================================================================

// CHECK INPUTS ==>>

function checkUserName(name) {
    const regex = /^[a-zA-Z0-9_]{3,50}$/;
    return regex.test(name);
}

function checkPassword(pass) {
    const regex = /^(?=.*[^a-zA-Z])\S{6,}$/; //at least 6 characters with no spaces
    return regex.test(pass);
}

function checkEmail(email) {
    const regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/; //any email will suffice
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
        error_input_red(self, "Username must contain only 3-50 letters, digits, underscores");
    } else {
        reset_input(self);
    }
}

function pass_blur(e) {
    const self = e.target;
    if (!self.value) { error_input_red(self, "This field must not be empty"); return; }
    if (!checkPassword(self.value)) {
        error_input_red(self, "Password must be at least 6 characters and contain no spaces");
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

function dob_blur(e) {
    const self = e.target;
    if (!self.value) { error_input_red(self, "This field must not be empty"); return; }
    const dob = new Date(self.value);
    const today = new Date();
    if (dob >= today) {
        error_input_red(self, "Date of birth must be in the past");
    } else {
        reset_input(self);
    }
}

function dashboard_submit(e) {
    const uname  = document.getElementById('username');
    const dob    = document.getElementById('dob');

    reset_input(uname);
    reset_input(dob);

    let nameValid = uname.value ? checkUserName(uname.value) : false;
    let dobValid  = false;

    if (!uname.value)    error_input_red(uname, "Username is required");
    else if (!nameValid) error_input_red(uname, "Only letters, digits, and underscores (3-16 characters)");

    if (!dob.value) {
        error_input_red(dob, "Date of birth is required");
    } else {
        const dobDate = new Date(dob.value);
        const today   = new Date();
        if (dobDate >= today) {
            error_input_red(dob, "Date of birth must be in the past");
        } else {
            dobValid = true;
            reset_input(dob);
        }
    }

    if (!nameValid || !dobValid) {
        e.preventDefault();
    }
}