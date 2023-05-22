export function random_rotate(){
    // Retourne une valeur comprise entre -2 et 2 deg
    let rotate_random = (Math.floor(Math.random() * 5) - 2) + 'deg';
    return rotate_random;
};