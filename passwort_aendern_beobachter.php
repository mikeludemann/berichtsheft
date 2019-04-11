<?php
require_once('secure.php');
require_once('connect.php');
require_once('session.php');

//require("index.php");

$was = isset($_GET["was"]) ? trim($_GET["was"]) : '';

if(!empty($email) && !empty($passwort)){
  $abfrage = "SELECT * FROM data WHERE email = '$email' AND passwort = '$passwort' LIMIT 1";
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
    
if($_SESSION['rolle'] < 2 ){
  header("Location: passwort_aendern.php?was=status");
  echo' <script type="text/javascript">
    function weiterleiten()
    {
    self.location.href="passwort_aendern.php?was=status";
    }
    window.setTimeout("weiterleiten()",0);
    </script>';
  die();
}
   
include('header_beobachter.html');

if(isset($_SESSION['email'])) {
  echo "<div class=\"abstand_content_historie\"><br>Herzlich Willkommen, <b>".$_SESSION['email']."</b><br/><br/>";
  require("navigation_beobachter.php");
  echo "</div>";
   
  if ( false===($errors=validateInput()) ) {     
     $queryParms['email'] = mysql_real_escape_string($_SESSION['email']);
     $queryParms['passwort'] = md5($_POST['passwort']);
     $queryParms['new_passwort'] = md5($_POST['new_passwort']);
     
     $query = "UPDATE data SET passwort='".$queryParms['new_passwort']."' WHERE email='".$queryParms['email']."' AND passwort='".$queryParms['passwort']."'";
     mysql_query($query) or die('Fehler beim Eintragen der neuen Werte');
     
     if (1!=mysql_affected_rows())
     {
         $errors = array();
         $errors['passwort'] = '<span class="red">Ihr aktuelles Passwort stimmt nicht!</span>';
     } else {
         echo '<script type="text/javascript">
            function weiterleiten()
            {
            self.location.href="Mein_Konto_beobachter.php?was=status";
            }
            window.setTimeout("weiterleiten()",500);
         </script>';
         echo '<div class="abstand_content"><br/>Passwort wurde geändert.<br/><br/><a href="Mein_Konto_beobachter.php?was=status">Hier klicken um zurück zu kommen.</a></div>';
     }
 }
     
 if ( false!==$errors) {
  ?>
        <head>
           <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1">
           <title>Passwort ändern - Berichtsheft</title>
           <link rel="stylesheet" type="text/css" href="format.css">
        </head>
        <div class="abstand_content">
        <h1>Passwort ändern</h1>
         <form method="POST" action="?was=status">
             <table>
               <tr>
                 <td>Aktuelles Passwort</td>
                 <td class="abstand_6">
                     <input type="password" name="passwort" size="30" class="input" placeholder="Passwort"/>
                     <?php
                      if (isset($errors['passwort']))
                      echo '', $errors['passwort'];
                     ?>
                 </td>
               </tr>
               <tr>
                 <td class="top">Neues Passwort</td>
                 <td class="abstand_6">
                     <input type="password" name="new_passwort" size="30" class="input" placeholder="Neues Passwort"/>
                     <?php
                      if (isset($errors['new_passwort']))
                        echo '', $errors['new_passwort'];
                        echo '<br><span class="size">(Passwort muss mindestens 5 Zeichen haben)</span>';
                     ?>
                 </td>
               </tr>
               <tr>
                 <td>Neues Passwort<br><span class="size">(Wiederholung)</span></td>
                 <td class="abstand_6 top">
                     <input type="password" name="repeatPW" size="30" class="input" placeholder="Neues Passwort wiederholen"/>
                     <?php
                      if (isset($errors['repeatPW']))
                        echo '', $errors['repeatPW'];
                     ?>
                 </td>
               </tr>
             </table>
             <div class="abstand_11">
                <input type="submit" style="width: 101px;" name="submit" value="Speichern"/>
                <a href="Mein_Konto_beobachter.php?was=status">
                    <input type="button" style="width: 101px;" value="Abbrechen"/>
                </a><br>
                <input type="reset" name="reset" style="width: 206px;" value="Zurücksetzen"/>
             </div>
         </form>
        </div>
 <?php
 }
   }
 
    //if(!isset($_SESSION['email']))
    //{
    //    <span class="red">Nur angemeldete Benutzer können Ihr Passwort ändern! </span><br>
    //    <a href="login.php?was=user">Anmelden</a><br>
    //
    //}


include ('footer_other.html');

function validateInput()
 {
     if ( !isset($_POST['passwort']) || !isset($_POST['new_passwort']) || !isset($_POST['repeatPW']) )
         return array();
      
     $retval = array();    
     if(5 > strlen($_POST['new_passwort']))
         $retval['new_passwort'] = '<span class="red">Das Passwort ist zu kurz</span>';
     
     if($_POST['new_passwort'] !== $_POST['repeatPW'])
         $retval['repeatPW'] = '<span class="red">Die "Wiederholung" des Passwortes unterscheidet sich von der ersten Eingabe</span>';
    
     return (empty($retval)) ? false : $retval;
}
?>
