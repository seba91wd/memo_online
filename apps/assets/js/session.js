// ------------------------------------------------------------------------------------
//
// LISTE DES TACHES
// 
// #1 - Menu navbar lv1
//
// #2 - Menu navbar lv2
//
//
//
// ------------------------------------------------------------------------------------

// ------------------------------------------------------------------------------------
// Inclinaison aléatoire des Mémo
// ------------------------------------------------------------------------------------

import { random_rotate } from './export/random_rotate.js';
let section = document.querySelectorAll('section');

for(let i = 0; i < section.length; i++){

    // Inclinaison aléatoire des Mémo
    section[i].style.transform = 'rotate(' + random_rotate() + ')';

    // Inclinaison aléatoire des Mémo au passage du curseur
    if(section[i].addEventListener('mouseover', function () {
        section[i].style.transform = 'rotate(' + random_rotate() + ')';
    }));
}

// ------------------------------------------------------------------------------------
// Rotation Mémo au clic, affichage des details et affichage edition 
// ------------------------------------------------------------------------------------

import { rotate_anim } from './export/rotate_anim.js';
let rotate_elem = document.querySelectorAll('.rotate_elem');

for ( let i = 0; i < rotate_elem.length; i++){
    rotate_elem[i].addEventListener('click', () => {

        if (rotate_elem[i].classList.contains('section_mesage')){
            let elem_1 = rotate_elem[i]
            let elem_2 = rotate_elem[i+1].closest('section');
            rotate_anim(elem_1, elem_2)
        }
        else if (rotate_elem[i].classList.contains('btn_detail_back')){
            let elem_1 = rotate_elem[i].closest('section')
            let elem_2 = rotate_elem[i-1]
            rotate_anim(elem_1, elem_2)
        }
        else if (rotate_elem[i].classList.contains('post_btn_edit')){
            let elem_1 = rotate_elem[i].closest('section')
            let elem_2 = rotate_elem[i+1].closest('section')
            rotate_anim(elem_1, elem_2)
        }
        else if (rotate_elem[i].classList.contains('icon_x')){
            let elem_1 = rotate_elem[i].closest('section')
            let elem_2 = rotate_elem[i-1].closest('section')
            rotate_anim(elem_1, elem_2)
        };
    });
};

// ------------------------------------------------------------------------------------
// Menu navbar lv1
// ------------------------------------------------------------------------------------

let nav_btn = document.querySelectorAll('.nav_btn');
let nav_div = document.querySelectorAll('.nav_div');

for ( let i = 0; i < nav_btn.length; i++ ){
    nav_btn[i].addEventListener('click', () => {

        if ( i == 0 ){
            if ( ! nav_div[i].classList.contains('active') ){
    
                nav_btn[0].classList.add('border_rad_off');
                nav_btn[2].classList.add('border_rad_off');

                nav_div[0].classList.remove('hidden');
                nav_div[1].classList.add('hidden');
                nav_div[2].classList.add('hidden');

                nav_div[0].classList.add('active');
                nav_div[1].classList.remove('active');
                nav_div[2].classList.remove('active');
            }
            else {
                nav_btn[0].classList.remove('border_rad_off');
                nav_btn[2].classList.remove('border_rad_off');
                
                nav_div[0].classList.add('hidden');
                nav_div[1].classList.add('hidden');
                nav_div[2].classList.add('hidden');

                nav_div[0].classList.remove('active');
                nav_div[1].classList.remove('active');
                nav_div[2].classList.remove('active');
            }

        }
        else if ( i == 1 ){
            if ( ! nav_div[i].classList.contains('active') ){
    
                nav_btn[0].classList.add('border_rad_off');
                nav_btn[2].classList.add('border_rad_off');

                nav_div[0].classList.add('hidden');
                nav_div[1].classList.remove('hidden');
                nav_div[2].classList.add('hidden');
                

                nav_div[0].classList.remove('active');
                nav_div[1].classList.add('active');
                nav_div[2].classList.remove('active');
            }
            else {
                nav_btn[0].classList.remove('border_rad_off');
                nav_btn[2].classList.remove('border_rad_off');
                
                nav_div[0].classList.add('hidden');
                nav_div[1].classList.add('hidden');
                nav_div[2].classList.add('hidden');

                nav_div[0].classList.remove('active');
                nav_div[1].classList.remove('active');
                nav_div[2].classList.remove('active');
            }
        }
        else if ( i == 2 ){
            if ( ! nav_div[i].classList.contains('active') ){
    
                nav_btn[0].classList.add('border_rad_off');
                nav_btn[2].classList.add('border_rad_off');

                nav_div[0].classList.add('hidden');
                nav_div[1].classList.add('hidden');
                nav_div[2].classList.remove('hidden');
                

                nav_div[0].classList.remove('active');
                nav_div[1].classList.remove('active');
                nav_div[2].classList.add('active');
            }
            else {
                nav_btn[0].classList.remove('border_rad_off');
                nav_btn[2].classList.remove('border_rad_off');
                
                nav_div[0].classList.add('hidden');
                nav_div[1].classList.add('hidden');
                nav_div[2].classList.add('hidden');

                nav_div[0].classList.remove('active');
                nav_div[1].classList.remove('active');
                nav_div[2].classList.remove('active');
            }
        }

        if ( nav_div[i].classList.contains('active') ){
            nav_div[i].closest('header').classList.add('shadow');
        }
        else {
            nav_div[i].closest('header').classList.remove('shadow');
        }
    })
}

// ------------------------------------------------------------------------------------
// Menu navbar lv2
// ------------------------------------------------------------------------------------

// BTN Créer un Mémo
let btn_create = document.querySelector('#btn_create');
let div_section_form = document.querySelector('#div_section_form')
let section_form = document.querySelector('#section_form');

btn_create.addEventListener('click', () => {
    if (section_form.classList.contains('hidden')){
        div_section_form.style.height = "0";
        setTimeout(() => {
            
            div_section_form.style.height = "330px";
            section_form.style.marginLeft = '-200%';
            section_form.classList.remove('hidden');
            setTimeout(() => {
                section_form.style = ''
            }, 50);
        }, 50);
    }
    else {
        div_section_form.style.height = "0";
        section_form.style.marginLeft = '-200%';
        setTimeout(() => {
            section_form.classList.add('hidden');
        }, 500);
    }
})

let cancel_create_post = document.querySelector('#cancel_create_post');
cancel_create_post.addEventListener('click', () => {
    div_section_form.style.height = "0";
    section_form.style.marginLeft = '-200%';
    setTimeout(() => {
        section_form.classList.add('hidden');
    }, 500);
})

// ------------------------------------------------------------------------------------
// Animation des boutons nécessitants une confirmation d'action
// ------------------------------------------------------------------------------------

let btn_with_conf = document.querySelectorAll('.btn_with_conf');
let div_confimation = document.querySelectorAll('.div_confimation');

for (let i = 0; i < btn_with_conf.length; i++){
    btn_with_conf[i].addEventListener('click', () => {
        div_conf_anim(i);
    });
};

function div_conf_anim(i){
    if (btn_with_conf[i].id == 'btn_load_session'){
        if (div_confimation[i].classList.contains('active')){
            div_confimation[i].classList.remove('active');
            div_confimation[i].style.width = '0px';
        }
        else {
            div_confimation[i].classList.add('active');
            div_confimation[i].style.width = '250px';
        };
    }
    else if (btn_with_conf[i].id == 'btn_bg_img'){
        if (div_confimation[i].classList.contains('active')){
            div_confimation[i].classList.remove('active');
            div_confimation[i].style.width = '0px';
        }
        else {
            div_confimation[i].classList.add('active');
            div_confimation[i].style.width = '130px';
        };
    }
    else if (btn_with_conf[i].id == 'btn_new_session_pass'){
        if (div_confimation[i].classList.contains('active')){
            div_confimation[i].classList.remove('active');
            div_confimation[i].style.width = '0px';
        }
        else {
            div_confimation[i].classList.add('active');
            div_confimation[i].style.width = '224px';
        };
    }
    else {
        if (div_confimation[i].classList.contains('active')){
            div_confimation[i].classList.remove('active');
            div_confimation[i].style.width = '0px';
        }
        else {
            div_confimation[i].classList.add('active');
            div_confimation[i].style.width = '27px';
        };
    };
};

// ------------------------------------------------------------------------------------
// BTN Donner des droits aux membres de la session
// ------------------------------------------------------------------------------------

let btn_management_right = document.querySelector('#btn_management_right')
let right_user_table = document.querySelector('#right_user_table')

btn_management_right.addEventListener('click', () => {
    if (right_user_table.classList.contains('hidden')){
        right_user_table.classList.remove('hidden');
        right_user_table.classList.add('active');
        right_user_table.style.height = '0px';
        right_user_table.style.padding = '0px';
        setTimeout(() => {
            right_user_table.style.height = '250px';
            right_user_table.style.padding = '6px';
        }, 10);

    }
    else {
        right_user_table.style.height = '0px';
        right_user_table.style.padding = '0px';
        setTimeout(() => {
            right_user_table.classList.add('hidden')
            right_user_table.classList.remove('active');
        }, 1000);
    }
})

// BTN Appliquer (a faire)
// doit afficher le tableau des droits apres l'actualisation (au lieu d'actualiser la page)


// ------------------------------------------------------------------------------------
// Background-color (balise <select> des mémo)
// ------------------------------------------------------------------------------------

let select_color = document.querySelectorAll('.select_color');
const color_array = ['color_1', 'color_2', 'color_3', 'color_4', 'color_5'];

for ( let i = 0; i < select_color.length; i++){
    select_color[i].addEventListener('change', () => {

        let select = select_color[i];
        
        // Selection de la balise parente "section"
        let section = select_color[i].closest('section');

        // Suppression des classe color_[y] active
        for ( let y = 0; y < color_array.length; y++ ){
            try {
                select.classList.remove(color_array[y]);
                section.classList.remove(color_array[y]);
            }
            catch (error) {
                // console.log(error);
            }
        }
        // Ajoute de la classe sélectionnée sur la balise "select"
        select.classList.add(select.value);
        
        // Ajoute de la classe sélectionnée sur la balise "section"
        section.classList.add(select.value)
        
    })
}

// ------------------------------------------------------------------------------------
// Changement de background-image (utilisateur)
// ------------------------------------------------------------------------------------

let body = document.querySelector('body');

// Verification de l'éxistance de la variable user_bg (envoyé depuis le controler de session.php)
if (typeof user_bg !== 'undefined') {
    body.style = 'background: url("' + user_bg + '") center center / cover no-repeat fixed';
}

// Vidage du cache pour le rechargement de la nouvel image (background)
let btn_bg_img_submit = document.querySelector('#btn_bg_img_submit')
btn_bg_img_submit.addEventListener('click', () => {
    location.reload(true);
})

// ------------------------------------------------------------------------------------
// BTN Filtre d'affichage
// ------------------------------------------------------------------------------------

let btn_filter = document.querySelector('#btn_filter')
let div_filter = document.querySelector('#div_filter')

btn_filter.addEventListener('click', () => {
    if (div_filter.classList.contains('hidden')){
        div_filter.classList.remove('hidden');
        div_filter.classList.add('active');
        div_filter.style.height = '0px';
        div_filter.style.padding = '0px';
        setTimeout(() => {
            div_filter.style.height = '130px';
            div_filter.style.padding = '6px';
        }, 10);

    }
    else {
        div_filter.style.height = '0px';
        div_filter.style.padding = '0px';

        setTimeout(() => {
            div_filter.classList.add('hidden')
            div_filter.classList.remove('active')

        }, 1000);
    }
})

// ------------------------------------------------------------------------------------
// Filtre d'affichage des Mémo
// ------------------------------------------------------------------------------------

// Opacité si décoché
let filter = document.querySelectorAll('.filter')
for (let i = 0; i < filter.length; i++){
    filter[i].addEventListener('change', () => {
        if (filter[i].id == 'filter_simple'){
            let section = document.querySelectorAll('.statut_0');
            for (let y = 0; y < section.length; y++){
                if (filter[i].checked){
                    section[y].style.opacity = '1';
                }
                else {
                    section[y].style.opacity = '0.5';
                }
            }
        }
        else if (filter[i].id == 'filter_valide'){
            let section = document.querySelectorAll('.statut_1');
            for (let y = 0; y < section.length; y++){
                if (filter[i].checked){
                    section[y].style.opacity = '1';
                }
                else {
                    section[y].style.opacity = '0.5';
                }
            }
        }
        else if (filter[i].id == 'filter_archive'){
            let section = document.querySelectorAll('.statut_2');
            for (let y = 0; y < section.length; y++){
                if (filter[i].checked){
                    section[y].style.opacity = '1';
                }
                else {
                    section[y].style.opacity = '0.5';
                }
            }
        }
    });
};

function filter_update(data) {
    // Modification asynchrone de la bdd
    $.ajax({
        url: 'function_async.php',
        type: 'POST',
        data: data,
        success: function(response) {
            // console.log(response);
        },
        error: function(error) {
            // console.error(error);
        }
    });
}

// Appliquer les modifications d'affichage
// Les variables JS "user_id" et "session_id" sont récupérés depuis le contrôler de session.php
let btn_filter_valid = document.querySelector('#btn_filter_valid')
btn_filter_valid.addEventListener('click', () => {

    let data = {
        'user_id': user_id,
        'session_id': session_id
    };

    for (let i = 0; i < filter.length; i++){
        if (filter[i].id == 'filter_simple'){
            let section = document.querySelectorAll('.statut_0');
            if (filter[i].checked){
                for (let y = 0; y < section.length; y++){
                    section[y].classList.remove('hidden');
                    data['memo_simple'] = '1';
                }
            }
            else {
                for (let y = 0; y < section.length; y++){
                    section[y].classList.add('hidden');
                    data['memo_simple'] = '0';
                }
            }
        }
        if (filter[i].id == 'filter_valide'){
            let section = document.querySelectorAll('.statut_1');
            if (filter[i].checked){
                for (let y = 0; y < section.length; y++){
                    section[y].classList.remove('hidden');
                    data['memo_valide'] = '1';
                }
            }
            else {
                for (let y = 0; y < section.length; y++){
                    section[y].classList.add('hidden');
                    data['memo_valide'] = '0';
                }
            }
        }
        if (filter[i].id == 'filter_archive'){
            let section = document.querySelectorAll('.statut_2');
            if (filter[i].checked){
                for (let y = 0; y < section.length; y++){
                    section[y].classList.remove('hidden');
                    data['memo_archive'] = '1';
                }
            }
            else {
                for (let y = 0; y < section.length; y++){
                    section[y].classList.add('hidden');
                    data['memo_archive'] = '0';
                }
            }
        }
    }
    filter_update(data);
})

for (let x = 0; x < section.length; x++){
    for (let z = 0; z < filter.length; z++){
        if (filter[z].id == 'filter_simple'){
            if (! filter[z].checked){
                if (section[x].classList.contains('statut_0')){
                    section[x].classList.add('hidden')
                };
            };
        };
        if (filter[z].id == 'filter_valide'){
            if (! filter[z].checked){
                if (section[x].classList.contains('statut_1')){
                    section[x].classList.add('hidden')
                };
            };
        };
        if (filter[z].id == 'filter_archive'){
            if (! filter[z].checked){
                if (section[x].classList.contains('statut_2')){
                    section[x].classList.add('hidden')
                };
            };
        };
    };
};

// ------------------------------------------------------------------------------------
// BTN Mettre le chat dehors
// ------------------------------------------------------------------------------------

function display_cat_update(data) {
    // Modification asynchrone de la bdd
    $.ajax({
        url: 'function_async.php',
        type: 'POST',
        data: data,
        success: function(response) {
            // console.log(response);
        },
        error: function(error) {
            // console.error(error);
        }
    });
}

let btn_cat_display = document.querySelector('#btn_cat_display');
let div_oneko = document.querySelector('#oneko');

btn_cat_display.addEventListener('click', () => {
    let data = {
        'user_id': user_id,
        'session_id': session_id
    };

    if (div_oneko.classList.contains('hidden')){
        div_oneko.classList.remove('hidden');
        btn_cat_display.innerHTML = '<img id="cat_png" src="../assets/img/cat.png" alt="Chat"> Sortir le chat'
        data['display_cat'] = '1';
    }
    else {
        btn_cat_display.innerHTML = '<img id="cat_png" src="../assets/img/cat.png" alt="Chat"> Rentrer le chat'
        div_oneko.classList.add('hidden');
        data['display_cat'] = '0';
    };
    display_cat_update(data)
});

// Affiche || cache le chat au chargement de la page
if (btn_cat_display.textContent == "Rentrer le chat"){
    div_oneko.classList.add('hidden')
}
else{
    div_oneko.classList.remove('hidden')
}
