<?php 
require_once('inc/init.inc.php');

// ENREGISTREMENT AVIS
if(isset($_POST['poster']))
{
    $a = $bdd->prepare("INSERT INTO commentaire (pseudo, message, date_enregistrement, produit_id) VALUES (:pseudo, :message, NOW(), :id_produit)");
    $a->bindValue(':pseudo', $_SESSION['user']['pseudo'], PDO::PARAM_STR);
    $a->bindValue(':message', $_POST['message'], PDO::PARAM_STR);
    $a->bindValue(':id_produit', $_GET['id_produit'], PDO::PARAM_INT);
    $a->execute();
}

// SELECTION DES AVIS EN BDD
$e = $bdd->prepare("SELECT produit_id, pseudo, message, DATE_FORMAT(date_enregistrement, '%d/%m/%Y à %H:%i:%s') AS date FROM commentaire WHERE produit_id = :id_produit ORDER BY date_enregistrement DESC LIMIT 0,10");
$e->bindValue(':id_produit', $_GET['id_produit'], PDO::PARAM_INT);
$e->execute();

// SI l'indice 'id_produit' est bien définit dans l'URL et que sa valeur n'est pas vide, cela veut dire que l'internaute a cliqué sur un lien 'en savoir plus' et par conséquent transmit les paramètre ex : 'id_produit=29'
if(isset($_GET['id_produit']) && !empty($_GET['id_produit']))
{
    // On selectionne tout en BDD par rapport à l'id_produit' transmis dans l'URL, afin d'afficher le détail d'un produit
    $r = $bdd->prepare("SELECT * FROM produit WHERE id_produit = :id_produit"); // 29
    $r->bindValue(':id_produit', $_GET['id_produit'], PDO::PARAM_INT);
    $r->execute();

    // SI la requete de selection retourne 1 résultat de la BDD, cela veut que l'id_produit transmis dans l'URL est connu en BDD, alors on entre dans la condition IF
    if($r->rowCount())
    {
        // Retourne un tableau ARRAY contenant les données du produit à afficher sur la page fiche_produit en fonction de l'id_produit de l'URL
        $p = $r->fetch(PDO::FETCH_ASSOC);
        // echo '<pre>'; print_r($p) ;echo '</pre>';
    }
    else // SINON l'id_produit transmis dans l'URL n'est pas connu en BDD, on redirige l'internaute vers la page boutique
    {
        header('location: boutique.php');
    }

}
else // Sinon l'indice 'id_produit' n'est pas définit dans l'URL ou sa valeur est vide, alors on entre dans la condition ELSE et on redirige l'internaute vers la boutique
{
    header('location: boutique.php');
}

require_once('inc/header.inc.php');
require_once('inc/nav.inc.php');
?>

<!-- Page Content -->
<div class="container">

    <div class="row">

        <div class="col-lg-3">

            <?php 
            // On selectionne les catégories DISTINCTE (élimine les doublons) dans la BDD
            $d = $bdd->query("SELECT DISTINCT categorie FROM produit");
            ?>

            <h4 class="my-4 text-center">Que Du Lourd !! VienDez Voir !!</h4>
            <div class="list-group text-center">
                <li class="list-group-item bg-dark text-white">CATEGORIES</li>

                <?php while($c = $d->fetch(PDO::FETCH_ASSOC)): 
                    //   echo '<pre>'; print_r($c); echo '</pre>';
                    // fetch() retourne un ARRAY par tour de boucle de WHILE contenant les données d'une catégorie
                    ?>

                    <a href="boutique.php?categorie=<?= $c['categorie'] ?>" class="list-group-item text-dark"><?= $c['categorie'] ?></a><!-- la boucle crée un lien par catégorie pour chaque tour de boucle -->
                
                <?php endwhile; ?>
            </div>
        </div>
        <!-- /.col-lg-3 -->

        <div class="col-lg-9">

            <div class="card mt-4">
                <div class="text-center">
                    <img class="card-img-top img-fluid" src="<?= $p['photo'] ?>" alt="<?= $p['photo'] ?>" style="width: 500px;">
                </div>
                <div class="card-body">

                    <h3 class="card-title"><?= $p['titre'] ?></h3>

                    <h4><?= $p['prix'] ?>€</h4>

                    <h5>Description du produit</h5>

                    <p class="card-text"><?= $p['description'] ?></p><hr>

                    <h5>Détails sur le produit</h5>

                    <p class="card-text"><strong>Catégorie :</strong> <a href="boutique.php?categorie=<?= $p['categorie'] ?>" class="alert-link text-primary"><?= $p['categorie'] ?></a></p>

                    <p class="card-text"><strong>Numéro du modèle de l'article : </strong> <?= $p['reference'] ?></p>

                    <p class="card-text"><strong>Couleur : </strong> <?= $p['couleur'] ?></p>

                    <p class="card-text"><strong>Taille : </strong> <?= $p['taille'] ?></p>

                    <p class="card-text"><strong>Service : </strong> <?= $p['public'] ?></p>

                    <?php if($p['stock'] <= 10 && $p['stock'] != 0): // Si le stock du produit est inférieur ou égal à 10 et que le stock est différent de 0, on entre dans la condition IF ?>

                        <p class="card-text font-italic text-danger">Attention ! Il ne reste plus que <?= $p['stock'] ?> exemplaire(s) en stock.</p>

                    <?php elseif($p['stock'] > 10): // SINON SI le stock du produit est supérieur à 10, on entre dans la condition ELSEIF ?>

                        <p class="card-text font-italic text-success">En stock !</p>

                    <?php endif; ?>    

                    <hr>

                    <?php if($p['stock'] > 0): // Si le stock du produit est supérieur, on entre dans la condition IF et l'internaute peut choisir une quantité et ajouter le produit dans le panier ?>    

                        <form method="post" action="panier.php" class="form-inline">
                            <input type="hidden" id="id_produit" name="id_produit" value="<?= $p['id_produit'] ?>">
                            <div class="form-group">
                                <select class="form-control" id="quantite" name="quantite">
                                <!--              5  <= 5 && 5  <= 30                 -->
                                <!--              $i <= 5 && $i <= 30                 -->
                                <?php for($i = 1; $i <= $p['stock'] && $i <= 30; $i++): ?>
                                
                                    <option value="<?= $i ?>"><?= $i ?></option>

                                <?php endfor; ?>
                                </select>
                            </div>
                            <input type="submit" class="btn btn-dark ml-2" name="ajout_panier" value="AJOUTER AU PANIER">
                        </form>

                    <?php else: // Sinon le stock du produit est à 0 en BDD, on entre dans la condition ELSE, on affiche un message d'erreur ?>

                        <p class="card-text font-italic text-danger">Rupture de stock !</p>

                    <?php endif; ?>
                 
                </div>
            </div>
            <!-- /.card -->

            <?php if(connect()): ?>
                
                <form method="post" action="" class="mt-5">
                    <div class="form-group">
                        <label for="message">Saisir votre message :</label>
                        <textarea class="form-control" name="message" id="message" rows="4"></textarea>
                    </div>
                    <input type="submit" class="btn btn-dark" name="poster">
                </form>

            <?php endif; ?>

            <div class="card card-outline-secondary my-4">
                
                <div class="card-header">
                    Derniers avis...
                </div>
                <div class="card-body">

                    <?php if($e->rowCount()): ?>

                        <?php while($comments = $e->fetch(PDO::FETCH_ASSOC)): ?>
                    
                            <p><?= $comments['message'] ?></p>
                            <small class="text-muted font-italic">Posté par <?= $comments['pseudo'] ?> le <?= $comments['date'] ?></small>
                            <hr>

                        <?php endwhile; ?>
                    
                        <a href="#" class="btn btn-success">Voir tout les avis</a>

                    <?php else: ?>

                        <p class="text-center font-italic my-2">Soyez le premier à poster un avis</p>

                    <?php endif; ?>
                </div>
            </div>
            <!-- /.card -->

        </div>
        <!-- /.col-lg-9 -->

    </div>

</div>
<!-- /.container -->

<?php 
require_once('inc/footer.inc.php');