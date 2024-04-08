console.log("Script works");

document.addEventListener('DOMContentLoaded', function() {
    var elems = document.querySelectorAll('.sidenav');
    var instances = M.Sidenav.init(elems);

    var elems = document.querySelectorAll('.dropdown-trigger');
    var instances = M.Dropdown.init(elems, {constrainWidth: false});

    var elems = document.querySelectorAll('.modal');
    var instances = M.Modal.init(elems);

    var selectElems = document.querySelectorAll('select');
    var instances = M.FormSelect.init(selectElems);

    var elems = document.querySelectorAll('.collapsible');
    var instances = M.Collapsible.init(elems);

    var elems = document.querySelectorAll('.datepicker');
    var instances = M.Datepicker.init(elems, {
        format: 'yy-mm-dd'
    });

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

    // шукаємо кнопку додавання ставки
    const addRateButton = document.getElementById("add-rate-button");
    if (addRateButton) {
        addRateButton.onclick = addRateButtonClick;
    }

    // шукаємо кнопку отримання ставок
    const getRatesButton = document.getElementById("get-rates-button");
    if (getRatesButton) {
        getRatesButton.onclick = getRatesButtonClick;
    }

    // шукаємо кнопку імпорту ставок
    const importRatesButton = document.getElementById("import-rates-button");
    if (importRatesButton) {
        importRatesButton.onclick = importRatesButtonClick;
    }
});

function importRatesButtonClick() {
    console.log("Import button is clicked");
}

function getRatesButtonClick() {
    const userIdInput = document.getElementById("all-rates-user");
    if(!userIdInput) throw "'all-rates-user' is not found";

    const allRatesSupplierSelect = document.getElementById("all-rates-supplier-select");
    if(!allRatesSupplierSelect) { throw "'all-rates-supplier-select' was not found";}

    const allRatesTransportTypeSelect = document.getElementById("all-rates-transport-type-select");
    if(!allRatesTransportTypeSelect) { throw "'all-rates-transport-type-select' was not found";}

    const startPointSelect = document.getElementById("all-rates-start-point-select");
    if(!startPointSelect) { throw "'all-rates-start-point-select' was not found";}

    const endPointSelect = document.getElementById("all-rates-end-point-select");
    if(!endPointSelect) { throw "'all-rates-end-point-select' was not found";}

    const allRatesContainerTypeSelect = document.getElementById("all-rates-container-type-select");
    if(!allRatesContainerTypeSelect) { throw "'all-rates-container-type-select' was not found";}

    const allRatesLineSelect = document.getElementById("all-rates-line-select");
    if(!allRatesLineSelect) { throw "'all-rates-line-select' was not found";}

    const formData = new FormData();
    formData.append("user-id", userIdInput.value);
    formData.append("supplier-trinity-code", allRatesSupplierSelect.value);
    formData.append("transport-type-trinity-code", allRatesTransportTypeSelect.value);
    formData.append("start-point", startPointSelect.value);
    formData.append("end-point", endPointSelect.value);
    formData.append("container-type", allRatesContainerTypeSelect.value);
    formData.append("line", allRatesLineSelect.value);

    fetch('/allrates', {
        method: 'POST',
        body: formData
    })
        .then(r => r.json())
        .then(j => {

                window.location.reload();

        });
}

function addRateButtonClick(e) {
    const userIdInput = document.getElementById("add-rate-user");
    if(!userIdInput) throw "'add-rate-user' is not found";

    const addRateSupplierSelect = document.getElementById("add-rate-supplier-select");
    if(!addRateSupplierSelect) { throw "'add-rate-supplier-select' was not found";}
    const addRateSupplierHelper = document.getElementById('add-rate-supplier-helper');
    if (!addRateSupplierHelper) throw "add-rate-supplier '.helper-text' is not found";

    const addRateTransportTypeSelect = document.getElementById("add-rate-transport-type-select");
    if(!addRateTransportTypeSelect) { throw "'add-rate-transport-type-select' was not found";}
    const addRateTransportTypeHelper = document.getElementById('add-rate-transport-type-helper');
    if (!addRateTransportTypeHelper) throw "add-rate-transport-type '.helper-text' is not found";

    const startPointSelect = document.getElementById("add-rate-start-point-select");
    if(!startPointSelect) { throw "'add-rate-start-point-select' was not found";}
    const startPointHelper = document.getElementById('add-rate-start-point-helper');
    if (!startPointHelper) throw "add-rate-startPointSelect '.helper-text' is not found";

    const endPointSelect = document.getElementById("add-rate-end-point-select");
    if(!endPointSelect) { throw "'add-rate-end-point-select' was not found";}
    const endPointHelper = document.getElementById('add-rate-end-point-helper');
    if (!endPointHelper) throw "add-rate-endPointSelect '.helper-text' is not found";

    const transitTimeInput = document.getElementById("add-rate-transit-time-input");
    if(!transitTimeInput) { throw "'add-rate-transit-time-input' was not found";}
    const transitTimeHelper = document.getElementById('add-rate-transit-time-helper');
    if (!transitTimeHelper) throw "Element 'add-rate-transit-time-helper' is not found";

    const unitPayloadInput = document.getElementById("add-rate-payload-input");
    if(!unitPayloadInput) { throw "'add-rate-payload-input' was not found";}
    const unitPayloadHelper = document.getElementById('add-rate-unit-payload-helper');
    if (!unitPayloadHelper) throw "Element 'add-rate-unit-payload-helper' is not found";

    const addRateAmountInput = document.getElementById("add-rate-amount-input");
    if(!addRateAmountInput) { throw "'add-rate-amount-input' was not found";}
    const addRateAmountHelper = document.getElementById('add-rate-amount-helper');
    if (!addRateAmountHelper) throw "Element 'add-rate-amount-helper' is not found";

    const addRateCurrencySelect = document.getElementById("add-rate-currency-select");
    if(!addRateCurrencySelect) { throw "'add-rate-currency-select' was not found";}
    const addRateCurrencyHelper = document.getElementById('add-rate-currency-helper');
    if (!addRateCurrencyHelper) throw "Element 'add-rate-currency-helper' is not found";

    const addRateEtdInput = document.getElementById("add-rate-etd-input");
    if(!addRateEtdInput) { throw "'add-rate-etd-input' was not found";}
    const addRateEtdHelper = document.getElementById('add-rate-etd-helper');
    if (!addRateEtdHelper) throw "Element 'add-rate-etd-helper' is not found";

    const addRateValidityInput = document.getElementById("add-rate-validity-input");
    if(!addRateValidityInput) { throw "'add-rate-validity-input' was not found";}
    const addRateValidityHelper = document.getElementById('add-rate-validity-helper');
    if (!addRateValidityHelper) throw "Element 'add-rate-validity-helper' is not found";

    const addRateContainerTypeSelect = document.getElementById("add-rate-container-type-select");
    if(!addRateContainerTypeSelect) { throw "'add-rate-container-type-select' was not found";}
    const addRateContainerTypeHelper = document.getElementById('add-rate-container-type-helper');
    if (!addRateContainerTypeHelper) throw "Element 'add-rate-container-type-helper' is not found";

    const addRateLineSelect = document.getElementById("add-rate-line-select");
    if(!addRateLineSelect) { throw "'add-rate-line-select' was not found";}
    const addRateLineHelper = document.getElementById('add-rate-line-helper');
    if (!addRateLineHelper) throw "Element 'add-rate-line-helper' is not found";

    const addRateRemarkTextarea = document.getElementById("add-rate-remark-textarea");
    if(!addRateRemarkTextarea) { throw "'add-rate-remark-textarea' was not found";}

    const addRateResultMesssage = document.getElementById("add-rate-result");
    if (!addRateResultMesssage) {throw "Element 'add-rate-result' was not found!"}
    
    const addServiceBlock = document.getElementById("add-rate-service-block");
    if (!addServiceBlock) {throw "Element 'add-rate-service-block' was not found!"}

    if (
        validateSelect(addRateSupplierSelect, addRateSupplierHelper) &&
        validateSelect(addRateTransportTypeSelect, addRateTransportTypeHelper) &&
        validateSelect(startPointSelect, startPointHelper) &&
        validateSelect(endPointSelect, endPointHelper) && 
        validateNumberInput(addRateAmountInput, addRateAmountHelper) && 
        validateSelect(addRateCurrencySelect, addRateCurrencyHelper) && 
        validateValidityDate(addRateValidityInput, addRateValidityHelper)
    ) {
        if (startPointSelect.value == endPointSelect.value) {
            endPointHelper.innerHTML = "End point could not be the same as start point".fontcolor("red");
        }
        else {

            if(addServiceBlock.style.display == "inline" &&
                !(
                    validateNumberInput(transitTimeInput, transitTimeHelper) &&
                    validateNumberInput(unitPayloadInput, unitPayloadHelper)
                )
                ) {
                    addRateResultMesssage.innerHTML = "Please enter correct data";
            }
            else {
                addRateResultMesssage.innerHTML = "";
                const formData = new FormData();
                formData.append("user-id", userIdInput.value);
                formData.append("supplier-trinity-code", addRateSupplierSelect.value);
                formData.append("transport-type-trinity-code", addRateTransportTypeSelect.value);
                formData.append("start-point", startPointSelect.value);
                formData.append("end-point", endPointSelect.value);
                formData.append("transit-time", transitTimeInput.value);
                formData.append("unit-payload", unitPayloadInput.value);
                formData.append("amount", addRateAmountInput.value);
                formData.append("currency", addRateCurrencySelect.value);
                formData.append("etd", addRateEtdInput.value);
                formData.append("validity", addRateValidityInput.value);
                formData.append("container-type", addRateContainerTypeSelect.value);
                formData.append("line", addRateLineSelect.value);
                formData.append("remark", addRateRemarkTextarea.value);
    
                fetch('/addrate', {
                    method: 'POST',
                    body: formData
                })
                    .then (r => r.json())
                    .then (j => {
                        console.log(j);
    
                        if(j.status == 0) {
                            addRateResultMesssage.innerHTML = j.data.message;
                            addRateResultMesssage.style.background = "lightpink";
                        }
                        else if (j.status == 2) {
                            addRateResultMesssage.innerHTML = j.data.message;
                            addServiceBlock.style.display = "inline";
                            addRateResultMesssage.style.background = "lightyellow";
                        }
                        else if (j.status == 1) {
                            addRateResultMesssage.innerHTML = j.data.message;
                            addRateResultMesssage.style.background = "lightgreen";

                            setTimeout(function() {
                                window.location.reload();
                              }, 3000);
                        }
                    });
            }
        }
    }
}

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

    const currencyRequestSelect = document.getElementById("request-currency-select");
    if(!currencyRequestSelect) {throw "'request-currency-select' was not found";}
    const currencyRequestHelper = document.getElementById("request-currency-helper");
    if (!currencyRequestHelper) {throw "Element 'request-currency-helper' is not found";}

    const requestResultMesssageInput = document.getElementById("request-result-message");
    if (!requestResultMesssageInput) {throw "Element 'request-result-message' was not found!"}

    const withRatesCheckbox = document.getElementById("with-rates-checkbox");
    if (!withRatesCheckbox) {throw "Element 'with-rates-checkbox' was not found!"}

    const validRatesCheckbox = document.getElementById("valid-rates-checkbox");
    if (!validRatesCheckbox) {throw "Element 'valid-rates-checkbox' was not found!"}

    const sortSelect = document.getElementById("sort-select");
    if (!sortSelect) {throw "Element 'sort-select' was not found!"}

    if (
            validateGrossWeight(grossWeightInput, grossWeightHelper) && 
            validateSelect(startPointSelect, startPointHelper) && 
            validateSelect(endPointSelect, endPointHelper) && 
            validateSelect(cargoSelect, cargoHelper) && 
            validateSelect(currencyRequestSelect, currencyRequestHelper)
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
                formData.append("valid-rates", validRatesCheckbox.checked);
                formData.append("sort-by", sortSelect.value);
                formData.append("currency", currencyRequestSelect.value);

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

function validateNumberInput (thisInput, inputHelper) {
    var check = true;

    if (thisInput.value <= 0) {   
        inputHelper.innerHTML = "Value should be positive!".fontcolor("red");
        check = false;
    } 
    else{
        inputHelper.innerHTML = "Accepted".fontcolor("#4caf50");
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

function validateValidityDate (dateInput, dateInputHelper) {
    var check = true;

    const date = new Date();
    let todayDay = date.getTime();
    let validityDay = (new Date(dateInput.value)).getTime();
    
    if (dateInput.value != "") {

        if (validityDay < todayDay) {
            dateInput.className = "invalid";
            dateInputHelper.setAttribute('data-error', "Please enter future date");
            check = false;
    
        }
        else {
            dateInput.className = "valid";
            dateInputHelper.setAttribute('data-success',"Accepted!")
        } 
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
