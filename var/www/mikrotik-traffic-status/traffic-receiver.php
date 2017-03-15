<?php
// check input data
if (isset($_GET[tx]) and isset($_GET[rx]) and isset($_GET[ip])) {
        $tx = $_GET[tx];
        $rx = $_GET[rx];
        $ip = $_GET[ip];
        $ts = time();
        $tx = str_replace(' ', '', $tx);
        $rx = str_replace(' ', '', $rx);
        file_put_contents("/var/mikrotik-ip.txt", $ip);
        // use sqlite3 to store values
        // schema: create table traffic(timestamp int, tx int, rx int, dtx int, drx int)
        // store timestamp, tx, rx and calculate the dtx and drx differences to the last values
        // after a reboot of the router, dtx is set to tx, drx is set to rx
        shell_exec("sqlite3 /var/mikrotik-traffic.sqlite \
        \"with lastval as (select tx,rx from traffic where timestamp = (select max(timestamp) from traffic)) \
         insert into traffic values ($ts, $tx, $rx, \
          case when $rx >= (select rx from lastval) then $tx - (select tx from lastval) else $tx end, \
          case when $rx >= (select rx from lastval) then $rx - (select rx from lastval) else $rx end) \
        \"");
} else {
        exit;
}

?>
