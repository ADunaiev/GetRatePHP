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
  });

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
            .then(console.log) ;
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
    else if (/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/i.test(userPassword.value)) {
        userPassword.className = "valid";
        passwordHelper.setAttribute('data-success', "Приймається!");
    }
    else {
        userPassword.className = "invalid";
        passwordHelper.setAttribute('data-error', "Мінімум 8 символів, щонайменьше одна літера та одна цифра");
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