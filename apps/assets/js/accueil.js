// *********************************************************************************************
//
// LISTE DES TACHES
// 
// #1 - backgroundColor et Inclinaison aléatoire
//
// #2 - Rotation des formulaires connexion / créer un compte
//
// #3 - Affichage du mot de passe si la nouvelle session est privé
//
// #4 - Controle des formulaire et affichage des messages d'erreur
//
// #5 - Controle des formulaire et affichage des messages d'erreur
//
//
//
//
//
// *********************************************************************************************

// #1 - backgroundColor et Inclinaison aléatoire

import { random_bg } from './export/random_bg.js';
import { random_rotate } from './export/random_rotate.js';
import { rotate_anim } from './export/rotate_anim.js';

let section = document.querySelectorAll('section');

for(let i = 0; i < section.length; i++){

    // backgroundColor aléatoire sur le reste des Mémo
    section[i].style.backgroundColor = random_bg();

    // Inclinaison aléatoire des Mémo
    section[i].style.transform = 'rotate(' + random_rotate() + ')';

    // Inclinaison aléatoire des Mémo au passage du curseur
    if(section[i].addEventListener('mouseover', () => {
        section[i].style.transform = 'rotate(' + random_rotate() + ')';
    }));
}

// *********************************************************************************************

// #2 - Rotation des formulaires connexion / créer un compte

let btn_create_account = document.querySelector('#btn_create_account');
let btn_back_connexion_1 = document.querySelector('#btn_back_connexion_1');

let div_connexion = document.querySelector('#div_connexion');
let div_create_account = document.querySelector('#div_create_account');

btn_create_account.addEventListener('click', () => {
    rotate_anim(div_connexion, div_create_account)
});
btn_back_connexion_1.addEventListener('click', () => {
    rotate_anim(div_create_account, div_connexion)
});


// *********************************************************************************************

// #3 - Rotation des formulaires connexion / mot de passe oublié

let btn_lost_pass = document.querySelector('#btn_lost_pass');
let btn_back_connexion_2 = document.querySelector('#btn_back_connexion_2');

let div_lost_password = document.querySelector('#div_lost_password');

btn_lost_pass.addEventListener('click', () => {
    rotate_anim(div_connexion, div_lost_password)
});
btn_back_connexion_2.addEventListener('click', () => {
    rotate_anim(div_lost_password, div_connexion)
});


// *********************************************************************************************

// #4 - Affichage du mot de passe si la nouvelle session est privé

let radio_type_session = document.querySelectorAll('.radio_type_session');
let div_session_pass = document.querySelector('#fieldset_session_pass');

for(let i = 0; i < radio_type_session.length; i++){
    radio_type_session[i].addEventListener('click', () => {
        if(radio_type_session[i].id == 'private'){
            // console.log('private');
            div_session_pass.classList.remove('hidden');
        }
        else{
            // console.log('public');
            div_session_pass.classList.add('hidden');
        }
    })
}

// *********************************************************************************************

// #5 - Controle des formulaire et affichage des messages d'erreur

let input_create_user = document.querySelectorAll('.input_create_user');
let div_create_user_error = document.querySelectorAll('.div_create_user_error');
let btn_create_user_submit = document.querySelector('#btn_create_user_submit');

for (let i = 0; i < input_create_user.length; i++){
    input_create_user[i].addEventListener('blur', () => {
        form_control(input_create_user[i], div_create_user_error[i]);
    });
};

function form_control(input, div_error) {

    let error = "false";

    // Controle du pseudo
    if (input.id == 'new_user_pseudo'){
        if (input.value.length < 2 ){
            div_error.innerHTML = '<p>2 caractères minimum</p>';
            error = true;
        }
        else if (input.value.length > 16 ){
            div_error.innerHTML = '<p>16 caractères maximum</p>';
            error = true;
        }
        else {
            div_error.innerHTML = 'Pseudo OK ✔';
        };
    };

    // Controle du mail
    if (input.id == 'new_user_mail'){
        const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9-]+\.[a-zA-Z.]{2,5}$/;
        if (input.value == ""){
            div_error.innerHTML = '<p>Email Vide !</p>';
            error = true;
        }
        else if (emailPattern.test(input.value) == false){
            div_error.innerHTML = '<p>ex : mon.mail@mon_domaine.fr</p>';
            error = true;
        }
        else {
            div_error.innerHTML = '<p>Email OK ✔</p>';
        };
    };

    // Controle du mot de passe
    if (input.id == 'new_user_password'){
        if (input.value.length < 6 ){
            div_error.innerHTML = '<p>6 caractères minimum</p>';
            error = true;
        }
        else if (input.value.length < 10 ){
            div_error.innerHTML = '<p>Faible mais acceptable 😐</p>';
        }
        else {
            div_error.innerHTML = '<p>Mot de passe OK ✔</p>';
        };
    }

    // Controle de la confirmation du mot de passe
    if (input.id == 'new_user_password_conf'){

        let new_user_password = document.querySelector('#new_user_password');
        if (input.value !== new_user_password.value){
            div_error.innerHTML = '<p>Confirmation du mot de passe non valide</p>';
            error = true;
        }
        else {
            div_error.innerHTML = '<p>Confirmation OK ✔</p>';
        }
    }

    // Controle du captcha
    if (input.id == 'question_captcha'){

        if (input.value.length < 1){
            div_error.innerHTML = '<p>captcha vide</p>';
            error = true;
        }
        else {
            div_error.innerHTML = '';
        }
    }

    // Ajout ou suppression de l'événement pour empêcher la soumission du formulaire
    if (error == true) {
        btn_create_user_submit.value = 'Erreur dans le formulaire ✍🏻';
        btn_create_user_submit.style.backgroundColor = '#f00';
        btn_create_user_submit.style.cursor = 'no-drop';
        // console.log("Formulaire Nok");
        } 
    else {
        btn_create_user_submit.value = 'Créer mon compte';
        btn_create_user_submit.style.backgroundColor = 'transparent';
        btn_create_user_submit.style.cursor = 'pointer';
        // console.log("Formulaire ok");
    };
};

// *********************************************************************************************

// #6 - Animation des icones "Mes sessions" et "Sessions rejointes"

let fieldset_anim = document.querySelectorAll('.fieldset_anim');
let fieldset_style = document.querySelectorAll('.fieldset_style');
let div_ico = document.querySelectorAll('.div_ico');

for (let i = 0; i < fieldset_anim.length; i++){
    fieldset_anim[i].addEventListener('click', () => {
        fieldset_animmation(fieldset_anim[i]);
    });
}

function fieldset_animmation(div_opened){

    let fieldset_style_active = div_opened.closest('.fieldset_style')
    let div_ico_active = div_opened.nextElementSibling;
    
    // Fermeture des div
    for (let y = 0; y < fieldset_anim.length; y++){
        fieldset_style[y].style = "border-color: #00000000; padding: 0;";
        div_ico[y].style = "opacity: 0; height: 0px";
    };


    // Ouverture de la div cliquée
    fieldset_style_active.style = "border-color: #000000; padding: 6px 14px 12px;";
    div_ico_active.style = "opacity: 1; height: 30px";

    // Fermeture des input type text
    ico_animation_close();

};

// *********************************************************************************************

// #7 - Affichage du formulaires ("Modifier nom de session")

let btn_new_name_session = document.querySelectorAll('.btn_new_name_session');
let btn_new_pass_session = document.querySelectorAll('.btn_new_pass_session');
let btn_del_session = document.querySelectorAll('.btn_del_session');
let btn_rem_session = document.querySelectorAll('.btn_rem_session');

for (let i = 0; i < btn_new_name_session.length; i++){
    try {
        btn_new_name_session[i].addEventListener('click', () => {
            ico_animation_close()
            ico_animation(btn_new_name_session[i], "name");
        });
    } catch (error) {
        // Aucune session crée par l'utilisateur
        // console.log(error);
    };
};

for (let i = 0; i < btn_new_pass_session.length; i++){ 
    try {
        btn_new_pass_session[i].addEventListener('click', () => {
            ico_animation_close()
            ico_animation(btn_new_pass_session[i], "pass");
        });
    } catch (error) {
        // Aucune session crée par l'utilisateur
        // console.log(error);
    };
};

for (let i = 0; i < btn_del_session.length; i++){ 
    try {
        btn_del_session[i].addEventListener('click', () => {
            ico_animation_close()
            ico_animation(btn_del_session[i], "del");
        });
    } catch (error) {
        // Aucune session crée par l'utilisateur
        // console.log(error);
    };
};

for (let i = 0; i < btn_rem_session.length; i++){ 
    try {
        btn_rem_session[i].addEventListener('click', () => {
            ico_animation_close()
            ico_animation(btn_rem_session[i], "rem");
        });
    } catch (error) {
        // Aucune session rejointe par l'utlisateur
        // console.log(error);
    };
};

function ico_animation(btn, option) {
    let form_new_name_session_active = btn.parentNode.querySelector('.form_new_name_session');
    let fieldset_style_active = btn.parentNode.parentNode;
    let input_new_session_name = fieldset_style_active.querySelector('.input_new_session_name');
    let form_new_pass_session = btn.parentNode.querySelector('.form_new_pass_session');
    let input_new_session_pass = fieldset_style_active.querySelector('.input_new_session_pass');
    let form_delete_session = btn.parentNode.querySelector('.form_delete_session');
    let form_remove_session = btn.parentNode.querySelector('.form_remove_session');

    if (option == "name"){
        if (form_new_name_session_active.classList.contains('hidden')){
            form_new_name_session_active.classList.remove('hidden');
            fieldset_style_active.style = "border-color: #000000; padding: 6px 14px 44px;";
            setTimeout(() => {
                form_new_name_session_active.style = "opacity: 1";
                input_new_session_name.style = "width: 200px !important";
            }, 10);
        }
        else {
            ico_animation_close();
            fieldset_style_active.style = "border-color: #000000; padding: 6px 14px 12px;";
        };
    }
    else if (option == "pass"){
        if (form_new_pass_session.classList.contains('hidden')){
            form_new_pass_session.classList.remove('hidden');
            fieldset_style_active.style = "border-color: #000000; padding: 6px 14px 44px;";
            setTimeout(() => {
                form_new_pass_session.style = "opacity: 1";
                input_new_session_pass.style = "width: 200px !important";
            }, 10);
        }
        else {
            ico_animation_close();
            fieldset_style_active.style = "border-color: #000000; padding: 6px 14px 12px;";
        };
    }
    else if (option == "del"){
        if (form_delete_session.classList.contains('hidden')){
            form_delete_session.classList.remove('hidden');
            fieldset_style_active.style = "border-color: #000000; padding: 6px 14px 52px;";
            setTimeout(() => {
                form_delete_session.style = "opacity: 1";
            }, 10);
        }
        else {
            ico_animation_close();
            fieldset_style_active.style = "border-color: #000000; padding: 6px 14px 12px;";
        };
    }
    else if (option == 'rem'){
        if (form_remove_session.classList.contains('hidden')){
            form_remove_session.classList.remove('hidden');
            fieldset_style_active.style = "border-color: #000000; padding: 6px 14px 52px;";
            setTimeout(() => {
                form_remove_session.style = "opacity: 1";
            }, 10);
        }
        else {
            ico_animation_close();
            fieldset_style_active.style = "border-color: #000000; padding: 6px 14px 12px;";
        };
    }
};

function ico_animation_close() {
    let form_new_name_session = document.querySelectorAll('.form_new_name_session');
    let input_new_session_name = document.querySelectorAll('.input_new_session_name');

    let form_new_pass_session = document.querySelectorAll('.form_new_pass_session');
    let input_new_session_pass = document.querySelectorAll('.input_new_session_pass');
    
    let form_delete_session = document.querySelectorAll('.form_delete_session');
    let form_remove_session = document.querySelectorAll('.form_remove_session');


    for (let i = 0; i < form_new_name_session.length; i++){
        try {
            if (form_new_name_session[i].classList.contains('hidden') == false){
                input_new_session_name[i].style = "width: 0px !important";
                form_new_name_session[i].style = "opacity: 0";
                setTimeout(() => {
                    form_new_name_session[i].classList.add('hidden');
                }, 1000);
            };
        } catch (error) {
            // console.log(error);
        }

        try {
            if (form_new_pass_session[i].classList.contains('hidden') == false){
                input_new_session_pass[i].style = "width: 0px !important";
                form_new_pass_session[i].style = "opacity: 0";
                setTimeout(() => {
                    form_new_pass_session[i].classList.add('hidden');
                }, 1000);
            };
        } catch (error) {
            // console.log(error);
        }

        try {
            if (form_delete_session[i].classList.contains('hidden') == false){
                form_delete_session[i].style = "opacity: 0";
                setTimeout(() => {
                    form_delete_session[i].classList.add('hidden');
                }, 1000);
            };
        } catch (error) {
            // console.log(error);
        }

        try {
            if (form_remove_session[i].classList.contains('hidden') == false){
                form_remove_session[i].style = "opacity: 0";
                setTimeout(() => {
                    form_remove_session[i].classList.add('hidden');
                }, 1000);
            };
        } catch (error) {
            // console.log(error);
        }
    };
};