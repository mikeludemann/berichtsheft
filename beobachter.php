<?php
require_once("secure.php");
require_once("connect.php");
require_once("session.php");
require_once("time.php");

//require("index.php");
$id = isset($_POST["id"]) ? trim($_POST["id"]) : '';

$id = (isset($_SESSION['id']) and !empty($_SESSION['id'])) ? $_SESSION['id'] : '';

#$email = isset($_POST["email"]) ? trim($_POST["email"]) : '';
//$vorname = isset($_POST["vorname"]) ? trim($_POST["vorname"]) : '';
//$nachname = isset($_POST["nachname"]) ? trim($_POST["nachname"]) : '';
//$betreuer = isset($_POST["betreuer"]) ? trim($_POST["betreuer"]) : '';

$statement = $db->prepare("SELECT vorname, nachname FROM data WHERE id=?");
$statement->bind_param('i', $id);
$statement->execute();
$statement->store_result();
$statement->bind_result($vorname, $nachname);
$statement->close();



$was = isset($_GET["was"]) ? trim($_GET["was"]) : '';

$loggedIn = $_SESSION['id'] != '';

if(!$loggedIn) {
    header('Location: login.php');
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
if($_SESSION['rolle'] <= 1) {
  header("Location: home.php");
  die("Keine Rechte.");
}

include("header_beobachter.html");
   

   
   echo "<div class=\"abstand_content_historie\"><br>Herzlich Willkommen, <b>".$_SESSION['email']."</b><br/><br/>";
   include("navigation_beobachter.php");
   ?>
   </div>
    <head>
        <title>Beobachter - Berichtsheft</title>
        <link rel="stylesheet" type="text/css" href="format.css">
    </head>
    <div id="content">
    <div class="abstand_content">
    <h1>Berichtsheft - Beobachter</h1>
    <ol>
      <li>Bitte wählen Sie den Zeitraum aus, von wann bis wann Sie die Berichtshefte haben möchten.</li>
      <li>Bitte wählen Sie den Nutzer aus, von dem Sie die Berichtshefte haben möchten.</li>
      <li>Anschließend müssten Sie dann nur noch auf den PDF-Download Button klicken.</li>
    </ol>
        <div class="abstand_left">
          <!--<form>-->
            <div class="abstand_bottom">
              &nbsp;<span class="label">Mein/e Auszubildende/r</span>
            </div>
              
              <table cellpadding="5">
                <tr>
                  <td class="w40">
                    Name
                  </td>
                  <td colspan="2" style="text-align: center;">
                    Von
                  </td>
                  <td colspan="2" style="text-align: center;">
                    Bis
                  </td>
                  <td style="text-align: center;">
                    PDF
                  </td>
                </tr>
              <?php
                $statement = $db->prepare("SELECT vorname, nachname, id FROM data WHERE rolle = 1");
                
                $jahr = $db->prepare("SELECT MIN(tstamp), MAX(tstamp) FROM berichte WHERE berichtsid = ?");
                
                $statement->execute();
                $statement->bind_result($vorname, $nachname, $id);
                $statement->store_result();
              
                while ($statement->fetch())
              {
              ?>
              <tr>
                <td colspan="6">
              <form action="weeks_prepare.php" method="post">
              <table class="w100">
              <tr>
                <td class="w40">
                  <span class="abstand_left">
                      <?php
                          printf ($vorname);
                          ?>
                            &nbsp;
                          <?php
                          printf ($nachname);
                      ?>
                  </span>
                </td>
                <td>
                  <select size="1" name="von_monat">
                    <option value="">Monat</option>
                    <option value="1">Januar</option>
                    <option value="2">Februar</option>
                    <option value="3">März</option>
                    <option value="4">April</option>
                    <option value="5">Mai</option>
                    <option value="6">Juni</option>
                    <option value="7">Juli</option>
                    <option value="8">August</option>
                    <option value="9">September</option>
                    <option value="10">Oktober</option>
                    <option value="11">November</option>
                    <option value="12">Dezember</option>
                  </select>
                </td>
                <td>
                  <select size="1" name="von_jahr">
                    <option value="">Jahr</option>
                    <?
                      $jahr->bind_param('i', $id);
                      $jahr->execute();
                      $jahr->bind_result($min, $max);
                      $jahr->fetch();
                      
                      $min = (int)date("Y", $min);
                      $max = (int)date("Y", $max);
                      for($i = $min; $i <= $max; $i++) {
                        printf("<option>%s</option>", $i);
                      }
                    ?>
                  </select>
                </td>
                <td>
                  <select size="1" name="bis_monat">
                    <option value="">Monat</option>
                    <option value="1">Januar</option>
                    <option value="2">Februar</option>
                    <option value="3">März</option>
                    <option value="4">April</option>
                    <option value="5">Mai</option>
                    <option value="6">Juni</option>
                    <option value="7">Juli</option>
                    <option value="8">August</option>
                    <option value="9">September</option>
                    <option value="10">Oktober</option>
                    <option value="11">November</option>
                    <option value="12">Dezember</option>
                  </select>
                </td>
                <td>
                  <select size="1" name="bis_jahr">
                    <option value="">Jahr</option>
                    <?
                      for($i = $min; $i <= $max; $i++) {
                        printf("<option>%s</option>", $i);
                      }
                    ?>
                  </select>
                </td>
                
                <td>
                  <span class="abstand_6">
                    <input type="hidden" name="id" value="<? echo $id; ?>" />
                    <input type="submit" value="PDF - Download">
                  </span>
                </td>
              </tr>
              </table>
              </form>
              </td>
              </tr>
              <?php
              }
              if ($statement->num_rows < 1) {
                echo "<tr><td><p>Keine Azubis angemeldet</p></td></tr>";
              }
              $statement->close();
              ?>
            </table>
           
        </div>
        </div>
    </div>
   
<div class="footer">
<?php
include("footer_other.html");
?>
</div>
