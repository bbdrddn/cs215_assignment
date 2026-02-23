document.getElementById("user_info_form").addEventListener('submit', dashboard_submit);
document.getElementById("upload-button").addEventListener('click', trigger_upload);
document.getElementById("file-upload-input").addEventListener('change', handleChangeFile);
document.getElementById("file-upload-input").addEventListener('cancel', handleCancelFile);
document.getElementById("remove-button").addEventListener('click', defaultProfile);
document.getElementById("username").addEventListener('blur', user_blur);
document.getElementById("dob").addEventListener('blur', dob_blur);