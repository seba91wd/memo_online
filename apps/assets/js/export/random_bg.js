export function random_bg(){
    // Tableau des couleurs des Mémo (background-color)
    let bg_color = [
        '#F25CBE',
        '#50C4F2',
        '#D7F205',
        '#F2D64B',
        '#F29829'
    ];

    // Retourne une valeur comprise entre 0 et 4
    let rotate_random = Math.floor(Math.random() * 5);
    bg_color = bg_color[rotate_random]
    return bg_color
}