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
                    type VARCHAR(10),
                    image VARCHAR(100)
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
        if(isset($row))
            $ISADMIN = $row["isAdmin"];
    }else
        setcookie("CovidToken", null);
?>

<!DOCTYPE html>
<html>
    <head>
        <title>SAV Covid19</title>
        <link rel="stylesheet" href="css.css">
        <link rel="icon" href="favicon.ico">
    </head>

    <body>
        <div class="bar">
            <img src="logo.png" class="logo" width="50px" height="70px" onclick="window.location.href = 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'">
            <a href="/">Accueil</a>
            <a href="/?menu=shields">Boucliers</a>
            <a href="/?menu=guns">Pistolets</a>
            <a href="/?menu=showall">Tout</a>
            <?php if(isset($_COOKIE["CovidToken"])): ?>
                <a href="/?menu=logout">Déconnexion</a>
                <div class="NameText">
                    <?php echo "<p><span style='color: #00FF00'>".$_COOKIE['CovidName']."</span></p>" ?>
                    <img src="icons/user.png">
                </div>
                <?php if($ISADMIN == 1): ?>
                    <a href="/?menu=admin"><span style='color: #FF0000'>Admin</span></a>
                <?php endif; ?>
            <?php else: ?>
                <a href="/?menu=login">Connexion</a>
            <?php endif; ?>
        </div>
        <script>
            function search(uk)
            {
                if(event.key == "Enter" && document.getElementById("searchbar").value != "")
                {
                    window.location.href = "/?menu=search&obj=".concat(document.getElementById("searchbar").value);
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

                        <div class="itemframe" onclick="window.location.href = ''">
                            <h1><?=$row["name"]?></h1>
                            <h2><?=$row["price"]."€"?></h2>
                            <img onclick="window.location.href = '<?=$row["image"]?>'" ondragstart="return false" src=<?=$row["image"]?>>
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
                            <img onclick="window.location.href = '<?=$row["image"]?>'" ondragstart="return false" src=<?=$row["image"]?>>
                            <p><?=$row["info"]?></p>
                        </div>

                    <?php

                }

                break;
            case 'showall':
                
                $res = $co->query("SELECT * FROM items");
                if($res->num_rows == 0) {
                    echo "Aucun article ;(";
                    break;
                }

                while ($row = mysqli_fetch_assoc($res)) {
                    
                    ?>

                        <div class="itemframe">
                            <h1><?=$row["name"]?></h1>
                            <h2><?=$row["price"]."€"?></h2>
                            <img onclick="window.location.href = '<?=$row["image"]?>'" ondragstart="return false" src=<?=$row["image"]?>>
                            <p><?=$row["info"]?></p>
                        </div>

                    <?php

                }

                break;
            case 'more':
                
                if(isset($_GET["id"])) {
                    $sqlsoup = $co->prepare("SELECT * FROM items WHERE id = ?");
                    $sqlsoup->bind_param("i", $_GET["id"]);
                    $sqlsoup->execute();
                    $row = mysqli_fetch_assoc($sqlsoup->get_result());
                    if(!isset($row))
                        break;

                    echo $row["name"];
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
                        <form id="creds" onsubmit="return checkCreds()" action="/" method="post">
                            <ul>
                                <li><input type="hidden" name="action" value="login"></li>
                                <li><input type="text" name="Username" placeholder="Nom d'Utilisateur" autocomplete="off" maxlength="20"></li>
                                <li><input type="password" name="Password" placeholder="Mot de passe"></li>
                                <li><input type="submit" name="submit"></li>
                            </ul>
                        </form>
                    </div>
                    <a href="/?menu=signin">Créer un compte</a>
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
                        <form id="creds" onsubmit="return checkCreds()" action="/" method="post">
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
                header("Location: /");
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
                            <img onclick="window.location.href = '<?=$row["image"]?>'" src=<?=$row["image"]?>>
                            <p><?=$row["info"]?></p>
                        </div>

                    <?php

                }
                break;
            case 'admin':
                if(isset($_COOKIE["CovidToken"]) && $_COOKIE["CovidToken"] != "locked") {
                    $sqlsoup = $co->prepare("SELECT * FROM users WHERE token LIKE ?");
                    $sqlsoup->bind_param("s", $_COOKIE["CovidToken"]);
                    $sqlsoup->execute();
                    $row = mysqli_fetch_assoc($sqlsoup->get_result());
                    if($row["isAdmin"] == 1) {
                        echo "Page Admin";

                        ?>
                            <div class="Box">
                                <p>Créateur d'objets</p>
                                <form id="chars" enctype="multipart/form-data" action="/" method="post">
                                    <ul>
                                        <li><input type="hidden" name="action" value="newitem"></li>
                                        <li><input type="text" name="Name" placeholder="Nom" autocomplete="off" maxlength="100"></li>
                                        <li><input type="number" step="any" name="Price" placeholder="Prix"></li>
                                        <li><input type="text" name="Info" placeholder="Description"></li>
                                        <li><input type="text" name="Type" placeholder="Type: shield, gun"></li>
                                        <li><input type="file" name="itemimage" accept="image/png, image/jpeg" value="Image"></li>
                                        <li><input type="submit" name="submit" value="Créer"></li>
                                    </ul>
                                </form>
                            </div>

                            <div class="Box2">

                                <p>Gestionnaire du magazin</p>
                                <ul>
                                    <?php
                                        $res = $co->query("SELECT * FROM items");
                                        while($row = mysqli_fetch_assoc($res))
                                        {
                                            ?>
                                                <li>
                                                    <div class="itemlist">
                                                        <p><?=$row["name"]?></p>
                                                        <form action="/"  method="post">
                                                            <input type="hidden" name="action" value="delitem">
                                                            <input type="hidden" name="id" value="<?=$row["id"]?>">
                                                            <button type="submit">Supp</button>
                                                        </form>
                                                    </div>
                                                </li>
                                            <?php
                                        }

                                    ?>

                                </ul>
                            </div>
                            <div class="Box2">
                                <p>Gestionnaire des comptes</p>
                                <ul>
                                    <?php
                                        $res = $co->query("SELECT * FROM users");
                                        while($row = mysqli_fetch_assoc($res))
                                        {
                                            if($row["id"] == 1)
                                                continue;
                                            ?>
                                                <li>
                                                    <div class="itemlist">
                                                        <p><?=$row["name"]?></p>
                                                        <form action="/"  method="post">
                                                            <input type="hidden" name="action" value="deluser">
                                                            <input type="hidden" name="id" value="<?=$row["id"]?>">
                                                            <button type="submit">Supp</button>
                                                        </form>
                                                    </div>
                                                </li>
                                            <?php
                                        }

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
                            $name = strip_tags($_POST["Username"]);
                            $sqlsoup->bind_param("ss", $name, $hash);
                            $sqlsoup->execute();
                        }else{
                            echo "<script>alert('Nom déja utilisé')</script>";
                            break;
                        }
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
                                $rand = random_bytes(200);
                                $token = hash("MD5", $rand);
                                $query = "UPDATE users SET token = '".$token."' WHERE id = ".$account['id'];
                                $co->query($query);
                                setcookie("CovidToken", $token);
                                setcookie("CovidName", $account["name"]);
                                header("Location: /");
                            }else
                                echo "<h1>Utilisateur ou mot de passe incorrect</h1>";

                        }else
                            echo "<h1>Utilisateur ou mot de passe incorrect</h1>";
                        break;

                    case 'newitem':
                        if(isset($_COOKIE["CovidToken"]) && strtolower($_COOKIE["CovidToken"]) != "locked") {
                            $sqlsoup = $co->prepare("SELECT * FROM users WHERE token LIKE ?");
                            $sqlsoup->bind_param("s", $_COOKIE["CovidToken"]);
                            $sqlsoup->execute();
                            $row = mysqli_fetch_assoc($sqlsoup->get_result());
                            if($row["isAdmin"] == 1) {
                                if(!file_exists("cache/"))
                                    mkdir("cache");
                                $pathimage = "placeholder.png";
                                if(isset($_FILES["itemimage"]) && preg_match("#jpeg|png#", $_FILES["itemimage"]["type"])) {
                                    $name = hash("MD5", random_bytes(200)).".".pathinfo($_FILES["itemimage"]["name"], PATHINFO_EXTENSION);
                                    move_uploaded_file($_FILES["itemimage"]["tmp_name"], "cache/".$name);
                                    $pathimage = "cache/".$name;
                                }
                                $sqlsoup = $co->prepare("INSERT INTO items (name, price, info, type, image) VALUE (?, ?, ?, ?, ?)");
                                $sqlsoup->bind_param("sdsss", $_POST["Name"], $_POST["Price"], $_POST["Info"], $_POST["Type"], $pathimage);
                                $sqlsoup->execute();
                                header("Location: /?menu=admin");
                            }
                        }
                        break;
                    case 'delitem':
                        if(isset($_COOKIE["CovidToken"]) && $_COOKIE["CovidToken"] != "locked") {
                            $sqlsoup = $co->prepare("SELECT * FROM users WHERE token LIKE ?");
                            $sqlsoup->bind_param("s", $_COOKIE["CovidToken"]);
                            $sqlsoup->execute();
                            $row = mysqli_fetch_assoc($sqlsoup->get_result());
                            if($row["isAdmin"] == 1) {
                                if(isset($_POST["id"])) {
                                    $sqlsoup = $co->prepare("SELECT * FROM items WHERE id = ?");
                                    $sqlsoup->bind_param("d", $_POST["id"]);
                                    $sqlsoup->execute();
                                    $row = mysqli_fetch_assoc($sqlsoup->get_result());
                                    if($row["image"] != "placeholder.png") {
                                        unlink($row["image"]);
                                    }


                                    $sqlsoup = $co->prepare("DELETE FROM items WHERE id = ?");
                                    $sqlsoup->bind_param("d", $_POST["id"]);
                                    $sqlsoup->execute();
                                    header("Location: /?menu=admin");
                                }
                            }
                        }
                        break;
                    case 'deluser':
                        if(isset($_COOKIE["CovidToken"]) && $_COOKIE["CovidToken"] != "locked") {
                            $sqlsoup = $co->prepare("SELECT * FROM users WHERE token LIKE ?");
                            $sqlsoup->bind_param("s", $_COOKIE["CovidToken"]);
                            $sqlsoup->execute();
                            $row = mysqli_fetch_assoc($sqlsoup->get_result());
                            if($row["isAdmin"] == 1) {
                                if(isset($_POST["id"])) {
                                    $sqlsoup = $co->prepare("DELETE FROM users WHERE id = ?");
                                    $sqlsoup->bind_param("d", $_POST["id"]);
                                    $sqlsoup->execute();
                                    header("Location: /?menu=admin");
                                }
                            }
                        }
                        break;
                    default:
                        
                        break;
                }
            }
            ?>
                <div class="titlebox">
                    <p>Notre entreprise Service Après Vente Covid a été créée pour ralentir la pandémie mondiale en proposant la vente de produits accessibles à tous et à des prix “abordables”</p>
                </div>
                <div class="titlebox">
                    <p>Équipe:<br><br>
                        -Loik Dijoux<br>
                        -Jordan Palacios<br>
                        -Antonin Laudon<br>
                        -Theophilus Homawoo<br>
                        -Bartosz Michalak</p>
                </div>
                <img src="gouv.png" class="gouv">
            <?php
        }
?>