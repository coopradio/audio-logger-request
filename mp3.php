<?php
// get start and end, converting to `date` format
$start = date("m-d-H_i_s", strtotime($_REQUEST["date"]. " " . $_REQUEST["start"]));
$end = date("m-d-H_i_s", strtotime($_REQUEST["date"] . " " . $_REQUEST["end"]));

// set bitrate
$bitrate = 128000;

// open archive directory
$archive_dir = "/archive";
$archive = opendir($archive_dir);

// create human readable variable names
$start_hour = substr($start, 6, 2);
$start_min = substr($start, 9, 2);
$end_hour = substr($end, 6, 2);
$end_min = substr($end, 9, 2);
$month = substr(date("F", mktime(0,0,0,substr($start,0,2), 10)), 0, 3);
$day = substr($start, 3, 2);
$year = substr($_REQUEST["date"], 0, 4);
$day_name = substr(date("D", mktime(1,0,0,date('m',strtotime($month)),$day,$year)), 0, 3);

$folder_name = "requested";
$file_name = $year . "_" . $month . $day ."_" . $day_name . "-" . $start_hour . ":" . $start_min . "-" . $end_hour . ":" . $end_min . ".mp3";
// loop variable definitions
$start_file = '';
$end_file = '';

$file_list = [];
// loop to find files and append add file list
while(($file = readdir($archive)) !== false) {
    // add start file
    if (substr($start, 0, 8) == substr($file, 0, 8)){ //If start file is the same as tested file, add file to file list
	    $start_file = $file;
        array_push($file_list, $file);
    }
    // add middle files if applicable
    if ((substr($start, 0, 5) == substr($file, 0, 5)) && (substr($start, 6, 2) < substr($file, 6, 2)) && (substr($end, 6, 2)) > (substr($file, 6, 2))){ // If month/day are the same AND the start time is less than the file time AND the end time is greater than the file time, add the file to the file list
        array_push($file_list, $file);
    }
    // add end file
    if (substr($end, 0, 8) == substr($file, 0, 8)){ // If end file is the same as test file, add file to file list
	    $end_file = $file;
        array_push($file_list, $file);
    }
}
	    
// find length of file
$l_hour = substr($end, 6, 2) - substr($start, 6, 2);
$l_min = substr($end, 9, 2) - substr($start, 9, 2);
$l_sec = substr($end, 12, 2) - substr($start, 12, 2);

$offset_sec = substr($start, 9, 2) * 60;
$length = ((($l_hour * 60) + $l_min) * 60) + $l_sec;
$end_point = $offset_sec + $length;

// sort the list
sort($file_list);

// create file list for shell command
$cat_list = [];

$length_bytes = $length * ($bitrate / 8.0); // find total length in bytes
foreach ($file_list as $file) {
	array_push($cat_list, "cat $archive_dir/$file"); // push current last file in list to list string
	
	if (!isset($offset_bytes)) // calculate offset in bytes // if this is the last file, set the offset byte variable
	{
		$offset_bytes = $offset_sec * (($bitrate) / 8.0); // calculation to find offset in bytes from offset in seconds
		if ($offset_bytes > 0) { // if offset is greater than 0 (ie if the end is not on the hour) add tail command with offset byte amount
			$striphead = "| tail -c $length_bytes";
		} else { // if no offset (last file is on the hour) set the striphead variable to be blank
			$striphead = "";
		} }
		
}
$length_and_offset = $length_bytes + $offset_bytes; // variable so as not to subtract offset twice
$cat_list= array_unique($cat_list); // remove any duplicate files (in case start and end are the same)
$cat_joined = implode(";", $cat_list); // create string from cat_list

// create shell command string
$command = "(" . implode(";", $cat_list). ")" . " | mp3cat --clean - - " . " | head -c $length_and_offset " . $striphead . " > $folder_name/$file_name";

// execute command
system($command, $retval);
/*
echo "Start Request: " . $_REQUEST["start"] . "<br>";
echo "End Request: " . $_REQUEST["end"] . "<br>";
echo "Date Request: " . $_REQUEST["date"] . "<br>";
echo "<br>";
echo "Bitrate: " . $bitrate . "<br>";
echo "Length hour: " . $l_hour . "<br>";
echo "Length minute: " . $l_min. "<br>";
echo "Length total (in seconds): " . $length. "<br>";
echo "Offset (in seconds): " . $offset_sec . "<br>";
echo "Offset (in bytes): " . $offset_bytes . "<br>";
echo "End (in seconds): " . $end_point . "<br>";
echo "<br>";
echo "Start: " . $start. "<br>";
echo "End: " . $end. "<br>";
echo "<br>";
echo "Start file: " . $start_file. "<br>";
echo "End file: " . $end_file. "<br>";
echo "<br>";
echo "Cat list: " . $cat_joined . "<br>";
echo "striphead: " . $striphead . "<br>";
echo "Command: " . $command . "<br>";
//echo var_dump($file_list);

echo "File list: " . $file_str . "<br>";
echo "File name: " . $file_name . "<br>";
//echo "avconv command: " . $command . "<br>";
//echo "avconv return value: " . $return . "<br>";
echo "<br>";
echo "Okay.". "<br>";
ini_set('display_errors', 'On');
error_reporting(E_ALL);
*/
?>

<!DOCTYPE html>
<html>
<head>
    <title><?echo $file_name;?></title>
    <meta charset='utf-8'>
    <style>
    body {
        text-align:center;
    }
    h1,h2,h3 {
        text-align:center;
    }
    p {
        text-align:center;
    }
    a {
        transition: color 2s;
    }
    a:hover {
        color:purple;
    }
    </style>
</head>
<body>
<img style="display:block; margin-top:20px; margin-left:auto;margin-right:auto;" border=0 src="http://www.coopradio.org/sites/default/files/coop_logo.png" alt="">

<h2><?echo $file_name?></h2>

<p>
    Please right click the link below and select the option that says "Save Link as" or "Download as".<br>
    <h3><a href="requested/<?echo $file_name;?>" download="requested/<?echo $file_name;?>">Download here</a></h3>
    <p>Below is an audio link that can be played in the browser. Please note, not all browsers support this capabality yet. It works for Chrome and Safari.</p>
    <audio controls>
        <source src="requested/<?echo $file_name;?>">
        <embed height="50" width="100" src="requested/<?echo $file_name;?>">
    </audio>
</body>
</html>
