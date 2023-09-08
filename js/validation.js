const validation = new JustValidate("#createAccount");

validation
    .addField("#username", [
        {
            rule: "required"
        }
    ])
    .addField("#firstname", [
        {
            rule: "required"
        }
    ])
    .addField("#surname", [
        {
            rule: "required"
        }
    ])


    .addField("#email", [
        {
            rule: "required"
        },
        {
            rule: "email"
        },
        {
            validator: (value) => () => {
                return fetch("http://localhost/PROJECT-APIGithub/api.php?validate-email", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({ email: value })
                })
                .then(response => response.json())
                .then(json => {
                    return json.available;
                });
            },
            errorMessage: "An account already exists with this email address"
        }
    ])

  


    .addField("#password", [
        {
            rule: "required"
        },
        {
            rule: "password"
        }
    ])
    .addField("#confirmpassword", [
        {
            validator: (value, fields) => {
                return value === fields["#password"].elem.value;
            },
            errorMessage: "Passwords must match!"
        }
    ])
    .onSuccess((event) => {
        document.getElementById("createAccount").submit();
    });

    