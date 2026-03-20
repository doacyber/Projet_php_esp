document.addEventListener('DOMContentLoaded', function () {

    //  Connexion
    const formLogin = document.querySelector('#formConnexion, form[action="connexion.php"]');

    if (formLogin) {
        formLogin.addEventListener('submit', function (e) {

            const loginInput = this.querySelector('input[name="mon_login"]');
            const passwordInput = this.querySelector('input[name="mon_pass"]');

            let hasError = false;
            let message = "";

            if (loginInput && loginInput.value.trim() === "") {
                message = "Veuillez saisir votre identifiant.";
                loginInput.focus();
                hasError = true;
            }

            if (!hasError && passwordInput && passwordInput.value === "") {
                message = "Mot de passe requis.";
                passwordInput.focus();
                hasError = true;
            }

            if (hasError) {
                e.preventDefault();
                afficherErreur(message);
            }
        });
    }

    //  Menu mobile 
    const btnMenu = document.getElementById('toggleMobileMenu');
    const nav = document.getElementById('mainNav');

    if (btnMenu && nav) {
        btnMenu.addEventListener('click', function () {
            nav.classList.toggle('nav-active');
            this.classList.toggle('open');
        });
    }

});


//Affichage d'un erreur

function afficherErreur(msg) {
    
    let box = document.querySelector('.alert-js');

    if (!box) {
        box = document.createElement('div');
        box.className = 'alert-js';
        box.style.cssText = "background:#7f1d1d;color:#fff;padding:1rem;margin-bottom:1rem;border-radius:4px;";
        
        const container = document.querySelector('main');
        if (container) {
            container.prepend(box);
        }
    }

    box.textContent = msg;
}

function logWarn(msg) {
    console.warn("[form-check] " + msg);
}