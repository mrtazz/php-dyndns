<?php

/* Please enter all domain names without the trailing dot.
   The name of your forward zone */
   $dnsorigin="field.example.net";

/* The admin's mail address */
   $adminmail="admin@example.net";

/* The authorative DNS server for the forward zone */
   $fnameserver=("ns1.example.net", "ns2.example.net");

/* The authorative DNS server for the reverse zone */
   $rnameserver=("ns1.example.net", "ns2.example.net");

/* Enter the database details of your SQLite database */
   $_db = "dns";
   $dbfolder = "/path/to/your/folder";

/* The folder where your zones should be found by the DNS server */
   $zonepath = "/path/to/your/folder";
   $zonefile = "/path/to/dns.zone";
   $revzonefile = "/path/to/dns.rev";

/* The address spaces used for reverse mapping. Please enter them in blocks
   consisting of the net part of the /24 nets */
   $ipnet=("192.168.0", "192.168.1");

/* The path to your sudo binary */
   $sudopath = "/usr/local/bin/sudo";










?>
