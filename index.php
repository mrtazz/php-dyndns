<html>
<head>
 <title>Megabit 2006 Field DNS</title>
 <link rel="stylesheet" type="text/css" href="style.css"/>
</head>
<body>

<div id=header></div>

<?php
  require("include/db.func.inc.php");
  require("include/func.inc.php");
  require("include/config.inc.php");

  $dbfilepath = $dbfolder."/dns.sqlite";
  $realdbfilepath = $dbfolder."/dnsreal.sqlite";
  $db = openDb($dbfilepath);
  $realdb = openDb($realdbfilepath);
  $_zonefile = $dbfolder."/dns.zone";
  $_realzonefile = $dbfolder."/dnsreal.zone";

if (isset($_POST['hostname'])) {
  $_POST['hostname'] = trim($_POST['hostname']);
  $_POST['hostname'] = substr($_POST['hostname'], 0, 254);
}
if (isset($_POST['ip'])) {
  $_POST['ip'] = trim($_POST['ip']);
}

if (empty($_POST)) {
  echo("<div class=formular>");
  echo("<b>Please enter your designated hostname:</b></br>");
  echo("<form action=\"index.php\" method=\"POST\">");
  echo("<table class=formular border=0 cellpadding=0 cellspacing=0><tr>");
  echo("<td>IP-Address:</td><td><input name=\"ip\" type=\"text\" value=\"".$_SERVER['REMOTE_ADDR']."\" size=13></td>");
  echo("</tr><tr>");
  echo("<td>Hostname:</td><td><input name=\"hostname\" type=\"text\" size=13/>.".$dnsorigin."</tr>");
  echo("</tr></table>");
  echo("<input type=\"submit\" value=\"Submit\"/>");
  echo("</form>");
  echo("<a href=\"listhostnames.php\">List registered hostnames</a><br>");
  echo("contact: <a href=\"mailto:".$adminmail."\">".$adminmail."</a><br>");
  echo("</div>");
} else {
  $hostname_sqlite = sqlite_escape_string($_POST['hostname']);
  $hostname_html = htmlspecialchars($_POST['hostname']);
  $ip_sqlite = sqlite_escape_string($_POST['ip']);
  $ip_html = htmlspecialchars($_POST['ip']);

  if (!isset($_POST['hostname'])) {
    echo "Please enter a valid hostname";
  } else {
    $iparray=explode(".", $_POST['ip']);
    $iperror=false;
    if ($iparray[0] != 84) {
      $iperror = true;
    }
    if ($iparray[1] != 38) {
      $iperror = true;
    }
    if (($iparray[2] < 225) || ($iparray[2] > 231)) {
      $iperror = true;
    }
    if (($iparray[3] < 1) || ($iparray[3] > 254)) {
      $iperror = true;
    }
    if ($iperror == true) {
      echo("<div class=formular>Invalid IP address, please try a valid one.<br>");
      echo("<a href=\"javascript:history.back();\">back</a></div>");
      exit;
    }

    $pattern='/^[a-zA-Z0-9\-]+$/i';
    $matches = preg_match($pattern, $_POST['hostname']);
    if ($matches == 0) {
      echo("<div class=formular>Invalid hostnames, please try another one.<br>");
      echo("<a href=\"javascript:history.back();\">back</a></div>");
      exit;
    }


    $table['hostname'] = $hostname_sqlite;
    $table['ip'] = $ip_sqlite;
    $rowsAffected = insertInDb($db, $_dns, $table);
    $serialquery = "SELECT MAX(id) FROM ".$_db;
    $serial = sqlite_array_query($db, $serialquery);
    $serial = $serial[0]['MAX(id)'];

    if ($rowsAffected != 1) {
      echo("<div class=formular>Hostname ".$hostname_html." already used.");
      echo("<a href=\"javascript:history.back();\">back</a></div>");
      exit;
    } 

    writeZoneFile($db, $_zonefile, $serial, $dnsorigin, $adminmail, $fnameserver);
    $cmd = "'named-checkzone -k fail -n ".$dnsorigin." dns.zone'";
    exec($cmd, $krempel, $exitCode);
    if ($exitCode == 0){
      $cmd = "'cp ".$dbfilepath." ".$realdbfilepath."'";
      exec($cmd);
      writeZoneFile($realdb, $_realzonefile,$serial);

      echo("<div class=formular>Congratulations!</br> ".$hostname_html." with the IP ".$ip_html." is now one of the cool guys.</div>");
    } else {
      echo("<div class=formular>An error occurred while adding ".$hostname_html." to the zone.</div>");
    }

    foreach ($ipnet as $revip) {
       $ip = explode(".", $revip);
       $revzonefile="dnsreal".$ip[2].".rev";
       writeReverseZoneFile($db, $revzonefile, $serial, $dnsorigin, $adminmail, $rnameserver, $revip, $ip[0], $ip[1]);
       $destination = $zonepath."/".$ip[2].$ip[1].ip[0].".in-addr.arpa.rev";
       $cmd = "'".$sudopath." cp ".$revzonefile." ".$destination."'";
       exec($cmd);
    }

    $cmd = "'cp ".$realdbfilepath." ".$dbfilepath."'";
    exec($cmd);
    $destination = $zonepath."/".$dnsorigin.".zone";
    $cmd = "'".$sudopath." cp ".$_realzonefile." ".$destination."'";
    exec($cmd);
    /* INSERT BIND RELOAD SCRIPT HERE */
  }
}

?>
</body>
</html>
