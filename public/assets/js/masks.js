
function maskPhone(selector) {
    const mobilePhone = {
        mask: '(00) 00000-0000'
    };
    
    const phone = {
        mask: '(00) 0000-00000'
    };

    let instance = new IMask(document.querySelector(selector), mobilePhone);

    instance.on('accept', () => {
        let value = instance.unmaskedValue;

        if (value.length === 10) {
            instance.updateOptions(phone);
    
            return;
        }
    
        instance.updateOptions(mobilePhone);
    });

    return instance;
}

function maskDocument(selector) {
    let instance = new IMask(document.querySelector(selector), {
        mask: '00.000.000/0000-00'
    });

    return instance;
}