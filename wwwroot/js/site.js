console.log("Script works");

document.addEventListener('DOMContentLoaded', function() {
    var elems = document.querySelectorAll('.sidenav');
    var instances = M.Sidenav.init(elems);

    var elems = document.querySelectorAll('.modal');
    var instances = M.Modal.init(elems);

    var selectElems = document.querySelectorAll('select');
    var instances = M.FormSelect.init(selectElems);

    var elems = document.querySelectorAll('.collapsible');
    var instances = M.Collapsible.init(elems);

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

    // шукаємо кнопку оновлення профілю. якщо находим додаємо обробник
    const profileUpdateButton = document.getElementById("profile-update-btn");
    if(profileUpdateButton){
        profileUpdateButton.onclick = profileUpdateButtonClick;
    }

    // шукаємо кнопку виходу. якщо находим додаємо обробник
    const signOutButton = document.querySelector("sign-out-btn");
    if(signOutButton){
        signOutButton.onclick = signOutButtonClick;
    }
    
    // шукаємо кнопку відправлення запиту
    const requestButton = document.getElementById("request-button");
    if (requestButton) {
        requestButton.onclick = requestButtonClick;
    }
});

function requestButtonClick(e) {

    const userIdInput = document.getElementById("profile-id");
    if(!userIdInput) throw "Profile-id is not found";

    const preloaderRequest = document.getElementById("request-preloader");
    if(!preloaderRequest) throw "Element 'request-preloader' is not found";

    const startPointSelect = document.getElementById("start-point-select");
    if(!startPointSelect) { throw "'start-point-select' was not found";}
    const startPointHelper = document.getElementById('start-point-helper');
    if (!startPointHelper) throw "startPointSelect '.helper-text' is not found";

    const endPointSelect = document.getElementById("end-point-select");
    if(!endPointSelect) {throw "'end-point-select' was not found";}
    const endPointHelper = document.getElementById('end-point-helper');
    if (!endPointHelper) throw "endPointSelect '.helper-text' is not found";

    const cargoSelect = document.getElementById("cargo-select");
    if(!cargoSelect) {throw "'cargo-select' was not found";}
    const cargoHelper = document.getElementById('cargo-helper');
    if (!cargoHelper) throw "cargoSelect '.helper-text' is not found";

    const grossWeightInput = document.getElementById("cargo-gw-input");
    if(!grossWeightInput) {throw "'cargo-gw-input' was not found";}
    const grossWeightHelper = grossWeightInput.parentNode.querySelector('.helper-text');
    if (!grossWeightHelper) throw "grossWeightInput '.helper-text' is not found";

    const requestResultMesssageInput = document.getElementById("request-result-message");
    if (!requestResultMesssageInput) {throw "Element 'request-result-message' was not found!"}

    const withRatesCheckbox = document.getElementById("with-rates-checkbox");
    if (!withRatesCheckbox) {throw "Element 'with-rates-checkbox' was not found!"}

    const sortSelect = document.getElementById("sort-select");
    if (!sortSelect) {throw "Element 'sort-select' was not found!"}

    
    if (
            validateGrossWeight(grossWeightInput, grossWeightHelper) && 
            validateSelect(startPointSelect, startPointHelper) && 
            validateSelect(endPointSelect, endPointHelper) && 
            validateSelect(cargoSelect, cargoHelper)  
        ) {

            if (startPointSelect.value === endPointSelect.value) {
                endPointHelper.innerHTML = "End point could not be the same as start point".fontcolor("red");
            } 
            else {
                requestResultMesssageInput.innerHTML = "";
                preloaderRequest.style.display = 'inline-flex';
                const formData = new FormData();
                formData.append("start-point", startPointSelect.value);
                formData.append("end-point", endPointSelect.value);
                formData.append("cargo", cargoSelect.value);
                formData.append("cargo-gw", grossWeightInput.value);
                formData.append("user-id", userIdInput.value);
                formData.append("with-rates", withRatesCheckbox.checked);
                formData.append("sort-by", sortSelect.value);

                fetch('/requests', {
                    method: 'POST',
                    body: formData,
                })
                    .then(r => r.json())
                    .then(j => {
                        if (j.status == 1) {
                            //window.location.href="/rates";
                            
                            window.location.reload();
                            preloaderRequest.style.display = 'none';
                        }
                        else {
                            requestResultMesssageInput.className = "card-panel red lighten-3";
                            requestResultMesssageInput.innerHTML = j.data.message;
                        }
                    });
                }
    
            }

    

}

function signOutButtonClick(e) {
    
    console.log("sign out button is pressed");

    
    fetch('auth', {
        method: 'DELETE'
    })
        .then(r => r.json())
        .then(j => {
            //console.log(j);
            
            if(j['status'] == 1) {
                console.log(j.data.message);
                window.location.href = "/";
            }
            else {
                console.log(j.data.message);
            }
        });
}

function profileUpdateButtonClick(e) {

    const updateProfileForm = e.target.closest('form');
    if(! updateProfileForm) {
        throw "'updateProfileForm' form was not found";
    }

    const userIdInput = document.getElementById("profile-id");
    if(!userIdInput) throw "Profile-id is not found";

    const nameUpdateInput = updateProfileForm.querySelector('input[name="profile-name"]');
    if(!nameUpdateInput) {throw "'nameUpdateInput' not found";}
    const profileNameHelper = nameUpdateInput.parentNode.querySelector('.helper-text');
    if (!profileNameHelper) throw "nameUpdateInput '.helper-text' is not found";

    const emailUpdateInput = updateProfileForm.querySelector('input[name="profile-email"]');
    if(!emailUpdateInput) {throw "'emailUpdateInput' not found";}
    const profileEmailHelper = emailUpdateInput.parentNode.querySelector('.helper-text');
    if (!profileEmailHelper) throw "emailUpdateInput '.helper-text' is not found";

    const passwordUpdateInput = updateProfileForm.querySelector('input[name="profile-password"]');
    if(!passwordUpdateInput) {throw "'passwordUpdateInput' not found";}
    const profilePasswordHelper = passwordUpdateInput.parentNode.querySelector('.helper-text');
    if (!profilePasswordHelper) throw "passwordUpdateInput '.helper-text' is not found";   

    const repeatUpdateInput = updateProfileForm.querySelector('input[name="profile-repeat"]');
    if(!repeatUpdateInput) {throw "'repeatUpdateInput' not found";}
    const profileRepeatHelper = repeatUpdateInput.parentNode.querySelector('.helper-text');
    if (!profileRepeatHelper) throw "repeatUpdateInput '.helper-text' is not found";

    const avatarUpdateInput = updateProfileForm.querySelector('input[name="profile-avatar"]');
    if(!avatarUpdateInput) {throw "'avatarUpdateInput' not found";}

    if( 
        validateName(nameUpdateInput, profileNameHelper) &&
        validateEmail(emailUpdateInput, profileEmailHelper) &&
        validatePassword(passwordUpdateInput, profilePasswordHelper) &&
        validateRepeatPassword(repeatUpdateInput, profileRepeatHelper, passwordUpdateInput)
    ) {
        const formData = new FormData();
        formData.append("user-id", userIdInput.value);
        formData.append("updated-name", nameUpdateInput.value);
        formData.append("updated-email", emailUpdateInput.value);
        formData.append("updated-password", passwordUpdateInput.value);
        if (avatarUpdateInput.files.length > 0) {
            formData.append("updated-avatar", avatarUpdateInput.files[0]);
        }

        fetch('/authupdate', {
            method: 'POST',
            body: formData,
        })
            .then(r => r.json())
            .then(j => {
                if (j.status == 1) {
                    window.location.href = "/";
                    //console.log(j);
                }
                else {
                    M.toast({html: j['data']['message']});
                }
            });
    }
    else {
        console.log("validation failed!");
    }
}

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
        .then( r => 
            r.json()
        )
        .then(j => {
            if(j['status'] == 1) {
                window.location.reload();
                //console.log(j);
            }
            else {
                M.toast({html: j['data']['message']});
            }       
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
    const nameHelper = nameInput.parentNode.querySelector('.helper-text');
    if (!nameHelper) throw "nameInput '.helper-text' is not found";

    const emailInput = signupForm.querySelector('input[name="user-email"]');
    if(!emailInput) {throw "emailInput not found";}
    const emailHelper = emailInput.parentNode.querySelector('.helper-text');
    if (!emailHelper) throw "emailInput '.helper-text' is not found";

    const passwordInput = signupForm.querySelector('input[name="user-password"]');
    if(!passwordInput) {throw "passwordInput not found";}
    const passwordHelper = passwordInput.parentNode.querySelector('.helper-text');
    if (!passwordHelper) throw "passwordInput '.helper-text' is not found";

    const repeatInput = signupForm.querySelector('input[name="user-repeat"]');
    if(!repeatInput) {throw "repeatInput not found";}
    const repeatPasswordHelper = repeatInput.parentNode.querySelector('.helper-text');
    if (!repeatPasswordHelper) throw "repeatInput '.helper-text' is not found";

    const avatarInput = signupForm.querySelector('input[name="user-avatar"]');
    if(!avatarInput) {throw "avatarInput not found";}

    /// Валідація даних
    if(
        validateName(nameInput, nameHelper) && 
        validateEmail(emailInput, emailHelper) && 
        validatePassword(passwordInput, passwordHelper) &&
        validateRepeatPassword(repeatInput, repeatPasswordHelper, passwordInput)
    ){
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

function validateGrossWeight (grossWeighInput, grossWeightHelper) {
    var check = true;

    if (grossWeighInput.value == "") {
        grossWeighInput.className = "invalid";
        grossWeightHelper.setAttribute('data-error', "Groww Weight should not be empty!");
        check = false;
    } 
    else if (grossWeighInput.value > 0) {
        grossWeighInput.className = "valid";
        grossWeightHelper.setAttribute('data-success', "Accepted");
    }
    else {
        grossWeighInput.className = "invalid";
        grossWeightHelper.setAttribute('data-error', "Groww Weight should be bigger than 0");
        check = false;
    }

    return check;
}

function validateSelect (thisSelect, selectHelper) {
    var check = true;

    if (thisSelect.value == "") {   
        selectHelper.innerHTML = "Select could not be empty!".fontcolor("red");
        check = false;
    } 
    else{
        selectHelper.innerHTML = "Accepted".fontcolor("#4caf50");
    }

    return check;
}

function validateName (nameInput, nameInputHelper) {
    var check = true;
    
    if (nameInput.value == "")
    {
        nameInput.className = "invalid";
        nameInputHelper.setAttribute('data-error', "Name can't be empty");
        check = false;
    }
    else if (/\d/.test(nameInput.value)) {
        nameInput.className = "invalid";
        nameInputHelper.setAttribute('data-error', "Name can't contain digits");
        check = false;

    }
    else if (/[^a-zа-яіЇє'ґ ]/i.test(nameInput.value)) {
        nameInput.className = "invalid";
        nameInputHelper.setAttribute('data-error', "Name can't contain special characters");
        check = false;
    }
    else {
        nameInput.className = "valid";
        nameInputHelper.setAttribute('data-success',"Accepted!")
    } 

    return check;
}

function validateEmail (dataInput, dataInputHelper) {
    var check = true;
 
    if (dataInput.value == "")
    {
        dataInput.className = "invalid";
        dataInputHelper.setAttribute('data-error', "Email can't be empty!");
        check = false;
        
    }
    else if (/^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/i.test(dataInput.value)) {
        dataInput.className = "valid";
        dataInputHelper.setAttribute('data-success', "Accepted!");
    }
    else {
        dataInput.className = "invalid";
        dataInputHelper.setAttribute('data-error', "Wrong email format!");
        check = false;
    }

    return check;
}

function validatePassword (dataInput, dataInputHelper) {
    var check = true;
 
    if (dataInput.value == "") {
        dataInput.className = "invalid";
        dataInputHelper.setAttribute('data-error', "Password could not be empty");
        check = false;
    }
    //  ^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#\-_)\^(0?&])[A-Za-z\d@$!\^)(0_%*#?
    else if (/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#\-_)\^(0?&])[A-Za-z\d@$!\^)(0_%*#?\-&]{8,}$/i.test(dataInput.value)) {
        dataInput.className = "valid";
        dataInputHelper.setAttribute('data-success', "Accepted!");
    }
    else {
        dataInput.className = "invalid";
        dataInputHelper.setAttribute('data-error', "Minimum eight characters, at least one letter, one number and one special character");
        check = false;
    }

    return check;
}

function validateRepeatPassword (dataInput, dataInputHelper, passwordInput) {
    var check = true;
 
    if (dataInput.value == "") {
        dataInput.className = "invalid";
        dataInputHelper.setAttribute('data-error', "Password repeat could not be empty");
        check = false;
    }
    else if(dataInput.value == passwordInput.value ){
        dataInput.className = "valid";
        dataInputHelper.setAttribute('data-success', "Accepted!");
    }
    else {
        dataInput.className = "invalid";
        dataInputHelper.setAttribute('data-error', "Passwords are not equal");
        check = false;
    }

    return check;
}
