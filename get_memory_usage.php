<?php // get_memory_usage.php 
/*
  Get free stats 100 times a second for the length of a Caffe mnist run.
  The automatic sleep_value correction becomes stable here at around 1
  second and so start the Caffe run at least a second after starting
  this program.
*/
  if ($argc != 2) {
    echo "php get_memory_usage.php number_of_seconds\n";
    exit;
  }

  $deciseconds = $argv[1] * 100; // $argv[1] is number of seconds to run.

  $target_interval = .01; // seconds
  $time_vector_length = 5;
  $time_vector = array();
  $sleep_value = 5000; // .05 seconds.
  $log = '';
  for($j=0;$j<$deciseconds;++$j) {
    $time_vector[] = microtime(true); // bool $get_as_float
    $log .= end($time_vector); // Use the last, most recent time in the vector.
    $log .= shell_exec('free');
    $log .= memory_get_usage()."\n";

    // memory_get_usage Returns the amount of memory, in bytes, that's currently being allocated to this PHP script.
    // http://php.net/manual/en/function.memory-get-usage.php

    // Use a running average with a gradual correction to converge to
    // .01 second intervals.
    if (count($time_vector) > $time_vector_length) {
      $sum = 0;
      for($k=1;$k<count($time_vector);++$k)
        $sum += $time_vector[$k]-$time_vector[$k-1];
      array_shift($time_vector); // pop the first value off.
      $avg = $sum / count($time_vector);
      $sleep_value += intval(($target_interval - $avg)*250000); // (1000000*.25);
    }

    // echo "sleep_value=$sleep_value\n";

    if ($sleep_value > 0)
      usleep($sleep_value);
  }

  // Write memory log to disk.
  file_put_contents('test/get_memory_usage.log',$log);
?>

