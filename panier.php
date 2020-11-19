<?php 
require_once('inc/init.inc.php');

if(isset($_POST['ajout_panier']))
{
    // echo '<pre>'; print_r($_POST); echo '</pre>';

    // On selectionne en BDD toute les données du produit qui vient d'être ajouté au panier
    // Cela nous permet d'avoir accès aux données du produit stockés dans le panier (référence, prix, photo etc..)
    $r = $bdd->prepare("SELECT * FROM produit WHERE id_produit = :id_produit");
    $r->bindValue(':id_produit', $_POST['id_produit'], PDO::PARAM_INT);
    $r->execute();

    $p = $r->fetch(PDO::FETCH_ASSOC);
    // echo '<pre>'; print_r($p); echo '</pre>';

    // On ajoute dans la session un produit à la validation du formulaire dans le fichier fiche_produit.php
    ajoutPanier($p['id_produit'], $p['photo'], $p['reference'], $p['titre'], $_POST['quantite'], $p['prix']);
    
}   

// SUPPRESSION PRODUIT DANS LE PANIER
if(isset($_GET['action']) && $_GET['action'] == 'suppression')
{
    // On récupère l'indice auquel se trouve le produit que l'on souhaite supprimer du panier afin de personnalier le message de validation de suppression (ajout du titre et référence du produit)
    //                                         29                   ARRAY
    $positionProduit = array_search($_GET['id_produit'], $_SESSION['panier']['id_produit']); // [0]

    $vd = "<div class='bg-success col-md-5 mx-auto text-center text-white rounded p-2 mb-2'>Le produit titre <strong>" . $_SESSION['panier']['titre'][$positionProduit] . "</strong> référence <strong>" . $_SESSION['panier']['reference'][$positionProduit] . "</strong> a bien été retiré du panier.</div>";

    suppProduit($_GET['id_produit']); // ON transmet l'id_produit du produit a supprimer du panier à la fonction suppProduit(). C'est la méthodfe array_splice() qui supprime chaque ligne dans les tableau ARRAY de la session
}

// CONTROLE STOCK PRODUIT
// Si l'indice 'payer' est bien définit, cela veut que l'internaute a cliqué sur le bouton 'VALIDER LE PAIEMENT' et donc par conséquent que l'attribut name 'payer' a été détecté
if(isset($_POST['payer']))
{
    // La boucle FOR tourne autant de fois qu'il y a d'id_produit dans la session, donc autant qu'il y a de produits dans le panier
    $error = '';
    for($i = 0; $i < count($_SESSION['panier']['id_produit']); $i++)
    {
        $r = $bdd->query("SELECT stock FROM produit WHERE id_produit = " . $_SESSION['panier']['id_produit'][$i]); // 29
        $s = $r->fetch(PDO::FETCH_ASSOC);
        // echo '<pre>'; print_r($s); echo '</pre>';
        
        // SI la quantite du stock du produit en BDD est inférireur à la quantité dans la session, c'est à dire la quantité commandée par l'internaute, alors on entre dans la condition IF
        if($s['stock'] < $_SESSION['panier']['quantite'][$i])
        {
            $error .= "<div class='bg-danger col-md-3 mx-auto text-center text-white rounded p-2 mb-2'>Stock restant du produit : <strong>$s[stock]</strong></div>";

            $error .= "<div class='bg-success col-md-3 mx-auto text-center text-white rounded p-2 mb-2'>Quantitée demandée du produit : <strong>" . $_SESSION['panier']['quantite'][$i] . "</strong></div>";

            // SI le stock en BDD est supérieur à 0 mais inférieur à la quantité demandée par l'internaute, alors on entre dans la condition IF
            if($s['stock'] > 0)
            {
                $error .= "<div class='bg-danger col-md-3 mx-auto text-center text-white rounded p-2 mb-2'>La quantite du produit <strong>" . $_SESSION['panier']['titre'][$i] . "</strong> référence <strong>" . $_SESSION['panier']['reference'][$i] . "</strong> a été modifiée car notre stock est insuffisant, vérifiez vos achats.</div>";

                // ON affecte la quantite restante en stock en BDD à la quantité du produit dans le panier dont la quantité demandée est supérieur par rapport au stock restant 
                $_SESSION['panier']['quantite'][$i] = $s['stock'];
            }
            else // Sinon le stock du produit en BDD est à 0, on entre dans la condition ELSE
            {
                $error .= "<div class='bg-danger col-md-3 mx-auto text-center text-white rounded p-2 mb-2'>Le produit <strong>" . $_SESSION['panier']['titre'][$i] . "</strong> référence <strong>" . $_SESSION['panier']['reference'][$i] . "</strong> a été supprimé car notre stock est rupture de stock, vérifiez vos achats.</div>";

                suppProduit($_SESSION['panier']['id_produit'][$i]); // on supprime dans la session le produit qui a un stock de 0, en rupture de stock
                $i--; // on fait un tour de boucle en arrière, on décrémente, car array_splice() remonte les indices inférieurs vers les indices supérieur, cela permet de ne pas oublier de contrôler un produit qui aurait remonté d'un indice dans le tableau ARRAY de la session
            }

            $e = true;
        }
    }

    // Si la variable $e n'est pas définit, cela veut que les stocks sont supérieur à la quantité commandée par l'internaute, on entre dans la condition IF
    if(!isset($e))
    {
        // ENREGISTREMENT DE LA COMMANDE
        $r = $bdd->exec("INSERT INTO commande (membre_id, montant, date_enregistrement) VALUES (" . $_SESSION['user']['id_membre'] . ", " . montantTotal() . ", NOW())");

        $idCommande = $bdd->lastInsertId(); // permet de récupérer le dernier id_commande crée dans la BDD afin de l'enregistrer dans la table details_commande, pour chaque produit à la bonne commande

        // La boucle FOR tourne autant de fois qu'il y a d'id_produit dans la session, donc autant qu'il y a de produits dans le panier
        for($i = 0; $i < count($_SESSION['panier']['id_produit']); $i++)
        {
            // Pour chaque tour de boucle FOR, on execute une requete d'insertion dans la table details_commande pour chaque produit ajouté
            // On récupère le dernier id_commande généré en BDD afin de relier chaque produit à la bonne commande dans la table details_commande
            $r = $bdd->exec("INSERT INTO details_commande (commande_id, produit_id, quantite, prix) VALUES ($idCommande, " . $_SESSION['panier']['id_produit'][$i] . ", " . $_SESSION['panier']['quantite'][$i] . ", " . $_SESSION['panier']['prix'][$i] . ")");

            // Dépréciation des stocks
            // Modifie la table 'produit' afin que le stock soit égal au stock de la BDD MOINS la quantité du produit commandé A CONDITION que l'id_produit de la BDD soit égal à l'id_produit du produit stocké dans le panier de la session 
            $r = $bdd->exec("UPDATE produit SET stock = stock - " . $_SESSION['panier']['quantite'][$i] . " WHERE id_produit = " . $_SESSION['panier']['id_produit'][$i]);
        }
        unset($_SESSION['panier']); // on supprime les éléments du panier dans la session après la validation du panier et l'insertion dans les tables 'commande' et  'details_commande'
        
        $_SESSION['num_cmd'] = $idCommande; // on stock l'id_commande dans la session après validation du panier
        header('location: validation_cmd.php'); // on redirige l'internaute après la validation du panier        
    } 
}


// echo '<pre>'; print_r($_SESSION); echo '</pre>';

require_once('inc/header.inc.php');
require_once('inc/nav.inc.php');
?>

<h1 class="display-4 text-center my-4">Mon Panier</h1>

<?php 
// Affichage des messages utilisateurs
if(isset($error)) echo $error; 
if(isset($vd)) echo $vd; 
?>

<table class="col-md-8 mx-auto table table-bordered text-center">
    
    <tr>
        <th>PHOTO</th>
        <th>REFERENCE</th>
        <th>TITRE</th>
        <th>QUANTITE</th>
        <th>PRIX unitaire</th>
        <th>PRIX total/produit</th>
        <th>SUPP</th>
    </tr>

    <?php if(empty($_SESSION['panier']['id_produit'])): // Si l'indice 'id_produit' dans le panier de la session est vide ou non définit, on entre dans la condition IF ?>

            <tr><td colspan="7" class="text-danger">Aucun produit dans le panier</td></tr>

        </table>

    <?php else: // Sinon des id_produit sont bien définit dans le panier de la session, on entre dans la condition ELSE et on affiche le contenu du panier ?>

        <!-- La boucle FOR tourne autant de fois qu'il y a d'id_produit dans la session, donc autant qu'il y a de produits dans le panier -->
        <?php for($i = 0; $i < count($_SESSION['panier']['id_produit']); $i++): ?>

            <tr>
                <!-- Pour chaque tour de boucle FOR, nous allons crochter aux indices numériques des différents ARRAY dans la session afin d'afficher la photo, le titre, la référence etc.. des produits ajoutés dans le panier -->
                <td><a href="fiche_produit.php?id_produit=<?= $_SESSION['panier']['id_produit'][$i]; ?>"><img src="<?= $_SESSION['panier']['photo'][$i]; ?>" alt="<?= $_SESSION['panier']['titre'][$i]; ?>" style="width: 100px;"></a></td>

                <td><?= $_SESSION['panier']['reference'][$i]; ?></td>

                <td><?= $_SESSION['panier']['titre'][$i]; ?></td>

                <td><?= $_SESSION['panier']['quantite'][$i]; ?></td>

                <td><?= $_SESSION['panier']['prix'][$i]; ?>€</td>

                <td><?= $_SESSION['panier']['quantite'][$i]*$_SESSION['panier']['prix'][$i]; ?>€</td>

                <td><a href="?action=suppression&id_produit=<?= $_SESSION['panier']['id_produit'][$i] ?>" class='btn btn-danger' onclick="return(confirm('En êtes vous certain ?'));"><i class='far fa-trash-alt'></i></a></td>
            </tr>
            
        <?php endfor; ?>

            <tr>
                <th>MONTANT TOTAL</th>
                <td colspan="4"></td>
                <th><?= montantTotal(); ?>€</th>
                <td></td>
            </tr>    

</table>

    <?php if(connect()): // Si l'internaute est connécté, il peut valider la paiement ?>

        <form action="" method="post" class="col-md-8 mx-auto pl-0">
            <input type="submit" name="payer" value="VALIDER LE PAIEMENT" class="btn btn-success">
        </form>

    <?php else: // Sinon l'internaute n'est pas connecté, on le renvoi vers la page connexion ?>

        <a href="<?= URL ?>connexion.php" class="offset-md-2 btn btn-success mb-3">IDENTIFIEZ-VOUS POUR VALIDER LA COMMANDE</a>
        
    <?php endif; ?>

<?php endif; ?>

<?php 
require_once('inc/footer.inc.php');
