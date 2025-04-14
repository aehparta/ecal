<html lang="en">
<head>
<title>Resistor divider calculator</title>
<link type="text/css" rel="stylesheet" href="css/site.css" />
<script type="text/javascript" src="js/jquery-1.11.2.min.js"></script>
<script type="text/javascript" src="js/site.js"></script>
</head>
<body>

<?php

$resistors = array(
    'SMD-0402'     => [
        100, 270, 280,
        22000,
    ],
    'SMD-0603'     => [
        100, 280,
        22000,
    ],
    'SMD-0805'     => [
        1, 2.2, 3.3, 4.7, 5.6, 6.8, 8.2,
        10, 22, 27, 33, 43, 47, 56, 68, 75, 82, 91,
        100, 150, 220, 240, 330, 430, 470, 560, 680, 750, 820, 910,
        1000, 1200, 1400, 1500, 2050, 2200, 3240, 3300, 3320, 3600, 4300, 4700, 5600, 6800, 7500, 8200, 9100,
        10000, 12000, 15000, 20000, 22000, 33000, 47000, 49900, 51000, 68000, 75000, 82000, 91000,
        100000, 150000, 200000, 220000, 232000, 274000, 330000, 470000, 499000, 560000, 680000, 820000,
        1000000, 1500000, 2200000, 2870000, 3300000, 3920000, 4700000,
        10000000,
    ],
    'SMD-1206'     => [
        22,
        220, 820,
        1000, 1200, 1500,
        10000, 22000, 33000, 47000,
        100000, 220000,
        10000000,
    ],
    'Through-hole' => [
        1, 2.2, 3.3, 4.7, 5.6, 7.5, 8.2,
        10, 15, 22, 27, 33, 39, 47, 56, 68, 75, 82,
        100, 120, 150, 180, 220, 270, 330, 390, 470, 510, 680, 820,
        1000, 1500, 2200, 3000, 3300, 3900, 4700, 5600, 6800, 7500, 8200,
        10000, 12100, 15000, 22000, 33000, 39000, 47000, 56000, 68000, 75000, 82000,
        100000, 150000, 180000, 220000, 330000, 470000, 560000, 680000,
        1000000, 1500000, 2000000, 3300000, 4700000, 5600000,
        10000000,
    ],
);

$resistor_set = 'SMD-0805';
$from         = 5;
$to           = 2.5;
$minr         = 0;
$maxr         = 100000000000;
if (isset($_GET['from']) and isset($_GET['to'])) {
    $resistor_set = $_GET['resistors'];
    $from         = floatval($_GET['from']);
    $to           = floatval($_GET['to']);
    $minr         = floatval($_GET['minr']);
    $maxr         = floatval($_GET['maxr']);
    if ($maxr <= $minr) {
        $maxr = 100000000000;
    }
}

echo "<table id='search'><tr>";
echo '<td><h1>Select resistor divider values:</h1>';
echo "<form method='get' action='index.php'>";
echo '<table>';
echo "<tr><td>Voltage to divide:</td><td><input class='value' type='text' name='from' value='$from' /> V</td></tr>";
echo "<tr><td>Voltage needed:</td><td><input class='value' type='text' name='to' value='$to' /> V</td></tr>";
echo "<tr><td>Minimum resistance:</td><td><input class='value' type='text' name='minr' value='$minr' /> Ohm</td></tr>";
echo "<tr><td>Maximum resistance:</td><td><input class='value' type='text' name='maxr' value='$maxr' /> Ohm</td></tr>";
echo "<tr><td>Resistor set:</td><td><select name='resistors' id='resistors'>";
foreach ($resistors as $k => $r) {
    $selected = '';
    if ($k == $resistor_set) {
        $selected = "selected='selected'";
    }
    echo "<option $selected value='$k'>$k</option>";
}
echo '</select></td></tr>';
echo "<tr><td><input type='submit' value='calculate' /></td></tr>";
echo '</table>';
echo '</form>';
echo "<input id='resistor-set-current' type='hidden' value='$resistor_set' />";
echo '</td>';

echo '<td>';
foreach ($resistors as $rk => $rv) {
    $i = 0;
    echo "<div id='$rk' class='resistor-table'><h1>$rk</h1><table><tr>";
    foreach ($rv as $r) {
        if ($r >= 1000000) {
            $rr = ($r / 1000000) . 'M';
        } else if ($r >= 1000) {
            $rr = ($r / 1000) . 'k';
        } else {
            $rr = $r . 'R';
        }

        echo "<td>$rr</td>";
        $i++;
        if ($i >= 10) {
            echo '</tr><tr>';
            $i = 0;
        }
    }
    echo '</tr></table></div>';
}
echo '</td>';

echo '</tr></table>';

function sort_by_deviance($a, $b)
{
    if ($a['deviance'] == $b['deviance']) {
        return 0;
    }
    return ($a['deviance'] < $b['deviance']) ? -1 : 1;
}

$results = array();
if (isset($_GET['from']) and isset($_GET['to'])) {
    foreach ($resistors[$resistor_set] as $r1) {
        if ($r1 < $minr) {
            continue;
        }
        if ($r1 > $maxr) {
            continue;
        }
        foreach ($resistors[$resistor_set] as $r2) {
            if ($r2 < $minr) {
                continue;
            }
            if ($r2 > $maxr) {
                continue;
            }
            if ($r1 >= 1000000) {
                $rr1 = ($r1 / 1000000) . 'M';
            } else if ($r1 >= 1000) {
                $rr1 = ($r1 / 1000) . 'k';
            } else {
                $rr1 = $r1 . 'R';
            }
            if ($r2 >= 1000000) {
                $rr2 = ($r2 / 1000000) . 'M';
            } else if ($r2 >= 1000) {
                $rr2 = ($r2 / 1000) . 'k';
            } else {
                $rr2 = $r2 . 'R';
            }

            $v         = $r2 / ($r2 + $r1) * $from;
            $deviance  = abs($to - $v);
            $current   = $from / ($r1 + $r2);
            $results[] = array(
                'v'        => $v,
                'deviance' => $deviance,
                'r1'       => $r1,
                'rr1'      => $rr1,
                'r2'       => $r2,
                'rr2'      => $rr2,
                'i'        => $current,
                'p'        => $v * $current,
            );
        }
    }

    usort($results, 'sort_by_deviance');

    echo '<h1>Results:</h1><table>';
    echo '<tr><th>voltage</th><th>deviance (V)</th><th>R1</th><th>R2</th><th>Current</th><th>Power</th></tr>';
    foreach ($results as $r) {
        echo '<tr>';
        echo '<td>' . number_format($r['v'], 6) . 'V</td><td>' . number_format($r['deviance'], 6) . 'V</td><td>' . $r['rr1'] . '</td><td>' . $r['rr2'] . '</td><td>' . number_format($r['i'], 6) . 'A</td><td>' . number_format($r['p'], 6) . ' W</td>';
        echo '</tr>';
    }
    echo '</table>';
}

?>


</body>
</html>
