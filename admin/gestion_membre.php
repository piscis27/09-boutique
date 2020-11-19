<?php 
require_once('../inc/init.inc.php');

// SI l'internaute N'EST PAS (!) admnistrateur du site, il n'a rien à faire sur cette page, on le redirige vers la page connexion
if(!adminConnect())
{
    header('location:' . URL . 'connexion.php');
}

// SUPPRESSION MEMBRE
if(isset($_GET['action']) && $_GET['action'] == "suppression")
{
    // On contrôle d'abord si le membre est associé à des commandes 
    $cmd = $bdd->prepare("SELECT * FROM commande WHERE membre_id = :id_membre");
    $cmd->bindValue(':id_membre', $_GET['id_membre'], PDO::PARAM_INT);
    $cmd->execute();

    // Si rowCount() retourne au moins résultat, cela veut dire que la requete SELECT retourne des commandes par rapport au membre que l'on souhaite supprimer, alors on entre dans la condition IF et on informe l'internaute
    if($cmd->rowCount())
    {
        $vd = "<p class='col-md-3 mx-auto bg-danger text-center text-white p-3 rounded my-4'>Impossible de supprimer le membre <strong>ID$_GET[id_membre]</strong> ! Des commandes sont en cours pour ce membre.</p>";
    }
    else // Sinon, aucune commande n'est associé au membre, on entre dans la condition ELSE et on supprime le membre de la BDD
    {
        $d = $bdd->prepare("DELETE FROM membre WHERE id_membre = :id_membre");
        $d->bindValue(':id_membre', $_GET['id_membre'], PDO::PARAM_INT); 
        $d->execute();

        $vd = "<p class='col-md-3 mx-auto bg-success text-center text-white p-3 rounded my-4'>Le membre <strong>ID $_GET[id_membre]</strong> a bien été supprimé !</p>";
    }

    
}

// MODIFICATION MEMBRE
if(isset($_GET['action']) && $_GET['action'] == "modification")
{
    if(isset($_GET['id_membre']) && !empty($_GET['id_membre']))
    {
        $r = $bdd->prepare("SELECT * FROM membre WHERE id_membre = :id_membre");
        $r->bindValue(':id_membre', $_GET['id_membre'], PDO::PARAM_INT);
        $r->execute();

        if($r->rowCount())
        {
            $m = $r->fetch(PDO::FETCH_ASSOC);
            // echo '<pre>'; print_r($m); echo '</pre>';
        }
        else
        {
            header('location: ' . URL . 'admin/gestion_membre.php');
        }
    }
    else
    {
        header('location: ' . URL . 'admin/gestion_membre.php');
    }

    // La boucle FOREACH génère une variable par tour de boucle
    // On se sert de la variable $k qui receptionne un indice du tableau ARRAY par tour de boucle pour créer une variable
    //          id_membre => 18
    foreach ($m as $k => $v) 
    {
        // 1er tour :
        // $ id_membre = (isset($m['id_membre'])) ? $m['id_membre'] : '';
        // 2ème tour :
        // $ pseudo = (isset($m['pseudo'])) ? $m['pseudo'] : '';
        // 3ème tour : 
        // etc...
        $$k = (isset($m[$k])) ? $m[$k] : '';
    }

    // REQUETE UPDATE MODIFICATION MEMBRE
    if($_POST)
    {
        // echo '<pre>'; print_r($_POST); echo '</pre>';
        $up = $bdd->prepare("UPDATE membre SET civilite = :civilite, nom = :nom, prenom = :prenom, adresse = :adresse, ville = :ville, code_postal = :code_postal, statut = :statut WHERE id_membre = :id_membre");
        $up->bindValue(':civilite', $_POST['civilite'], PDO::PARAM_STR);
        $up->bindValue(':nom', $_POST['nom'], PDO::PARAM_STR);
        $up->bindValue(':prenom', $_POST['prenom'], PDO::PARAM_STR);
        $up->bindValue(':adresse', $_POST['adresse'], PDO::PARAM_STR);
        $up->bindValue(':ville', $_POST['ville'], PDO::PARAM_STR);
        $up->bindValue(':code_postal', $_POST['code_postal'], PDO::PARAM_INT);
        $up->bindValue(':statut', $_POST['statut'], PDO::PARAM_INT);
        $up->bindValue(':id_membre', $_GET['id_membre'], PDO::PARAM_INT);
        $up->execute();

        $vUpdt = "<p class='col-md-3 mx-auto bg-success text-center text-white p-3 rounded my-4'>Le membre <strong>ID $_GET[id_membre]</strong> a bien été modifié !</p>";

        // on définit la superglobale $_GET à vide afin d'être redirigé vers la gestion_membre après la modification du membre, donc après la validation du formulaire
        $_GET = '';
    }
}


require_once('../inc/header.inc.php');
require_once('../inc/nav.inc.php');

// echo $_SERVER['PHP_SELF'];

// Exo : Afficher l'ensemble de la table membre sous forme de tableau HTML (sauf le mot de passe)
// SELECT + TABLE + FETCH 
// Prévoir 2 colonnes supplémentaire pour la modification et suppression de chaque membre

$r = $bdd->query("SELECT id_membre AS ID, pseudo, nom, prenom, email, civilite, ville, code_postal AS 'CODE POSTAL', adresse, statut AS ROLE FROM membre");
?>

<h1 class="display-4 text-center my-4">Liste des membres</h1>

<?php 
// Affichage des messages utilisateurs
if(isset($vd)) echo $vd; 
if(isset($vUpdt)) echo $vUpdt; 

// TRAITEMENT AFFICHAGE NOMBRE ADMIN
$n = $bdd->query("SELECT * FROM membre WHERE statut = 1");

// CONDITION AFFICHAGE NOMBRE MEMBRES
if($r->rowCount() == 1)
    $txtM = 'membre enregistré';
else
    $txtM = 'membres enregistrés';

// CONDITION AFFICHAGE NOMBRE ADMIN
if($n->rowCount() == 1)
    $txtA = 'administrateur';
else
    $txtA = 'administrateurs';
?>

<h6><span class="badge badge-success"><?= $r->rowCount() ?></span> <?= $txtM ?></h6>

<h6><span class="badge badge-info"><?= $n->rowCount() ?></span> <?= $txtA ?></h6>

<table class="table table-bordered text-center"><tr>
<?php 
//             < 10
for($i = 0; $i < $r->columnCount(); $i++): 
    $c = $r->getColumnMeta($i);    
    // echo '<pre>'; print_r($c); echo '</pre>';
?>
    <th><?= strtoupper($c['name']) ?></th>

<?php 
endfor; 
?>
    <th>EDIT</th>
    <th>SUPP</th>
</tr>
<?php while($m = $r->fetch(PDO::FETCH_ASSOC)): 
    // echo '<pre>'; print_r($m); echo '</pre>';
?>

    <tr>    <!--  [pseudo]   titi78 -->
    <?php foreach($m as $k => $v): ?>
        
        <?php if($k == 'ROLE'): ?>

                <?php if($v == 0): ?>

                    <td>MEMBRE</td>

                <?php else: ?>

                    <td class="bg-info text-white">ADMIN</td>

                <?php endif; ?>    

        <?php else: ?>    

            <td><?= $v ?></td>

        <?php endif; ?>

    <?php endforeach; ?>
    
        <td><a href="?action=modification&id_membre=<?= $m['ID'] ?>" class="btn btn-dark"><i class='far fa-edit'></i></a></td>

        <td><a href="?action=suppression&id_membre=<?= $m['ID'] ?>" class="btn btn-danger" onclick="return(confirm('En êtes vous certain ?'));"><i class='far fa-trash-alt'></i></a></td>               

    </tr>

<?php endwhile; ?>    
</table>

<?php if(isset($_GET['action']) && $_GET['action'] == 'modification'): ?>

    <form method="post" class="col-md-6 mx-auto mt-5" enctype="multipart/form-data">
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="civilite">Civilité</label>
                <select id="civilite" name="civilite" class="form-control">
                    <option value="homme" <?php if($civilite == 'homme') echo 'selected'; ?>>Monsieur</option>
                    <option value="femme" <?php if($civilite == 'femme') echo 'selected'; ?>>Madame</option>
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="reference">Pseudo</label>
                <input type="text" class="form-control" id="pseudo" name="pseudo" placeholder="ex : toto78" value="<?= $pseudo ?>" disabled>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="nom">Nom</label>
                <input type="text" class="form-control" id="nom" name="nom" value="<?= $nom ?>">
            </div>
            <div class="form-group col-md-6">
                <label for="prenom">Prénom</label>
                <input type="text" class="form-control" id="prenom" name="prenom" value="<?= $prenom ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-12">
                <label for="email">Email</label>
                <input type="text" class="form-control" id="email" name="email" value="<?= $email ?>" disabled>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-7">
                <label for="adresse">Adresse</label>
                <input type="text" class="form-control" id="adresse" name="adresse" value="<?= $adresse ?>">
            </div>
            <div class="form-group col-md-3">
                <label for="ville">Ville</label>
                <input type="text" class="form-control" id="ville" name="ville" value="<?= $ville ?>">
            </div>
            <div class="form-group col-md-2">
                <label for="code_postal">Code Postal</label>
                <input type="text" class="form-control" id="code_postal" name="code_postal" value="<?= $code_postal ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-12">
                <label for="statut">Rôle</label>
                <select id="statut" name="statut" class="form-control">
                    <option value="0" <?php if($statut == 0) echo 'selected'; ?>>MEMBRE</option>
                    <option value="1" <?php if($statut == 1) echo 'selected'; ?>>ADMINISTRATEUR</option>
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-dark mb-3">MODIFICATION MEMBRE</button>
    </form>

<?php 
endif;
require_once('../inc/footer.inc.php');