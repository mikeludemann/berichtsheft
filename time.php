<?php

// zu einem gegebenen timestamp den timestamp des beginns der zugehörigen woche bestimmen
function timestamp_week_start($date) {
  return mktime(0, 0, 0, date("m", $date), date("d", $date) - date("N", $date) + 1, date("Y", $date));
}

// zu einem gegebenen Timestamp den timestamp des endes der zugehörigen woche bestimmen (Sonntag)
function timestamp_week_end($date) {
  return mktime(0, 0, 0, date("m", $date), date("d", $date) + 7 - date("N", $date), date("Y", $date));
}

// von einer woche eines jahres den timestamp des beginns dieser woche bestimmen
function week_to_timestamp($week, $year) {
  $ts = mktime(0,0,0,1,4,$year); // http://de.wikipedia.org/wiki/Woche#Kalenderwoche - 4.Januar ist immer in erster Kalenderwoche
  //while(!date("W", $ts) === $week) {
  $ts = mktime(0,0,0, date("m", $ts), date("d", $ts) + ($week - 1) * 7, $year);
  return timestamp_week_start($ts);
}

// woche eines timestamps
function week_from_timestamp($ts) {
  return date("W", $ts);
}

// jahr eines timestamps
function year_from_timestamp($ts) {
  return date("Y", $ts);
}

// formatiertes datum und uhrzeit von gegebenen timestamp
function time_string_from_timestamp($ts) {
  return date("d.m.Y H:i:s", $ts);
}

// formatiertes Datum von gegebenen timestamp
function date_string_from_timestamp($ts) {
  return date("d.m.Y", $ts);
}

// formatiertes Datum mit Wochentag von gegebenem Timestamp (Programmabbruch, wenn samstag oder sonntag übergeben!)
function date_with_dotw_from_timestamp($ts) {
  //var_dump($ts);
  $day = "";
  switch (date("N", $ts)) {
    case 1:
      $day = "Montag";
      break;
    case 2:
      $day = "Dienstag";
      break;
    case 3:
      $day = "Mittwoch";
      break;
    case 4:
      $day = "Donnerstag";
      break;
    case 5:
      $day = "Freitag";
      break;
    case 6:
      $day = "Samstag";
      break;
    case 7:
      $day = "Sonntag";
      break;
    default:
      return "Fehler: ungültiger Wochentag. Dies sollte nicht passieren.";
  }
  return $day . date(": d.m.Y", $ts);
}

// alle timestamps der (arbeits)woche einer gegebenen woche im jahr
function timestamps_in_week($week, $year) {
  $result = array();
  $start = week_to_timestamp($week, $year);
  for ($i = 0; $i < 5; $i++) {
    $result[] = mktime(0, 0, 0, date("m", $start), date("d", $start) + $i, $year);
  }
  return $result;
}

// zwei gegebene timestamps: alle wochen mit allen timestamps der tage dazwischen
function weeks_between_timestamps($start, $end) {
  $going = $start;
   
  $result = array();
  
  while($going <= $end) {
    $result[] = timestamps_in_week(week_from_timestamp($going), date("Y", $going));
    $going = mktime(0, 0, 0, date("m", $going), date("d", $going) + 7, date("Y", $going));
  }
  
  return $result;
}

// timestamp auf 0:00:00 Uhr normalisieren
function normalize_timestamp($ts) {
  return mktime(0, 0, 0, date("m", $ts), date("d", $ts), date("Y", $ts));
}

/*
$start = mktime(0,0,0, 11, 27, 2011);
$end = mktime(0,0,0, 2, 7, 2012);

$erg = weeks_between_timestamps($start, $end);

foreach($erg as $woche) {
  foreach($woche as $tag) {
    echo $tag . "   " . time_string_from_timestamp($tag);
    echo "<br>";
  }
}
*/
  
?>
