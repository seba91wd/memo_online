export function rotate_anim(elem_1, elem_2) {
    let defaut = '1s';
    let time = '0.5';
    elem_1.style.transition = time + 's';
    elem_1.style.transform  = 'rotateY(0deg)';
    elem_1.style.transform = 'rotateY(90deg)';
    setTimeout(() => {
        // on remet la transition de l'element par defaut = 1s
        elem_1.style.transition = defaut;
        elem_1.classList.add('hidden');

        elem_2.style.transform = 'rotateY(90deg)';
        elem_2.classList.remove('hidden');
        elem_2.style.transition = time + 's';
        setTimeout(() => {
            elem_2.style.transform = 'rotateY(0deg)';
            setTimeout(() => {
                // on remet la transition de l'element par defaut = 1s
                elem_2.style.transition = defaut;
            }, time * 1000);
        }, 50);
    }, (time * 1000));
};