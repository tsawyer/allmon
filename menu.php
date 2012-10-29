<div id="menu">
<?php
$current_url = array_pop(explode('/', $_SERVER['REQUEST_URI']));

// Read INI file
if (!file_exists('allmon.ini')) {
    die("Couldn't load ini file.\n");
}
$config = parse_ini_file('allmon.ini', true);
#print "<pre>"; print_r($config); print "</pre>";

if (count($config) == 0) {
    die("Check ini file format.\n");
}

// Make a list of menu items
$items = array();
$i=0;
foreach($config as $n => $data) {
    $items[$i]['node']=$n;
    $items[$i]['url'] = "link.php?$n";
    if ($data['voter']) {
        $i++;
        $items[$i]['node'] = "$n Voter";
        $items[$i]['url'] = "voter.php?$n";
    }
    $i++;
}
$items[$i]['node'] = "About";
$items[$i]['url'] = "about.php";

#print "<pre>"; print_r($items); print "</pre>";
?>
<ul>
<?php 
foreach ($items as $item) {
    if($current_url == $item['url']) {
        print "<li><a class=\"active\" href=\"" . $item['url'] .  "\">" . $item['node'] . "</a></li>\n";
    } else {
        print "<li><a href=\"" . $item['url'] .  "\">" . $item['node'] . "</a></li>\n";
    }
    
}
?>
</ul>
</div>
<div class="clearer"></div>
