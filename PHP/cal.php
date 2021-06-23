<?php
// Set your timezone
date_default_timezone_set('America/Chicago');

require_once "database.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Get prev & next month
if (isset($_GET['ym'])) {
    $ym = $_GET['ym'];
} else {
    // This month
    $ym = date('Y-m');
}

// Check format
$timestamp = strtotime($ym . '-01');
if ($timestamp === false) {
    $ym = date('Y-m');
    $timestamp = strtotime($ym . '-01');
}

// Today
$today = date('Y-m-j', time());

// For H3 title
$html_title = date('Y / m', $timestamp);

// Create prev & next month link     mktime(hour,minute,second,month,day,year)
$prev = date('Y-m', mktime(0, 0, 0, date('m', $timestamp)-1, 1, date('Y', $timestamp)));
$next = date('Y-m', mktime(0, 0, 0, date('m', $timestamp)+1, 1, date('Y', $timestamp)));
// You can also use strtotime!
// $prev = date('Y-m', strtotime('-1 month', $timestamp));
// $next = date('Y-m', strtotime('+1 month', $timestamp));

// Number of days in the month
$day_count = date('t', $timestamp);
 
// 0:Sun 1:Mon 2:Tue ...
$str = date('w', mktime(0, 0, 0, date('m', $timestamp), 1, date('Y', $timestamp)));
//$str = date('w', $timestamp);


// Create Calendar!!
$weeks = [];
$week = '';

// Add empty cell
$week .= str_repeat('<td></td>', $str);

$req = $conn->query("SELECT * FROM e_events WHERE MONTH(`start`) = ".date("m", $timestamp)." AND YEAR(`start`) = ".date("Y", $timestamp)." ORDER BY `start`");

$conn->close();

//$res = $req->fetchAll(PDO::FETCH_ASSOC);
$res = [];
while ($temp = $req->fetch_array(MYSQLI_NUM)) {
    $res[] = $temp;
}

$eventsReq = "";
// $str = date('w', mktime(0, 0, 0, date('m', $timestamp), 1, date('Y', $timestamp)));

//for ($i = 0; $i < count($res); $i++) {
for ($day = 1; $day <= $day_count; $day++, $str++) {
    $date = $ym . '-' . $day;

    for ($i = 0; $i < count($res); $i++){
        if ($res[$i][5] == $date) // $res[x][5] = start
    	   $eventsReq .= "<li class=\"event\" id=\"e_".$day."_".($i+1)."\" onclick=\"eventClick('e_".$day."_".($i+1)."','".$day."', event)\">".$res[$i][1]."</li>"; // $res[x][1] = title
    }

    if ($today == $date) {
    	$week .= '<td onclick="dateClick('.$day.', event)" class="today" id="'.$day.'"><span class="textJour">' . $day;
    } else {
    	$week .= '<td onclick="dateClick('.$day.', event)" id="'.$day.'"><span class="textJour">' . $day;
    }

    $week .= '</span><br><ul class="contenuJour">'.$eventsReq.'</ul><input class="ajouterEventInput" id="ajouterJour'.$day.'" type=\"text\" style="color:black;position:relative;display:block;visibility:hidden;" placeholder="Nouvel évènement"></input></td>';

    $eventsReq = "";


    // This is where the event posting ends
    // End of the week OR End of the month
    if ($str % 7 == 6 || $day == $day_count) {
    	if ($day == $day_count) {
    		$week .= str_repeat('<td></td>', 6 - ($str % 7)); // Add empty cell
    	}
    	$weeks[] = '<tr>' . $week . '</tr>';

        // Prepare for new week
    	$week = '';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>PHP Calendar</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css?family=Noto+Sans" rel="stylesheet">
    <style>
        .container {
            font-family: 'Noto Sans', sans-serif;
            margin-top: 80px;
        }
        h3 {
            margin-bottom: 30px;
        }
        th {
            height: 30px;
            text-align: center;
        }
        td {
            height: 100px;
        }
        .today {
            background: orange;
        }
        th:nth-of-type(1), td:nth-of-type(1) {
            color: red;
        }
        th:nth-of-type(7), td:nth-of-type(7) {
            color: blue;
        }
    </style>
</head>
<body>
    <div class="container">
        <h3><a href="?ym=<?php echo $prev; ?>">&lt;</a> <?php echo $html_title; ?> <a href="?ym=<?php echo $next; ?>">&gt;</a></h3>
        <table class="table table-bordered">
            <tr>
                <th>S</th>
                <th>M</th>
                <th>T</th>
                <th>W</th>
                <th>T</th>
                <th>F</th>
                <th>S</th>
            </tr>
            <?php
                foreach ($weeks as $week) {
                    echo $week;
                }
            ?>
        </table>
    </div>
</body>
</html>
