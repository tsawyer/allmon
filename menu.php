<div id="menu">
<?php
$current_url = urldecode(array_pop(explode('/', $_SERVER['REQUEST_URI'])));

// Read allmon INI file
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
    if (@$data['menu'] == 1) {
        $items[$i]['node']=$n;
        $items[$i]['url'] = "link.php?node=$n";
        $i++;
    }
}

// Add Voter(s) if any
if (file_exists('voter.ini')) {
    $arr = parse_ini_file('voter.ini', true);
    #print "<pre>"; print_r($arr); print "</pre>";

    foreach($arr as $name => $data) {
        $items[$i]['node'] = $name;
        $items[$i]['url'] = "voter.php?node={$data['node']}";
        $i++;
    }
}

// Add in groups if any
if (file_exists('groups.ini')) {
    $arr = parse_ini_file('groups.ini', true);
    #print "<pre>"; print_r($arr); print "</pre>";

    foreach($arr as $name => $data) {
        $items[$i]['node'] = $name;
        $items[$i]['url'] = "link.php?group=$name";
        $i++;
    }
}

// Add custom items to menu
foreach($config as $n => $data) {
    if (!empty($data['url'])) {
        $items[$i]['node']=$n;
        $items[$i]['url'] = $data['url'];
        $i++;
    }
}
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
<!-- Login opener -->
<li><a href="#" id="loginlink">Login</a></li>
<li><a href="#" id="logoutlink">Logout</a></li>

</ul>
</div>
<div class="clearer"></div>

<!-- Login form -->
<div id="login" caption="Login">
<div>
<form method="post" action="">
<table>
<tr><td>Username:</td><td><input style="width: 150px;" type="text" name="user"></td></tr>
<tr><td>Password:</td><td><input style="width: 150px;" type="password" name="password"></td></tr>
</table>
</form>
</div>
</div>

<!-- Command response area -->
<div id="test_area"></div>
<?php #print "<pre>data: "; print_r($current_url); print "</pre>"; ?>