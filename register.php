<?php

session_start();
$errors = array();
function register($un,$pw1,$pw2,$email,$errors){

    $fpr = fopen("users.json", 'r');
    $jsonr = fread($fpr,filesize("users.json"));
    $users = json_decode($jsonr,true);

    $nameOK = true;
    $pwOK = true;
    $emailOK = true;
    $emailFormat = true;

    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $emailFormat = false;
        $errors['emailformat'] = "Nem helyes e-mail formátum!";
    }

    if ( $pw1 != $pw2){
        $pwOK = false;
        $errors['password'] = "Jelszavak nem egyeznek!";
    }



    foreach ($users as $user){
        if($user['username'] === $un){
            $nameOK = false;
            $errors['username'] = "Felhasználónév már létezik!";
        }
        if($user['email'] === $email){
            $emailOK = false;
            $errors['email'] = "E-mail cím már létezik!";
        }
    }

    

    if ($nameOK && $pwOK && $emailOK && $emailFormat){
        $newUser = Array();
        $count = count($users) + 1;
        $newID = "userid" . $count;
        $newUser['userid'] = $newID;
        $newUser['username'] = $un;
        $newUser['email'] = $email;
        $newUser['password'] = $pw1;
        $newUser['isAdmin'] = false;

        $users[$newID] = $newUser;
        

        $jsonw = json_encode($users, JSON_PRETTY_PRINT);
        $fpw = fopen("users.json", 'w');
        fwrite($fpw, $jsonw);
        $errors['none'] = ":)";

        $_SESSION['username'] = $un;
        $_SESSION['password'] = $pw1;
        $_SESSION['id'] = $newID;
        $_SESSION['admin'] = False;
    }
    return $errors;
    
}
if(isset($_POST['username']) && isset( $_POST['password1']) && isset($_POST['password2']) && isset($_POST['email']) && $_POST['username'] !="" &&  $_POST['password1'] !="" && $_POST['password2'] !="" && $_POST['email'] !=""){
$errors=register($_POST['username'], $_POST['password1'], $_POST['password2'], $_POST['email'],$errors);
}
if(isset($_POST['username']) && $_POST['username'] === ""){
    $errors['username'] = "Kötelező nevet megadni!";
}
if(isset($_POST['password1']) && $_POST['password1'] === ""){
    $errors['password'] = "Kötelező jelszót megadni!";
}
if(isset($_POST['password2']) && $_POST['password2'] === ""){
    $errors['password'] = "Kötelező jelszót megadni!";
}
if(isset($_POST['email']) && $_POST['email'] === ""){
    $errors['email'] = "Kötelező e-mailt megadni!";
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" type="text/css"
                    href="style.css">
  
  <title>Regisztráció</title>
</head>
<body>
<ul>
  <li><a href=index.php>Főoldal</a></li>
  <li><a href=vote.php>Szavazás</a></li>
  <li><a href=login.php>Bejelentkezés</a></li>
  <li><a class="active"href=register.php>Regisztráció</a></li>
  <?php 
    if(isset($_SESSION['admin']) && $_SESSION['admin']){
      echo "<li><a href=admin.php>Admin oldal</a></li>";
    }
  ?>
</ul>

  <div class="header">
        <h2>Regisztráció</h2>
    </div>
      
    <form method="post" action="register.php">
  
    <?php
        if(isset($errors['none'])){
            echo "<div class = success> Sikeres regisztráció!</div>";
        }
    ?>
  
        <div class="input-group">
            <label>Felhasználó név</label>
            <input type="text" name="username" value="<?php echo isset($_POST['username']) ? $_POST['username'] : ''; ?>" >
            <?php
                if(isset($errors['username'])){
                    echo "<div class = error> {$errors['username']}</div>";
                }
            ?>
        </div>
        <div class="input-group">
            <label>E-mail cím</label>
            <input type="text" name="email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>" >
            <?php
                if(isset($errors['email'])){
                    echo "<div class = error> {$errors['email']}</div>";
                }
            ?>
        </div>
        <div class="input-group">
            <label>Jelszó</label>
            <input type="password" name="password1" value="<?php echo isset($_POST['password1']) ? $_POST['password1'] : ''; ?>">
        </div>
        <div class="input-group">
            <label>Jelszó mégegyszer</label>
            <input type="password" name="password2" value="<?php echo isset($_POST['password2']) ? $_POST['password2'] : ''; ?>">
            <?php
                if(isset($errors['password'])){
                    echo "<div class = error> {$errors['password']}</div>";
                }
            ?>
        </div>
        <div class="input-group">
            <button type="submit" class="btn"
                        name="registerUser">
                Regisztrálás
            </button>
        </div>

    </form>

</body>
</html>