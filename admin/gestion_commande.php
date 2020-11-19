<?php 
require_once('../inc/init.inc.php');

// SI l'internaute N'EST PAS (!) admnistrateur du site, il n'a rien à faire sur cette page, on le redirige vers la page connexion
if(!adminConnect())
{
    header('location:' . URL . 'connexion.php');
}

// AFFICHAGE DETAILS COMMANDE
if(isset($_GET['action']) && $_GET['action'] == 'details')
{
    if(isset($_GET['id_commande']) && !empty($_GET['id_commande']))
    {
        $dCmd = $bdd->prepare("SELECT dc.produit_id AS ID, p.photo, p.reference, p.titre, p.categorie, dc.quantite, dc.prix FROM details_commande dc INNER JOIN produit p ON dc.produit_id = p.id_produit AND dc.commande_id = :id_commande");
        $dCmd->bindValue(':id_commande', $_GET['id_commande'], PDO::PARAM_INT);
        $dCmd->execute();

        if(!$dCmd->rowCount())
        {
            header('location: ' . URL . 'admin/gestion_commande.php');
        }
    }
    else
    {
        header('location: ' . URL . 'admin/gestion_commande.php');
    }

    $mt = $bdd->prepare("SELECT montant FROM commande WHERE id_commande = :id_commande");
    $mt->bindValue(':id_commande', $_GET['id_commande'], PDO::PARAM_INT);
    $mt->execute();

    $montant = $mt->fetch(PDO::FETCH_ASSOC);
}

require_once('../inc/header.inc.php');
require_once('../inc/nav.inc.php');


// AFFICHAGE DES COMMANDES
$r = $bdd->query("SELECT id_commande AS CMD, id_membre AS 'N° CLIENT', email, prenom, nom, adresse, DATE_FORMAT(date_enregistrement, '%d/%m/%Y à %H:%i:%s') AS DATE, montant, etat FROM membre INNER JOIN commande ON membre_id = id_membre");
?>

<h1 class="display-4 text-center my-4">Liste des Commandes</h1>

<?php
if($r->rowCount() == 1)
    $txt = 'commande';
else
    $txt = 'commandes';
?>

<h6><span class="badge badge-success"><?= $r->rowCount() ?></span> <?= $txt ?></h6>

<table class="table table-bordered text-center"><tr>
<?php for($i = 0; $i < $r->columnCount(); $i++): 
      $c = $r->getColumnMeta($i);
    //   echo '<pre>'; print_r($c); echo '</pre>';
?>
    <th><?= strtoupper($c['name']) ?></th>

<?php endfor; ?>

    <th>MODIF</th>
    <th>VOIR</th>
    <th>SUPP</th>

</tr>
<?php while($cmd = $r->fetch(PDO::FETCH_ASSOC)): 
    // echo '<pre>'; print_r($cmd); echo '</pre>';
    ?>
    
    <tr>
    <?php foreach($cmd as $k => $v): ?>

        <?php if($k == 'montant'): ?>

            <td><strong><?= $v ?>€</strong></td>

        <?php else: ?>

            <?php if($k == 'etat'): 
                
                if($v == 'en_cours_de_traitement')
                    echo "<td>En préparation</td>";
                elseif($v == 'envoye')
                    echo "<td>Expédiée</td>";
                else 
                    echo "<td>Livrée</td>";
                ?>

            <?php else: ?>

                <td><?= $v ?></td>

            <?php endif; ?>

        <?php endif; ?>

    <?php endforeach; ?>

        <td><a href="?action=modification&id_commande=<?= $cmd['CMD'] ?>" class="btn btn-dark"><i class='far fa-edit'></i></a></td>
        <td><a href="?action=details&id_commande=<?= $cmd['CMD'] ?>" class="btn btn-primary"><i class="fas fa-search"></i></a></td>
        <td><a href="?action=suppression&id_commande=<?= $cmd['CMD'] ?>" class="btn btn-danger"><i class='far fa-trash-alt'></i></a></td>

    </tr>

<?php endwhile; ?>
</table>

<!-- AFFICHAGE DETAILS COMMANDE -->
<?php if(isset($_GET['action']) && $_GET['action'] == 'details'): ?>

    <h4 class="text-center my-4">Détails de la commande <span class="text-info">CMD<?= $_GET['id_commande'] ?></span></h4>

    <table class="col-md-8 mx-auto table table-bordered text-center mb-5"><tr>
    <?php for($i = 0; $i < $dCmd->columnCount(); $i++): 
        $c = $dCmd->getColumnMeta($i);
        //   echo '<pre>'; print_r($c); echo '</pre>';
    ?>
        <th><?= strtoupper($c['name']) ?></th>

    <?php endfor; ?>

        <th>PRIX / PRODUIT</th>

    </tr>
    <?php while($p = $dCmd->fetch(PDO::FETCH_ASSOC)):
        //   echo '<pre>'; print_r($p); echo '</pre>';
        ?>

        <tr>
        <?php foreach($p as $k => $v): ?>

            <?php if($k == 'photo'): ?>

                <td><img src="<?= $v ?>" alt="<?= $p['titre'] ?>" style="width: 50px;"></td>

            <?php else: ?>

                <?php if($k == 'prix'): ?>

                    <td><?= $v ?>€</td>

                <?php else: ?>

                    <td><?= $v ?></td>

                <?php endif; ?>    

            <?php endif; ?>    

        <?php endforeach; ?>

            <td><strong><?= $p['quantite']*$p['prix'] ?>€</strong></td>
        </tr>

    <?php endwhile; ?>

        <tr class="bg-dark text-white">
            <th colspan="7">MONTANT TOTAL</th>
            <th><?= $montant['montant'] ?>€</th>
        </tr>

    </table>

<?php endif; ?>

<?php 
require_once('../inc/footer.inc.php');


