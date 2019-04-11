<?php
require_once("secure.php");
require_once("time.php");

function check($name, $array) {
    return (isset($array[$name]) and !empty($array[$name])) ? true : false;
}

if (check('von_monat', $_POST) and check('von_jahr', $_POST) and check('bis_monat', $_POST) and check('bis_monat', $_POST) and check('id', $_POST)) {
    $tstamp_start = mktime(0,0,0, $_POST['von_monat'], 1, $_POST['von_jahr']);
    $tstamp_end = mktime(0,0,0, $_POST['bis_monat'] + 1, -1, $_POST['bis_jahr']);
    $id = $_POST['id'];
    header('Location: weeks.php?id=' . $id . '&start=' . $tstamp_start . '&end=' . $tstamp_end);
    printf("<a href='weeks.php?id=%i&start=%i&end=%i'>Hier</a> klicken, wenn Sie nicht automatisch weitergeleitet werden.", $id, $tstamp_start, $tstamp_end);
} else {
    echo "Nicht alle Daten korrekt angegeben. <a href='beobachter.php'>Hier</a> klicken um zurück zu gehen.";
}

?>
