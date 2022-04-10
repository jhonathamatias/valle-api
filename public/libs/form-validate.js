const FormValidate = {
    form: null,
    fields: {},
    rules: {},

    init(formSelector, options) {
        this.form = document.querySelector(formSelector);
        this.rules = options.rules;
    },

    getFields() {
        const formFields = this.form.querySelectorAll('.form-field');
        let fields = {};

        formFields.forEach(field => {
            fields[field.name] = field;
        });

        return fields;
    },

}