<?php
/*******************************************
 * nicht ausführen, bis dies getestet ist. *
 *******************************************/
header("Location: login.php");
die();
/*******************************************/

require_once('secure.php');
require_once("connect.php");
require_once("session.php");

$email = isset($_POST["email"]) ? trim($_POST["email"]) : '';
$passwort = isset($_POST["password"]) ? trim($_POST["password"]) : '';
$new_passwort = isset($_POST["new_passwort"]) ? trim($_POST["new_passwort"]) : '';

include("header_home.html");

if(isset($_POST['submit']) AND $_POST['submit']=='Abschicken'){

    $errors = array();
    if (!isset($_POST['email']))
        $errors[] = "<div class=\"abstand_content\">Bitte benutzen Sie unser Passwortformular</div>";
    else{
        if(trim($_POST['email']) == "")
            $errors[] = "<div class=\"abstand_content\">Geben Sie Ihre E-Mail ein.</div>";
        $sql = "SELECT
                    email
                FROM
                    data
                WHERE
                    email = '".mysql_real_escape_string(trim($_POST['email']))."'
                    ";
        $result = mysql_query($sql) OR die("<pre>\n".$sql."</pre>\n".mysql_error());
        $row = mysql_fetch_assoc($result);
        if (!$row)
            $errors[] = "<div class=\"abstand_content\">Ihre E-Mail [Nutzer] konnte nicht gefunden werden.</div>\n";
    }
    if (count($errors))    {
         echo "<div class=\"abstand_content\"><br/>Ihr Passwort konnte nicht versendet werden.<br/>\n".
               "<br/>\n</div>";
         foreach($errors as $error)
             echo $error."<br/>\n";
             echo "<div class=\"abstand_content\">Zurück zum <a href=\"".$_SERVER['PHP_SELF']."\">Formular</a></div></br/>\n";
    }

    else {
        $passwort = substr(md5(microtime()),0,8);
        $sql = "UPDATE
                    data
                SET
                    passwort = '".md5(trim($passwort))."'
                WHERE
                    email = '".mysql_real_escape_string(trim($_POST['email']))."'
                ";
        mysql_query($sql) OR die("<pre>\n".$sql."</pre>\n".mysql_error());

        $empfaenger = $row['email'];
        $titel = "Neues Passwort";
        $mailbody = "Ihr neues Passwort lautet:\n\n".
                    $new_passwort."\n\n".
                    "Ihr altes Passwort wurde gelöscht.";
        $header = "From: admin@xyz.com\n";
        if(@mail($empfaenger, $titel, $mailbody, $header)){
            echo "<div class=\"abstand_content\">Ihr neues Passwort wurde erfolgreich an Ihre E-Mail Adresse versandt.<br/>\n".
                 "Zurück zur <a href=\"login.php?was=user\">Startseite</a></div>\n";
        }

        else{
            echo "<div class=\"abstand_content\"><br/>Beim Senden der E-Mail trat ein Fehler auf.<br/>\n".
                 "Zurück zum <a href=\"".$_SERVER['PHP_SELF']."\">Formular</a>.</div><br/>";
        }
    }
}

    else    {
        echo "<form ".
             " name=\"passwort\" ".
             " action='' ".
             " method=\"post\" ".
             " accept-charset=\"ISO-8859-1\">\n";
        echo    '<html>
                    <head>
                        <title>Passwort vergessen - Berichtsheft</title>
                        <link rel="stylesheet" type="text/css" href="format.css">
                    </head>
                    <div class="abstand_content">
                <h1>Passwort vergessen?</h1>
                Geben Sie Ihre E-Mail Adresse ein, damit wir Ihnen ein Passwort zu senden können.
                <br/><br/>
                </html>';
        echo "E-Mail :\n";
        echo "<input type=\"email\" name=\"email\" maxlength=\"50\" size=\"27\" placeholder=\"E-Mail\">\n";
        echo "<br/>\n";
        echo "<div class=\"abstand_6_1\"><table><tr><td><input type=\"submit\" name=\"submit\" value=\"Abschicken\"></td><td><a class=\"links_blue\" href=\"login.php?was=user\">" ?><input type="button" value="Abbrechen"> <?php  echo "</a></td></tr></table>\n";
        echo "<table><tr><td><input type=\"reset\" style=\"width:188px;\" value=\"Zurücksetzen\"></td></tr></table></div></div>";
        echo "</form>\n"; 
    }
    
    echo '<div class="footer">';
       
include("footer_other.html");

echo '</div >';
?>
