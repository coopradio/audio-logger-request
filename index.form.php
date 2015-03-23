<form action="mp3.php">
Choose date <select name=date>
<?
foreach (range($days,0) as $ago) {
  $date = system ("date -d '$ago days ago' +'%Y%m%d'");
  echo "<option value=\"$date\"";
  if ($ago == 0) {
    echo " selected";
    $nice = "today";
  } elseif ($ago == 1) {
    $nice = "yesterday";
  } else {
    $nice = system ("date -d '$ago days ago' +'%a %b %d'");
  }
  echo ">$nice</option>\n";
}
?></select><br>
Use 24-hour times:<br>11am = 11:00:00<br>1pm = 13:00:00<br>7pm = 19:00:00<br>
(The last two second digits are optional)<br>
Choose start time <input type=text name=start value="19:00:00" size=7><br>
Choose stop time <input type=text name=end value="19:30:00" size=7><br>
<input type=hidden name=kbps value=<? echo $kbps; ?>>
<input type=submit name=download value=Download>
<!--
<input type=submit name=stream value="stream m3u"><br>
-->
<? if (++$asdfasdf==1) { ?><br>
<? } ?></form>
