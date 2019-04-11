<?php
require_once('secure.php');
require_once("connect.php");
require_once("session.php");
//require("index.php");



//$email = isset($_POST["email"]) ? trim($_POST["email"]) : '';
$vorname = isset($_POST["vorname"]) ? trim($_POST["vorname"]) : '';
$nachname = isset($_POST["nachname"]) ? trim($_POST["nachname"]) : '';
//$passwort = isset($_POST["password"]) ? trim($_POST["password"]) : '';
//$passwort = md5($passwort);
$ihk = isset($_POST["ihk"]) ? trim($_POST["ihk"]) : '';
$start_day = isset($_POST['start_day']) ? trim($_POST['start_day']) : 0;
$start_month = isset($_POST['start_month']) ? trim($_POST['start_month']) : 0;
$start_year = isset($_POST['start_year']) ? trim($_POST['start_year']) : 0;

$start = mktime(0, 0, 0, $start_month, $start_day, $start_year);
//$taetigkeit = isset($_POST["taetigkeit"]) ? trim($_POST["taetigkeit"]) : '';



$was = isset($_GET["was"]) ? trim($_GET["was"]) : '';

if (!isset($_SESSION['email']) or empty($_SESSION['email'])) {
  header("Location: login.php");
  die();
}

$loggedIn = true;


if (!empty($vorname) and !empty($nachname) and !empty($ihk)) {
  $statement = $db->prepare("UPDATE data SET vorname = ?, nachname = ?, ihk = ?, start = ? WHERE id = ?");
  $statement->bind_param('sssii', $vorname, $nachname, $ihk, $start, $_SESSION['id']);
  $statement->execute();
  $statement->close();
}

   
if($_SESSION['rolle'] > 1){
    header("Location: Mein_Konto_beobachter.php?was=status");
    echo' <script type="text/javascript">
      function weiterleiten()
      {
      self.location.href="Mein_Konto_beobachter.php?was=status";
      }
      window.setTimeout("weiterleiten()",0);
  </script>';
  }

   ?>
<html>
  

    <head>
        <title>Mein Konto - Berichtsheft</title>
        <link rel="stylesheet" type="text/css" href="format.css">
    </head>

    <body>
    
    <?
      include("header.html"); 
      echo "<div class=\"abstand_content_historie\"><br>Herzlich Willkommen, <b>".$_SESSION['email']."</b><br/><br/>";
      include("navigation.php");
      echo "</div>";
    ?>
    
    <div id="content">
    <div class="abstand_content">
    <h1>Mein Konto</h1>
    <form action="" method="post">
    <?php
    $statement = $db->prepare("SELECT vorname, nachname, email, ihk, taetigkeit, start FROM data WHERE id = ?");
    $id = $_SESSION['id'];
    $statement->bind_param('i', $id);
    $statement->execute();
    $statement->bind_result($vorname, $nachname, $email, $ihk, $taetigkeit, $start);
    $statement->store_result();
    $num_rows = $statement->num_rows;
    $statement->fetch();
    $statement->close();
    
    $start_day = date("d", $start);
    $start_month = date("m", $start);
    $start_year = date("Y", $start);
    
    if (/*$zeile = mysql_fetch_object($db_erg)*/true)
    {
        ?>
        <table>
            <tr>
                <td>
                    Vorname:
                </td>
                <td class="abstand_6">
                <?php
                    printf("<input type='text' name='vorname' value='%s' />", $vorname);
                    //echo "$zeile->vorname , $zeile->nachname";
                ?>
                </td>
            </tr>
            <tr>
                <td>
                    Nachname:
                </td>
                <td class="abstand_6">
                <?php
                    printf("<input type='text' name='nachname' value='%s' />", $nachname);
                    //echo "$zeile->vorname , $zeile->nachname";
                ?>
                </td>
            </tr>
            <tr>
                <td>
                    IHK-Nr.:
                </td>
                <td class="abstand_6">
                <?php
                    //echo "$zeile->ihk";
                    printf("<input type='text' name='ihk' value='%s' />", $ihk);
                ?>
                </td>
            </tr>
            <tr>
              <td>
                Startdatum (TT MM JJJJ):
              </td>
              <td class="abstand_6">
                <?
                  printf("<input type='number' size='3' maxlength='2' name='start_day' value='%s' />&#160;", $start_day);
                  printf("<input type='number' size='3' maxlength='2' name='start_month' value='%s' />&#160;", $start_month);
                  printf("<input type='number' size='6' maxlength='4' name='start_year' value='%s' />", $start_year);
                ?>
              </td>
            </tr>
            <tr>
              <td/>
              <td class="abstand_6">
                <input type="submit" value="�ndern" />
              </td>
            </tr>
            <tr>
                <td>
                    Beruf/Tätigkeit:
                </td>
                <td class="abstand_6">
                <?php
                    //echo "$zeile->taetigkeit";
                    printf("%s", $taetigkeit);
                ?>
                </td>
            </tr>
            <tr>
                <td>
                    E-Mail:
                </td>
                <td class="abstand_6">
                <?php
                    //echo "$zeile->email";
                    printf("%s", $email);
                ?>
                </td>
            </tr>
            <tr>
                <td>
                    Dein Passwort:
                </td>
                <td class="abstand_6">
                <?php
                    echo '********';
                    //echo "". $zeile['passwort'] . "";
                ?>
                </td>
            </tr>
        </table>
    </form>
        <div class="abstand_5_1">
            <a href="passwort_aendern.php?was=status">
                <div class="links_blue">Passwort ändern</div>
            </a><br>
        </div>
    </div>
    </div>
    <?php
    //mysql_free_result( $db_erg );
   
?>
<div class="footer">
<?php
   }
include("footer_other.html");
?>
</div>
</body>
</html>
