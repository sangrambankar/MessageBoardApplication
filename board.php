<?php
    session_start();
?>
<!DOCTYPE HTML>
<html>
<head>
  <title>Message Board</title>
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
  <div id="snackbar"></div>
  <div class="header">
    <h1>Message Board Application</h1>

    <form action="board.php" method="GET" style="position: fixed;right: 0; top: 5px;">
      <h1 style="float:left;">Welcome, <?php if(isset($_SESSION['username'])){ echo $_SESSION['fullname']; } ?></h1>
      <input type="hidden" name="logout" value="true">
      <input type="submit" value="Logout" class="clear button">
    </form>
  </div>
  <div class="container">
    <?php
    error_reporting(E_ALL);
    ini_set('display_errors','On');
    try {
      $pdo = new PDO("mysql:host=127.0.0.1:3306;dbname=board","root","",array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
      $pdo->beginTransaction();
      $pdo->commit();
    

      if(isset($_GET['logout']) && isset($_SESSION['username'])){
        session_unset();
        header('Location:login.php');
      }else if(isset($_GET['replyto'])) {
        if($_POST['message']!=''){
          $query="insert into posts values('".uniqid()."','".$_GET['replyto']."','".$_SESSION['username']."',now(),'".$_POST['message']."');";
          $statement = $pdo->prepare($query);
          $statement->execute();
          header('Location:board.php');
        }else {
          echo '<script type="text/javascript">showMsg("Please enter some text in message box.");</script>';
          //header('Location:board.php');
        }
      }else if(isset($_POST['message'])){
        if($_POST['message']!=''){
          $query="insert into posts values('".uniqid()."',null,'".$_SESSION['username']."',now(),'".$_POST['message']."');";
          $statement = $pdo->prepare($query);
          $statement->execute();
          header('Location:board.php');
        }else {
          echo '<script type="text/javascript">showMsg("Please enter some text in message box.");</script>';
          //header('Location:board.php');
        }
      }

      if(isset($_SESSION['username'])){
        print '<form id="newpostform" action="board.php" method="POST">
                <div class="left">
                  <h1>New Post Section<h1><br><br>
                  <h1>Message Box</h1>
                  <textarea name="message" rows="3" cols="50" form="newpostform" placeholder="write to post something"></textarea>
                  <input type="submit" value="NewPost" class="button">
                </div>
              <div class="right">';

          $query = 'select count(*) from posts p,users u where u.username=p.postedby;';
          if ($res = $pdo->query($query)) {
            if ($res->fetchColumn() > 0) {
              $query='select p.id,u.username,u.fullname,p.datetime,p.replyto,p.message from posts p,users u where u.username=p.postedby order by p.datetime desc;';
              foreach ($pdo->query($query) as $row) {
                $replyToPost="";
                if($row['replyto']!=null)
                  $replyToPost='Reply to Post Id: '.$row['replyto'];
                  print '<div class="card">
                          <div class="carditems">
                              <div class="line">
                                <div class="line-left"><img src="https://cdn2.iconfinder.com/data/icons/pictograms-vol-1/400/calendar-32.png"/><span>'.$row['datetime'].'</span></div>
                                <div class="line-right"><img src="https://cdn2.iconfinder.com/data/icons/pictograms-vol-1/400/exclamation-32.png"/><span>'.$row['id'].'</span></div>
                              </div>
                              <div class="line">
                                <div class="line-left"><img src="https://cdn2.iconfinder.com/data/icons/pictograms-vol-1/400/human-32.png"/><span>Username : '.$row['username'].'</span></div>
                                <div class="line-right"><img src="https://cdn2.iconfinder.com/data/icons/pictograms-vol-1/400/star-32.png"/><span>Full Name : '.$row['fullname'].'</span></div>
                              </div>';
                              if($replyToPost!='')
                                print '<div class="line"><img src="https://cdn2.iconfinder.com/data/icons/pictograms-vol-1/400/humans-32.png" /><span>'.$replyToPost.'</span></div>';

                  print'</div>
                          <div class="cardfooter">
                            <div class="line"><img src="https://cdn2.iconfinder.com/data/icons/pictograms-vol-1/400/message-32.png"/><span>'.$row['message'].'</span></div>
                            <div class="postFooter">
                              <input style="margin:10px 0px 10px 10px; vertical-align:middle;" value="Reply" class="clearbutton" type="submit" formaction="board.php?replyto='.$row['id'].'"></input>
                            </div>
                          </div>
                  </div></form>';
              }
            }else {
              print '<h1>No Posts to display. Create a post by entering a message and clicking on NewPost button above.</h1>';
            }
          
        }
      }else {
        print '<br><br><h1>Sessiona has been timeout. Please login again <a href="login.php"><br><br><br><input type="submit" value="Login" class="button"></a></h1>';
      }
    }catch (PDOException $e) {
      print "Error!: " . $e . "<br/>";
      die();
    }
?>
  </div>
</body>

</html>
