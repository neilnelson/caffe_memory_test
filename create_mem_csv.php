<?php // create_mem_csv.php
/*
  
*/
  // $day = '2016-04-26';
  $run_log_name = 'test/run.log';
  $day = date('Y-m-d',filemtime($run_log_name));

  // $begin_time = '11:53:29.875337'; // from caffe test/run.log.
  $run_log = file_get_contents($run_log_name);
  $run_log_lines = explode("\n",$run_log);
  unset($run_log);

  $begin_time = '';
  foreach($run_log_lines as $line) {
    if (substr($line,0,1) == 'I' && strpos($line,'.cpp:')) {
      $fields = preg_split('/\s+/', trim($line));
      $begin_time = $fields[1];
      break;
    }
  }
  unset($run_log_lines);

  if (!strlen($begin_time)) {
    echo "ERROR: Expected beginning time in test/run.log\n";
    exit;
  }

  $begin_time = explode('.',$begin_time);
  $begin_date_time = $day.' '.$begin_time[0];
  $begin_time_float = strtotime($begin_date_time)
    +$begin_time[1]/1000000;

  $log_txt = file_get_contents('test/get_memory_usage.log');
  $lines = explode("\n",$log_txt);
  unset($log_txt);
  $items = array();
  foreach($lines as $line) {
    if (strpos($line, 'total')) {
      $begin_item = true;
      if (isset($log_item) && count($log_item))
        $items[] = $log_item;
      unset($log_item);
    }
    $log_item[] = $line;
  }
  if (isset($log_item) && count($log_item)) {
    $items[] = $log_item;
    unset($log_item);
  }
  unset($lines);
/*
1461693208.8298             total       used       free     shared    buffers     cached
Mem:      16421400    3965544   12455856      19312     623912    1482968
-/+ buffers/cache:    1858664   14562736
Swap:     32767996          0   32767996
226504
*/
  $output = "seconds,mem_used\n";
  foreach($items as $item) {
    $fields = preg_split('/\s+/', trim($item[0]));
    $seconds = floatval($fields[0])-$begin_time_float;
    $fields = preg_split('/\s+/', trim($item[2]));
    $mem_used = intval($fields[2]);
    $fields = preg_split('/\s+/', trim($item[4]));
    // Convert program used bytes to kilobytes.
    $mem_used -= (intval($fields[0]) / 1024);
    // Convert total kilobytes to megabytes for easier chart read.
    $mem_used /= 1024;
    $output .= $seconds.','.$mem_used."\n"; 
  }

  file_put_contents('test/mem_used.csv', $output);
?>

