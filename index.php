<?php  
    
    $server = "localhost:5555";
    $user = "root";
    $pass = "dbdisk";

    $ISADMIN = 0;

    if(!isset($co)) {
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
                    id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
                    name VARCHAR(100),
                    price FLOAT(24),
                    info MEDIUMTEXT,
                    type VARCHAR(10)
                )");
                $co->query("CREATE TABLE users
                (
                    id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
                    name VARCHAR(20),
                    hash VARCHAR(100),
                    token VARCHAR(40),
                    isAdmin BOOL
                )");
                $co->query("INSERT INTO users (name, hash, token, isAdmin) VALUE 
                        ('superuser',
                        'b4967e11a22aee03ffbca84ecf16bb4cd98e1357198ca52c79c523af70fea6ce',
                        'locked',
                        TRUE 
                        )"); //Compte admin hardcodé
            }
        }
    }
    if(isset($_COOKIE["CovidToken"])) {
        $sqlsoup = $co->prepare("SELECT * FROM users WHERE token LIKE ?");
        $sqlsoup->bind_param("s", $_COOKIE["CovidToken"]);
        $sqlsoup->execute();

        $row = mysqli_fetch_assoc($sqlsoup->get_result());

        $ISADMIN = $row["isAdmin"];
    }
?>

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
            <?php if(isset($_COOKIE["CovidToken"])): ?>
                <a href="index.php?menu=logout">Déconnexion</a>
                <div class="NameText">
                    <?php echo "<p><span style='color: #00FF00'>".$_COOKIE['CovidName']."</span></p>" ?>
                    <img src="icons/user.png">
                </div>
                <?php if($ISADMIN == 1): ?>
                    <a href="index.php?menu=admin"><span style='color: #FF0000'>Admin</span></a>
                <?php endif; ?>
            <?php else: ?>
                <a href="index.php?menu=login">Connexion</a>
            <?php endif; ?>
        </div>
        <script>
            function search(uk)
            {
                if(event.key == "Enter" && document.getElementById("searchbar").value != "")
                {
                    window.location.href = "index.php?menu=search&obj=".concat(document.getElementById("searchbar").value);
                }
            }
        </script>
        <input id="searchbar" type="text" class="search" name="Recherche" placeholder="Recherche" onkeydown="search(this)">
    </body>
</html>

<?php 

    if(isset($_GET["menu"])) {
        switch ($_GET["menu"]) {
            case 'shields':
                $res = $co->query("SELECT * FROM items WHERE type='shield'");
                if($res->num_rows == 0) {
                    echo "Aucun article ;(";
                    break;
                }

                while ($row = mysqli_fetch_assoc($res)) {
                    
                    ?>

                        <div class="itemframe">
                            <h1><?=$row["name"]?></h1>
                            <h2><?=$row["price"]."€"?></h2>
                            <img src="placeholder.png">
                            <p><?=$row["info"]?></p>
                        </div>

                    <?php

                }
                break;
            case 'guns':
                
                $res = $co->query("SELECT * FROM items WHERE type='gun'");
                if($res->num_rows == 0) {
                    echo "Aucun article ;(";
                    break;
                }

                while ($row = mysqli_fetch_assoc($res)) {
                    
                    ?>

                        <div class="itemframe">
                            <h1><?=$row["name"]?></h1>
                            <h2><?=$row["price"]."€"?></h2>
                            <img src="placeholder.png">
                            <p><?=$row["info"]?></p>
                        </div>

                    <?php

                }

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
                        <form id="creds" onsubmit="return checkCreds()" action="index.php" method="post">
                            <ul>
                                <li><input type="hidden" name="action" value="login"></li>
                                <li><input type="text" name="Username" placeholder="Nom d'Utilisateur" autocomplete="off" maxlength="20"></li>
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
                        <form id="creds" onsubmit="return checkCreds()" action="index.php" method="post">
                            <ul>
                                <li><input type="hidden" name="action" value="signin"></li>
                                <li><input type="text" name="Username" placeholder="Nom d'Utilisateur" autocomplete="off" maxlength="20"></li>
                                <li><input type="password" name="Password" placeholder="Mot de passe"></li>
                                <li><input type="password" name="PasswordC" placeholder="Confirmer mot de passe"></li>
                                <li><input type="submit" name="submit"></li>
                            </ul>
                        </form>
                    </div>
                </div>

                <?php
                break;
            case 'logout':
                $sqlsoup = $co->prepare("UPDATE users SET token = 'locked' WHERE token = ?");
                $sqlsoup->bind_param("s", $_COOKIE["CovidToken"]);
                $sqlsoup->execute();
                setcookie("CovidToken", null);
                setcookie("CovidName", null);
                header("Location: index.php");
                break;
            case 'search':
                if(!isset($_GET["obj"])) {
                    echo "<h1>Bad Request</h1>";
                    break;
                }
                $req = "%".$_GET["obj"]."%";
                $sqlsoup = $co->prepare("SELECT * FROM items WHERE name LIKE ?");
                $sqlsoup->bind_param("s", $req);
                $sqlsoup->execute();
                $res = $sqlsoup->get_result();
                if($res->num_rows == 0) {
                    echo "Aucun résultat ;(";
                    break;
                }else
                    echo $res->num_rows." Résultats";

                while ($row = mysqli_fetch_assoc($res)) {
                    
                    ?>

                        <div class="itemframe">
                            <h1><?=$row["name"]?></h1>
                            <h2><?=$row["price"]."€"?></h2>
                            <img src="placeholder.png">
                            <p><?=$row["info"]?></p>
                        </div>

                    <?php

                }
                break;
            case 'admin':
                if(isset($_COOKIE["CovidToken"])) {
                    $sqlsoup = $co->prepare("SELECT * FROM users WHERE token LIKE ?");
                    $sqlsoup->bind_param("s", $_COOKIE["CovidToken"]);
                    $sqlsoup->execute();
                    $row = mysqli_fetch_assoc($sqlsoup->get_result());
                    if($row["isAdmin"] == 1) {
                        echo "Page Admin";

                        ?>
                            <div class="Box">
                                <p>Créateur d'objets</p>
                                <form id="chars" action="index.php" method="post">
                                    <ul>
                                        <li><input type="hidden" name="action" value="newitem"></li>
                                        <li><input type="text" name="Name" placeholder="Nom" autocomplete="off" maxlength="20"></li>
                                        <li><input type="number" name="Price" placeholder="Prix"></li>
                                        <li><input type="text" name="Info" placeholder="Description"></li>
                                        <li><input type="text" name="Type" placeholder="Type: shield, gun"></li>
                                        <li><input type="submit" name="submit"></li>
                                    </ul>
                                </form>
                            </div>

                            <div class="Box">
                                <p>Gestionnaire du magazin</p>
                                <ul>

                                    <?php

                                    ?>

                                </ul>
                            </div>
                        <?php


                    }else
                        echo "Que cherchez vous ici?";
                }else
                    echo "Que cherchez vous ici?";
                break;
            default:
                echo "<h1>Erreur 404</h1>
                      <p>Page non trouvée</p>";
                break;
        }
        }else{
            if(isset($_POST["action"])) {
                switch ($_POST["action"]) {
                    case 'signin':
                        if(!($_POST["Username"] && $_POST["Password"]))
                            break;
                        if(strlen($_POST["Username"]) > 20)
                            break;
                        $sqlsoup = $co->prepare("SELECT * FROM users WHERE name LIKE ?");
                        $sqlsoup->bind_param("s", $_POST["Username"]);
                        $sqlsoup->execute();
                        if($sqlsoup->get_result()->num_rows == 0) {
                            $hash = hash("sha256", $_POST["Password"], false);
                            $query = "INSERT INTO users (name, hash, token, isAdmin) VALUE (?, ?, 'locked', FALSE)";
                            $sqlsoup = $co->prepare($query);
                            $sqlsoup->bind_param("ss", $_POST["Username"], $hash);
                            $sqlsoup->execute();
                        }else{
                            echo "Nom déja utilisé";
                        }

                        ?>
                            <h1>Compte crée avec success!</h1>
                            <a href="index.php?menu=login">Retour a la page de connexion</a>
                        <?php

                        break;
                    case 'login':
                        if(!($_POST["Username"] && $_POST["Password"]))
                            break;
                        $sqlsoup = $co->prepare("SELECT * FROM users WHERE name LIKE ?");
                        $sqlsoup->bind_param("s", $_POST["Username"]);
                        $sqlsoup->execute();
                        $res = $sqlsoup->get_result();
                        if($res->num_rows != 0) {
                            $account = mysqli_fetch_assoc($res);
                            if($account["hash"] == hash("sha256", $_POST["Password"])) {
                                $rand = random_bytes(50);
                                $token = hash("MD5", $rand);
                                $query = "UPDATE users SET token = '".$token."' WHERE id = ".$account['id'];
                                $co->query($query);
                                setcookie("CovidToken", $token);
                                setcookie("CovidName", $account["name"]);
                                header("Location: index.php");
                            }else
                                echo "<h1>Utilisateur ou mot de passe incorrect</h1>";

                        }else
                            echo "<h1>Utilisateur ou mot de passe incorrect</h1>";
                        break;

                    case 'newitem':
                        if(isset($_COOKIE["CovidToken"])) {
                            $sqlsoup = $co->prepare("SELECT * FROM users WHERE token LIKE ?");
                            $sqlsoup->bind_param("s", $_COOKIE["CovidToken"]);
                            $sqlsoup->execute();
                            $row = mysqli_fetch_assoc($sqlsoup->get_result());
                            if($row["isAdmin"] == 1) {
                                $sqlsoup = $co->prepare("INSERT INTO items (name, price, info, type) VALUE (?, ?, ?, ?)");
                                $sqlsoup->bind_param("sdss", $_POST["Name"], $_POST["Price"], $_POST["Info"], $_POST["Type"]);
                                $sqlsoup->execute();
                                echo $sqlsoup->error;
                            }
                        }
                        break;
                    default:
                        
                        break;
                }
            }
        }
?>