// functions for login_page ==================================================================

function login_submit(e) {
    const uname = document.getElementById('username');
    const pwd   = document.getElementById('password');

    // Reset previous error states before re-validating
    reset_input(uname);
    reset_input(pwd);

    const nameValid = uname.value ? checkUserName(uname.value) : false;
    const passValid = pwd.value   ? checkPassword(pwd.value)   : false;

    if (!nameValid) error_input_red(uname, "Username must be 3–16 characters");
    if (!passValid) error_input_red(pwd,   "Password must be atleast 8 characters!");
    
    if (!nameValid || !passValid) {
        e.preventDefault(); // Only block submission if validation fails
    }
}

// functions for SignUp_page ==================================================================


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

// CHANGE COLORS FOR INPUT ==>>

function error_input_red(field, message = "Invalid input.") {
    field.classList.add("error_border");

    const error_field = field.parentElement.nextElementSibling;
    error_field.textContent = message;           // Set the error message text
    error_field.classList.remove("error_hidden");
    error_field.classList.add("error_visible");
}

function reset_input(field) {
    field.classList.remove("error_border");

    const error_field = field.parentElement.nextElementSibling;
    error_field.textContent = "a";
    error_field.classList.remove("error_visible");
    error_field.classList.add("error_hidden");
}

// blur handler ================+++++++++++++++========+=====++=====++==+++++===+=++=+=++=++=+==++=+==+==+=++=+==+
function user_blur(e){
    self = e.target;
    if(!checkUserName(self.value)){
        error_input_red(self, "Username must be 3–16 characters");
    }else
    {
        reset_input(self);
    }
}

function pass_blur(e){
    self = e.target;
    if(!checkPassword(self.value)){
        error_input_red(self, "Password must be atleast 8 characters!");
    }else
    {
        reset_input(self);
    }
}
