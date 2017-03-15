<?php
// check input values
if (isset($_GET[start]) and isset($_GET[end])) {
 $start = $_GET[start];
 $end = $_GET[end];
 // query sum of dtx bytes and drx bytes since the given start time until the given end time
 echo shell_exec("sqlite3 /var/mikrotik-traffic.sqlite \".header on\" \".mode csv\" \"\
 select t1.timestamp as date,sum(t2.dtx/1024/1024) as 'TX',sum(t2.drx/1024/1024) as 'RX' \
 from traffic t1, traffic t2 \
 where t2.timestamp <= t1.timestamp and t1.timestamp > strftime('%s', '$start') and t1.timestamp < strftime('%s', '$end') \
  and t2.timestamp > strftime('%s', '$start') and t2.timestamp < strftime('%s', '$end')
 group by t1.timestamp
 ;\"");
} else {
 // query sum of dtx bytes and drx bytes
 echo shell_exec("sqlite3 /var/mikrotik-traffic.sqlite \".header on\" \".mode csv\" \"\
 select timestamp as date,dtx/1024 as 'TX',drx/1024 as 'RX' \
 from traffic \
 where dtx <> tx and drx <> rx
 ;\"");
}
/* to show nun-cumulative values use: select dtx, drx ... where dtx <> tx and drx <> rx */
?>
