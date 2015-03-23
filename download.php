<?
$start = ereg_replace ("[^0-9]",".",$_GET['start']);
$end = ereg_replace ("[^0-9]",".",$_GET['end']);
$date = ereg_replace ("[^0-9]","",$_GET['date']);
$kbps = ereg_replace ("[^0-9]","",$_GET['kbps']);

if ($_GET['stream'] == "stream") {
  header ("Status: 302 Moved temporarily");
  header ("Location: /mp3/${kbps}/$date.$start-$end.pls");
} elseif ($_GET['stream'] == "stream m3u") {
  header ("Status: 302 Moved temporarily");
  header ("Location: /mp3/${kbps}/$date.$start-$end.m3u");
} else {
  header ("Status: 302 Moved temporarily");
  header ("Location: /mp3/${kbps}/$date.$start-$end.mp3");
}
?>
