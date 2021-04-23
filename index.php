<!DOCTYPE html>
<html>
    <head>
        <title>Covid19</title>
        <link rel="stylesheet" href="css.css">
        <link rel="icon" href="favicon.ico">
    </head>

    <body>
        <div class="bar">
            <img src="logo.png" class="logo" width="50px" height="70px">
            <a href="index.php">Accueil</a>
            <a href="index.php?menu=shields">Boucliers</a>
            <a href="index.php?menu=guns">Pistolets</a>
            <a href="index.php?menu=login" class="login">Connexion</a>
        </div>
        <input type="text" class="search" name="Recherche" placeholder="Recherche">
    </body>
</html>

<?php 
    $server = "localhost:5555";
    $user = "root";
    $pass = "dbdisk";

    $co = new mysqli($server, $user, $pass);
    if($co->connect_error) {
        echo "<h1>La connexion a la base de données a échoué</h1>
              <p>Contactez l'administrateur du site</p>";
    }else{
        if(!$co->select_db("covidshop")) {
            $co->query("CREATE DATABASE covidshop");
            $co->select_db("covidshop");
            $co->query("CREATE TABLE items
            (
                name VARCHAR(100),
                price FLOAT(24),
                info MEDIUMTEXT,
                type VARCHAR(10)
            )");
            $co->query("CREATE TABLE users
            (
                name VARCHAR(20),
                pass VARCHAR(100)
            )");
        }
    }

    if(isset($_GET["menu"])) {
        switch ($_GET["menu"]) {
            case 'shields':
                echo "Boucliers";
                break;
            case 'guns':
                echo 'Pistolets';
                break;
            case 'login':
                ?>

                <script>
                    function checkCreds()
                    {
                        var form = document.getElementById("creds");
                        var formData = new FormData(form);
                        if(formData.get("Username") == "")
                            return false;
                        if(formData.get("Password") == "")
                            return false;
                        return true;
                    }
                </script>
                <div class="Loginbox">
                    <h1>Connexion</h1>
                    <div>
                        <form id="creds" onsubmit="return checkCreds()" action="intercept.php" method="post">
                            <ul>
                                <li><input type="text" name="Username" placeholder="Nom d'Utilisateur"></li>
                                <li><input type="password" name="Password" placeholder="Mot de passe"></li>
                                <li><input type="submit" name="submit"></li>
                            </ul>
                        </form>
                    </div>
                    <a href="index.php?menu=signin">Créer un compte</a>
                </div>

                <?php 
                break;
            case 'signin':
                ?>

                    <script>
                        function checkCreds()
                        {
                            var form = document.getElementById("creds");
                            var formData = new FormData(form);
                            if(formData.get("Username") == "")
                                return false;
                            if(formData.get("Password") == "")
                                return false;
                            if(formData.get("Password") != formData.get("PasswordC"))
                            {
                                alert("Les mots de passe ne correspondes pas");
                                return false;
                            }
                            return true;
                        }
                    </script>
                    <div class="Loginbox">
                    <h1>Création de compte</h1>
                    <div>
                        <form id="creds" onsubmit="return checkCreds()" action="intercept.php" method="post">
                            <ul>
                                <li><input type="text" name="Username" placeholder="Nom d'Utilisateur"></li>
                                <li><input type="password" name="Password" placeholder="Mot de passe"></li>
                                <li><input type="password" name="PasswordC" placeholder="Confirmer mot de passe"></li>
                                <li><input type="submit" name="submit"></li>
                            </ul>
                        </form>
                    </div>
                </div>

                <?php
                break;
            default:
                echo "<h1>Erreur 404</h1>
                      <p>Page non trouvée</p>";
                break;
        }
        }else{
            
        }
?>