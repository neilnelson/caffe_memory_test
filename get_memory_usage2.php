<?php // get_memory_usage2.php
/*
  Get free stats every second. Write stats to mem_use mysql table.

  echo "php get_memory_usage2.php number_of_observations\n";
  echo "zero for number_of_observations allow the program to\n";
  echo "continue until swap used goes above 500000 kilobytes,\n";
  echo "just under 0.5 gigabytes.\n";
*/
  $max_observations = 0; // Unlimited observations.
  if ($argc > 1)
    $max_observations = $argv[1];

  $max_swap_used = 500000; // kilobytes
  if ($argc > 2)
    $max_swap_used = $argv[2];

  $target_interval = 10; // seconds
  $time_vector_length = 5;
  $time_vector = array();
  $sleep_value = 10.0; // seconds.
  $converge_rate = 0.25;
  $swap_used = 0;
  $caffe_iterations_pid = 0;

  if (file_exists('test/mem_info.log'))
    system('rm test/mem_info.log');

  for($j=0;
      ($max_observations == 0 || $j < $max_observations)
      && $swap_used < $max_swap_used;
      ++$j) {
    $time_vector[] = microtime(true); // bool $get_as_float
    $log = 'microtime '.end($time_vector)."\n";

    $log .= shell_exec('cat /proc/meminfo');
    $log .= 'program_usage '.memory_get_usage()."\n";
    file_put_contents('test/mem_info.log',$log,FILE_APPEND);

    // Get pid of caffe_iterations.php.
    if (!$caffe_iterations_pid) {
      $pid_line = shell_exec('ps -ef | grep caffe_iterations | grep -v grep');
      if (strlen(trim($pid_line))) {
        $fields = preg_split('/\s+/', trim($pid_line));
        $caffe_iterations_pid = $fields[1];
        echo "caffe_iterations_pid=$caffe_iterations_pid\n";
      }
    }

    $free = trim(shell_exec('free'));
    $lines = explode("\n",$free);
    $fields = preg_split('/\s+/', trim($lines[3]));
    $swap_used = $fields[2];

    if ($swap_used > $max_swap_used) {
      if ($caffe_iterations_pid)
        system("kill -9 $caffe_iterations_pid");
      else
        echo "Kill caffe_iterations before your computer freezes up!\n";
    }
    unset($free);
    unset($lines);
    unset($fields);

    // The following code converges to 10 seconds sleep between observations.
    if (count($time_vector) > $time_vector_length) { // Get past the inital loading of the vector.
      $weighted_sum = 0;
      $k_sum = 0;
      for($k=1;$k<count($time_vector);++$k) {
        $k_sum += $k;
        $weighted_sum += ($time_vector[$k]-$time_vector[$k-1])*$k;
      }
      array_shift($time_vector); // pop the first value off.
      $avg = $weighted_sum / $k_sum;
      $sleep_value += ($target_interval - $avg)*$converge_rate;

      // echo "swap_used=$swap_used sleep_value=$sleep_value\n";

      $sleep_seconds = intval($sleep_value);
      sleep($sleep_seconds);

      $sleep_microseconds
        = intval(($sleep_value - $sleep_seconds)*1000000);
      if ($sleep_microseconds > 0)
        usleep($sleep_microseconds);
    }
    else
      sleep($target_interval);
  }
?>

