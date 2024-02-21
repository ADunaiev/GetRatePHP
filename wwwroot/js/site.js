console.log("Script works");

document.addEventListener('DOMContentLoaded', function() {
    var elems = document.querySelectorAll('.sidenav');
    var instances = M.Sidenav.init(elems);

    var elems = document.querySelectorAll('.modal');
    var instances = M.Modal.init(elems);

    var elems = document.querySelectorAll('select');
    var instances = M.FormSelect.init(elems);

    // шукаємо кнопку реєстрації. якщо находим додаємо обробник
    const signUpButton = document.getElementById("signup-button");
    if(signUpButton){
        signUpButton.onclick = signupButtonClick;
    }

    // шукаємо кнопку авторізації. якщо находим додаємо обробник
    const authButton = document.getElementById("auth-button");
    if(authButton){
        authButton.onclick = authButtonClick;
    }
  });

  function authButtonClick(e){

    var check = true;
    // шукаємо батьківський елемент кнопки (e.target)
    const signinForm = e.target.closest('form');
    if(! signinForm) {
        throw "Signin form was not found";
    }

    const signinEmailInput = signinForm.querySelector('input[name="sign-in-email"]');
    if(!signinEmailInput) {throw "Element 'sign-in-email' not found";}
    const signinEmailInputHelper = signinEmailInput.parentNode.querySelector('.helper-text');
    if(!signinEmailInputHelper) {throw "Element signinEmail '.helper' is not found"}

    const signinPasswordInput = signinForm.querySelector('input[name="sign-in-password"]');
    if(!signinPasswordInput) {throw "Element 'sign-in-password' not found";}
    const signinPasswordInputHelper = signinPasswordInput.parentNode.querySelector('.helper-text');
    if(!signinPasswordInputHelper) {throw "Element signinPassword '.helper' is not found"}

    if (signinEmailInput.value == "") {
        check = false;
        signinEmailInput.className = "invalid";
        signinEmailInputHelper.setAttribute('data-error', "Email could not be empty!");
    }
    else if (signinPasswordInput.value == "") {

        signinEmailInput.className = "valid";
        check = false;
        signinPasswordInput.className = "invalid";
        signinPasswordInputHelper.setAttribute('data-error', "Password could not be empty!");
    }
    else if (check == true) {  
        signinPasswordInput.className = "valid";
        
        // передаємо - формуємо запит

        fetch(`/auth?email=${signinEmailInput.value}&password=${signinPasswordInput.value}`)
            .then( r => r.text())
            .then( t => {
                console.log(t);
            }) ;
    }

  }

  function signupButtonClick(e) {
    // console.log("signup button clicked");
    // шукаємо батьківський елемент кнопки (e.target)

    const signupForm = e.target.closest('form');
    if(! signupForm) {
        throw "Signup form was not found";
    }
    
    const nameInput = signupForm.querySelector('input[name="user-name"]');
    if(!nameInput) {throw "nameInput not found";}

    const emailInput = signupForm.querySelector('input[name="user-email"]');
    if(!emailInput) {throw "emailInput not found";}

    const passwordInput = signupForm.querySelector('input[name="user-password"]');
    if(!passwordInput) {throw "passwordInput not found";}

    const repeatInput = signupForm.querySelector('input[name="user-repeat"]');
    if(!repeatInput) {throw "repeatInput not found";}

    const avatarInput = signupForm.querySelector('input[name="user-avatar"]');
    if(!avatarInput) {throw "avatarInput not found";}

    /// Валідація даних
    if(validateSignUpForm()){
        // формуємо дані для передачі на бекенд
        const formData = new FormData();
        formData.append("user-name", nameInput.value);
        formData.append("user-email", emailInput.value);
        formData.append("user-password", passwordInput.value);
        if (avatarInput.files.length >0){
            formData.append("user-avatar", avatarInput.files[0]);
        }
        
        // передаємо - формуємо запит

        fetch("/auth", {
            method: 'POST', 
            body: formData
        })
            .then( r => r.json())
            .then( j => {
                console.log(j);

                document.getElementById("sign-up-form").innerHTML =
                 j['data']['message'];

            }) ;
    }

}

function validateSignUpForm(formNode) {
    var check = true;

    const userEmail = document.getElementById("user-email");
    if(!userEmail) throw "Element 'user-email' is not found - Validation stopped!";
    const emailHelper = userEmail.parentNode.querySelector('.helper-text');
    if (!emailHelper) throw "email '.helper-text' is not found";

    const userName = document.getElementById("user-name");
    if(!userName) throw "Element 'user-name' is not found - Validation stopped!";
    const nameHelper = userName.parentNode.querySelector('.helper-text');
    if (!nameHelper) throw "userName '.helper-text' is not found";

    const userPassword = document.getElementById("user-password");
    if(!userPassword) throw "Element 'user-password' is not found - Validation stopped!";
    const passwordHelper = userPassword.parentNode.querySelector('.helper-text');
    if (!passwordHelper) throw "userPassword '.helper-text' is not found";

    const userRepeatPassword = document.getElementById("user-repeat-password");
    if(!userRepeatPassword) throw "Element 'user-repeat-password' is not found - Validation stopped!";
    const repeatPasswordHelper = userRepeatPassword.parentNode.querySelector('.helper-text');
    if (!repeatPasswordHelper) throw "userRepeatPassword '.helper-text' is not found";

    const avatarFile = document.getElementById("avatar-file");
    if(!avatarFile) throw "Element 'avatar-file' is not found - Validation stopped!";

    // валідація email
    if (userEmail.value == "")
    {
        userEmail.className = "invalid";
        emailHelper.setAttribute('data-error', "Email не може бути порожним");
        check = false;
        
    }
    else if (/^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/i.test(userEmail.value)) {
        userEmail.className = "valid";
        emailHelper.setAttribute('data-success', "Приймається!");
    }
    else {
        userEmail.className = "invalid";
        emailHelper.setAttribute('data-error', "Не вірний формат email");
        check = false;
    }

    // валідація імені 
    if (userName.value == "")
    {
        userName.className = "invalid";
        nameHelper.setAttribute('data-error', "Ім'я не може бути порожним");
        check = false;
    }
    else if (/\d/.test(userName.value)) {
        userName.className = "invalid";
        nameHelper.setAttribute('data-error', "Ім'я не може мистити цифри");
        check = false;

    }
    else if (/[^a-zа-яіЇє'ґ ]/i.test(userName.value)) {
        userName.className = "invalid";
        nameHelper.setAttribute('data-error', "Ім'я не може мистити спецзнаки");
        check = false;
    }
    else {
        userName.className = "valid";
        nameHelper.setAttribute('data-success',"Приймається!")
    }

    // валідація пароля
    if (userPassword.value == "") {
        userPassword.className = "invalid";
        passwordHelper.setAttribute('data-error', "Пароль не може бути порожним");
        check = false;
    }
    //  ^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#\-_)\^(0?&])[A-Za-z\d@$!\^)(0_%*#?
    else if (/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#\-_)\^(0?&])[A-Za-z\d@$!\^)(0_%*#?\-&]{8,}$/i.test(userPassword.value)) {
        userPassword.className = "valid";
        passwordHelper.setAttribute('data-success', "Приймається!");
    }
    else {
        userPassword.className = "invalid";
        passwordHelper.setAttribute('data-error', "Minimum eight characters, at least one letter, one number and one special character");
        check = false;
    }

    //валідація перевірки пароля
    if (userRepeatPassword.value == "") {
        userRepeatPassword.className = "invalid";
        repeatPasswordHelper.setAttribute('data-error', "Повторення паролю не може бути порожним");
        check = false;
    }
    else if(userRepeatPassword.value == userPassword.value ){
        userRepeatPassword.className = "valid";
        repeatPasswordHelper.setAttribute('data-success', "Приймається!");
    }
    else {
        userRepeatPassword.className = "invalid";
        repeatPasswordHelper.setAttribute('data-error', "Паролі не збігаються");
        check = false;
    }

    return check;
}