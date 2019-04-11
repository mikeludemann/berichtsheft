<?php 
require_once('secure.php');
require_once("connect.php");
require_once("session.php");

$email = isset($_POST["email"]) ? trim($_POST["email"]) : '';

$passwort = isset($_POST["passwort"]) ? trim($_POST["passwort"]) : '';

$was = isset($_GET["was"]) ? trim($_GET["was"]) : ''; 


$loggedIn = isset($_SESSION['email']) and !empty($_SESSION['email']);

if (!$loggedIn && !empty($passwort) && !empty($email)) {
  $passwort = md5($passwort);
  $abfrage = "SELECT email, passwort, rolle, id FROM data WHERE email = '$email' AND passwort = '$passwort' LIMIT 1";
  $ergebnis = mysql_query($abfrage);
  $row = mysql_fetch_object($ergebnis);
  $loggedIn = mysql_num_rows($ergebnis) ? true : false;
  if ($loggedIn) {
    $_SESSION['email'] = $email;
    $_SESSION['id'] = $row->id;
    $_SESSION['rolle'] = $row->rolle;
  }
}

$rolle = isset($_SESSION["rolle"]) ? trim($_SESSION["rolle"]) : '';

if ($loggedIn and $rolle == '1' and $was == 'email') {
    header('Location: historie.php?was=status');
    echo' <script type="text/javascript">
        function weiterleiten()
        {
        self.location.href="historie.php?was=status";
        }
        window.setTimeout("weiterleiten()",500);
    </script>';
    echo '<div class="abstand_content"><br/>Ihre Anmeldung war erfolgreich! <br/><br/>
    Falls Sie nicht automatisch weitergeleitet werden, klicken Sie bitte auf <a class="links_blue" href="historie.php?was=status">weiter</a></div><br/>';
    die();
}


if ($loggedIn and $rolle == '2' and $was == 'email') {
    header('Location: beobachter.php?was=status'); // http weiterleitung
    echo' <script type="text/javascript">
        function weiterleiten()
        {
        self.location.href="beobachter.php?was=status";
        }
        window.setTimeout("weiterleiten()",500);
    </script>';
    echo '<div class="abstand_content"><br/>Ihre Anmeldung war erfolgreich! <br/><br/>
    Falls Sie nicht automatisch weitergeleitet werden, klicken Sie bitte auf <a class="links_blue" href="beobachter.php?was=status">weiter</a></div><br/>';
    die();
}

if ($loggedIn and $rolle == '3' and $was == 'email') {
    header('Location: admin.php?was=status');
    echo' <script type="text/javascript">
        function weiterleiten()
        {
        self.location.href="admin.php?was=status";
        }
        window.setTimeout("weiterleiten()",500);
    </script>';
    echo '<div class="abstand_content"><br/>Ihre Anmeldung war erfolgreich! <br/><br/>
    Falls Sie nicht automatisch weitergeleitet werden, klicken Sie bitte auf <a class="links_blue" href="historie.php?was=status">weiter</a></div><br/>';
    die();
}

if ($was == "logout") {
    session_destroy();
    header('Location: home.php');
    echo '<script type="text/javascript">
        function weiterleiten()
        {
        self.location.href="home.php";
        }
        window.setTimeout("weiterleiten()",500);
    </script>';
    echo "<div class=\"abstand_content\"><br/>Ihre Abmeldung war erfolgreich! <br/><br/>
      Falls Sie nicht automatisch weitergeleitet werden, klicken Sie bitte auf <a class=\"links_blue\" href=\"home.php\">weiter</a></div><br/>";
      die();
}

if (isset($_SESSION['email'])) {
    header("Location: Mein_Konto.php");
} else {
    
include("header_home.html");

    $error = '';
    $errorClass = '';
    if (isset($_POST['email']) || isset($_POST['passwort'])) {
        $error = '<span class="fehler">Bitte geben Sie Ihre korrekten Daten ein!</span><br/><br/>';
        $errorClass = 'fehler';
    }
    echo '<head>
        <title>Berichtsheft</title>
        <link rel="stylesheet" type="text/css" href="format.css">
    </head>
    <div id="content">
        <div class="abstand_content">
        <h1>Anmeldung</h1>
        '. $error .'
        <form action="?was=email" Method="post">
            <table>
                <tr>
                    <td>
                        <span class="'. $errorClass . '">E-Mail:</span>
                    </td>
                    <td>
                        <div class="abstand_10">
                            <input type="text" name="email" size="25" placeholder="E-Mail">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="'. $errorClass . '">Dein Passwort:</span>
                    </td>
                    <td>
                        <div class="abstand_10">
                            <input type="password" name="passwort" size="25" placeholder="Passwort">
                        </div>
                    </td>
                </tr>
            </table>
            <div class="abstand_3">
                <table>';
                    /*<tr>
                        <td>
                            <a href="neues_Passwort.php">
                                <div class="size links_blue">Passwort vergessen?</div>
                            </a>
                        </td>
                    </tr>*/
                    echo '
                    <tr>
                        <td>
                            <a href="registration.php">
                                <div class="size links_blue">Noch nicht registriert?</div>
                            </a>
                        </td>
                    </tr>
                </table>
                <div class="abstand">
                <table>
                    <tr>
                        <td>
                            <input type="submit" value="Anmelden">
                        </td>
                        <td>
                            <a href="home.php">
                                <input type="button" value="Abbrechen">
                            </a>
                        </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </form>';
}
echo '<div class="footer">';

include("footer_other.html");

echo '</div>';

?> 
