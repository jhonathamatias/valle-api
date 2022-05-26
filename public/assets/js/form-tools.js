class FormTools {
    constructor(form, options) {
        const { rules, events } = options;

        this.form = form;
        this.fields = form.querySelectorAll('.form-field');
        this.rules = rules;
        this.events = events;

        this.dispatchLister();
    }

    getData() {
        let data = {};

        this.fields.forEach(element => {
            data[element.name] = element.value
        });

        return data;
    }

    getFields() {
        let fields = {};

        this.fields.forEach(field => {
            fields[field.name] = field;
        });

        return fields;
    }

    getFieldNames() {
        let names = {};

        this.fields.forEach(field => {
            names[field.name] = field.getAttribute('data-name');
        });

        return names;
    }

    validate() {
        const data = this.getData();
        const names = this.getFieldNames();
        const validator = new Validator(data, this.rules);

        validator.setAttributeNames(names);

        if (validator.fails()) {
            this.fieldInvalid(this.getFields(), validator.errors);
            return false;
        }
        this.fieldValid(this.getFields());

        return true;
    }

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
    }

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
    }

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
    }

    fieldHelpText(field, message, type = "error") {
        let parent = field.parentNode;
        let helperText = parent.querySelector('.helper-text');

        if (field instanceof HTMLSelectElement) {
            helperText = parent.parentNode.querySelector('.helper-text');
        }

        helperText.setAttribute('data-' + type, message);
    }

    addListenerMulti(element, eventNames, listener) {
        const events = eventNames.split(',');
        for (let i = 0, iLen = events.length; i < iLen; i++) {
            element.addEventListener(events[i], listener, false);
        }
    }

    dispatchLister() {
        this.fields.forEach(field => {
            this.addListenerMulti(field, this.events, event => {
                const { target } = event;

                this.validateOne(target, {
                    [target.name]: target.value,
                }, {
                    [target.name]: this.rules[target.name]
                }, {
                    [target.name]: target.getAttribute('data-name')
                });
            });
        });
    }

    reset() {
        this.form.reset();
    }

    submit(callback) {
        this.form.onsubmit = e => {
            e.preventDefault();
            
            callback(this.getData(), this.validate());
        }
    }
}