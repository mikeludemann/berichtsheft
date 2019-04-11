<?php
require_once('secure.php');

echo "<h3> Stellen Sie sicher, dass die Verbindungsdaten in connect.php richtig eingetragen sind</h3>";

require_once('connect.php');

function check($name, $array) {
	return (isset($array[$name]) and !empty($array[$name])) ? true : false;
}

if(check('email', $_POST) and check('password', $_POST) and check('password2', $_POST)) {
	if ($_POST['password'] != $_POST['password2']) {
		die("Die Passwörter stimmen nicht überein. Bitte versuchen Sie es noch einmal.");
	}
	$password = md5($_POST['password']);

	$berichte = $db->query("CREATE TABLE IF NOT EXISTS `berichte` (
                            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                            `berichtsid` int(10) NOT NULL,
                            `tstamp` int(11) NOT NULL,
                            `taetigkeit` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                            PRIMARY KEY (`id`)
                          ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;");

	$user = $db->query("CREATE TABLE IF NOT EXISTS `data` (
                      `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                      `vorname` varchar(30) NOT NULL,
                      `nachname` varchar(30) NOT NULL,
                      `username` varchar(30) NOT NULL,
                      `passwort` varchar(50) NOT NULL,
                      `new_passwort` varchar(50) NOT NULL,
                      `email` varchar(50) NOT NULL,
                      `ihk` varchar(25) DEFAULT NULL,
                      `taetigkeit` varchar(100) NOT NULL,
                      `user_geloescht` tinyint(4) NOT NULL,
                      `letzter_login` datetime NOT NULL,
                      `rolle` int(11) NOT NULL DEFAULT '1',
                      `start` int(11) DEFAULT NULL,
                      PRIMARY KEY (`id`),
                      UNIQUE KEY `email` (`email`)
                    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;");

	if($user and $berichte) {
		$statement = $db->prepare('INSERT INTO data (vorname, nachname, username, passwort, email, ihk, taetigkeit, user_geloescht, letzter_login, rolle) VALUES ("admin", "admin", "admin", ?, ?, "admin", "admin", "0", "0000-00-00 00:00:00", 3)');

		$statement->bind_param('ss', $password, $_POST['email']);
		$statement->execute();
		$statement->close();

		echo "Die Datenbank wurde erstellt. Sie können sich jetzt als Admin <a href='login.php'>hier</a> einloggen.";
		unlink("setup_db.php"); // setup skript nach erfolgreicher erstellung löschen.
	} else {
		die("Fehler beim erstellen der Datenbank!");
	}
} else {
?>
<form action="" method="post">
	<table cellpadding="5">
		<tr>
			<td>
				Email:
			</td>
			<td>
				<input type="text" maxlength="50" name="email" placeholder="Email" />
			</td>
		</tr>
		<tr>
			<td>
				Passwort:
			</td>
			<td>
				<input type="password" name="password" placeholder="Passwort" />
			</td>
		</tr>
		<tr>
			<td>
				Passwort wiederholen:
			</td>
			<td>
				<input type="password" name="password2" placeholder="wiederholen" />
			</td>
		</tr>
	</table>
	<input type="submit" value="Abschicken" />
</form>
<?}?>



