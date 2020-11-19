<?php 
require_once('inc/init.inc.php');

// SI l'internaute N'EST PAS (!) connecté, cela veut dire que l'indice 'user' n'est pas définit dans la session, alors il n'a rien à faire sur la page profil, on le redirige vers la page connexion
if(!connect())
{
    header('location: connexion.php');
}

require_once('inc/header.inc.php');
require_once('inc/nav.inc.php');

// echo '<pre>'; print_r($_SESSION); echo '</pre>';

?>

<h1 class="display-4 text-center my-4">Bonjour <span class="text-info"><?= $_SESSION['user']['pseudo'] ?></span></h1>

<!-- Exo : afficher les informations personnelles de l'internuaute contenu en session ($_SESSION['user']) sur la page profil avec de la mise en forme -->

<div class="col-md-3 mx-auto card mb-3 shadow-lg">
    <div class="card-body">

        <h5 class="card-title text-center">Vos informations personnelle</h5><hr>

        <?php foreach($_SESSION['user'] as $key => $value): // on passe en revue le tableau ARRAY à l'indice 'user' dans la session ?>

            <?php if($key != 'id_membre' && $key != 'statut'): // on exclu à l'affichage le statut et l'id_membre de l'utilisateur ?>

                <p class="card-text"><strong><?= $key ?></strong> : <span class=""><?= $value ?></span></p>
            
            <?php endif; ?>

        <?php endforeach; ?>

        <a href="#" class="card-link">Modifier</a>

    </div>
</div>

<?php 
require_once('inc/footer.inc.php');