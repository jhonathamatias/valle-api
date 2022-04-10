const formTools = new FormTools(document.getElementById('form-collaborator'), {
    rules: {
        user_id: 'required',
        occupation_id: 'required'
    },
    events: 'blur,change'
});

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

const loadingButton = new LoadingButton(document.getElementById('collaborator-create'));

async function fillUsers() {
    try {
        const { data } = await axios.get('/api/v1/users');
        const { user_id } = formTools.getFields();

        data.users.forEach(user => {
            user_id.add(new Option(`${user.name} - ${user.email}`, user.id));
        });

        M.FormSelect.init(user_id);
    } catch (err) {

    }
}

async function fillOccupations() {
    try {
        const { data } = await axios.get('/api/v1/collaborators/occupation');
        const { occupation_id } = formTools.getFields();

        data.occupation.forEach(user => {
            occupation_id.add(new Option(user.occupation, user.id));
        });

        M.FormSelect.init(occupation_id);
    } catch (err) {

    }
}

// grid.render(document.getElementById('collaborator-grid'));

Validator.useLang('pt_BR');

window.addEventListener('load', () => {
    fillUsers();
    fillOccupations();
});

formTools.submit(async (data, validate) => {
    if (validate === false) {
        return;
    }

    for (const key in data) {
        data[key] = Number(data[key]);
    }

    loadingButton.start('Criando...');

    try {
        await axios.post('/api/v1/collaborators', {
            collaborator: data
        });

        loadingButton.stop();

        toast('Colaborador criado!');
        Modals[0].close();
        formTools.reset();
    } catch(err) {
        const { data, status } = err.response;
        
        if (status === 400 ) {
            console.error(data);
            toast(data.error.message);
        }

        loadingButton.stop();
    }
});