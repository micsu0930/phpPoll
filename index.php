<?php
session_start();

$pollsFile = fopen("polls.json","r");
$strPolls = fread($pollsFile, filesize("polls.json"));
$polls = json_decode($strPolls,true);
$actual = array();
$old = array();
foreach($polls as $poll){
    $strDate = strtotime($poll['deadline']);
    
    if($strDate > time()){
        array_push($actual,$poll);
    } else {
        array_push($old,$poll);
    }


}
$coll = array_column($actual, 'createdAt');

array_multisort($coll, SORT_DESC, $actual);
$coll2 = array_column($old, 'createdAt');
array_multisort($coll2, SORT_DESC, $old);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" type="text/css"
                    href="style.css">
  
  <title>Főoldal</title>
</head>
<body>
<ul>
  <li><a class="active" href=index.php>Főoldal</a></li>
  <li><a href=vote.php>Szavazás</a></li>
  <li><a href=login.php>Bejelentkezés</a></li>
  <li><a href=register.php>Regisztráció</a></li>
  <?php 
    if(isset($_SESSION['admin']) && $_SESSION['admin']){
      echo "<li><a href=admin.php>Admin oldal</a></li>";
    }
  ?>
  

</ul>
<div class= "header">
<h1>Szavazó lapok:</h1>
</div>
<?php if(isset($_SESSION['username'])) : ?>
  <?php foreach($actual as $act) : ?>
    
    <div class="header">
    <h2><?= $act["question"] ?></h2>
    </div>
    <form method="post" action="vote.php">
        <?php
        foreach($act['options'] as $o){
            echo "{$o}: {$act['answers'][$o]} szavazat <br>";
        }
        echo "<div class = input-group>";
        echo "Kiírva: {$act['createdAt']}";
        echo "</div>";
        echo "<div class = input-group>";
        echo "Lejár: {$act['deadline']}";
        echo "</div>";
        ?>
        <button type="submit" class="btn"
                        >
                Szavazok!
            </button>
    </form>
<?php endforeach; ?>
<?php endif; ?>
<?php if(!isset($_SESSION['username'])) : ?>
  <?php foreach($actual as $act) : ?>
    
    <div class="header">
    <h2><?= $act["question"] ?></h2>
    </div>
    <form method="post" action="login.php">
        <?php
        foreach($act['options'] as $o){
            echo "{$o}: {$act['answers'][$o]} szavazat <br>";
        }
        echo "<div class = input-group>";
        echo "Kiírva: {$act['createdAt']}";
        echo "</div>";
        echo "<div class = input-group>";
        echo "Lejár: {$act['deadline']}";
        echo "</div>";
        ?>
        <button type="submit" class="btn"
                        >
                Szavazok!
            </button>
    </form>
<?php endforeach; ?>
<?php endif; ?>
<br>
<?php foreach($old as $ol) : ?>
    
    <div class="header-old">
    <h2><?= $ol["question"] ?></h2>
    </div>
    <form method="post" action="vote.php">
        <?php
        foreach($ol['options'] as $o){
            echo "{$o}: {$ol['answers'][$o]} szavazat <br>";
        }
        echo "<div class = input-group>";
        echo "Kiírva: {$ol['createdAt']}";
        echo "</div>";
        echo "<div class = input-group>";
        echo "Lejárt: {$ol['deadline']}";
        echo "</div>";
        ?>

    </form>
<?php endforeach; ?>
</body>
</html>