<?php
echo '<h3 style="color:'.[hex_color].';display:inline-block;padding:.2em;">'.[cal_name].'</h3>';

// Set your timezone
date_default_timezone_set('America/Chicago');

$servername = '';
$username = '';
$password = '';
$db = '';
//Comment the following for production server
$port = 0000;
$socket = "";
$conn = mysqli_connect($servername,$username,$password,$db,$port,$socket);
//Uncomment below for production server
//$conn = mysqli_connect($servername,$username,$password,$db);


if (!$conn)
{
echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

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
$html_title = date('F Y', $timestamp);

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
$week .= str_repeat('<td class="empty-cells"></td>', $str);

$req = $conn->query("SELECT * FROM e_events WHERE MONTH(`start`) = ".date("m", $timestamp)." AND YEAR(`start`) = ".date("Y", $timestamp)." AND cal_id = ".[cal_id]." ORDER BY `start`");

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
		$event_id = $res[$i][0];
		$string_link = sc_make_link(form_e_events_view, id=$event_id);
        if (strtotime($res[$i][5]) == strtotime($date)) // $res[x][5] = start
    	   $eventsReq .= '<li class="event" id="e_"'.$day.'"_"'.($i+1).'"><a href="'.$string_link.'" title="View the Event Details">'.$res[$i][1].'</a></li>'; // $res[x][1] = title
    }

    if ($today == $date) {
    	$week .= '<td class="today" id="'.$day.'"><span class="day_text">' . $day;
    } else {
    	$week .= '<td id="'.$day.'"><span class="day_text">' . $day;
    }

    $week .= '</span><br><ul class="day_content">'.$eventsReq.'</ul></td>';

    $eventsReq = "";


    // This is where the event posting ends
    // End of the week OR End of the month
    if ($str % 7 == 6 || $day == $day_count) {
    	if ($day == $day_count) {
    		$week .= str_repeat('<td class="empty-cells"></td>', 6 - ($str % 7)); // Add empty cell
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
    <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">
    <style>
        .container {
            margin: 0;
			font-family:'Montserrat', sans-serif;
			font-size:smaller;
        }
        h3 {
            margin-bottom: 20px;
			text-shadow:1px 1px 1px darkgrey;
        }
		table.table, table.table-bordered {
			margin-left:0;
		}
        th {
            height: 30px;
            text-align: center;
        }
        td {
            height: 100px;
			width:5em;
        }
		.empty-cells {
			background:#DEEBC9;/*TX*/
		}
        .today {
            background: #e8f7cf;/*TX*/
        }
		ul.day_content {
			margin-left:0;
			padding-left:0;
		}
		ul.day_content li.event {
			list-style:none;
			display:inline-block;
			color:<?php echo [hex_color]; ?>;
			text-shadow:1px 0 0 black;
			margin-left:0;
			padding-left:0;
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