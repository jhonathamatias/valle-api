(function () {
    Validator.useLang('pt_BR');

    const rules = {
        email: 'required|email',
        password: 'required|min:6'
    };

    const buttonSubmit = {
        button: document.querySelector("#button-continue button"),

        loading(isLoading) {
            if (isLoading) {
                this.button.querySelector(".btn-text").style.display = 'none';
                this.button.querySelector("#loading").style.display = 'block';
                this.button.classList.add('disabled');
            } else {
                this.button.querySelector(".btn-text").style.display = 'block';
                this.button.querySelector("#loading").style.display = 'none';
                this.button.classList.remove('disabled');
            }
        }
    }

    const formFields = {
        email: document.getElementById("email"),
        password: document.getElementById("password"),

        applyError({ errors }) {
            if (!errors) {
                return;
            }

            for (const key in errors) {
                this[key].classList.add('invalid');

                this.applyMessage(this[key], errors[key][0]);
            }
        },

        applySuccess(fields) {
            if (!fields) {
                return;
            }

            for (const key in fields) {
                this[key].classList.remove('invalid');
                this[key].classList.add('valid');

                this.applyMessage(this[key], 'Tudo certo!', 'success');
            }
        },

        applyMessage(field, message, type = "error") {
            let parent = field.parentNode;
            let helperText = parent.querySelector('.helper-text');

            helperText.setAttribute('data-' + type, message);
        },

        fieldsEvents: function (events, callback) {
            events.forEach(event => {
                this.email.addEventListener(event, callback);
                this.password.addEventListener(event, callback);
            });
        }
    };

    const signInForm = {
        getUserName() {
            return formFields.email.value;
        },

        getPassword() {
            return formFields.password.value;
        },

        validate(data, rules) {
            let validator = new Validator(data.values, rules);

            validator.setAttributeNames(data.custom_names);

            if (validator.fails()) {
                formFields.applyError(validator.errors);

                return false;
            }

            formFields.applySuccess(data.values);

            return true;
        },

        submit(callback = () => { }) {
            const buttonContinue = document.querySelector("#button-continue button");

            buttonContinue.addEventListener("click", e => {
                e.preventDefault();

                callback({
                    email: this.getUserName(),
                    password: this.getPassword()
                });
            });
        }
    };

    const buttonPasswordVisibility = document.getElementById('password-visibility');

    function passwordVisibility() {
        const icon = document.querySelector('#password-visibility i');

        if (formFields.password.type === 'text') {
            formFields.password.type = 'password';
            icon.innerHTML = 'visibility';
            return;
        }

        icon.innerHTML = 'visibility_off';
        formFields.password.type = 'text';
    }

    function toastHTML(text) {
        M.toast({ html: `<span><strong>${text}</strong></span>` });
    }

    async function send(data) {
        buttonSubmit.loading(true);

        try {
            const response = await axios.post('/signin/auth', data);

            buttonSubmit.loading(false);

            window.top.location.href = window.top.location.origin + '/users';
        } catch (err) {
            buttonSubmit.loading(false);

            if (err.response.status === 401) {
                toastHTML('Usuário ou senha incorretos!');
                return;
            }

            toastHTML('Entre em contato com o suporte!');
        }

    }

    formFields.fieldsEvents(['blur'], ({ target }) => {
        const data = {
            values: {
                [target.id]: target.value
            },
            custom_names: {
                [target.id]: target.getAttribute('data-name')
            }
        }
        signInForm.validate(data, {
            [target.id]: rules[target.id]
        });
    });

    signInForm.submit(data => {
        const validated = signInForm.validate({
            values: data,
            custom_names: {
                email: 'Email',
                password: 'Senha'
            }
        }, rules);

        if (!validated) {
            return false;
        }

        send(data);
    });

    buttonPasswordVisibility.onclick = () => {
        passwordVisibility();
    }
})();

