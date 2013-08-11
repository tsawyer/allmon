<?php 
include "header.php";

// Get Allstar database file
$db = "astdb.txt";
$astdb = array();
if (file_exists($db)) {
    $fh = fopen($db, "r");
    if (flock($fh, LOCK_SH)){
        while(($line = fgets($fh)) !== FALSE) {
            $arr = split("\|", trim($line));
            $astdb[$arr[0]] = $arr;
        }
    }
    flock($fh, LOCK_UN);
    fclose($fh);
}

// get URI
$node = @trim(strip_tags($_GET['node']));
$group = @trim(strip_tags($_GET['group']));
$voter = @trim(strip_tags($_GET['voter']));

if (empty($node) AND empty($group)) {
    die ("Please provide a properly formated URI. (ie link.php?node=1234 | link.php?group=name)");
}

// Read allmon INI file
if (!file_exists('allmon.ini')) {
    die("Couldn't load ini file.\n");
}
$config = parse_ini_file('allmon.ini', true);
#print "<pre>"; print_r($config); print "</pre>";

// Type = group or node for server.php?
if (!empty($group)) {
    $type = 'group';
    $node = $group;
    $heading = $group;
} else {
    $type = 'node';
    $nodeURL = "http://stats.allstarlink.org/nodeinfo.cgi?node=$node";

    // If $node is in Allstar database
    if (array_key_exists($node, $astdb)) {
        $nodeRow = $astdb[$node];
        $info = $nodeRow[1] . ' ' . $nodeRow[2] . ' ' . $nodeRow[3];
        $heading = "Node <a href='$nodeURL' target='_blank'>$node</a> $info";
    } else {
        $heading = "Node <a href='$nodeURL' target='_blank'>$node</a>"; 
    }
}

// Build a list of nodes in the group
$nodes = array();
if (!empty($group)) {
    // Read Groups INI file
    if (!file_exists('groups.ini')) {
        die("Couldn't load group ini file.\n");
    }
    $gconfig = parse_ini_file('groups.ini', true);
    
    $group = $_GET['group'];
    $nodes = split(",", $gconfig[$group]['nodes']);
}
#print_r($nodes); print "$type $node";

?>
<script type="text/javascript">
    // prevent IE caching
    $.ajaxSetup ({  
        cache: false,
        timeout: 3000
    });    

    // when DOM is ready
    $(document).ready(function() {
        var ajax_request;
        
        // Ajax display
        function updateServer( ) {
            if(typeof ajax_request !== 'undefined') {
                ajax_request.abort();
            }
            ajax_request = $.ajax( { url:'server.php', data: { '<?php echo $type; ?>' : '<?php echo $node; ?>'}, type:'get', success: function(result) {
                    $('#link_list').html(result);
                }, complete: updateServer, timeout: 30000
            });
        }
        
        // Ready... set... go.
        updateServer();
    });
</script>
<h2>
<?php echo $heading; ?>
</h2>

<!-- Connect form -->
<div id="connect_form">
<?php 
if (count($nodes) > 0) {
    print "<select id=\"localnode\">";
    foreach ($nodes as $node) {
        print "<option value=\"$node\">$node</option>";
    }
    print "</select>\n";
} else {
    print "<input type=\"hidden\" id=\"localnode\" value=\"$node\">\n";
}
?>
    <input type="text" id="node">
    Permanent <input type="checkbox">
    <input type="button" value="Connect" id="connect">
    <input type="button" value="Monitor" id="monitor">
    <input type="button" value="Local Monitor" id="localmonitor">
</div>

<div id="link_list">Loading...</div>
<?php include "footer.php"; ?>