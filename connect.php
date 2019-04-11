<?php
	require_once("session.php");
		
    $mysqlhost="localhost"; // MySQL-Host angeben

    $mysqldb="berichtsheft"; // Datenbank angeben

    $mysqluser="root"; // MySQL-User angeben

    $mysqlpwd="admin"; // Passwort vom MySQL-User angeben

    $connection=mysql_connect($mysqlhost, $mysqluser, $mysqlpwd) or
	
	  die("Verbindungsversuch fehlgeschlagen.");

    mysql_select_db($mysqldb, $connection) or die("Konnte die Datenbank nicht waehlen.");
    
    
    
	 
  $db = new mysqli($mysqlhost, $mysqluser, $mysqlpwd, $mysqldb);
  if (mysqli_connect_errno()) {
    die ('Konnte keine Verbindung zur Datenbank aufbauen: ' . mysqli_connect_error() . '(' . mysqli_connect_errno() . ')');
  }
    
?> 
