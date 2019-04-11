<?php
require_once('secure.php');
require_once("connect.php");
require_once("session.php");

//require("index.php");

$id = isset($_POST["id"]) ? trim($_POST["id"]) : '';
$email = isset($_POST["email"]) ? trim($_POST["email"]) : '';
$vorname = isset($_POST["vorname"]) ? trim($_POST["vorname"]) : '';
$nachname = isset($_POST["nachname"]) ? trim($_POST["nachname"]) : '';
$passwort = isset($_POST["password"]) ? trim($_POST["password"]) : '';
$passwort = md5($passwort);
$ihk = isset($_POST["ihk"]) ? trim($_POST["ihk"]) : '';
$taetigkeit = isset($_POST["taetigkeit"]) ? trim($_POST["taetigkeit"]) : '';



$was = isset($_GET["was"]) ? trim($_GET["was"]) : '';

if(!empty($email) && !empty($passwort)){
  $abfrage = "SELECT * FROM data WHERE email = '$email' && passwort = '$passwort'";
  $ergebnis = mysql_query($abfrage);
  $row = mysql_fetch_object($ergebnis);
  $loggedIn = mysql_num_rows($ergebnis) ? true : false;
}

if(!isset($_SESSION['email'])) {
    header("Location: login.php");
    echo' <script type="text/javascript">
            function weiterleiten()
            {
            self.location.href="login.php";
            }
            window.setTimeout("weiterleiten()",0);
        </script>';
   include ("home.html");
   die();
}

if($_SESSION['rolle'] < 2) {
  header("Location: home.php");
  echo' <script type="text/javascript">
            self.location.href="home.php";
        </script>';
  die();
}

include("header_beobachter.html");


if(isset($_SESSION['email'])) {
   echo "<div class=\"abstand_content_historie\"><br>Herzlich Willkommen, <b>".$_SESSION['email']."</b><br/><br/>";
   if ($_SESSION['rolle'] == 3) {
      include("navigation.php");
   } else {
      include("navigation_beobachter.php");
   }
   ?>
   </div>
    <head>
        <title>Mein Konto - Berichtsheft</title>
        <link rel="stylesheet" type="text/css" href="format.css">
    </head>
    <div id="content">
        <div class="abstand_content">
    <h1>Mein Konto</h1>
   
    <?php
    $sql = "SELECT vorname, nachname, email, passwort, taetigkeit FROM data WHERE email = '" . $_SESSION['email'] . "' LIMIT 1";
    $db_erg = mysql_query($sql);
    if ( ! $db_erg )
    {
      die('Ungültige Abfrage: ' . mysql_error());
    }
    
    if ($zeile = mysql_fetch_object($db_erg))
    {
        ?>
        <table>
            <tr>
                <td>
                    Name:
                </td>
                <td class="abstand_6">
                <?php
                    echo "$zeile->vorname , $zeile->nachname";
                ?>
                </td>
            </tr>
            <tr>
                <td>
                    Beruf/Tätigkeit:
                </td>
                <td class="abstand_6">
                <?php
                    echo "$zeile->taetigkeit";
                ?>
                </td>
            </tr>
            <tr>
                <td>
                    E-Mail:
                </td>
                <td class="abstand_6">
                <?php
                    echo "$zeile->email";
                ?>
                </td>
            </tr>
            <tr>
                <td>
                    Dein Passwort:
                </td>
                <td class="abstand_6">
                    ********
                </td>
            </tr>
        </table>
        <div class="abstand_5_1">
            <a href="passwort_aendern_beobachter.php?was=status">
                <div class="links_blue">Passwort ändern</div>
            </a><br>
        </div>
        </div>
    </div>
    <?php
    }
    mysql_free_result( $db_erg );
   
?>
<div class="footer">
<?php
   }
include("footer_other.html");
?>
</div>
