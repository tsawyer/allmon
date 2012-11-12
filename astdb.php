#! /usr/bin/php -q
<?php

// Get allstar database
$url = "https://allstarlink.org/cgi-bin/allmondb.pl";
$contents = '';
$fh = fopen($url, 'r');
while (!feof($fh)) {
  $contents .= fread($fh, 8192);
}
fclose($fh);

// Save the data
$db = "astdb.txt";
if (! $fh = fopen($db, 'w')) {
    die("Cannot open $db.");
}
if (!flock($fh, LOCK_EX))  {
    echo 'Unable to obtain lock.';
    exit(-1); 
}
if (fwrite($fh, $contents) === FALSE) {
    die ("Cannot write $db.");
}
fclose($fh);
?>
