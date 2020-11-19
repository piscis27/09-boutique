<?php 
require_once('inc/init.inc.php');

// Lorque l'internaute clique sur le lien 'deconnexion', il transmet dans le même temps dans l'URL les paramètres 'action=deconnexion'
// La condition IF permet de vérifier si l'indice 'action' est bien définit dans l'URL et qu'il a pour valeur 'deconnexion', on entre dans le IF seulement dans le cas où l'internaute clique sur  'deconnexion'
if(isset($_GET['action']) && $_GET['action'] == 'deconnexion')
{
    // Pour que l'internaute soit déconnecté, il faut soit supprimer la session ou vider une partie afin que l'indice 'user' dans la session ne soit plus définit

    // session_destroy(); // suppression du fichier session
    unset($_SESSION['user']); // supprime le tableau ARRAY indice 'user' dans la session
}

// SI l'internaute est connecté, cela veut dire que l'indice 'user' est bien définit dans la session, alors il n'a rien à faire sur la page connexion, on le redirige vers sa page profil
if(connect())
{
    header("location: profil.php");
}

// echo '<pre>'; print_r($_POST); echo '</pre>';

if($_POST)
{
    // On selectionne tout en BDD à condition que le champ pseudo ou email soit égal à la donnée saisie par l'internaute dans le formulaire dans le champs pseudo/email
    //                                                                      gregorylacroix78@gmail.com
    $data = $bdd->prepare("SELECT * FROM membre WHERE pseudo = :pseudo OR email = :email");
    $data->bindValue(':pseudo', $_POST['pseudo_email'], PDO::PARAM_STR);
    $data->bindValue(':email', $_POST['pseudo_email'], PDO::PARAM_STR);
    $data->execute();

    // SI la requete sde selection retourne 1 résultat, celz veut dire que l'email ou le pseudo saisie par l'internaute est existant en BDD, alors on entre dans la condition IF
    if($data->rowCount())
    {
        // echo "pseudo ou email existant en BDD";
        
        $user = $data->fetch(PDO::FETCH_ASSOC);
        // echo '<pre>'; print_r($user); echo '</pre>';

        // Contrôle mot de passe en clair en BDD
        // $_POST['password'] == $user['mdp']

        // password_verify() permet de comparer une clé de hachage à une chaine de caractères
        // arguments : password_verify('la chaine a comparer', 'la clé de hachage')
        if(password_verify($_POST['password'], $user['mdp']))
        {
            // echo 'MDP ok !!';
            // SI nous entrons dans cette condition, cela veut dire que l'internaute a correctement remplit le formulaire de connexion 

            // On passe en revue toute les données de l'internaute recupérées en BDD de l'internaute qui a correctement remplit le forulaire de connexion
            // $user : tableau ARRAY contenant toute les données de l'utilisateur en BDD
            //              [mdp] => mdp
            foreach($user as $key => $value)
            {   // [mdp]
                if($key != 'mdp') // on exclu le mdp dans le fichier session
                {
                    // $_SESSION['user'][pseudo] = titi78
                    $_SESSION['user'][$key] = $value;
                }
            }
            // On crée dans la session un indice 'user' contenant un tableau ARRAY avec toute les données de l'utilisateur
            // C'est ce qui permettra d'identifier l'utilisateur connecté sur le site et cela lui permettra de naviguer sur le site tout en restant connecté
            // echo '<pre>'; print_r($_SESSION); echo '</pre>';

            // Une fois l'internaute connecté, on le redirige vers sa page profil
            header('location: profil.php');
        }
        else
        {
            // echo 'erreur MDP !!';
            $error = "<p class='col-md-3 mx-auto bg-danger text-white text-center p-3 rounded'>Identifiants ou mot de passe erronés.</p>";
        }
    }
    else // SINON le pseudo ou email n'est pas connu en BDD, on entre dans la condition ELSE
    {
        // echo "erreur pseudo ou email";
        $error = "<p class='col-md-3 mx-auto bg-danger text-white text-center p-3 rounded'>Identifiants ou mot de passe erronés.</p>";
    }

}

require_once('inc/header.inc.php');
require_once('inc/nav.inc.php');
?>

<h1 class="display-4 text-center my-4">Identifiez-vous</h1>

<?php if(isset($error)) echo $error; // affichage du message d'erreur en cas d'erreur d'identifiants ?>

<form method="post" class="col-md-4 mx-auto" action="">
    <div class="form-group">
        <label for="pseudo_email">Pseudo / Email</label>
        <input type="text" class="form-control" id="pseudo_email" name="pseudo_email" value="<?php if(isset($_POST['pseudo_email'])) echo $_POST['pseudo_email']; ?>">
    </div>
    <div class="form-group">
        <label for="password">Mot de passe</label>
        <input type="password" class="form-control" id="password" name="password"> 
    </div>
    <button type="submit" class="btn btn-dark">CONNEXION</button>
</form>

<?php 
require_once('inc/footer.inc.php');