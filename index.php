<?php
$title = 'MÉMO ONLINE';
$style = './apps/assets/styles/index.css';
include './apps/inc/head.php';
?>

<h1 class="border">MÉMO-ONLINE</h1>

<main class="flex">
    <?php

    // Appel de la fonction send_mail()
    include './apps/assets/functions/send_mail.php';

    // pdo + declaration de "$msg"
    include './apps/inc/init.php';

    // Controler de l'accueil
    include './apps/controllers/accueil.php';

    ?>
</main>

<footer>
<?php if ($msg !== ""){
    echo '<div class="msg"><p>' . $msg . '</p></div>';
} ?>
</footer>

<script type="module" src="./apps/assets/js/signature.js"></script>
<script type="module" src="./apps/assets/js/accueil.js"></script>
<?php
include './apps/inc/foot.php';

include './apps/assets/functions/global_debug.php';
?>