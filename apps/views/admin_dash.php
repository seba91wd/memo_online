<?php
// Vue admin_dash.php

// Title de page
$title = "Tableau de bord";
// style de la page
$style = '../assets/styles/admin_dash.css';

include '../inc/head.php';

?>

<header>
    <h1>Tableau de bord</h1>
</header>

<main>
    <div id="user_list" class="table border">
        <p>Liste des utilisateurs</p>
        <div class="flex">
            <div class="cellule cell_6 border">user_id</div>
            <div class="cellule cell_6 border">user_pseudo</div>
            <div class="cellule cell_6 border">user_mail</div>
            <div class="cellule cell_6 border">user_date</div>
            <div class="cellule cell_6 border">user_statut</div>
            <div class="cellule cell_6 border">Supprimer</div>
        </div>
        <?php if (is_array($all_user_list)){ foreach($all_user_list as $row){ ?>
            <div class="flex">
                <div class="cellule cell_6 border">
                    <p><?php echo $row['user_id']; ?></p>
                </div>
                <div class="cellule cell_6 border">
                    <p><?php echo $row['user_pseudo']; ?></p>
                </div>
                <div class="cellule cell_6 border">
                    <p><?php echo $row['user_mail']; ?></p>
                </div>
                <div class="cellule cell_6 border">
                    <p><?php echo $row['user_date']; ?></p>
                </div>
                <div class="cellule cell_6 border">
                    <p><?php echo $row['user_statut']; ?></p>
                </div>
                <div class="cellule cell_6 border">
                    <form method="post">
                        <input type="image" src="../assets/img/icon_x.png" alt="Supprimer">
                        <input type="hidden" name="del_user" value="<?php echo $row['user_id']; ?>">
                    </form>
                </div>
            </div>
        <?php
            } // end foreach
        } // end is_array()
        ?>
    </div> <!-- end user_list -->

    <div id="create_user" class="table border">
        <p>Créer un utilisateurs</p>

        <div class="flex">
            <div class="cellule cell_5 border">pseudo</div>
            <div class="cellule cell_5 border">Mot de passe</div>
            <div class="cellule cell_5 border">E-mail</div>
            <div class="cellule cell_5 border">Date création</div>
            <div class="cellule cell_5 border">Statut</div>
        </div>

        <form method="post">
            <div class="flex">
                <div class="cellule cell_5 border">
                    <input type="text" name="user_pseudo">
                </div>
                <div class="cellule cell_5 border">
                    <input type="text" name="user_password">
                </div>
                <div class="cellule cell_5 border">
                    <input type="email" name="user_mail">
                </div>
                <div class="cellule cell_5 border">
                    <input type="datetime-local" name="user_date">
                </div>
                <div class="cellule cell_5 border">
                    <input type="text" name="user_statut" placeholder="0=inactif 1=actif">
                </div>
            </div>
            <input type="submit" name="create_user" value="Valider">
        </form>
    </div> <!-- end create_user -->

    <div id="session_list" class="table border">
        <p>Liste des sessions</p>
        <div class="flex">
            <div class="cellule cell_6 border">session_id</div>
            <div class="cellule cell_6 border">session_name</div>
            <div class="cellule cell_6 border">session_type</div>
            <div class="cellule cell_6 border">session_admin</div>
            <div class="cellule cell_6 border">session_update</div>
            <div class="cellule cell_6 border">Supprimer</div>
        </div>
        <?php if (is_array($all_session_list)){ foreach($all_session_list as $row){ ?>
            <div class="flex">
                <div class="cellule cell_6 border">
                    <p><?php echo $row['session_id']; ?></p>
                </div>
                <div class="cellule cell_6 border">
                    <p><?php echo $row['session_name']; ?></p>
                </div>
                <div class="cellule cell_6 border">
                    <p><?php echo $row['session_type']; ?></p>
                </div>
                <div class="cellule cell_6 border">
                    <p><?php echo $row['session_user_id']; ?></p>
                </div>
                <div class="cellule cell_6 border">
                    <p><?php echo $row['session_update']; ?></p>
                </div>
                <div class="cellule cell_6 border">
                    <form method="post">
                        <input type="image" src="../assets/img/icon_x.png" alt="Supprimer">
                        <input type="hidden" name="delete_session" value="<?php echo $row['session_id']; ?>">
                    </form>
                </div>
            </div>
        <?php
            } // end foreach
        } // end is_array()
        ?>
    </div> <!-- end session_list -->

    <div id="create_session" class="table border">
        <p>Créer une sessions</p>
        <div class="flex">
            <div class="cellule cell_5 border">Nom de session</div>
            <div class="cellule cell_5 border">Mot de passe</div>
            <div class="cellule cell_5 border">Type</div>
            <div class="cellule cell_5 border">Utilisateur admin</div>
            <div class="cellule cell_5 border">Date création</div>
        </div>

        <form method="post">
            <div class="flex">
                <div class="cellule cell_5 border">
                    <input type="text" name="session_name">
                </div>
                <div class="cellule cell_5 border">
                    <input type="text" name="session_pass" placeholder="vide si public">
                </div>
                <div class="cellule cell_5 border">
                    <select name="session_type" id="session_type">
                        <option value="0">0 = Publique</option>
                        <option value="1" selected>1 = Privé</option>
                    </select>
                </div>
                <div class="cellule cell_5 border">
                    <input type="text" name="session_user_id" placeholder="user_id">
                </div>
                <div class="cellule cell_5 border">
                    <input type="datetime-local" name="session_update">
                </div>
            </div>
            <input type="submit" name="create_session" value="Valider">
        </form>
    </div> <!-- end create_session -->

</main>

<footer>
    <?php
    if ($msg != ""){
        echo '<div class="error">';
        echo $msg;
        echo '</div>';
    }
    ?>
</footer>

<?php

// debug
// include '../assets/functions/global_debug.php';

include '../inc/foot.php';
?>