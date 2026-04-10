// register_userdashboard.js — DOM2 event registration for the user dashboard

// ── Upload / Remove picture buttons ─────────────────────────────────────────
document.getElementById("upload-button").addEventListener('click', trigger_upload);
document.getElementById("file-upload-input").addEventListener('change', handleChangeFile);
document.getElementById("file-upload-input").addEventListener('cancel', handleCancelFile);

document.getElementById("remove-button").addEventListener('click', function () {
    // Set hidden flag so the server knows to NULL the picture
    document.getElementById("remove-picture-flag").value = "1";
    // Clear the file input so no new file is uploaded
    document.getElementById("file-upload-input").value = "";
    // Show egg placeholder immediately
    defaultProfile();
});

// ── Blur validation ──────────────────────────────────────────────────────────
document.getElementById("username").addEventListener('blur', user_blur);
document.getElementById("dob").addEventListener('blur', dob_blur);

// ── Save Changes — AJAX submit (DOM2 registration) ───────────────────────────
document.getElementById("save-button").addEventListener('click', function (e) {
    e.preventDefault();

    // Re-use existing client-side validation helpers from eventhandler.js
    const uname = document.getElementById('username');
    const dob   = document.getElementById('dob');

    reset_input(uname);
    reset_input(dob);

    let nameValid = uname.value ? checkUserName(uname.value) : false;
    let dobValid  = false;

    if (!uname.value)    error_input_red(uname, "Username is required");
    else if (!nameValid) error_input_red(uname, "Only letters, digits, and underscores (3-50 characters)");

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

    if (!nameValid || !dobValid) return; // stop — show inline errors

    // Build FormData from the form (includes file input if any)
    const form     = document.getElementById("user_info_form");
    const formData = new FormData(form);

    // Hide any previous feedback
    showAjaxMessage('', '');

    // ── Send via fetch() ────────────────────────────────────────────────────
    fetch('update_profile.php', {
        method: 'POST',
        body:   formData
    })
    .then(function (response) {
        if (!response.ok) {
            throw new Error('Network error: ' + response.status);
        }
        return response.json();
    })
    .then(function (data) {
        if (data.success) {
            // ── DOM manipulation: update visible values ──────────────────
            document.getElementById('username').value        = data.username;
            document.getElementById('dob').value             = data.dob;
            document.getElementById('profile-photo-frame').setAttribute('src', data.avatar);

            // Reset the remove-picture flag after a successful save
            document.getElementById("remove-picture-flag").value = "0";
            // Clear file input
            document.getElementById("file-upload-input").value = "";

            showAjaxMessage('success', data.message || 'Profile updated successfully!');
        } else {
            // ── Show field-level errors from server ──────────────────────
            if (data.errors) {
                if (data.errors.username) {
                    error_input_red(document.getElementById('username'), data.errors.username);
                }
                if (data.errors.dob) {
                    error_input_red(document.getElementById('dob'), data.errors.dob);
                }
                // Generic fallback message
                const first = Object.values(data.errors)[0];
                showAjaxMessage('error', first);
            } else {
                showAjaxMessage('error', data.error || 'Update failed. Please try again.');
            }
        }
    })
    .catch(function (err) {
        showAjaxMessage('error', 'Request failed: ' + err.message);
    });
});

// ── Helper: show / hide the AJAX feedback paragraphs ─────────────────────────
function showAjaxMessage(type, message) {
    const errorEl   = document.getElementById('ajax-error');
    const successEl = document.getElementById('ajax-success');

    // Reset both
    errorEl.textContent = '\u00A0';
    errorEl.classList.remove('error_visible');
    errorEl.classList.add('error_hidden');

    successEl.textContent = '\u00A0';
    successEl.classList.remove('error_visible');
    successEl.classList.add('error_hidden');

    if (type === 'error' && message) {
        errorEl.textContent = message;
        errorEl.classList.remove('error_hidden');
        errorEl.classList.add('error_visible');
    } else if (type === 'success' && message) {
        successEl.textContent = message;
        successEl.classList.remove('error_hidden');
        successEl.classList.add('error_visible');
    }
}