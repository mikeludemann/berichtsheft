<?php
require_once('secure.php');
require_once("connect.php");
require_once("session.php");
require_once("time.php");
// require("index.php");

if(!isset($_SESSION['email'])) {
	header("Location: home.php");
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

include("header.html");

$berichtsid = $_SESSION['id'];

$statement = $db->prepare("SELECT start FROM data WHERE id = ?");
$statement->bind_param('i', $berichtsid);
$statement->execute();
$statement->bind_result($startstamp);
$statement->fetch();
$statement->close();

if ($startstamp === NULL) {
	$startstamp = 0;
}


	//$startstamp =/* "SELECT startstamp FROM userdaten WHERE `berichtsid` = ".$berichtsid. */ 1312149600; /* <= timestamp 01.08.2011*/
	
	//$endstamp = /* "SELECT endstamp FROM userdaten WHERE `berichtsid` = ".$berichtsid. */ 1401573600; /*<= timestamp 01.06.2014*/
	$endstamp = mktime(0, 0, 0, date("m", $startstamp), date("d", $startstamp), date("Y", $startstamp) + 4);
	
	$startyear = date('Y',$startstamp);
	$startmonth = date('m',$startstamp);
	$endmonth = date('m',$endstamp);
	$endyear = date('Y',$endstamp);
	$dateyear = date('Y',time());
	$datemonth = date ('m',time());

	
	
	
$was = isset($_GET["was"]) ? trim($_GET["was"]) : '';

   
if($_SESSION['rolle'] > 1){
	header("Location: home.php");
    echo' <script type="text/javascript">
      function weiterleiten()
      {
      self.location.href="beobachter.php?was=status";
      }
      window.setTimeout("weiterleiten()",0);
  </script>';
  die();
}

$mark_year = (isset($_POST['year']) and !empty($_POST['year'])) ? $_POST['year'] : $startyear;
$mark_month = (isset($_POST['month']) and !empty($_POST['month'])) ? $_POST['month'] : 1;
  
if(isset($_SESSION['email']))
    {
    echo "<div class=\"abstand_content_historie\"><br>Herzlich Willkommen, <b>".$_SESSION['email']."</b><br/><br/>";
    require("navigation.php");
    //include('historie.php');
    echo '</div><head>
        <title>Willkommen - Berichtsheft</title>
        <link rel="stylesheet" type="text/css" href="format.css">
    </head>
    <div id="content">
    <div class="abstand_content_historie">
    <h1>Aktueller Stand</h1>';
	?>
	<html>
		<body>
			
			<form action="" method="post">
				<input type="hidden" name="month" value="<? echo date("m", time()); ?>" />
				<input type="hidden" name="year" value="<? echo date("Y", time()); ?>" />
				<input type="submit" value="Dieser Monat" />
			</form>
				<fieldset>
						<legend>Bitte wählen Sie ein Datum aus:</legend>
							<form method="post" action="historie.php">
									<table width="100">
										<tr>
											<td>Jahr: </td>
												<td>
													<select name="year">
														<?php 
															
															for($y = $startyear; $y <= $endyear; $y++ ) { 
																if ($y == $mark_year) {
																	printf('<option value="%s" selected="selected">%s</option>', $y, $y);
																} else {
																	printf('<option value="%s">%s</option>', $y, $y);
																}
															} 
															
														?>										
													</select>
												</td>
										</tr>
										<tr>
											<td>Monat: </td>
												<td>
													<select name="month">
														<?php
															printf('<option value="1" %s>Januar</option>', 1 == $mark_month ? "selected='selected'" : '');
															printf('<option value="2" %s>Februar</option>', 2 == $mark_month ? "selected='selected'" : '');
															printf('<option value="3" %s>März</option>', 3 == $mark_month ? "selected='selected'" : '');
															printf('<option value="4" %s>April</option>', 4 == $mark_month ? "selected='selected'" : '');
															printf('<option value="5" %s>Mai</option>', 5 == $mark_month ? "selected='selected'" : '');
															printf('<option value="6" %s>Juni</option>', 6 == $mark_month ? "selected='selected'" : '');
															printf('<option value="7" %s>Juli</option>', 7 == $mark_month ? "selected='selected'" : '');
															printf('<option value="8" %s>August</option>', 8 == $mark_month ? "selected='selected'" : '');
															printf('<option value="9" %s>September</option>', 9 == $mark_month ? "selected='selected'" : '');
															printf('<option value="10" %s>Oktober</option>', 10 == $mark_month ? "selected='selected'" : '');
															printf('<option value="11" %s>November</option>', 11 == $mark_month ? "selected='selected'" : '');
															printf('<option value="12" %s>Dezember</option>', 12 == $mark_month ? "selected='selected'" : '');
														?>
													</select>
												</td>
										</tr>
									</table>
								<input type="submit" name="choose" value="auswählen"/>
							</form>
				</fieldset>
			<div class="footer">;
				<?php include('footer_other.html'); ?>
			</div>
				</body>
		</html>

		
	<?php
		if (!isset($_POST['month']) or empty($_POST['month']) or !isset($_POST['year']) or empty($_POST['year'])) {
			die();
		}
		
	/* VARIABLEN FÜR DAS SOLL UND IST ARRAY */
					$solltage = array();
					$isttage = array();
					$zwischenstamp = array();
					$von = mktime(0,0,0,$_POST['month'] , 1,$_POST['year']);
					$bis = mktime(0,0,0, $_POST['month']+1,1,$_POST['year'] ) -60*60*24; 
					$start = $von;
					$end = $endstamp > $bis ? $bis : $endstamp;
					
					
					/* SOLL SCHLEIFE */
					if (date('N', $start) != 1) {
						$start_listing = $start - date('N', $start) * 60 * 60 *24;
					} else {
						$start_listing = $start;
					}
					if (date('N', $end) < 5) {
						$end_listing = $end + (5 - date('N', $end)) * 60 * 60 * 24;
					} else {
						$end_listing = $end;
					}
						for($i = $start_listing; $i <= $end_listing; $i = $i + 60*60*24)
							{
								if (date("N", $i) <= 5)
									$solltage[] = $i;
							} 
						
					// $abfrage = "SELECT tstamp FROM `Berichte` WHERE `tstamp` = ".$_GET['tstamp']; 		
					// $db_erg = mysql_query($abfrage);
				
						if(isset($_POST['month']) && isset($_POST['year'])) {
						
						$zeitraum = $db-> prepare("SELECT * FROM `Berichte` WHERE `berichtsid` = ? AND `tstamp` >= ? AND tstamp <= ? ORDER BY tstamp ASC");
						$zeitraum->bind_param('iii',$berichtsid, $start_listing, $end_listing);
						//$zeitraum->bind_param('iii',$berichtsid, $von, $bis);
						$zeitraum->execute();
						$result = $zeitraum-> get_result();
						$zeitraum->close();
						
						if ( $startstamp > $von  || $endstamp < $bis  ) {
						
						echo 'Monat liegt nicht im Ausbildungsbereich';
						} else {
       echo '<br/><table class="w100" cellspacing="0">
            <tr>
                <td class="w50 label border_bottom">
                    <span class="label">IST</span>
                </td>
                <td class="w50 label abstand_10 border_navi_rubrik border_bottom">
                    <span class="label">SOLL</span>
                </td>
            </tr>';
		
		while ($arrBerichte = $result->fetch_assoc()) {
			$isttage[] = $arrBerichte['tstamp'];
		}
		
		foreach($solltage as $datum_zeile) {
			$link = '<a href="bericht.php?tstamp=' . $datum_zeile . '">' . date_with_dotw_from_timestamp($datum_zeile) . '</a>';
			if ($datum_zeile < $start or $datum_zeile > $end) {
				$link = '<i>' . $link . '</i>';
			}
			echo "<tr><td class='w50'>";
			if (in_array($datum_zeile, $isttage)) {
				echo $link . "</td><td class='w50 abstand_10 border_navi_rubrik'>&#160;</td>";
			} else {
				echo "&#160;</td><td class='w50 abstand_10 border_navi_rubrik'>" . $link . "</td>";
			}
			echo "</tr>";
		}
			
		echo '</table><br/>';
		
		printf('<form action="weeks.php" method="get"><input type="hidden" name="id" value="%s" /><input type="hidden" name="start" value="%s" /><input type="hidden" name="end" value="%s" />', $_SESSION['id'], $start, $end);
		echo '<input type="submit" name="pdf" value="PDF generieren"/>';
		printf('</form>');
		echo ' <br><br>
        <div class="abstand_9">
            <a class="links_blue" href="bericht.php">
                Zur Berichtsseite
            </a>
        </div>
    </div>
    </div><br>';
    
		} 
		}
		}
  
?>
