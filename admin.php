<?php
  require_once('secure.php');
  require_once('connect.php');
  require_once("session.php");
  
  if (!isset($_SESSION['email'])) {
    die("Nicht eingeloggt");
  }
  
  if ($_SESSION['rolle'] != 3) {
    die ("Keine Rechte");
  }
  
  if(isset($_POST['send'])) {
    if (isset($_POST['rolle']) and !empty($_POST['rolle'])) {
      $rolle = $_POST['rolle'];
      if (($rolle == "1" or $rolle == "2" or $rolle == "3") and isset($_POST['id']) and !empty($_POST['id']) and $_POST['id'] != $_SESSION['id']) {
        $id = $_POST['id'];
        $statement = $db->prepare("UPDATE data SET rolle=? WHERE id=?");
        $statement->bind_param('ii', $rolle, $id);
        $statement->execute();
        $statement->close();
      }
    }
  }
  
  $statement = $db->prepare("SELECT id, vorname, nachname, email, ihk, rolle FROM data");
  $statement->execute();
  $statement->store_result();
  $statement->bind_result($id, $vorname, $nachname, $email, $ihk, $rolle);

?>
<html>
<head>
<link href="format.css" type="text/css" rel="stylesheet">
</head>
<body>

<?php
include('header.html');
include('navigation.php');
?>

<div class="abstand_content_historie">
  <h2>Legende:</h2>
  <p>
    Azubis können Berichtshefte anlegen und nur Ihre Daten sehen.<br/>
    Beobachter können Berichtshefte aller Azubis einsehen und PDFs generieren.<br/>
    Admins können anderen Nutzern die Rollen von Azubi, Beobachter oder Admin geben.
  </p>
  <? if($statement->num_rows <= 1) {
    printf("<p>Noch keine Benutzer angemeldet.</p>");
  }?>
    <table border="1" id="user_list">
      <? while($statement->fetch()) {
        if($_SESSION['id'] == $id) continue;
        echo "<tr>";
        printf('<form action="" method="post">');
        $r_1 = 1 == $rolle ? "selected='selected'" : "";
        $r_2 = 2 == $rolle ? "selected='selected'" : "";
        $r_3 = 3 == $rolle ? "selected='selected'" : "";
        printf("<td>%s</td><td>%s</td><td>%s</td><td>%s</td>", $vorname, $nachname, $email, $ihk);
        printf("<td>Rolle: <select name='rolle' size='1'><option value='1' %s>Azubi</option><option value='2' %s>Beobachter</option><option value='3' %s>Admin</option></select><input type='hidden' name='id' value='%s' /></td>", $r_1, $r_2, $r_3, $id);
        printf("<td><input type='submit' name='send' value='ändern'/></td>");
        printf("</form>");
        echo "</tr>";
      } ?>
    </table>
</div>

<div class="footer">
<?
include('footer_other.html');
?>
</div>
</body>
</html>
