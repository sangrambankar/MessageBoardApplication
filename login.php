<!DOCTYPE HTML>
<html>
<head>
  <title>MessageBoard</title>
  <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Lato" />
  <link rel="stylesheet" type="text/css" href="stylesheet.css">
</head>
<script type="text/javascript">
function showMsg(message) {
    var x = document.getElementById("snackbar")
    x.innerHTML = '<h1>'+message+'</h1>';
    x.className = "show";
    setTimeout(function(){ x.className = x.className.replace("show", ""); }, 5000);
}
</script>
<body>
<div class="header"><h1>Message Board Application</h1></div>
<div class="container">
  <div id="login" class="left">
    <p><h1>Login User</h1></p>
    <form action="login.php" method="POST">
      <input type="text" name="username" placeholder="Username"></br></br>
      <input type="password" name="password" placeholder="Password"></br></br>
      <input type="submit" value="Login" class="button">
    </form>
  </div>

  <div id="register" class="right">
    <p><h1>Register User</h1></p>
    <form action="login.php" method="POST">
      <input type="text" name="username" placeholder="Username"></br></br>
      <input type="password" name="password" placeholder="Password"></br></br>
      <input id="fullname" type="text" name="fullname" placeholder="FullName"></br></br>
      <input id ="email" type="text" name="email" placeholder="Email"></br></br>

      <input type="submit" value="Register" class="button">
    </form>
  </div>

  <div id="snackbar"></div>
<div>
</body>
</html>
<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors','On');
try {
  $pdo = new PDO("mysql:host=127.0.0.1:3306;dbname=board","root","",array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
  $pdo->beginTransaction();
  $pdo->commit();

  if(isset($_POST['username']) && isset($_POST['password']) && isset($_POST['fullname']) && isset($_POST['email'])){

    if($_POST['username']!='' && $_POST['password']!=''&& $_POST['fullname']!=''&& $_POST['email']!=''){
      $query='select * from users where username="'.$_POST['username'].'";';
      $statement = $pdo->prepare($query);
      $statement->execute();
      $exists=false;
      while ($row = $statement->fetch()) {
        $exists=true;
      }

      if(!$exists){
        $query='insert into users values("'.$_POST['username'].'","' . md5($_POST['password']) . '","'.$_POST['fullname'].'","'.$_POST['email'].'");';
        $statement = $pdo->prepare($query);
        $statement->execute();
        $_SESSION['username']=$_POST['username'];
        $_SESSION['fullname']=$_POST['fullname'];
        echo '<script type="text/javascript">showMsg("Registration successfull.");</script>';
        header( 'Location: board.php');
      }else{
        echo '<script type="text/javascript">showMsg("Username already exists. Try a different Username");</script>';
      }
    }else{
      echo '<script type="text/javascript">showMsg("Error: Enter valid values in Register form");</script>';
    }
  }else if(isset($_POST['username']) && isset($_POST['password'])){
    if($_POST['username']!='' && $_POST['password']!=''){
      $statement = $pdo->prepare('select username,fullname from users where username="'.$_POST['username'].'" and password="'.md5($_POST['password']).'";');
      $row=$statement->execute();
      if(!$row){
        echo '<script type="text/javascript">showMsg("Somehthing went wrong. Please try again later!");</script>';
        header('Location: login.php');
      }else{
        while ($row = $statement->fetch()) {
          $_SESSION['username']=$row['username'];
          $_SESSION['fullname']=$row['fullname'];
          header( 'Location: board.php');
        }
      }
    }else{
        echo '<script type="text/javascript">showMsg("You should enter username/password");</script>';
    }
  }
}catch (PDOException $e) {
  print "Error!: " . $e->getMessage() . "<br/>";
  die();
}

?>
