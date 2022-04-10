const rules = {
    user_name: 'required|min:4',
    user_email: 'required|email',
    user_password: 'required|min:6',
    user_type_id: 'required'
};

const User = {
    create(data) {
        return axios.post('/api/v1/users', data);
    }
}

const UserForm = {
    form: document.getElementById('user-form'),

    init() {
        const fields = this.form.querySelectorAll('.form-field');

        fields.forEach(field => {
            this.addListenerMulti(field, 'blur,change', event => {
                const { target } = event;

                this.validateOne(target, {
                    [target.name]: target.value, target
                }, {
                    [target.name]: rules[target.name]
                }, {
                    [target.name]: target.getAttribute('data-name')
                });
            });
        });
    },

    addListenerMulti(element, eventNames, listener) {
        const events = eventNames.split(',');
        for (let i = 0, iLen = events.length; i < iLen; i++) {
            element.addEventListener(events[i], listener, false);
        }
    },

    getData() {
        const fields = this.form.querySelectorAll('.form-field');

        let data = {};

        fields.forEach(element => {
            data[element.name] = element.value
        });

        return data;
    },

    getFields() {
        const formFields = this.form.querySelectorAll('.form-field');
        let fields = {};

        formFields.forEach(field => {
            fields[field.name] = field;
        });

        return fields;
    },

    validate() {
        const userData = this.getData();
        const validator = new Validator(userData, rules);
        const names = {
            user_name: 'Nome',
            user_email: 'Email',
            user_password: 'Senha',
            user_type_id: 'Tipo de usúario'
        };

        validator.setAttributeNames(names);

        if (validator.fails()) {
            this.fieldInvalid(this.getFields(), validator.errors);
            return false;
        }
        this.fieldValid(this.getFields());

        return true;
    },

    validateOne(element, data, rule, name) {
        const validator = new Validator(data, rule);
        const field = {
            [element.name]: element
        };

        validator.setAttributeNames(name);

        if (validator.fails()) {
            this.fieldInvalid(field, validator.errors);
            return false;
        }

        this.fieldValid(field);
        return true;
    },

    fieldInvalid(fields, { errors }) {
        if (!errors) {
            return;
        }

        for (const key in errors) {
            fields[key].classList.add('invalid');

            if (fields[key] instanceof HTMLSelectElement) {
                fields[key].parentNode.classList.add('invalid');
            }

            this.fieldHelpText(fields[key], errors[key][0]);
        }
    },

    fieldValid(fields) {
        if (!fields) {
            return;
        }

        for (const key in fields) {
            fields[key].classList.remove('invalid');
            fields[key].classList.add('valid');


            if (fields[key] instanceof HTMLSelectElement) {
                fields[key].parentNode.classList.remove('invalid');
                fields[key].parentNode.classList.add('valid');
            }
            this.fieldHelpText(fields[key], '', 'success');
        }
    },

    fieldHelpText(field, message, type = "error") {
        let parent = field.parentNode;
        let helperText = parent.querySelector('.helper-text');

        if (field instanceof HTMLSelectElement) {
            helperText = parent.parentNode.querySelector('.helper-text');
        }

        helperText.setAttribute('data-' + type, message);
    }
}

const formReset = () => {
    document.getElementById('user-form').reset();
}

const grid = new gridjs.Grid({
    columns: [
        {
            id: 'id',
            formatter: (cell) => gridjs.html('<div class="avatar"><img width="30" src="/assets/img/user.png" alt=""></div>')
        },
        'Nome',
        'Email',
        {
            id: 'id',
            name: 'Ações',
            formatter: (cell) => gridjs.html(`
                <a class="btn-flat waves-effect" disabled><i class="material-icons">mode</i></a>
            `)
        }
    ],
    server: {
        url: '/api/v1/users',
        then: data => data.users.map(user => [user.id, user.name, user.email, user.id])
    },

    pagination: true,
    search: {
        server: {
            url: (prev, keyword) => `${prev}?name=${keyword}`
        }
    },
    language: {
        'search': {
            'placeholder': 'Pesquise por nome'
        },
        'pagination': {
            previous: '<<',
            next: '>>',
            showing: 'Exibindo',
            to: 'a',
            of: 'de',
            results: 'resultados'
        }
    },
    className: {
        pagination: 'pagination'
    }
});

grid.render(document.getElementById('user-grid'));

Validator.useLang('pt_BR');

UserForm.init();

document.getElementById('user-create').onclick = async event => {

    if (UserForm.validate() === false) {
        return;
    }

    const loadingButton = new LoadingButton(event.currentTarget);
    const data = UserForm.getData();

    data.user_type_id = Number(data.user_type_id);

    loadingButton.start('Criando...');

    User.create(data).then(resp => {
        loadingButton.stop();
        toast('Usúario criado!');
        Modals[0].close();
        grid.updateConfig({
            server: {
                url: '/api/v1/users',
                then: data => data.users.map(user => [user.id, user.name, user.email, user.id])
            },
        }).forceRender();
    }).catch(err => {
        const { data, status } = err.response;

        loadingButton.stop();

        if (status === 400) {
            toast(data.error.message);
        }
    });

};
