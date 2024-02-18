<h1>Реєстрація користувача</h1>
<form method="post" id="sign-up-form">
    <div class="row">
        <div class="col s12">
            <div class="row">
                
                <div class="input-field col s6">
                    <input id="user-email" name="user-email" type="email" class="validate">
                    <label for="user-email">Email</label>
                    <span class="helper-text" data-error="wrong" data-success="right">Email</span>
                </div>

                <div class="input-field col s6">
                    <input id="user-name" name="user-name" type="text" class="validate">
                    <label for="user-name">Ім'я</label>
                    <span class="helper-text" data-error="wrong" data-success="right">Використовувайте лише літери</span>
                </div>

                <div class="input-field col s6">
                    <input id="user-password" name="user-password" type="password" class="validate">
                    <label for="user-password">Пароль</label>
                    <span class="helper-text" data-error="wrong" data-success="right">Пароль</span>
                </div>

                <div class="input-field col s6">
                    <input id="user-repeat-password" name="user-repeat" type="password" class="validate">
                    <label for="user-repeat-password">Повторить пароль</label>
                    <span class="helper-text" data-error="wrong" data-success="right">Повторить пароль</span>
                </div>

                <div class="file-field input-field col s9">
                    <div class="btn">
                        <span>Аватар</span>
                        <input id="avatar-file" name="user-avatar" type="file">
                    </div>
                    <div class="file-path-wrapper">
                        <input  id="avatar-file-path" class="file-path validate" type="text">
                    </div>
                </div>

            </div>  

        </div>

    </div>
    <div class="row">
        <div class="col s3">
            <button type="button" class="btn" id="signup-button">Реєстрація</button>
        </div>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', () => {
    const signUpForm = document.getElementById("sign-up-form");
    if(!signUpForm) throw "Can't find element with id 'sign-up-form'"
    else signUpForm.addEventListener('submit', signUpFormSubmit);

});

function signUpFormSubmit(e) {
    e.preventDefault();

    var result = validateSignUpForm(e.target);

    if (result !== true) {
        alert(result);
    }
    else {

    }
}

function validateSignUpForm(formNode) {

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
    }
    else if (/^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/i.test(userEmail.value)) {
        userEmail.className = "valid";
        emailHelper.setAttribute('data-success', "Приймається!");
    }
    else {
        userEmail.className = "invalid";
        emailHelper.setAttribute('data-error', "Не вірний формат email");
    }

    // валідація імені 
    if (userName.value == "")
    {
        userName.className = "invalid";
        nameHelper.setAttribute('data-error', "Ім'я не може бути порожним");
    }
    else if (/\d/.test(userName.value)) {
        userName.className = "invalid";
        nameHelper.setAttribute('data-error', "Ім'я не може мистити цифри");

    }
    else if (/[^a-zа-яіЇє'ґ ]/i.test(userName.value)) {
        userName.className = "invalid";
        nameHelper.setAttribute('data-error', "Ім'я не може мистити спецзнаки");
    }
    else {
        userName.className = "valid";
        nameHelper.setAttribute('data-success',"Приймається!")
    }

    // валідація пароля
    if (userPassword.value == "") {
        userPassword.className = "invalid";
        passwordHelper.setAttribute('data-error', "Пароль не може бути порожним");
    }
    else if (/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/i.test(userPassword.value)) {
        userPassword.className = "valid";
        passwordHelper.setAttribute('data-success', "Приймається!");
    }
    else {
        userPassword.className = "invalid";
        passwordHelper.setAttribute('data-error', "Мінімум 8 символів, щонайменьше одна літера та одна цифра");
    }

    //валідація перевірки пароля
    if (userRepeatPassword.value == "") {
        userRepeatPassword.className = "invalid";
        repeatPasswordHelper.setAttribute('data-error', "Повторення паролю не може бути порожним");
    }
    else if(userRepeatPassword.value == userPassword.value ){
        userRepeatPassword.className = "valid";
        repeatPasswordHelper.setAttribute('data-success', "Приймається!");
    }
    else {
        userRepeatPassword.className = "invalid";
        repeatPasswordHelper.setAttribute('data-error', "Паролі не збігаються");
    }

    return true;
}
</script>
