<?php

function writeZoneFile($db, $file, $serial, $zoneorigin, $adminmail, $nameserver) {

        $mail=explode("@", $adminmail);

        $f = fopen($file,"w");
        fwrite($f, "\$TTL 3D\n");
        fwrite($f, "\$ORIGIN ".$zoneorigin.".\n");
        fwrite($f, "@ \t IN \t SOA \t ".$zoneorigin.". \t ".$mail[0].".".$mail[1].". (\n");
        fclose($f);
        $f = fopen($file, "a");
        fwrite($f,"\t \t \t ");
        fwrite($f, $serial);
        //fwrite($f, $_zoneversion);
        fwrite($f, " \n");
        fwrite($f,"\t \t \t 300 \n");
        fwrite($f,"\t \t \t 150 \n");
        fwrite($f,"\t \t \t 604800 \n");
        fwrite($f,"\t \t \t 300 ) \n");
        fwrite($f,"; \n");
	foreach ($nameserver as $ns) {
	  fwrite($f,"\t \t IN \t NS \t ".$ns.". \n");
	}
        fwrite($f,"; \n");

        $query = "SELECT * FROM ".$_db;
        $result = sqlite_array_query($db, $query, SQLITE_ASSOC);

        foreach ($result as $row) {
          fwrite($f,$row['hostname']);
          fwrite($f,"\t IN \t A ");
          fwrite($f,$row['ip']);
          fwrite($f,"\n");
        }

        fclose($f);
}

function writeReverseZoneFile($db, $file, $serial, $zoneorigin, $adminmail, $nameserver, $subnet, $classa, $classb) {

        $mail=explode("@", $adminmail);

	$f = fopen($file,"w");
        fwrite($f, "\$TTL 3D\n");
        fwrite($f, "\$ORIGIN ".$subnet.".".$classb.".".$classa.".in-addr.arpa.\n");
        fwrite($f, "@ \t IN \t SOA \t ".$subnet.".".$classb.".".$classa.".in-addr.arpa. \t ".$mail[0].".".$mail[1].". (\n");
        fclose($f);
        $f = fopen($file, "a");
        fwrite($f,"\t \t \t ");
        fwrite($f, $serial);
        //fwrite($f, $_zoneversion);
        fwrite($f, " \n");
        fwrite($f,"\t \t \t 300 \n");
        fwrite($f,"\t \t \t 150 \n");
        fwrite($f,"\t \t \t 604800 \n");
        fwrite($f,"\t \t \t 300 ) \n");
        fwrite($f,"; \n");
        
	foreach ($nameserver as $ns) {
	  fwrite($f,"\t \t IN \t NS \t ".$ns.". \n");
	}

        fwrite($f,"; \n");

        $query = "SELECT * FROM ".$db;
        $result = sqlite_array_query($db, $query, SQLITE_ASSOC);

        foreach ($result as $row) {
          $iparray=explode(".", $row['ip']);

          //fwrite($f,$iparray[3].".".$iparray[2]);
          if ($iparray[2] == $subnet) {
            fwrite($f,$iparray[3]);
            fwrite($f,"\t IN \t PTR ");
            fwrite($f,$row['hostname'].".".$zoneorigin.".");
            fwrite($f,"\n");
          }
        }

        fclose($f);

}

?>
