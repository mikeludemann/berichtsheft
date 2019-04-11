<?php
	require_once('secure.php');
	require_once('connect.php');
	require_once('time.php');
	require_once("session.php");


		/* Loginüberprüfung */

		if (!isset($_SESSION['email']) or empty($_SESSION['email'])) {
			die ("nicht eingeloggt");
		}

		if ($_SESSION['rolle'] > 1) {
			die ("Nur Azubis können Berichtshefte pflegen.");
		}

	$berichtsid = $_SESSION['id'];



		if (isset($_GET['tstamp']) and !empty($_GET['tstamp'])) {
			$tstamp_in = normalize_timestamp($_GET['tstamp']);
		} else {
			$tstamp_in = normalize_timestamp(time());
		}

		if (!isset($_POST['feiertag']) and !isset($_POST['urlaub'])) {
			$taetigkeit_in = isset($_POST['taetigkeit']) ? $_POST['taetigkeit'] : "";
	$taetigkeit_in = str_replace("\\r\\n", "\n", $taetigkeit_in);
		} else {
			$taetigkeit_in = isset($_POST['feiertag']) ? "Feiertag" : "Urlaub";
		}

	// Wenn Speichen, dann IF Abfrage ob num rows > 0, wenn ja update. Wenn nicht speichern des eintrags 
		if(isset($_POST['save']) or isset($_POST['feiertag']) or isset($_POST['urlaub'])) {

		//$statement = $db->prepare("SELECT * FROM `berichte` WHERE `tstamp` = ? AND `berichtsid`=?");
		$statement = $db->prepare("SELECT count(*) FROM `berichte` WHERE `tstamp` = ? AND `berichtsid`=?");
		$statement->bind_param('ii', $tstamp_in, $berichtsid);
		$statement->execute();

		$statement->bind_result($tstamp);
		$statement->fetch();
		$num_rows = $tstamp;
		$statement->close();


			// $abfrage = "SELECT tstamp FROM `Berichte` WHERE `tstamp` = ".$_GET['tstamp']." AND `berichtsid`=".$berichtsid."  LIMIT 0,1"; 
			// $db_erg = mysql_query($abfrage);
			// $num_rows = mysql_num_rows($db_erg);

				if ($num_rows > 0) {

					$test = $db-> prepare("UPDATE `Berichte` SET `tstamp`=?, `taetigkeit`=? WHERE `tstamp`=? AND `berichtsid`=?");
					$test->bind_param('isii',$_POST['tstamp'],$taetigkeit_in, $tstamp_in, $berichtsid);
					$test->execute();
					$test->close();


					// $test = "UPDATE `Berichte` SET `tstamp`='".$_POST['tstamp']."', `taetigkeit`='".$_POST['taetigkeit']."'WHERE `tstamp`=".$_GET['tstamp']." AND `berichtsid`=".$berichtsid;
					// mysql_query($test); 
					} else {

						$eintragen = $db-> prepare("INSERT INTO `Berichte` ( berichtsid, tstamp, taetigkeit) VALUES (?,?,?)");
						$eintragen->bind_param('iis',$berichtsid,$_POST['tstamp'], $taetigkeit_in);
						$eintragen->execute();
						$eintragen->close();


						// $eintragen = "INSERT INTO `Berichte` ( berichtsid, tstamp, taetigkeit) VALUES ( '".$berichtsid."', '".$_POST['tstamp']."', '".$_POST['taetigkeit']."' )";
						// mysql_query($eintragen);
				}

			}

		//if (isset($_GET['tstamp'])) {

			$abfragen = $db-> prepare("SELECT id, berichtsid, tstamp, taetigkeit FROM `Berichte` WHERE `tstamp`=? AND `berichtsid`=?");
			$abfragen->bind_param('ii', $tstamp_in,$berichtsid);
			$abfragen->execute();
			$abfragen->bind_result($id,$berichtsid,$tstamp,$taetigkeit);
			$abfragen->fetch();
			$abfragen->close();

			// $sql = "SELECT id, berichtsid, tstamp, taetigkeit FROM `Berichte` WHERE `tstamp`=".$_GET['tstamp']." AND `berichtsid`=".$berichtsid;
			// $db_erg = mysql_query($sql);
			// $arrBericht = mysql_fetch_array($db_erg, MYSQL_ASSOC);
			// mysql_free_result($db_erg);
		//}

	// löschen des berichts
		// if(isset($_POST['delete'])) {

			// $delete = $db-> prepare("DELETE FROM `Berichte` WHERE `id`=? AND `berichtsid`=?");
			// $delete->bind_param('ii', $_GET['id'],$berichtsid);
			// $delete->execute();
			// $delete->close();

			// $delete = "DELETE FROM `Berichte` WHERE `id`=".$id." AND `berichtsid`=".$berichtsid;
			// mysql_query($delete);
		// } 
?>

<html>
	<body>

		<div class="header">
			<?php
				include ('header_beobachter.html');
				include ('navigation.php');
			?>

		</div>
		</br>

		<div class="content">
			<form method="post" action="">



			<div style="float:left;margin-left:20px">
			<?php include('rcalender.php'); ?>
			</div> 
			<?php
				if (isset($_POST['feiertag'])) {
					$taetigkeit = 'Feiertag';
				} elseif (isset($_POST['urlaub'])) {
					$taetigkeit = 'Urlaub';
				} ?>
			<div align="right" style="float:left;margin-left:20px;">
				<?php
					echo '<h3>' . date_with_dotw_from_timestamp($date) . '</h3>'; 
					echo' <textarea name="taetigkeit" rows="12" cols="80">'.$taetigkeit.'</textarea>';
				?>
			<table>
			<tr >
				<td><input type="hidden" name="tstamp" value="<?php echo $date; ?>"/></td>
				<td><input type="submit" name="feiertag" value="Feiertag" ></td>
				<td><input type="submit" name="urlaub" value="Urlaub" ></td>
				<td> </td>
				<td> </td>
				<td><input type="submit" name="save" value="speichern" style="width:120px"/></td>
			</tr> 
		</table>

			</div>

			<div style="clear:both"/>


	</form>
</div>


	<div class="footer">
	<?php 
	include('footer_other.html'); 
	?>

	</div>
	</body>
</html>
