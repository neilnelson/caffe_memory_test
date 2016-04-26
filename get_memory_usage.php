<?php // get_memory_usage.php
/*
  Get free stats 100 times a second for the length of a Caffe mnist run.
*/
  $log = '';
  for($j=0;$j<3000;++$j) { // 5000
    $log .= microtime(true); // bool $get_as_float
    $log .= shell_exec('free');
    $log .= memory_get_usage()."\n";

    // memory_get_usage Returns the amount of memory, in bytes, that's currently being allocated to this PHP script.
    // http://php.net/manual/en/function.memory-get-usage.php

    usleep(6964); // Should give about .01 seconds between observations for this computer. This is the remaining about of time in .01 seconds after all the other processing, such as above, is done.
  }

  // Write memory log to disk.
  file_put_contents('test/get_memory_usage.log',$log);
?>

