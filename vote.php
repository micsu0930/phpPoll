<?php
session_start();

$errors = array();

    $pollsFile = fopen("polls.json", "r");
    $strPolls = fread($pollsFile, filesize("polls.json"));
    $polls = json_decode($strPolls, true);
    $actual = array();
    $old = array();


    foreach ($polls as $poll) {
        $strDate = strtotime($poll['deadline']);

        if ($strDate > time()) {
            array_push($actual, $poll);
        } else {
            array_push($old, $poll);
        }


    }
    fclose($pollsFile);
    $coll = array_column($actual, 'deadline');
    array_multisort($coll, SORT_ASC, $actual);
    $coll2 = array_column($old, 'deadline');
    array_multisort($coll2, SORT_ASC, $old);

    $answers = array_slice($_POST, 1);

    if (isset($_SESSION['id'])) {

    if (isset($_POST['id'])) {
        if (count($answers) > 0) {




            if (isset($_POST['id'])) {
                $canVote = true;
                foreach ($polls[$_POST['id']]['voted'] as $v) {
                    if ($_SESSION['id'] === $v) {
                        $canVote = false;
                        $errors['already'] = "Erre már szavaztál!";
                    }
                }

                if ($canVote) {
                    foreach (array_slice($_POST, 1) as $index) {
                        $polls[$_POST['id']]['answers'][array_keys($polls[$_POST['id']]['answers'])[$index]]++;

                    }
                    array_push($polls[$_POST['id']]['voted'], $_SESSION['id']);

                    $json = json_encode($polls, JSON_PRETTY_PRINT);
                    $fp = fopen('polls.json', 'w');
                    fwrite($fp, $json);
                    fclose($fp);
                    $errors['none'] = ":)";

                }
            }
        } else {
            $errors['missing'] = "Kötelező szavazatot megadni!";
        }
    }

} else {
    $errors['nouser'] = "Jelentkezz be!";
}
/*
foreach($_POST as $paramname=>$paramval){
    $id = substr($paramname, 0, 4);
    $tmp = $polls[$id]['answers'][array_keys($polls[$id]['answers'])[0]];

}*/

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" type="text/css"
                    href="style.css">
  
  <title>Szavazás</title>
</head>
<body>
<ul>
  <li><a href=index.php>Főoldal</a></li>
  <li><a class="active" href=vote.php>Szavazás</a></li>
  <li><a href=login.php>Bejelentkezés</a></li>
  <li><a href=register.php>Regisztráció</a></li>
  <?php 
    if(isset($_SESSION['admin']) && $_SESSION['admin']){
      echo "<li><a href=admin.php>Admin oldal</a></li>";
    }
  ?>


</ul>
<?php
        if(isset($errors['none'])){
            echo "<div class = header ><div class = succes> Sikeresen leadva!</div></div>";
        }
        if(isset($errors['missing'])){
        echo "<div class = header ><div class = error> {$errors['missing']}</div></div>";
        }
        if(isset($errors['already'])){
            echo "<div class = header ><div class = error> {$errors['already']}</div></div>";
        }
        if(isset($errors['nouser'])){
            echo "<div class = header ><div class = error> {$errors['nouser']}</div></div>";
        }
    ?>
<div class= "header">
<h1>Szavazó lapok:</h1>
</div>
</div>
  <?php foreach($actual as $act) : ?>
    
    <div class="header">
    <h2><?= $act["question"] ?></h2>
    </div>
    <form method="post" action="vote.php">
        <?php
        echo "<input type = hidden id= {$act['id']} name=id value = {$act['id']}> ";
            if($act['isMultiple'] === true){
                $c=0;
                
                foreach($act['options'] as $q){
                    
                    $qID = $act['id']."$c";
                    echo "<div class = input-group-vote>";
                echo "<input type = checkbox id= {$qID} name={$c} value = {$c}> <label for= {$qID}>{$q}</label>";
                
                echo "</div>";
                $c++;
                }

            } else {
                $c = 0;

                foreach ($act['options'] as $q) {
                    
                    $qID = $act['id'] . "$c";
                    echo "<div class = input-group-vote>";
                echo "<input type = radio id= {$qID} name={$act['id']} value = {$c}> <label for= {$qID}>{$q}</label>";

                    echo "</div>";
                    $c++;
            }
            echo "<div class = input-group>";
        echo "Kiírva: {$act['createdAt']}";
        echo "</div>";
        echo "<div class = input-group>";
        echo "Lejár: {$act['deadline']}";
        echo "</div>";
        }
        ?>
        <button type="submit" class="btn"
                        value=<?=$act['id']?>>
                Beküld!
            </button>
    </form>
<?php endforeach; ?>
</body>
</html>