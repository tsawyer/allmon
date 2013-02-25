<?php
include('allmon.inc.php');

// Filter and validate user input
$node = @trim(strip_tags($_POST['node']));
$perm = @trim(strip_tags($_POST['perm']));
$button = @trim(strip_tags($_POST['button']));
$localnode = @trim(strip_tags($_POST['localnode']));

if (! preg_match("/^\d+$/",$node)) {
    die("Please provide node number to connect.\n");
}
if (! preg_match("/^\d+$/",$localnode)) {
    die("Please provide local node number.\n");
}

// Read configuration file
if (!file_exists('allmon.ini')) {
    die("Couldn't load ini file.\n");
}
$config = parse_ini_file('allmon.ini', true);
#print "<pre>"; print_r($config); print "</pre>";

// Open a socket to Asterisk Manager
$fp = connect($config[$localnode]['host']);
login($fp, $config[$localnode]['user'], $config[$localnode]['passwd']);

// Which ilink command?
if ($button == 'connect') {
    if ($perm == 'on') {
        $ilink = 13;
        print "<b>Permanent Connecting $node to $localnode</b>";
    } else {
        $ilink = 3;
        print "<b>Connecting $node to $localnode</b>";
    }
} elseif ($button == 'monitor') {
    if ($perm == 'on') {
        $ilink = 12;
        print "<b>Permanent Monitoring $node from $localnode</b>";
    } else {
        $ilink = 2;
        print "<b>Monitoring $node from $localnode</b>";
    }
}

// Do it
if ((@fwrite($fp,"ACTION: COMMAND\r\nCOMMAND: rpt cmd $localnode ilink $ilink $node\r\n\r\n")) > 0 ) {
    // Get response, but do nothing with it
    $rptStatus = get_response($fp);
    #print "<pre>===== start =====\n";
    #print_r($rptStatus);            
    #print "===== end =====\n</pre>";
} else {
    die("Command failed!\n");
}

?>