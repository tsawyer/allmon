<?php 
include "header.php";

$node = array_pop(explode('?', $_SERVER['REQUEST_URI']));
$nodeURL = "http://stats.allstarlink.org/nodeinfo.cgi?node=$node";

// If no node number use first non-voter in INI
if (!preg_match("/^\d+$/", $node)) {
    die ("Please provide node number. (ie link.php?1234)");
}

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

if (array_key_exists($node, $astdb)) {
    $nodeRow = $astdb[$node];
    $info = $nodeRow[4] . ' ' . $nodeRow[5] . ' ' . $nodeRow[6];
}

?>
<script type="text/javascript">
    // prevent IE caching
    $.ajaxSetup ({  
        cache: false  
    });    

    // when DOM is ready
    $(document).ready(function() {
        var ajax_request;
        
        // Ajax display
        function updateServer( ) {
            if(typeof ajax_request !== 'undefined') {
                ajax_request.abort();
            }
            ajax_request = $.ajax( { url:'server.php', data: { 'node' :  <?php echo $node; ?>}, type:'get', success: function(result) {
                    $('#link_list').html(result);
                }
            });
        }
        
        // Go and repeat every 1 second.
        updateServer();
        setInterval(updateServer, 800);

    });
</script>
<h2>
<?php 
    if (empty($info)) {
        print "Node <a href='$nodeURL' target='_blank'>$node</a>"; 
    } else {
        print "Node <a href='$nodeURL' target='_blank'>$node</a> $info";
    }
?>
</h2>

<!-- Login form -->
<div id="login" caption="Login">
    <div>
        <form method="post" action="">
            <table>
                <tr>
                    <td>Username:</td>
                    <td><input style="width: 150px;" type="text" name="user"></td>
                </tr>
                <tr>
                    <td>Password:</td>
                    <td><input style="width: 150px;" type="password" name="password"></td>
                </tr>
            </table>
        </form>
    </div>
</div>

<!-- Login opener -->
<a href="#" id="loginlink">Login</a>

<a href="#" id="logoutlink">Logout</a>

<!-- Connect form -->
<div id="connect_form">
    <input type="text" id="node">
    Permanent <input type="checkbox">
    <input type="hidden" id="localnode" value="<?php echo $node; ?>">
    <input type="button" value="Connect" id="connect">
</div>

<!-- Command response area -->
<div id="test_area"></div>

<div id="link_list">Loading node.</div>
<?php include "footer.php"; ?>
