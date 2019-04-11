<?php
require_once('secure.php');
require_once("session.php");

if (!isset($_GET['id']) or empty($_GET['id'])) {
  die('Keine Benutzerid angegeben.');
}
if (!isset($_GET['start']) or empty($_GET['start'])) {
  die('Kein Startdatum angegeben.');
}
if (!isset($_GET['end']) or empty($_GET['end'])) {
  die('Kein Enddatum angegeben.');
}

if ($_GET['start'] > $_GET['end']) {
  die('Startdatum mu� vor Enddatum liegen.');
}


if(!isset($_SESSION['email']) or empty($_SESSION['email'])) {
  die('Nicht eingeloggt!');
}

if($_SESSION['rolle'] <= 1 and $_SESSION['id'] != $_GET['id']) {
  die('Keine Berechtigung, diese Berichtsdaten anzusehen');
}

$user_id = $_GET['id'];
$start = $_GET['start'];
$end = $_GET['end'];


// PDF generieren
require_once('fpdf/fpdf.php');
// Mit Datenbank verbinden
require_once('connect.php');
// Hilfsfunktionen zur Zeitberechnung
require_once('time.php');

/*
 Seite ist 
 - 210 mm breit
 - 297 mm hoch
 
 Rand ist 
 - 20 mm oben, rechts, links
 - 10 mm unten
 */


Class PDF extends FPDF
{
  function PDF( $user, $dimensions ) {
    $this->user = $user;
    

    
    $this->dimensions = $dimensions;
    
    parent::FPDF();
  }

  // Erweiterung von fpdf.org, von yukihiro_o
  // http://www.fpdf.org/en/script/script33.php
  // Gestrichelte Linien zeichnen.
  function SetDash($black=false, $white=false)
  {
    if($black and $white)
      $s=sprintf('[%.3f %.3f] 0 d', $black*$this->k, $white*$this->k);
    else
      $s='[] 0 d';
      $this->_out($s);
  }
  
  // Eine Seite mit Daten f�llen.
  function fillpage($data)
  {
    $dimensions = $this->dimensions;
  
    $this->Rect($dimensions['border']['l'], $dimensions['border']['t'], $dimensions['border']['r'] - $dimensions['border']['l'], $dimensions['border']['b'] - $dimensions['border']['t']); // Außenrahmen
    $this->Line($dimensions['border']['l'], 30, $dimensions['border']['r'], 30); // Linie unter Berichtsheft
    $this->Line(155, $dimensions['border']['t'], 155, 30); // Trennlinie 'Berichtsheft' - Seite

    
    for($i = 0; $i < 5; ++$i) { // Trennlinien Tage
      $offset = $dimensions['days']['start'] + $dimensions['days']['step'] * $i;
      $this->Line($dimensions['border']['l'], $offset, $dimensions['border']['r'], $offset);
      
      $this->SetDash(1, 1);
      for($j = 1; $j < 6; ++$j) { // gestrichelte Linien innerhalb eines Tageseintrages
        $t = $offset + $j * ($dimensions['days']['step'] / 6);
        $this->Line($dimensions['border']['l'], $t, $dimensions['border']['r'], $t);
      }
      $this->SetDash();
    }
    $this->Line($dimensions['border']['l'], $dimensions['days']['end'], $dimensions['border']['r'], $dimensions['days']['end']); // Letzte Linie unterhalb der Tage
    
    
    $this->SetFont('Arial', '', 24);
    $this->Cell(140,10, 'Berichtsheft'); // Überschrift Berichtsheft
    $this->SetFont('Arial', 'B', 16);
    $this->Cell(0,10, 'Seite '. $this->PageNo(), 0, 1); // Seitenzahl
    $this->SetFont('Arial', '', 11);
    $this->Cell($dimensions['table_width']['half'], 8, 'Name des Auszubildenden:', 0, 0);
    $this->Cell($dimensions['table_width']['half'], 8, 'IHK-Nr. des Auszubildenden:', 0, 1);
     
    $this->SetFont('Arial', '', 20);
    //$this->Cell($dimensions['table_width']['half'], 8, $data['user']['name'], 0, 0); // Name des Auszubildenden
    $this->Cell($dimensions['table_width']['half'], 8, $this->user['name'], 0, 0); // Name des Auszubildenden
    //$this->Cell($dimensions['table_width']['half'], 8, $data['user']['ihk_nr'], 0, 1); // IHK Nr des Auszubildenden
    $this->Cell($dimensions['table_width']['half'], 8, $this->user['ihk'], 0, 1); // IHK Nr des Auszubildenden
     
    $this->Line($dimensions['border']['l'], 47, $dimensions['border']['r'], 47); // Linie unter Name / IHK Nr.
     
    $this->Cell($dimensions['table_width']['half'], 12, '', 0, 0);
    $this->Cell($dimensions['table_width']['half'], 12, 'Woche: ' . week_from_timestamp($data[0]['timestamp']), 0, 1); // Kalenderwoche
     
    $this->Line($dimensions['middle']['table'], 56, $dimensions['border']['r'], 56); // Trennlinien zwischen Kalenderwoche und Datum
     
    $this->SetFont('Arial', '', 12);
    $this->Cell($dimensions['table_width']['half'], 2, 'Tätigkeiten', 0, 0);
    //$this->Cell($dimensions['table_width']['half'] / 2, 2, 'vom: ' . date('d.m.Y', $data['date']['start'][0]), 0, 0); // Startdatum der Kalenderwoche
    $this->Cell($dimensions['table_width']['half'] / 2, 2, 'vom: ' . date_string_from_timestamp($data[0]['timestamp']), 0, 0); // Startdatum der Kalenderwoche
    //$this->Cell($dimensions['table_width']['half'] / 2, 2, 'bis: ' . '19.1.2038', 0, 1); // Enddatum der Kalenderwoche TODO:
    $this->Cell($dimensions['table_width']['half'] / 2, 2, 'bis: ' . date_string_from_timestamp($data[4]['timestamp']), 0, 1); // Enddatum der Kalenderwoche TODO:
     
    $this->Line($dimensions['middle']['table'] + $dimensions['table_width']['half'] / 2, 56, $dimensions['middle']['table'] + $dimensions['table_width']['half'] / 2, $dimensions['days']['start']); // Trennlinie zwischen STart- und Enddatum
    
    $this->Line($dimensions['middle']['table'], 30, $dimensions['middle']['table'], 62); // Trennlinie Mitte Kopf
    
    
    $this->SetFont('Arial', '', 10);
    $this->SetY($dimensions['days']['end'] + 3);
    $offset_line_richtigkeit = 20;
    $this->MultiCell($offset_line_richtigkeit, 5, "Für die\nRichtigkeit");
    
    $this->Line($offset_line_richtigkeit + $dimensions['margin']['l'], $dimensions['days']['end'], $offset_line_richtigkeit + $dimensions['margin']['l'], $dimensions['border']['b']);
    
    $width_signatures = $dimensions['table_width']['full'] - $offset_line_richtigkeit;
    $width_signatures_half = $width_signatures / 2;
    $x_signatures_half = $width_signatures_half + $dimensions['margin']['l'] + $offset_line_richtigkeit;
    $this->Line($x_signatures_half, $dimensions['days']['end'], $x_signatures_half, $dimensions['border']['b']);
    
    
    // Platzhalter für Datum und Unterschrift
    $this->SetY($dimensions['days']['end'] +  10);
    $this->SetX($dimensions['margin']['l'] + $offset_line_richtigkeit + 4);
    $this->Cell(23, 5, 'Datum', 'T', 0, 'C');
    $this->SetX($this->GetX() + 4);
    $this->Cell(40, 5, 'Auszubildender', 'T', 0, 'C');
    $this->SetX($x_signatures_half + 4);
    $this->Cell(23, 5, 'Datum', 'T', 0, 'C');
    $this->SetX($this->GetX() + 4);
    $this->Cell(40, 5, 'Ausbilder', 'T', 0, 'C');
    
    for($i = 0; $i < count($data); $i++) {
      $this->entry_day($data[$i]['timestamp'], $dimensions['days']['start'] + $i * 42, 7, $data[$i]['taetigkeit']);
    }
  }

  // Eintrag für einen Tag machen
  function entry_day($day, $start, $step, $data)
  {
    $day = date_with_dotw_from_timestamp($day);
    $dimensions = $this->dimensions;
    
    $this->SetFont('Arial', 'B', '11');
    $this->SetXY($dimensions['margin']['l'], $start);
    $this->Cell(0, $step, $day, 0, 1);
    $this->Setfont('Arial', '', '11');
    
    $data = preg_split('/((?<!\\\|\r)\n)|((?<!\\\)\r\n)/', $data);
    
    for($i = 0; $i < count($data); ++$i)
    {
      $this->SetX($this->GetX() + 5);
      $this->Cell(0, $step, $data[$i], 0, 1);
    }
  }

  
  function addweek($data)
  {
    $this->AddPage();
    $this->fillpage($data);
  }
}

function get_weeks_data($week_start, $week_end, $statement) {
  $statement->bind_param('ii', $week_start, $week_end);
  $statement->execute();
  $statement->bind_result($tstamp, $taetigkeit);
  $statement->store_result();
  if ($statement->num_rows < 5) {
    die('Fehler: Es fehlen Daten für Woche ' . week_from_timestamp($week_start) . ' in Jahr ' . year_from_timestamp($week_start) . '.');
  }
  $result = array();
  while($statement->fetch()) {
    $a = array('timestamp' => $tstamp, 'taetigkeit' => $taetigkeit);
    $result[] = $a;
  }
    
  return $result;
}

$statement = $db->prepare("SELECT vorname, nachname, ihk FROM data WHERE id = ?");
$statement->bind_param('i', $user_id);

$statement->execute();
$statement->bind_result($vorname, $nachname, $ihk);
$statement->fetch();
$statement->close();

$user = array('name' => $vorname . ' ' . $nachname, 'ihk' => $ihk);

$dimensions = array();
// Dimensionen auf der Seite zur Ausrichtung von Linien und Schrift
$dimensions['page'] = array('x' => 210, 'y' => 297); //Maximale Werte für Breite und Höhe (mm)
$dimensions['margin'] = array('t' => 20, 'l' => 20, 'r' => 20, 'b' => 10); // Seitenränder *t*op *l*eft *r*ight *b*ottom
$dimensions['bordermargin'] = 2; // Abstand Rahmen zur Schrift
$dimensions['border'] = array('t' => $dimensions['margin']['t'] - $dimensions['bordermargin'], 'l' => $dimensions['margin']['l'] - $dimensions['bordermargin'], 'r' => $dimensions['page']['x'] - $dimensions['margin']['r'] + $dimensions['bordermargin'], 'b' => $dimensions['page']['y'] - $dimensions['margin']['b'] + $dimensions['bordermargin']); // Werte für äußeren Rand, berechnet aus seitengröße, margin und bordermargin
$dimensions['days'] = array('start' => 62, 'step' => 42); // Startpunkt der Tage, Höhe eines einzelnen Tages
$dimensions['days']['end'] = $dimensions['days']['start'] + 5 * $dimensions['days']['step'];  // Berechneter Wert für Ende der Tage
$dimensions['middle'] = array('x' => $dimensions['page']['x'] / 2, 'y' => $dimensions['page']['y'] / 2, 'table' => $dimensions['border']['l'] + ($dimensions['border']['r'] - $dimensions['border']['l']) / 2); // Mitte der Seite von verschiedenen Aspekten aus gesehen
$dimensions['table_width'] = array('full' => $dimensions['page']['x'] - $dimensions['margin']['l'] - $dimensions['margin']['r'], 'half' => ($dimensions['page']['x'] - $dimensions['margin']['l'] - $dimensions['margin']['r']) / 2); // Breite der Tabelle mit den Einträgen

$pdf = new PDF($user, $dimensions);
$pdf->AliasNbPages(); // Alias für Nummer der Seiten (im Moment werden keine Seitenzahlen eingefügt)
$pdf->SetMargins(20, 20, 10); // Seitenränder festlegen
$pdf->SetFont('Arial', 'BI', 20);
$pdf->SetAutoPageBreak(false); // nicht automatisch neue Seite starten

$filename = "Berichtshefte " . $vorname . " " . $nachname . " KW " . week_from_timestamp($start) . "-" . year_from_timestamp($start) . " bis KW " . week_from_timestamp($end) . "-" . year_from_timestamp($end) . ".pdf";

$weeks = weeks_between_timestamps($start, $end);

$statement = $db->prepare("SELECT tstamp, taetigkeit FROM berichte WHERE berichtsid = " . $user_id . " AND tstamp >= ? AND tstamp <= ? ORDER BY tstamp");


foreach($weeks as $week) {
  $week_data = get_weeks_data($week[0], end($week), $statement);
  $pdf->addweek($week_data);
}

if (isset($_GET['format']) and $_GET['format'] == 'browser') {
  $pdf->Output($filename, 'I');
} else {
  $pdf->Output($filename, 'D');
}

$statement->close();
  
?>
