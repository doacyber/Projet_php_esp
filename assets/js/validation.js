document.addEventListener('DOMContentLoaded', function() {

    var formConn = document.getElementById('formConnexion');
    if (formConn) {
        formConn.addEventListener('submit', function(e) {
            var ok = true;

            var login = document.getElementById('login').value.trim();
            var mdp   = document.getElementById('mot_de_passe').value;

            viderErreurs(['err-login', 'err-mdp']);

            if (login.length === 0) {
                montrerErreur('err-login', 'Veuillez saisir votre login.');
                ok = false;
            }
            if (mdp.length === 0) {
                montrerErreur('err-mdp', 'Veuillez saisir votre mot de passe.');
                ok = false;
            }

            if (!ok) e.preventDefault();
        });
    }

    var formArt = document.getElementById('formArticle');
    if (formArt) {
        formArt.addEventListener('submit', function(e) {
            var ok = true;

            viderErreurs(['err-titre', 'err-desc', 'err-cat', 'err-contenu']);

            var titre = document.getElementById('titre');
            if (titre && titre.value.trim().length < 3) {
                montrerErreur('err-titre', 'Le titre doit faire au moins 3 caractères.');
                ok = false;
            }

            var desc = document.getElementById('description_courte');
            if (desc && desc.value.trim().length > 300) {
                montrerErreur('err-desc', 'Description trop longue (max 300 caractères).');
                ok = false;
            }

            var cat = document.getElementById('categorie_id');
            if (cat && cat.value == '') {
                montrerErreur('err-cat', 'Choisissez une catégorie.');
                ok = false;
            }

            var contenu = document.getElementById('contenu');
            if (contenu && contenu.value.trim().length < 10) {
                montrerErreur('err-contenu', 'Le contenu est trop court.');
                ok = false;
            }

            if (!ok) e.preventDefault();
        });

        var desc = document.getElementById('description_courte');
        if (desc) {
            desc.addEventListener('input', function() {
                var restant = 300 - this.value.length;
                var el = document.getElementById('err-desc');
                if (el) {
                    if (restant < 0) {
                        el.textContent = 'Trop long de ' + Math.abs(restant) + ' car.';
                    } else if (restant < 50) {
                        el.textContent = restant + ' caractères restants';
                        el.style.color = '#e67e22';
                    } else {
                        el.textContent = '';
                    }
                }
            });
        }
    }

    var formCat = document.getElementById('formCat');
    if (formCat) {
        formCat.addEventListener('submit', function(e) {
            var nom = document.getElementById('nom').value.trim();
            viderErreurs(['err-nom']);
            if (nom.length < 2) {
                montrerErreur('err-nom', 'Nom trop court (minimum 2 caractères).');
                e.preventDefault();
            }
        });
    }

    var formUser = document.getElementById('formUser');
    if (formUser) {
        formUser.addEventListener('submit', function(e) {
            var ok = true;
            viderErreurs(['err-prenom','err-nom','err-login','err-mdp','err-mdp2']);

            var prenom = document.getElementById('prenom');
            if (prenom && prenom.value.trim().length < 2) {
                montrerErreur('err-prenom', 'Prénom trop court.');
                ok = false;
            }

            var nom = document.getElementById('nom');
            if (nom && nom.value.trim().length < 2) {
                montrerErreur('err-nom', 'Nom trop court.');
                ok = false;
            }

            var login = document.getElementById('login');
            if (login && login.value.trim().length < 3) {
                montrerErreur('err-login', 'Login trop court (min 3 caractères).');
                ok = false;
            }

            var mdp  = document.getElementById('mot_de_passe');
            var mdp2 = document.getElementById('mdp_confirm');

            if (mdp && mdp.value.length > 0 && mdp.value.length < 6) {
                montrerErreur('err-mdp', 'Mot de passe trop court (min 6 caractères).');
                ok = false;
            }
            if (mdp && mdp2 && mdp.value !== mdp2.value) {
                montrerErreur('err-mdp2', 'Les mots de passe ne correspondent pas.');
                ok = false;
            }

            if (mdp && mdp.required && mdp.value.length === 0) {
                montrerErreur('err-mdp', 'Mot de passe obligatoire.');
                ok = false;
            }

            if (!ok) e.preventDefault();
        });
    }

});

function montrerErreur(id, msg) {
    var el = document.getElementById(id);
    if (el) el.textContent = msg;
}

function viderErreurs(ids) {
    ids.forEach(function(id) {
        var el = document.getElementById(id);
        if (el) el.textContent = '';
    });
}
