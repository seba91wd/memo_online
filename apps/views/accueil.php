<?php
// Vue accueil.php

// ------------------------------------------------------------------------------------
// Connexion utilisateur
// ------------------------------------------------------------------------------------
?>
<section id="div_connexion" class="<?php if (isset($_GET['action'])){echo 'hidden';} ?>">
    <h2>Connexion</h2>
    <?php
    if (user_is_connected()) {
        ?>
        <p>Vous etes connecté</p>
        <p>Pseudo : <?php echo $_SESSION['user']['user_pseudo'] ?></p>
        <p>E-mail : <?php echo $_SESSION['user']['user_mail'] ?></p>
        <a class="btn_deconnexion" href="./?action=deconnexion">Déconnexion</a>

        <!-- Ce bouton n'a pas d'autre utilité que d'éviter un message d'erreur en JS, il restera en display none -->
        <!-- Sa serai bien mieu de gerer ce probleme dans le JS avec un try catch -->
        <button class="hidden" id="btn_create_account">Créer mon compte</button>
        <button class="hidden" id="btn_lost_pass">Mot de passe oublié</button>
    <?php
    } else {
    ?>
        <form method="post">
            <fieldset>
                <legend><label for="user_mail">Email</label></legend>
                <input type="text" id="user_mail" name="user_mail" placeholder="mon_email@mon_domaine.com">
            </fieldset>
            <fieldset>
                <legend><label for="user_password">Mot de passe</label></legend>
                <input type="password" id="user_password" name="user_password" placeholder="Mot de passe">
            </fieldset>
            <input type="submit" value="Se connecter">
        </form>
        <button id="btn_lost_pass">Mot de passe oublié</button>
        <p>Vous n'avez pas de compte ?</p>
        <button id="btn_create_account">Créer mon compte</button>
    <?php
    }
    ?>
</section>

<?php
// ------------------------------------------------------------------------------------
// Créer une session
// ------------------------------------------------------------------------------------
if (user_is_connected()){
?>
<section>
    <h2>Créer une session</h2>
    <form method="post" enctype="multipart/form-data">

        <fieldset>
            <legend>Type de session</legend>
            <label for="public">Publique</label>
            <input type="radio" class="radio_type_session" id="public" name="session_type" value="0">
            <label for="private">Privé</label>
            <input type="radio" class="radio_type_session" id="private" name="session_type" value="1">
        </fieldset>

        <fieldset id="fieldset_session_pass" class="hidden">
            <legend>
                <label for="session_pass">Mot de passe session</label>
            </legend>
            <input type="password" id="session_pass" name="session_pass" placeholder="@_-Még@_MoT_dE_pAsS">
        </fieldset>

        <fieldset>
            <legend>
                <label for="session_name">Nom de la session</label>
            </legend>
            <input type="text" name="session_name" id="session_name" placeholder="Ma session" required>
        </fieldset>
        <input type="submit" value="Créer une session">
    </form>
</section>
<?php
}

// ------------------------------------------------------------------------------------
// Créer un utilisateur (Créer mon compte)
// ------------------------------------------------------------------------------------

if (! user_is_connected()){
?>
<section id="div_create_account" class="hidden">
    <h2>Créer mon compte</h2>
    <form method="post">
        <fieldset>
            <legend><label for="new_user_pseudo">Pseudo</label></legend>
            <input 
                type="text" 
                id="new_user_pseudo" 
                class="input_create_user"
                name="new_user_pseudo" 
                placeholder="Pseudo" 
                <?php if (isset($user_pseudo)) {echo 'value="'. $user_pseudo .'"';} ?>
            >
            <div class="div_create_user_error"></div>
        </fieldset>
        <fieldset>
            <legend><label for="new_user_mail">Adresse mail</label></legend>
            <input 
                type="email" 
                id="new_user_mail" 
                class="input_create_user"
                name="new_user_mail" 
                placeholder="mon_email@mon_domaine.com"
                <?php if (isset($user_mail)) {echo 'value="'. $user_mail .'"';} ?>
            >
            <div class="div_create_user_error"></div>
        </fieldset>
        <fieldset>
            <legend><label for="new_user_password">Mot de passe</label></legend>
            <input 
                type="password" 
                id="new_user_password" 
                class="input_create_user"
                name="new_user_password" 
                placeholder="@_-Még@_MoT_dE_pAsS"
                <?php if (isset($user_password)) {echo 'value="'. $user_password .'"';} ?>
            >
            <div class="div_create_user_error"></div>
        </fieldset>
        <fieldset>
            <legend><label for="new_user_password_conf">Confirmation mot de passe</label></legend>
            <input 
                type="password" 
                id="new_user_password_conf" 
                class="input_create_user"
                name="new_user_password_conf" 
                placeholder="@_-Még@_MoT_dE_pAsS"
                <?php if (isset($user_password_conf)) {echo 'value="'. $user_password_conf .'"';} ?>
            >
            <div class="div_create_user_error"></div>
        </fieldset>
        <fieldset>
            <legend><label for="question_captcha">Est tu humain ?</label></legend>
            <p>Quelle est la couleur du cheval blanc d'Henri IV ?</p>
            <input 
                type="text" 
                id="question_captcha" 
                class="input_create_user"
                name="question_captcha"
                placeholder="pourpre"
                <?php if (isset($question_captcha)) {echo 'value="'. $question_captcha .'"';} ?>
            >
            <div class="div_create_user_error"></div>
        </fieldset>
        <input type="submit" id="btn_create_user_submit" value="Créer mon compte">
    </form>
    <p>En t'inscrivant, tu acceptes les <a href="./apps/assets/doc/CGU.pdf" target="_blank">Conditions d'utilisation</a> et la politique de <a href="./apps/assets/doc/Politique_de_Gestion_des_Données.pdf" target="_blank">Gestion des données</a></p>
    <button id="btn_back_connexion_1">➰Retour vers Connexion</button>
</section>

<?php
} else {
    // Ce bouton (caché) ne sert qu'à éviter un message d'erreur JS
    // Reprendre ce code pour gerer le probleme dans le JS avec un try catch
    echo '<button class="hidden" id="btn_back_connexion_1">➰Retour vers Connexion</button>';
}
?>

<?php
// ------------------------------------------------------------------------------------
// Rejoindre une session
// ------------------------------------------------------------------------------------
if (user_is_connected()){
    ?>
    <section class="">
        <h2>Rejoindre une session</h2>
        <form method="post" enctype="multipart/form-data">

            <fieldset>
                <legend>
                    <label for="load_session_name">Nom de la session</label><br>
                </legend>
                <input type="text" name="load_session_name" id="load_session_name" placeholder="Ma session"><br>
            </fieldset>

            <fieldset>
                <legend>
                    <label for="load_session_pass">Mot de passe de la session</label>
                </legend>
                <input type="password" name="load_session_pass" id="load_session_pass" placeholder="Ma session"><br>
            </fieldset>

            <input type="submit" value="Rejoindre la session">
        </form>
    </section>
    <?php
};

// ------------------------------------------------------------------------------------
// Mes sessions
// ------------------------------------------------------------------------------------

if (user_is_connected()){
    ?>
    <section>
        <h2>Mes sessions</h2>
        <?php
            if ($user_session_admin != false){
                foreach($user_session_admin as $row){
                ?>
                <fieldset class="fieldset_style">
                    <legend class="fieldset_anim"><?php echo $row['session_name']; ?></legend>
                    <div class="div_ico">
                        <a href="./apps/controllers/session.php?name=<?php echo $row['session_name']; ?>"><img src="./apps/assets/img/enter.ico" title="Rejoindre la session"></a>
                        <img class="btn_new_name_session" src="./apps/assets/img/edit.ico" title="Modifier le nom de la session">
                        <img class="btn_new_pass_session" src="./apps/assets/img/cadenas.ico" title="Modifier le mot de passe de la session">
                        <img class="btn_del_session" src="./apps/assets/img/trash.ico" title="Supprimer la session">

                        <form class="hidden form_new_name_session" method="post" enctype="multipart/form-data">
                            <input type="text" class="input_new_session_name" name="input_new_session_name" placeholder="Ma session" >
                            <input type="image" src="./apps/assets/img/icon_v.png" alt="Valider le nom" title="Valider le nom">
                            <input type="hidden" name="rename_session" value="<?php echo $row['session_name']; ?>">
                        </form>

                        <form class="hidden form_new_pass_session" method="post" enctype="multipart/form-data">
                            <input type="text" class="input_new_session_pass" name="input_new_session_pass" placeholder="mY_haRdcoreP@Ss" >
                            <input type="image" src="./apps/assets/img/icon_v.png" alt="Valider le mot de passe" title="Valider le mot de passe">
                            <input type="hidden" name="change_session_pass" value="<?php echo $row['session_name']; ?>">
                        </form>
                        
                        <form class="hidden flex form_delete_session" method="post" enctype="multipart/form-data">
                            <p class="del_msg">Supprimer<br><?php echo $row['session_name'] ?></p>
                            <input type="image" src="./apps/assets/img/icon_v.png" alt="Valider la suppression" title="Valider la suppression">
                            <input type="hidden" name="delete_session" value="<?php echo $row['session_name']; ?>">
                        </form>
                    </div>
                </fieldset>
                <?php
                }
            }
            else {
                echo '<p>Vous n\'avez pas encors crée de session</p>';
            }
        ?>
    </section>
    <?php
}

// ------------------------------------------------------------------------------------
// Sessions rejointes
// ------------------------------------------------------------------------------------

if (user_is_connected()){
    ?>
    <section>
    <h2>Sessions rejointes</h2>
    <?php
    if ($user_session_member != false){
        foreach($user_session_member as $row){
            ?>
                <fieldset class="fieldset_style">
                    <legend class="fieldset_anim"><?php echo $row['session_name']; ?></legend>
                    <div class="div_ico">
                        <a href="./apps/controllers/session.php?name=<?php echo $row['session_name']; ?>"><img src="./apps/assets/img/enter.ico" title="Rejoindre la session"></a>
                        <img class="btn_rem_session" src="./apps/assets/img/leave.ico" title="Quitter la session">
                        <form class="hidden form_remove_session" method="post" enctype="multipart/form-data">
                            <p class="del_msg">Quitter la session<br><?php echo $row['session_name'] ?></p>
                            <input type="image" src="./apps/assets/img/icon_v.png" alt="Valider la suppression" title="Valider la suppression">
                            <input type="hidden" name="remove_session" value="<?php echo $row['session_name']; ?>">
                        </form>
                    </div>
                </fieldset>
            <?php
        }
    }
    else {
        echo '<p>Vous n\'avez rejoint aucune session</p>';
    }
    ?>

    </section>
    <?php
}


// ------------------------------------------------------------------------------------
// Mot de passe oublié
// ------------------------------------------------------------------------------------

if (! user_is_connected()){

    if ($change_pass == false){?>
    <section id="div_lost_password" class="hidden">
        <h2>Mot de passe oublié</h2>
        <form method="post">
            <fieldset>
                <legend><label for="mail_lost_pass">Adresse e-mail</label></legend>
                <input 
                    type="email" 
                    id="mail_lost_pass" 
                    class="input_create_user"
                    name="mail_lost_pass" 
                    placeholder="monmail@mondomaine.fr" 
                    <?php if (isset($user_mail)) {echo 'value="'. $user_mail .'"';} ?>
                >
                <!-- <div class="div_create_user_error"></div> -->
            </fieldset>
    
            <fieldset>
                <legend><label for="pass_lost_pseudo">Pseudo</label></legend>
                <input 
                    type="text" 
                    id="pass_lost_pseudo" 
                    class="input_create_user"
                    name="pass_lost_pseudo" 
                    placeholder="Pseudo" 
                    <?php if (isset($user_pseudo)) {echo 'value="'. $user_pseudo .'"';} ?>
                >
                <div class="div_create_user_error"></div>
            </fieldset>
            <input type="submit" id="btn_pass_lost_submit" value="Valider">
        </form>
    
        <button id="btn_back_connexion_2">➰Retour vers Connexion</button>
    </section>
    
    <?php
    } elseif ($change_pass == true){
    ?>
    <section class="">
        <h2>Modifier son mot de passe</h2>
        <form method="post">
            <fieldset>
                <legend><label for="change_user_password">Mot de passe</label></legend>
                <input 
                    type="password" 
                    id="change_user_password" 
                    class="input_create_user"
                    name="change_user_password" 
                    placeholder="@_-Még@_MoT_dE_pAsS"
                    <?php if (isset($user_password)) {echo 'value="'. $user_password .'"';} ?>
                >
            </fieldset>
            <fieldset>
                <legend><label for="change_user_password_conf">Confirmation mot de passe</label></legend>
                <input 
                    type="password" 
                    id="change_user_password_conf" 
                    class="input_create_user"
                    name="change_user_password_conf" 
                    placeholder="@_-Még@_MoT_dE_pAsS"
                    <?php if (isset($user_password_conf)) {echo 'value="'. $user_password_conf .'"';} ?>
                >
            </fieldset>
            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
            <input type="submit" id="btn_change_pass_submit" value="Valider">
        </form>
    </section>
    
    <?php
    } 
} else {
    // Ce bouton (caché) ne sert qu'à éviter un message d'erreur JS
    echo '<button class="hidden" id="btn_back_connexion_2">➰Retour vers Connexion</button>';
    };
?>