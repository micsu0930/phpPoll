<?php
session_start();

$errors = array();

function login($un,$pw,$errors){
    $fp = fopen("users.json", 'r');
    $json = fread($fp,filesize("users.json"));
    $users = json_decode($json,true);
    

    $nameOK = false;
    $pwOK = false;

    foreach($users as $user){
        
        if ($user['username'] === $un){
            $nameOK = true;
        }
        if ($user['password'] === $pw){
            $pwOK = true;
        }
    }

    if($nameOK && $pwOK){
        $_SESSION['username'] = $un;
        $_SESSION['password'] = $pw;
        $errors['none'] = ":)";
        foreach ($users as $us) {
            if($us['username'] === $un && $us['password'] === $pw){
                $_SESSION['id'] = $us['id'];
                $_SESSION['admin'] = $us['isAdmin'];
            }
        }
    }
    if(!$nameOK){
        $errors['username']="Nem létező felhasználónév";
    }
    if(!$pwOK){
        $errors['password']="Hibás jelszó";
    }

    return $errors;
}

//$_SESSION['username'] = $_POST['username'];
//$_SESSION['password'] = $_POST['password'];



if(isset($_POST['username']) && isset($_POST['password']) && ($_POST['username'] !="" && $_POST['password'] != "")){
    $errors=login($_POST['username'], $_POST['password'] ,$errors);
    
}

if(isset($_POST['username']) && $_POST['username'] === ""){
    $errors['username'] = "Kötelező nevet megadni!";
}
if(isset($_POST['password']) && $_POST['password'] === ""){
    $errors['password'] = "Kötelező jelszót megadni";
}



?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <title>Bejelentkezés</title>
  <link rel="stylesheet" type="text/css"
                    href="style.css">
</head>
<body>
<ul>
  <li><a href=index.php>Főoldal</a></li>
  <li><a href=vote.php>Szavazás</a></li>
  <li><a class="active" href=login.php>Bejelentkezés</a></li>
  <li><a href=register.php>Regisztráció</a></li>
  <?php 
    if(isset($_SESSION['admin']) && $_SESSION['admin']){
      echo "<li><a href=admin.php>Admin oldal</a></li>";
    }
  ?>
</ul>

<div class="header">
        <h2>Jelentkezz be itt!</h2>
    </div>
      
    <form method="post" action="login.php">
    <?php
        if(isset($errors['none'])){
            echo "<div class = success> Sikeres bejelentkezés!</div>";
        }
    ?>
        
        
        <div class="input-group">
            <label>Felhasználó név</label>
            <input type="text" name="username" >
            
        </div>
        <?php
        if(isset($errors['username'])){
            echo "<div class = error> {$errors['username']}</div>";
        }
        ?>
        
        <div class="input-group">
            <label>Jelszó</label>
            <input type="password" name="password">
            
        </div>
        <?php
        if(isset($errors['password'])){
            echo "<div class = error> {$errors['password']}</div>";
        }
        ?>
        <div class="input-group">
            <button type="submit" class="btn"
                        name="loginUser">
                Bejelentkezés
            </button>
        </div>

    </form>
</body>
</html>