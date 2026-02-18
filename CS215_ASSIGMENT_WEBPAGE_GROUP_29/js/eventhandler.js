// functions for login_page ==================================================================
function login_submit(e){
    e.preventDefault();
    let uname = document.getElementById('username').value;
    let pwd = document.getElementById('password').value;

    uname?checkUserName(uname):alert("Username not Entered"); //yet to add color change and error for no input
    (pwd || !(pwd.length < 8))?checkPassword(pwd):alert("Password not Entered"); //yet to add color change and error for no input
}
// functions for SignUp_page ==================================================================


//Common FUNCTION DOWN HERE ==================================================================

//CHECK INPUTS ==>>

function checkUserName(name){
    const regex = /^[a-zA-Z0-9_-]{3,16}$/;
    alert(regex.test(name)); //yet to add if true or false, changes color and error
}

function checkPassword(pass){
    const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
    alert(regex.test(pass)); //yet to add if true or false, changes color and error
}

//CHANGE COLORS FOR INPUT ==>>

//CHANGE VISIBILITY FOR ERRORS ==>>

//CHANGE TEXT FOR ERRORS ==>>
