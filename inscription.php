<?php 
require_once('inc/init.inc.php');

// SI l'internaute est connecté, cela veut dire que l'indice 'user' est bien définit dans la session, alors il n'a rien à faire sur la page inscription, on le redirige vers sa page profil
if(connect())
{
    header("location: profil.php");
}

// 2. Contrôler en PHP que l'on receptionne bien toute les données saisies dans le formulaire 
// echo '<pre>'; print_r($_POST); echo '</pre>';

if($_POST)
{
    // bordure rouge en cas d'erreur dans le formulaire
    $border = "border border-danger";

    // 3. Contrôler la validité du pseudo, si le pseudo est existant en BDD, alors on affiche un message d'erreur. Faites de même pour le champ 'email'

    // CONTROLE DISPONIBILITE PSEUDO 

    // On selectionne TOUT en BDD A CONDITION que le champ pseudo soit égal au pseudo que l'internaute a saisie dans la champ du formulaire
    $verifPseudo = $bdd->prepare("SELECT * FROM membre WHERE pseudo = :pseudo"); // :pseudo (marqueur nominatif ici vide)
    // $verifPseudo : objet PDOStatement 
    $verifPseudo->bindValue(':pseudo', $_POST['pseudo'], PDO::PARAM_STR); // on transmet le pseudo saisie dans le formulaire dans le marqueur déclaré :pseudo
    $verifPseudo->execute(); // executions de la requete préparée

    // SI la requete de selection a retourné au moins 1 résultat, cela veut dire que le pseudo est connu en BDD, alors on entre dans le IF et on affiche un message d'erreur à l'internaute

    if(empty($_POST['pseudo']))
    {
        $errorPseudo = "<p class='text-danger font-italic'>Merci de renseigner un Pseudo.</p>";

        $error = true;
    }
    elseif($verifPseudo->rowCount())
    {
        $errorPseudo = "<p class='text-danger font-italic'>Pseudo indisponible. Merci d'en saisir un nouveau.</p>";

        $error = true;
    }

    // CONTROLE DISPONIBILITE EMAIL 
    $verifEmail = $bdd->prepare("SELECT * FROM membre WHERE email = :email");
    $verifEmail->bindValue(':email', $_POST['email'], PDO::PARAM_STR);
    $verifEmail->execute();

    
    // SI le champ email est laissé par l'internaute, alors on entre dans le IF
    if(empty($_POST['email']))
    {
        $errorEmail = "<p class='text-danger font-italic'>Merci de renseigner un Email.</p>";

        $error = true;
    }
    elseif($verifEmail->rowCount())
    {
        // SI la condition IF renvoie TRUE, cela veut dire que rowCount() retourne un INT donc une ligne de la BDD, donc l'Email est connu en BDD
        // SI la condition IF renvoie FALSE, cela veut dire que rowCount() retourne un BOOLEAN FALSE, donc l'Email n'est pas connu en BDD
        $errorEmail = "<p class='text-danger font-italic'>Compte existant à cette adresse Email.</p>";

        $error = true;
    }

    // 4. Informer l'internaute si les mots de passe ne correspondent pas.
    // SI la valeur du champ 'mot de passe' est différente du champs 'confirmer votre mot de passe', alors on netre dans la condition IF
    if($_POST['mdp'] != $_POST['confirm_mdp'])
    {
        $errorMdp = "<p class='text-danger font-italic'>Les mots de passe ne correspondent pas.</p>";

        $error = true;
    }


    if(!isset($error))
    {
        // 5. Gérer les failles XSS
        foreach($_POST as $key => $value)
        {
            $_POST[$key] = htmlspecialchars($value);
        }

        // cryptage du mot de passe en BDD
        // Les mots de passe ne sont jamais gardés en clair dans la BDD
        // password_hash() : fonction prédéfinie qui crée une clé de hachage dans pour le mot de passe dans la BDD
        $_POST['mdp'] = password_hash($_POST['mdp'], PASSWORD_BCRYPT);

        // 6. SI l'internaute a correctement remplit le formulaire, réaliser le traitement PHP + SQL permettant d'insérer le membre en BDD (requete préparée | prepare() + bindValue())

        $insert = $bdd->prepare("INSERT INTO membre (pseudo, mdp, nom, prenom, email, civilite, ville, code_postal, adresse) VALUES (:pseudo, :mdp, :nom, :prenom, :email, :civilite, :ville, :code_postal, :adresse)");
        $insert->bindValue(':pseudo', $_POST['pseudo'], PDO::PARAM_STR);
        $insert->bindValue(':mdp', $_POST['mdp'], PDO::PARAM_STR);
        $insert->bindValue(':nom', $_POST['nom'], PDO::PARAM_STR);
        $insert->bindValue(':prenom', $_POST['prenom'], PDO::PARAM_STR);
        $insert->bindValue(':email', $_POST['email'], PDO::PARAM_STR);
        $insert->bindValue(':civilite', $_POST['civilite'], PDO::PARAM_STR);
        $insert->bindValue(':ville', $_POST['ville'], PDO::PARAM_STR);
        $insert->bindValue(':code_postal', $_POST['code_postal'], PDO::PARAM_INT);
        $insert->bindValue(':adresse', $_POST['adresse'], PDO::PARAM_STR);
        $insert->execute();

        // Après l'insertion du membre en BDD, on le redirige vers la page validation_inscription.php grace à la fonction prédéfinie header()
        header("location: validation_inscription.php");
    }
}



require_once('inc/header.inc.php');
require_once('inc/nav.inc.php');
?>



<h1 class="display-4 text-center my-4">Créer votre compte</h1>

<form method="post" class="col-md-6 mx-auto">
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="civilite">Civilité</label>
            <select id="civilite" name="civilite" class="form-control">
                <option value="femme">Madame</option>
                <option value="homme">Monsieur</option>
            </select>
        </div>
        <div class="form-group col-md-6">
            <label for="pseudo">Pseudo</label>

            <!-- si le pseudo est connu en BDD, on affecte la classe bootstrap 'border border-danger' afin d'avoir la bordure du input en rouge -->
            <input type="text" class="form-control <?php if(isset($errorPseudo)) echo $border; ?>" id="pseudo" name="pseudo" placeholder="ex : toto78" value="<?php if(isset($_POST['pseudo'])) echo $_POST['pseudo']; ?>">

            <?php if(isset($errorPseudo)) echo $errorPseudo; // affichage du message d'erreur si le pseudo est connu en BDD ?>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="mdp">Mot de passe</label>
            <input type="text" class="form-control <?php if(isset($errorMdp)) echo $border; ?>" id="mdp" name="mdp">
        </div>
        <div class="form-group col-md-6">
            <label for="confirm_mdp">Confirmer votre mot de passe</label>
            <input type="text" class="form-control <?php if(isset($errorMdp)) echo $border; ?>" id="confirm_mdp" name="confirm_mdp">

            <?php if(isset($errorMdp)) echo $errorMdp; ?>

        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="nom">Nom</label>
            <input type="text" class="form-control" id="nom" name="nom">
        </div>
        <div class="form-group col-md-6">
            <label for="prenom">Prénom</label>
            <input type="text" class="form-control" id="prenom" name="prenom">
        </div>
    </div>
    <div class="form-group">
        <label for="email">Email</label>

        <input type="text" class="form-control <?php if(isset($errorEmail)) echo $border; // affiche 'border border-danger' classe bootstrap ?>" id="email" name="email" placeholder="ex : exemple@exemple.com" value="<?php if(isset($_POST['email'])) echo $_POST['email'] ?>">
        <!-- SI l'indice 'email' est bien définit, cela veut dire que le formulaire a été validé, alors on affiche comme valeur par défaut dans le champ, l'email saisie par l'internaute -->

        <?php if(isset($errorEmail)) echo $errorEmail; // affichage du message d'erreur si le pseudo est connu en BDD ?>

    </div>
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="adresse">Adresse</label>
            <input type="text" class="form-control" id="adresse" name="adresse">
        </div>
        <div class="form-group col-md-4">
            <label for="ville">Ville</label>
            <input type="text" class="form-control" id="ville" name="ville">
        </div>
        <div class="form-group col-md-2">
            <label for="code_postal">Code postal</label>
            <input type="text" class="form-control" id="code_postal" name="code_postal">
        </div>
    </div>
    <button type="submit" class="btn btn-dark">INSCRIPTION</button>
</form>

<?php 
require_once('inc/footer.inc.php');