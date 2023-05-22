<?php
// Vue session.php

// Title de page
$title = $session_info['session_name'];
// style de la page
$style = '../assets/styles/session.css';

include '../inc/head.php';

?>
<header>
    <h1><?php echo $session_info['session_name'] ?></h1>
    <nav>
        <div>
            <button id="btn_post_it" class="nav_btn">MÉMO</button>
            <button id="btn_session" class="nav_btn">SESSION</button>
            <button id="btn_option" class="nav_btn">OPTION</button>
        </div>

        <div id="div_post_it" class="nav_div hidden">
            <button id="btn_create">➕Créer un Mémo</button>

            <div class="<?php if (user_have_right($user_info, "user_rule")){echo 'flex';}else{echo 'hidden';} ?>">
                <button class="btn_with_conf">💔Supprimer tous les Mémo</button>
                <div class="div_confimation">
                    <form method="post">
                        <input type="image" src="../assets/img/icon_v.png" alt="Valider">
                        <?php if ( user_have_right($user_info, "user_rule") ){ ?>
                        <input type="hidden" name="delete_all_post">
                        <?php } ?>
                    </form>
                </div>
            </div>

        </div> <!-- div_post_it -->

        <div id="div_session" class="nav_div hidden">
            <div id="div_management_right" class="<?php if (user_have_right($user_info, "user_rule")){echo '';}else{echo 'hidden';} ?>">
                <button id="btn_management_right">Donner des droits aux membres de la session</button>

                <div id="right_user_table" class="hidden border_table">

                    <div class="flex">
                        <div class="border_table width_20"><p>Pseudo</p></div>
                        <div class="border_table width_20"><p>Créer</p></div>
                        <div class="border_table width_20"><p>Modifier</p></div>
                        <div class="border_table width_20"><p>Supprimer</p></div>
                        <div class="border_table width_20"><p>Valider</p></div>
                    </div>

                    <form method="post">
                        <?php
                        if (user_have_right($user_info, "user_rule")){

                        if (is_array($member_info)){
                            foreach($member_info as $row){
                        ?>
                        <div class="flex">

                            <div class="border_table width_20">
                                <p><?php echo $row['user_pseudo'] ?></p>
                            </div>

                            <div class="border_table width_20">
                                <input type="checkbox" name="create_right[<?php echo $row['user_id'] ?>]" <?php if ($row['create_post'] == 1){echo 'checked';} ?>>
                            </div>

                            <div class="border_table width_20">
                                <input type="checkbox" name="edit_right[<?php echo $row['user_id'] ?>]" <?php if ($row['edit_post'] == 1){echo 'checked';} ?>>
                            </div>

                            <div class="border_table width_20">
                                <input type="checkbox" name="delete_right[<?php echo $row['user_id'] ?>]" <?php if ($row['delete_post'] == 1){echo 'checked';} ?>>
                            </div>

                            <div class="border_table width_20">
                                <input type="checkbox" name="valide_right[<?php echo $row['user_id'] ?>]" <?php if ($row['valide_post'] == 1){echo 'checked';} ?>>
                            </div>
                        </div>
                        <?php 
                            }; // end foreach 
                        } // end if (is_array($member_info)) 
                        ?>
                        <div class="flex">
                            <input id="btn_update_user_right" class="border_table width_50" type="submit" name="update_user_right" value="Appliquer">
                            <button id="btn_reset_update_right" class="border_table width_50">Annuler</button>
                        </div>
                        <?php
                        };
                        ?>
                    </form>
                </div>
            </div> 

            <?php 
            if (user_have_right($user_info, "user_rule")){ 
            ?>
            <div class="flex">
                <button id="btn_new_session_pass" class="btn_with_conf">Modifier le mot de passe de la session</button>
                <div class="div_confimation">
                    <form method="post" class="flex">
                        <input type="text" name="new_session_pass">
                        <input type="image" src="../assets/img/icon_v.png" alt="Valider">
                    </form>
                </div>
            </div>
            <?php
            };
            ?>

            <div class="flex">
                <button id="btn_load_session" class="btn_with_conf">Charger une session</button>
                <div class="div_confimation">
                    <form method="post" class="flex">
                        <input type="text" name="name">
                        <input type="image" src="../assets/img/icon_v.png" alt="Valider">
                    </form>
                </div>
            </div>
            
            <div class="flex">
                <button id="btn_leave_session" class="btn_with_conf">Quitter la session</button>
                <div class="div_confimation">
                    <a href="../../"><img src="../assets/img/icon_v.png" alt="Valider"></a>
                </div>
            </div>

        </div> <!-- div_session -->

        <div id="div_option" class="nav_div hidden">

            <div class="flex">
                <button id="btn_bg_img" class="btn_with_conf">Choisir le fond</button>
                <div class="div_confimation">
                <form method="post" id="div_bg_img_browse" enctype="multipart/form-data">
                    <label id="btn_bg_img_browse" for="bg_img">Parcourir</label>
                    <input id="bg_img" type="file" name="image"accept=".jpg, .jpeg, .png, .gif">
                    <input id="btn_bg_img_submit" name="bg_img_submit" type="image" src="../assets/img/icon_v.png" alt="Valider">
                </form>
                </div>
            </div>

            <div>
                <button id="btn_filter" class="nav_btn_lv1">Filtre d'affichage</button>
                <div id="div_filter" class="border_table hidden">

                    <div class="flex">
                        <div class="border_table width_33"><p>Simple</p></div>
                        <div class="border_table width_33"><p>Valider</p></div>
                        <div class="border_table width_33"><p>Archiver</p></div>
                    </div>

                    <div id="div_filter_case" class="flex">
                        <div class="border_table width_33">
                            <input id="filter_simple" class="filter" type="checkbox" <?php echo $checked_filter_simple ?>>
                        </div>
                        <div class="border_table width_33">
                            <input id="filter_valide" class="filter" type="checkbox" <?php echo $checked_filter_valide ?>>
                        </div>
                        <div class="border_table width_33">
                            <input id="filter_archive" class="filter" type="checkbox" <?php echo $checked_filter_archive ?>>
                        </div>
                    </div>

                    <div class="border_table">
                        <button id="btn_filter_valid">Appliquer</button>
                    </div>
                </div>
            </div>
            
            <button class="nav_btn_lv1" id="btn_cat_display"><img id="cat_png" src="../assets/img/cat.png" alt="Chat"><?php echo $btn_cat_display ?></button>
        </div> <!-- div_option -->
    </nav>
</header>

<main>
    <div id="div_section_form">
        <?php
        // Affichage du formulaire
        ?>
        <section id="section_form" class="color_1 hidden">
            <form method="post">
                <?php if (user_have_right($user_info, "create_post")) { ?>
                    <h3 class="hidden">w3c_depandancy</h3>
                    <input class="form_title bold" name="post_title" type="text" placeholder="Titre">
                    <textarea class="form_text" name="post_mesage"></textarea>
                <?php } else { ?>
                    <h2>😒Tu n'a pas le droit🤐</h2>
                    <p>
                        Pour créer des nouveaux Mémo,
                        demande le droit à l'administrateur de la session.
                    </p>
                <?php } ?>
    
                <?php // la div form_btn est séparée de la condition "if" pour éviter une erreur JS ?>
                <div class="form_btn <?php if (!user_have_right($user_info, "create_post")) {echo 'hidden';} ?>">
                    <select class="select_color btn_xv" name="post_color">
                        <option class="color_1" value="color_1" selected="selected">Couleur 1</option>
                        <option class="color_2" value="color_2">Couleur 2</option>
                        <option class="color_3" value="color_3">Couleur 3</option>
                        <option class="color_4" value="color_4">Couleur 4</option>
                        <option class="color_5" value="color_5">Couleur 5</option>
                    </select>
                    <div class="icon_xv">
                        <img id="cancel_create_post" src="../assets/img/icon_x.png" alt="Annuler">
                        <input type="image" src="../assets/img/icon_v.png" alt="Valider">
                        <?php
                        if (user_have_right($user_info, "create_post")) {
                            // Si l'utilisateur dispose du droit 'create_post == 1'
                            // on autorise le formulaire a envoyer '$_POST[create_post]'
                        ?>
                            <input type="hidden" name="create_post">
                        <?php } ?>
                    </div>
                </div>
            </form>
        </section>
    </div><!-- div_section_form -->

    <div id="div_section_post">
        <?php
        // Affichage des données de la base
        if ($list_post != false) {
            for ($i = 0; $i < count($list_post); $i++) {
            ?>
                <section class="section_mesage rotate_elem <?php echo $list_post[$i]['post_color'] . ' ' . 'statut_' . $list_post[$i]['post_statut'] ?>">
                    <div class="img_bg"></div>
                    <h2><?php echo $list_post[$i]['post_title'] ?></h2>
                    <p><?php echo $list_post[$i]['post_mesage'] ?></p>
                </section>

                <section class="section_detail hidden <?php echo $list_post[$i]['post_color'] ?>">
                    <button class="btn_detail_back rotate_elem">➰Retour</button>
                    <p>Ecrit par : <?php echo $list_post[$i]['user_pseudo'] ?></p>
                    <p>Ecrit le : <?php echo $list_post[$i]['post_date_fr'] ?></p>
                    <p>MaJ le : <?php echo $list_post[$i]['post_date_update_fr'] ?></p>
                    <div class="mesage_btn">
                        <?php if (user_have_right($user_info,"edit_post")){ ?>
                        <button class="post_btn_edit rotate_elem">📝Modifier le contenu</button>
                        <form method="post">
                            <input type="hidden" name="btn_valid_post" value="<?php echo $list_post[$i]['post_id'] ?>">
                            <input type="submit" value="✔Valider le Mémo">
                        </form>
                        <?php } ?>

                        <?php if (user_have_right($user_info, "user_rule")){ ?>
                        <form method="post">
                            <input type="hidden" name="btn_archive_post" value="<?php echo $list_post[$i]['post_id'] ?>">
                            <input type="submit" value="📚Archiver">
                        </form>
                        <?php } ?>

                        <?php if (user_have_right($user_info, "delete_post")){ ?>
                        <form method="post">
                            <button>💔Supprimer</button>
                            <input type="hidden" name="delete_post" value="<?php echo $list_post[$i]['post_id'] ?>">
                        </form>
                        <?php } ?>
                    </div>
                </section>


                <section class="section_edit hidden <?php echo $list_post[$i]['post_color'] ?>">
                    <form method="post">
                        <input class="form_title bold" name="post_title" type="text" placeholder="Titre" value="<?php echo $list_post[$i]['post_title'] ?>">
                        <textarea class="form_text" name="post_mesage"><?php echo strip_tags($list_post[$i]['post_mesage'], "<br />") ?></textarea>

                        <div class="form_btn">
                            <select class="select_color btn_xv" name="post_color">
                                <option class="color_1" value="color_1" selected="selected">Couleur 1</option>
                                <option class="color_2" value="color_2">Couleur 2</option>
                                <option class="color_3" value="color_3">Couleur 3</option>
                                <option class="color_4" value="color_4">Couleur 4</option>
                                <option class="color_5" value="color_5">Couleur 5</option>
                            </select>
                            <div class="icon_xv">
                                <img class="icon_x rotate_elem" src="../assets/img/icon_x.png" alt="Annuler">
                                <input type="image" src="../assets/img/icon_v.png" alt="Valider">
                                <?php
                                if (user_have_right($user_info, "edit_post")) {
                                    // Si l'utilisateur dispose du droit 'edit_post == 1'
                                    // on autorise le formulaire a envoyer '$_POST[edit_post]'
                                ?>
                                    <input type="hidden" name="edit_post" value="<?php echo $list_post[$i]['post_id'] ?>">
                                <?php } ?>
                            </div>
                        </div>
                    </form>
                </section>
            <?php
            };
        }
        else {
            ?>
            <section class="section_mesage color_1">
                <h2>Créer mon 1er Mémo</h2>
                <p>
                    Cette session est vide,<br>
                    - Click sur "MÉMO"<br>
                    - Click sur "➕Créer un Mémo"<br>
                    - Donnes un "titre" a ton Mémo<br>
                    - Retranscris tes idées<br>
                    - Tu peux choisir une couleur<br>
                    - Click sur -> <input type="image" src="../assets/img/icon_v.png" alt="Valider">
                </p>
            </section>
            <?php
        }
        ?>
    </div>
</main>

<footer>
    <div>
        <?php
        if ($msg !== ""){
            echo $msg;
        };
        ?>
    </div>
</footer>

<script type="module" src="../assets/js/session.js"></script>
<script src="../assets/js/oneko.js"></script>

<?php
// Ces lignes de code JS permettent de récupérer le nom de l'image de l'utilisateur
// Définir la variable bg en tant que propriété de l'objet window
// echo '<script>window.user_bg = "' . $bg . '";</script>';

// Appel de la fonction load_bg_image depuis le fichier session.js avec user_bg en tant qu'argument

// echo '<script type="module">import { load_bg_image } from "../assets/js/session.js"; load_bg_image(window.user_bg);</script>';

// debug
// include '../assets/functions/global_debug.php';

include '../inc/foot.php';
?>