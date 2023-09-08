
function setFormMessage(formElement, type, message) {
    const messageElement = formElement.querySelector(".form__message");

    messageElement.textContent = message;
    messageElement.classList.remove("form__message--success", "form__message--error");
    messageElement.classList.add(`form__message--${type}`);
}

document.addEventListener("DOMContentLoaded", () => {
    const loginForm = document.querySelector("#login");
    const createAccountForm = document.querySelector("#createAccount");

    
    document.querySelector("#linkCreateAccount").addEventListener("click", e => { 
        // e is reference to event object. Can be used to prevent default (going to href in link),
        // and allowing this code to be executed such as showing/hiding
        e.preventDefault();
        loginForm.classList.add("form--hidden");
        createAccountForm.classList.remove("form--hidden");
    });

    document.querySelector("#linkLogin").addEventListener("click", e => {
        e.preventDefault();
        loginForm.classList.remove("form--hidden");
        createAccountForm.classList.add("form--hidden");
    });


    loginForm.addEventListener("submit", e => {
        e.preventDefault();

        const formData = new FormData(loginForm);

        fetch("http://localhost/PROJECT-APIGithub/api.php?check-login", {
            method: "POST",
            body: formData,
        })
        .then(response => response.text()) // Returning response as json stopped working for some reason, despite it working in postman
        .then(text => {
            const data = JSON.parse(text.trim()); // Changed the response to text, then parsed back to json to allow login
            if (data.success) {
                // If login is successful, redirect to the home page
                setFormMessage(loginForm, "success", "Login successful");
                sessionStorage.setItem('logged_in', true);
                window.location.href = "index.php";
            } else {
                // If login is unsuccessful, display an error message
                setFormMessage(loginForm, "error", "Invalid username/password combination");
            }
        })
        .catch(error => {
            console.error("Error:", error);
        });

    });

    createAccountForm.addEventListener("submit", e => {
        e.preventDefault();

        const formData = new FormData(createAccountForm);

        const xhr = new XMLHttpRequest();
        xhr.open("POST", "src/process-signup.php");
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        
        xhr.send(new URLSearchParams(formData));


    });

});