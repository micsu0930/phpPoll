<?php

session_start();



function makePoll($array){
  $fp = fopen("polls.json", 'r');
  $json = fread($fp,filesize("polls.json"));
  $polls = json_decode($json,true);

  array_pop($array);
  $newPoll = Array();
  $count = count($polls) + 1;
  $newId = "poll" . $count;
  $newPoll['id'] = $newId;
  $newPoll['question'] = $array['question'];

  $temp = $array;
  if(isset($_POST['multiple'])){
    
    $temp = array_slice($temp, 3);
    
    

    $options = Array();
    foreach($temp as $option){
      $options[] = $option;
    }
    $newPoll['options'] = $options;
    $newPoll['isMultiple'] = True;
    $newPoll['createdAt'] = date("Y-m-d");
    $newPoll['deadline'] = $array['deadline'];

    $answers = array();
    foreach($options as $id){
      $answers[$id] = 0;
    }
    $newPoll['answers'] = $answers;
    $newPoll['voted'] = array();

  } else {
    $temp = array_slice($temp, 2);
   
    echo "<br>";

    $options = Array();
    foreach($temp as $option){
      $options[] = $option;
    }
    $newPoll['options'] = $options;
    $newPoll['isMultiple'] = False;
    $newPoll['createdAt'] = date("Y-m-d");
    $newPoll['deadline'] = $array['deadline'];

    $answers = array();
    foreach($options as $id){
      $answers[$id] = 0;
    }
    $newPoll['answers'] = $answers;
    $newPoll['voted'] = array();

  }

  $polls[$newId] = $newPoll;
  
  $fpw = fopen("polls.json", "w");
  $newData = json_encode($polls,JSON_PRETTY_PRINT);
  fwrite($fpw, $newData);
  fclose($fp);
  

}
if (isset($_POST['question'])) {
  makePoll($_POST);
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
  
  <title>Admin oldal</title>
</head>
<body>
<ul>
  <li><a href=index.php>Főoldal</a></li>
  <li><a href=vote.php>Szavazás</a></li>
  <li><a href=login.php>Bejelentkezés</a></li>
  <li><a href=register.php>Regisztráció</a></li>
  
  <?php 
    if(isset($_SESSION['admin']) && $_SESSION['admin']){
      echo "<li><a class = active href=admin.php>Admin oldal</a></li>";
    }
  ?>
</ul>

<div class="header">
        <h2>Szavazás létrehozása</h2>
    </div>
      
    <form method="post" action="admin.php">
  
        
  
        <div class="input-group">
            <label>Kérdés</label>
            <input type="text" name="question" >
        </div>
        <div class="input-group">
          <label>Határidő</label>
          <input type="date" name = "deadline">
        </div>
        <div class="input-group">
            <label>Több választós</label>
            <input type="checkbox" name="multiple"  >
        </div>
        <div class="input-group">
            <label>Kérdés:</label>
            <input type="text" name="input1" id="inputField1" oninput="newField(1)">
        </div>
        <div id="newFields" class="input-group"></div>
        <script>
          var nextId = 2;
          var newFields = {};
          function newField(id) {
            var input = document.getElementById("inputField"+id);
            if (input) {
              var value = input.value;
              if (value.length > 0 && !newFields[id]) {
                newFields[id] = true;
                var newDiv = document.createElement("div");
                newDiv.innerHTML = '<input type="text" name = "input'+ nextId +'"id="inputField'+ nextId +'" oninput="newField('+ nextId +')" placeholder="Opció '+ nextId +'">';
                newDiv.setAttribute("class","input-group");
                document.getElementById("newFields").appendChild(newDiv);
                nextId++;
              }
            }
          }
        </script>
        
        <div class="input-group">
            <button type="submit" class="btn"
                        >
                Létrehozás
            </button>
        </div>

    </form>
</body>
</html>