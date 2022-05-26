let MSelectInstances = null; 
let Modals = null;

function initSidenav() {
    var elems = document.querySelectorAll('.sidenav');
    var instances = M.Sidenav.init(elems);
}

function initModal() {
    var elems = document.querySelectorAll('.modal');
    Modals = M.Modal.init(elems);
}

function initSelect() {
    var elems = document.querySelectorAll('select');
    MSelectInstances = M.FormSelect.init(elems, {
        preventScrolling: false
    });
}

function initTooltip() {
    var elems = document.querySelectorAll('.tooltipped');
    var instances = M.Tooltip.init(elems);
}

function initFlat() {
    var elems = document.querySelectorAll('.fixed-action-btn');
    var instances = M.FloatingActionButton.init(elems, {
        direction: 'left'
    });
}

function toast(text) {
    M.toast({ html: `<span><strong>${text}</strong></span>` });
}

class LoadingButton {
    constructor(button) {
        this.button = button;
        this.initialHTML = button.innerHTML;
    }

    preloader(text) {
        return `<div class="preloader-wrapper ex-small active" style="margin-right: 8px">
            <div class="spinner-layer spinner-green-only">
            <div class="circle-clipper left">
                <div class="circle"></div>
            </div><div class="gap-patch">
                <div class="circle"></div>
            </div><div class="circle-clipper right">
                <div class="circle"></div>
            </div>
            </div>
        </div> <span>${text}</span>`;
    }

    start(text) {
        this.button.disabled = true;
        this.button.style.opacity = '0.5';
        this.button.innerHTML = this.preloader(text);
    }

    stop() {
        this.button.disabled = false;
        this.button.style.opacity = '1';
        this.button.innerHTML = this.initialHTML;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    initSidenav();
    initModal();
    initSelect();
    initTooltip();
    initFlat();
});