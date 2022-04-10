const rules = {
    company_name: 'required|min:4',
    company_email: 'required|email',
    company_phone: 'required|phone',
    company_document: 'required'
};

const Company = {
    create(data) {
        return axios.post('/api/v1/company', data);
    }
}

const CompanyForm = {
    form: document.getElementById('company-form'),

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
        const companyData = this.getData();
        const validator = new Validator(companyData, rules);
        const names = {
            company_name: 'Nome',
            company_email: 'Email',
            company_phone: 'Telefone',
            company_document: 'Documento'
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
    document.getElementById('company-form').reset();
}

const grid = new gridjs.Grid({
    columns: [
        'Nome',
        'Email',
        'Telefone',
        'Documento'
    ],
    server: {
        url: '/api/v1/company',
        method: 'GET',
        then: data => data.company.map(company => [company.name, company.email, company.phone, company.document])
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

grid.render(document.getElementById('company-grid'));

maskPhone('#company_phone');
maskDocument('#company_document');

Validator.useLang('pt_BR');

Validator.register('phone', (value) => {
    const mobilePhone = value.match(/^\(\d{2}\) \d{5}-\d{4}$/);
    const phone = value.match(/^\(\d{2}\) \d{4}-\d{4}$/);

    return (phone || mobilePhone);

}, 'Formato de :attribute inválido');

Validator.register('phone', (value) => {
    const mobilePhone = value.match(/^\(\d{2}\) \d{5}-\d{4}$/);
    const phone = value.match(/^\(\d{2}\) \d{4}-\d{4}$/);

    return (phone || mobilePhone);

}, 'Formato de :attribute inválido');

CompanyForm.init();

document.getElementById('company-create').onclick = async event => {

    if (CompanyForm.validate() === false) {
        return;
    }

    const loadingButton = new LoadingButton(event.currentTarget);
    const data = CompanyForm.getData();

    loadingButton.start('Adicionando...');

    Company.create({
        company: {
            name: data.company_name,
            email: data.company_email,
            phone: data.company_phone,
            document: data.company_document,
        }
    }).then(resp => {
        loadingButton.stop();
        toast('Empresa adicionada!');
        Modals[0].close();
        grid.updateConfig({
            server: {
                url: '/api/v1/company',
                then: data => data.company.map(company => [company.name, company.email, company.phone, company.document])
            },
        }).forceRender();
        formReset();
    }).catch(err => {
        const { data, status } = err.response;

        loadingButton.stop();

        if (status === 400) {
            toast(data.error.message);
        }
    });

};
