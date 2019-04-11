<?php
require_once("secure.php");
require_once("session.php");

$is_admin = (isset($_SESSION['rolle']) and $_SESSION['rolle'] == 3) ? true : false;
$width = $is_admin ? "33" : "25";
?>
<head>
    <title>Berichtsheft</title>
    <link rel="stylesheet" type="text/css" href="format.css">
</head>
<div class="border_navi main">
    <table class="w100">
        <tr>
            <?
            if (!$is_admin) {
                printf('<td class="w%s">
                            <a href="bericht.php?was=status" class="white navi">
                                <div class="abstand_7 center">
                                    Berichtsheft
                                </div>
                            </a>
                        </td>
                        <td class="w%s">
                            <a href="historie.php?was=status" class="white navi">
                                <div class="border_navi_rubrik_2 abstand_7 center">
                                    Historie
                                </div>
                            </a>
                        </td>', $width, $width);
            }
            ?>
            <td class="w<? echo $width;?>">
                <a href="Mein_Konto.php?was=status" class="white navi">
                    <div class="<? if (!$is_admin) echo "border_navi_rubrik_2"; ?> abstand_7 center">
                        Mein Konto
                    </div>
                </a>
            </td>
            <?
            if($is_admin) {
                printf('<td class="w%s">
                        <a href="admin.php?was=status" class="white navi">
                            <div class="border_navi_rubrik_2 abstand_7 center">
                                Admin
                            </div>
                        </a>
                    </td>', $width);
            }
            ?>
            <td class="w<? echo $width;?>">
                <a href="login.php?was=logout" class="white navi">
                    <div class="border_navi_rubrik_2 abstand_7 center">
                        Logout
                    </div>
                </a>
            </td>
        </tr>
    </table>
</div>
