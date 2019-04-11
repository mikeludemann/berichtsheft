<?php

/*Funktion für Monat,Tag und Jahr vor und zurück

 PHP prüft das Skript ob in der URL die Variable timestamp gesetzt ist und ob die übergebene Variable einen Wert enthält. Wenn die Variable nicht gesetzt ist so wird der aktuelle Timestamp in die Variable $date geschrieben ansonsten wird der Wert aus der Variable Timestamp übernommen. 

*/
function monthBack( $timestamp ){
    return mktime(0,0,0, date("m",$timestamp)-1,date("d",$timestamp),date("Y",$timestamp) );
}
function yearBack( $timestamp ){
    return mktime(0,0,0, date("m",$timestamp),date("d",$timestamp),date("Y",$timestamp)-1 );
}

function dayBack ( $timestamp ) {
    $result = mktime(0,0,0, date("m", $timestamp),date("d",$timestamp)-1,date("Y",$timestamp) );
    while( date("N", $result) > 5) {
        $result = mktime(0,0,0, date("m", $result),date("d",$result)-1,date("Y",$result) );
    }
    return $result;
    //return mktime(0,0,0, date("m", $timestamp),date("d",$timestamp)-1,date("Y",$timestamp) );
}

function dayForward ( $timestamp ) {
    //return mktime(0,0,0, date("m", $timestamp),date("d",$timestamp)+1,date("Y",$timestamp) );
    $result = mktime(0,0,0, date("m", $timestamp),date("d",$timestamp)+1,date("Y",$timestamp) );
    while( date("N", $result) > 5) {
        $result = mktime(0,0,0, date("m", $result),date("d",$result)+1,date("Y", $result) );
    }
    return $result;
}

function monthForward( $timestamp ){
    return mktime(0,0,0, date("m",$timestamp)+1,date("d",$timestamp),date("Y",$timestamp) );
}
function yearForward( $timestamp ){
    return mktime(0,0,0, date("m",$timestamp),date("d",$timestamp),date("Y",$timestamp)+1 );
}

/*
Funktion get Calender
Als erstes wird die Anzahl der Tages des aktuellen Monats ermittelt. Dies kann ganz einfach für die PHP Funktion date() gemacht werden. Dafür wird der Parameter "t" als String und der aktuelle Timestamp an date() übergeben. 

*/


function getCalender($date,$headline = array('Mo','Di','Mi','Do','Fr','Sa','So')) {
    $sum_days = date('t',$date);
    $LastMonthSum = date('t',mktime(0,0,0,(date('m',$date)-1),0,date('Y',$date)));
    
    foreach( $headline as $key => $value ) {
        echo "<div class=\"day headline\">".$value."</div>\n";
    }
    
    for( $i = 1; $i <= $sum_days; $i++ ) {
        $day_name = date('D',mktime(0,0,0,date('m',$date),$i,date('Y',$date)));
        $day_number = date('w',mktime(0,0,0,date('m',$date),$i,date('Y',$date)));
        
        if( $i == 1) {
            $s = array_search($day_name,array('Mon','Tue','Wed','Thu','Fri','Sat','Sun'));
            for( $b = $s; $b > 0; $b-- ) {
                $x = $LastMonthSum-$b;
                echo '<div class="day before">'.sprintf("%02d",$x)."</div>\n";
            }
        } 
        
        if( $i == date('d',$date)) {
            echo '<div class="day current">'.sprintf("%02d",$i)."</div>\n";
        } else {
            if (in_array($day_name, array('Sat', 'Sun'))) {
                echo '<div class="day normal weekend">' . sprintf("%02d",$i) . "</div>\n";
            } else {
                echo '<div class="day normal"><a href="?tstamp='.mktime(0,0,0,date('m',$date),$i,date('Y',$date)).'">'.sprintf("%02d",$i)."</a></div>\n";    
            }
            
        }
        
        if( $i == $sum_days) {
            $next_sum = (6 - array_search($day_name,array('Mon','Tue','Wed','Thu','Fri','Sat','Sun')));
            for( $c = 1; $c <=$next_sum; $c++) {
                echo "<div class=\"day after\"> ".sprintf("%02d",$c)." </div>\n"; 
            }
        }
    }
}
?>


<html>
<head>
<style type="text/css">
body {
    font-family:verdana;
    font-size:12px;
}
a {
    color:black;
    text-decoration: none;
}
.calender a:hover {
    text-decoration: underline;
    background: #228b22;
}
.calender {
    width:280px;
    border:8px solid #228b22;
}
* html .calender,
* + html .calender {
    width:282px;
}
.calender div.after,
.calender div.before{
    color:silver;
}
.weekend {
    color: #888888;
}
.day {
    float:left;
    width:40px;
    height:40px;
    line-height: 40px;
    text-align: center;
}
.day.headline {
    background:#006400;
    color:white;
    font-weight: bold;
}
.day.current {
    font-weight:bold;
    background: #006400;
    color:white;
}
.clear {
    clear:left;
}
.pagination {
    text-align: center;
    line-height:20px;
    font-weight: bold;
    color:white;
    background:#006400;
}
.pagination a {
    width:17px;
    height:17px;
    float:left;
    color:white;
}
.pagination span {
    display:block;
    float:left;
    width: 170px;
}
.clear {
    clear:both;
}
</style>
</head>
<body>
<?php

if( isset($_REQUEST['tstamp']) and !empty($_REQUEST['tstamp'])) $date = $_REQUEST['tstamp'];
else {
  $date = time();
  $date = mktime(0, 0, 0, date("m", $date), date("d", $date), date("Y", $date));
}

$arrMonth = array(
    "January" => "Januar",
    "February" => "Februar",
    "March" => "M&auml;rz",
    "April" => "April",
    "May" => "Mai",
    "June" => "Juni",
    "July" => "Juli",
    "August" => "August",
    "September" => "September",
    "October" => "Oktober",
    "November" => "November",
    "December" => "Dezember"
);
    
$headline = array('Mo','Di','Mi','Do','Fr','Sa','So');

?>

<div class="calender" >
    <div class="pagination">
        <a href="?tstamp=<?php echo yearBack($date); ?>" class="last">|&laquo;</a> 
        <a href="?tstamp=<?php echo monthBack($date); ?>" class="last">&laquo;</a> 
        <a href="?tstamp=<?php echo dayBack($date); ?>" class="last">&laquo;</a>
        <span><?php echo $arrMonth[date('F',$date)];?> <?php echo date('Y',$date); ?></span>
        <a href="?tstamp=<?php echo dayForward($date); ?>" class="next">&raquo;</a>
        <a href="?tstamp=<?php echo monthForward($date); ?>" class="next">&raquo;</a>
        <a href="?tstamp=<?php echo yearForward($date); ?>" class="next">&raquo;|</a>
        <div class="clear"></div>  
    </div>
    <?php getCalender($date,$headline); ?>
    <div class="clear"></div>
</div>

</body>
</html>
