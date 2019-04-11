<?php
require_once('secure.php');
require_once("connect.php");
require_once("session.php");

error_reporting(-1);
ini_set('display_errors', TRUE);

include("header_home.html");

echo '<html>
    <head>
        <title>Registration - Berichtsheft</title>
        <link rel="stylesheet" type="text/css" href="format.css">
    </head>
    <div class="abstand_content">
<h1 class="ueberschrift_neu">Registrierung</h1>
</div>';

if (file_exists("setup_db.php")) {
    echo "<div class='abstand_content'>Warnung: Installation ist noch nicht beendet. Registrierungen sind noch nicht möglich.</div>";
    echo "<div class='footer'>";
    include ("footer_other.html");
    echo "</div>";
    die();
}

$out = '<br>
<div class="abstand_content">
<form action="registration.php" method="post">
<table>
    <tr>
        <td>
            Vorname:*
        </td>
        <td>
            <input type="text" size="27" maxlength="50" name="vorname" placeholder="Vorname">
        </td>
    </tr>
    <tr>
        <td>
            Nachname:*
        </td>
        <td>
            <input type="text" size="27" maxlength="50" name="nachname" placeholder="Nachname">
        </td>
    </tr>
    <tr>
        <td>
            Username:*
        </td>
        <td>
            <input type="text" size="27" maxlength="50" name="username" placeholder="Username">
        </td>
    </tr>
    <tr>
        <td>
            E-Mail:*
        </td>
        <td>
            <input type="text" size="27" maxlength="50" name="email" placeholder="E-Mail">
        </td>
    </tr>
    <tr>
        <td>
            Beruf/Tätigkeit:*
        </td>
        <td>
            <select size="1" maxlength="50" name="taetigkeit" placeholder="Tätigkeit" class="width_dropdown">
                <optgroup label="Jobs">
                    <optgroup class="margin_left" label="IT">
                        <option class="margin_left" value="Administrator">Administrator</option>
                        <option class="margin_left" value="Fachinformatiker Anwendungsentwicklung">Fachinformatiker Anwendungsentwicklung</option>
                        <option class="margin_left" value="Fachinformatiker Systemintegration">Fachinformatiker Systemintegration</option>
                        <option class="margin_left" value="Informatiker">Informatiker</option>
                        <option class="margin_left" value="Software Developer">Software Developer</option>
                    </optgroup>
                    <optgroup class="margin_left"label="PM">
                        <option class="margin_left" value="Projektmanager/in">Projektmanager/in</option>
                    </optgroup>
                    <optgroup class="margin_left" label="Sales">
                        <option class="margin_left" value="Pre-Sales Manager/in">Pre-Sales Manager/in</option>
                        <option class="margin_left" value="Sales Manager/in">Sales Manager/in</option>
                    </optgroup>
                    <optgroup class="margin_left" label="Financial">
                        <option class="margin_left" value="Financial Manager/in">Financial Manager/in</option>
                    </optgroup>
                    <optgroup class="margin_left" label="Account Manager">
                        <option class="margin_left" value="Account Manager/in">Account Manager/in</option>
                        <option class="margin_left" value="Key Account Manager/in">Key Account Manager/in</option>
                    </optgroup>
                    <optgroup class="margin_left" label="Design">
                        <option class="margin_left" value="Designer/in">Designer/in</option>
                    </optgroup>
                </optgroup>
            </select>
        </td>
    </tr>
    <tr>
        <td>
            IHK/HKW-Nr.:*
        </td>
        <td>
            <input type="text" size="27" maxlength="50" name="ihk" placeholder="IHK_HWK-Nummer">
        </td>
    </tr>
    <tr>
        <td>
            Passwort:*
        </td>
        <td>
            <input type="password" size="27" maxlength="50" name="passwort" placeholder="Passwort">
        </td>
    </tr>
    <tr>
        <td>
            Passwort wiederholen:*
        </td>
        <td>
            <input type="password" size="27" maxlength="50" name="passwort2" placeholder="Passwort wiederholen">
        </td>
    </tr>
    <tr>
        <td>
            Start der Ausbildung (TT MM JJJJ):
        </td>
        <td>
            <input name="start_day" maxlength="2" size="3" type="number" />
            <input name="start_month" maxlength="2" size="3" type="number" />
            <input name="start_year" maxlength="4" size="6" type="number" />
        </td>
    </tr>
</table>
<br/>
<input type="checkbox" name="AGB" value="1" id="AGB"/>
<label for="AGB">Ich bin mit den <a class="links_blue" href="terms_of_use.php">Nutzungsbedingungen</a> einverstanden.</label><br>
    <div class="abstand_5_2">
        <table>
            <tr>
                <td>
                    <input type="submit" name="submit" value="Registrieren">
                </td>
                <td>
                    <a href="login.php?was=user">
                        <input type="button" value="Abbrechen">
                    </a>
                </td>
            </tr>
        </table>
        <table>
            <tr>
                <td>
                    <input type="reset" style="width:191px;" value="Zurücksetzen">
                </td>
            </tr>
        </table>
    </div>
<br>
</form>
</div>
<div class="size abstand_content">
Bitte geben Sie Ihre vollständigen Daten für die Registrierung ein.<br>
Die (*) gekennzeichneten Felder sind Pflichtfelder und müssen ausgefüllt werden.<br>
</div>
<br>
<div class="footer">
    <div class="main">
        <div>(c) Copyright 2010</div>
    </div>
</div>
</html>';

if(isset($_POST['submit']))

{

$email = $_POST["email"];
$vorname = $_POST["vorname"];
$nachname = $_POST["nachname"];
$username = $_POST["username"];
$ihk = $_POST["ihk"];
$passwort = $_POST["passwort"];
$passwort2 = $_POST["passwort2"];
$taetigkeit = $_POST["taetigkeit"];

$start_day = !empty($_POST["start_day"]) ? $_POST["start_day"] : 0;
$start_month = !empty($_POST["start_month"]) ? $_POST["start_month"] : 0;
$start_year = !empty($_POST["start_year"]) ? $_POST["start_year"] : 0;

$start = mktime(0, 0, 0, $start_month, $start_day, $start_year);

        $errors = array();

        if(!isset($_POST['vorname'],
                  $_POST['nachname'],
                  $_POST['email'],
                  $_POST['passwort'],
                  $_POST['taetigkeit'],
                  $_POST['ihk'],
                  $_POST["username"]))

if($passwort != $passwort2 OR $email == "" OR $passwort == "" OR $vorname == "" OR $nachname == "" or $taetigkeit == "" or $username == "" or $ihk == "")
    {
    echo "<div class=\"abstand_content\">Eingabefehler. Bitte alle Felder korrekt ausfüllen. <a href=\"registration.php\">Zurück zur Registrierung</a>";
    echo $out;
    echo "</div>";
    exit;
    }

 if(!isset($_POST['AGB']))
 {
    echo '<br><div class="fehler abstand_content">Sie müssen die Nutzungsbedingungen akzeptieren!</div>'.$out;
     exit;
 }

if($passwort == $passwort2 and $email and $vorname and $nachname and $taetigkeit and $username and $ihk)
{

$passwort = md5($passwort);

$result = mysql_query("SELECT id FROM data WHERE email = '".$email."'");
$menge = mysql_num_rows($result);

if($menge == 0)
    {

    $eintrag = "INSERT INTO data (vorname, nachname, email, passwort, taetigkeit, ihk, username, start) VALUES ('$vorname', '$nachname', '$email', '$passwort', '$taetigkeit', '$ihk', '$username', '$start')";
    $eintragen = mysql_query($eintrag);

    if($eintragen == true)
        {
            ?>
                <script type="text/javascript">
                    function weiterleiten()
                    {
                    self.location.href="login.php?was=user";
                    }
                    window.setTimeout("weiterleiten()",500);
                </script>
            <?php
        echo "<div class=\"abstand_content\"><br/>Benutzername <b>$email</b> wurde erstellt. <br/><br/>
        Falls Sie nicht automatisch weitergeleitet werden, klicken Sie bitte auf <a class=\"links_blue\" href=\"login.php?was=user\">Weiter zur Anmeldung</a></div><br/>";
        include ("footer_other.html");
        }
    else
        {
        echo "<div class=\"abstand_content\"><br>Fehler beim Speichern des Benutzernames.<br> <a class=\"links_blue\" href=\"registration.php\">Zurück zur Registrierung</a></div>".$out;
        }
    }

else
    {
    echo "<div class=\"abstand_content\"><br>Benutzername schon vorhanden.<br><br> <a class=\"links_blue\" href=\"registration.php\">Zurück zur Registrierung</a></div><br><br>";
    include ("footer_other.html");
    }
    }

else
  {
    echo '<form action="registration.php" method="post">';
    echo '<div class="abstand_content"><br>Die Eingabedaten sind fehlerhaft. Füllen Sie bitte alle Pflichtfelder aus!<br><br>';
    echo '<input type="hidden" name="email" value="'.$_POST['email'].'">';
    echo '<a class="links_blue" href="registration.php">Zurück zur Registrierung</a></div>';
    //echo '<input type="submit" name="zurueck" value="Zurück zur Registration">';
    echo '</form>';
    include('footer_other.html');

  }
}

else {

    echo $out;
}

?> 
