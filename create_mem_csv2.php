<?php // create_mem_csv2.php
/*
*/
  $csv_filename = 'test/mem_info.csv';
  $csv_header_written = false;
  $start_seconds = 0;
  $start_swap = 0;

  $meminfo = file_get_contents('test/mem_info.log');
  $lines = explode("\n",$meminfo);
  unset($meminfo);

  // echo 'count($lines)='.count($lines)."\n";

  // Break log lines into individual observations.
  $observation = array();
  // $cycles = 0;
  foreach($lines as $line) {
    // if (++$cycles > 50) exit;
    $line = trim($line);
    if (!strlen($line))
      continue;
    if (strpos($line, 'microtime') !== false
    && count($observation)) {
      // var_dump($observation);
      get_meminfo_fields($observation);
      unset($observation);
      $observation = array();
    }
    // var_dump($csv_header_written);
    $observation[] = $line;
  }

  if (count($observation))
    get_meminfo_fields($observation, $csv_header_written);

function get_meminfo_fields($observation_lines) {
  global $csv_filename, $csv_header_written, $start_seconds,
    $start_swap;

  // echo "begin of get_meminfo_fields\n";
  // var_dump($csv_header_written);

  $header = array();
  if (!$csv_header_written) {
    foreach($observation_lines as $line) {
      $fields = preg_split('/\s+/', trim($line));
      if ($fields[0] == 'microtime') {
        $col_name = 'seconds'; // first field
        $start_seconds = floatval($fields[1]);
      }
      else
        $col_name = trim($fields[0],':');
      if ($col_name == 'SwapFree')
        $start_swap = intval($fields[1]);

      if (!in_array($col_name,$header))
        $header[] = $col_name;
      else
        break;
    }
    $header[] = 'final_free';
    $header[] = 'swap';
    $output = implode(',',$header)."\n";
    // echo "output=$output\n";
    file_put_contents($csv_filename,$output);
    unset($header);
    $csv_header_written = true;
  }

  $output = '';
  $final_free = 0;
  $swap = 0;
  foreach($observation_lines as $line) {
    $fields = preg_split('/\s+/', trim($line));
    if ($fields[0] == 'microtime') {
      $output .= ','.(floatval($fields[1])-$start_seconds);
      // echo 'microtime - $fields[1]='.$fields[1]." start_seconds=$start_seconds output=$output\n";
    }
    else if ($fields[0] == 'program_usage')
      $output .= ','.($fields[1]/1024);
    else if (count($fields) == 3 && $fields[2] == 'kB')
      $output .= ','.$fields[1];
    else if (intval($fields[1]) == 0)
      $output .= ','.$fields[1];
    else if (count($fields) == 3) { // Unexpected if not 'kB' > 0.
      echo 'found '.$fields[2]."\n";
      $output .= ',?';
    }
    else {
      echo 'found '.$line."\n";
      $output .= ',?';
    }
    $col_name = trim($fields[0],':');
    // echo "col_name=$col_name\n";


    // if ($col_name == 'MemTotal')
    //   $final_free += $fields[1];
    // else
    if ($col_name == 'MemFree') {
      $final_free += $fields[1];
      // echo "final_free=$final_free ".'fields[1]='.$fields[1].' fields[0]='.$fields[0]."\n";
    }
    else if ($col_name == 'Buffers'
    || $col_name == 'Cached') {
      $final_free += $fields[1];
      // echo "final_free=$final_free ".'fields[1]='.$fields[1].' fields[0]='.$fields[0]."\n";
    }
    else if ($col_name == 'program_usage') {
      $final_free += $fields[1]/1024;
      // echo "final_free=$final_free ".'fields[1]='.$fields[1].' fields[0]='.$fields[0]."\n";
    }
    else if ($col_name == 'SwapFree') {
      $swap = $start_swap - intval($fields[1]);
      // echo "SwapFree swap=$swap start_swap=$start_swap ".'$fields[1]='.$fields[1]."\n";
    }
  }
  // echo "final_free=$final_free\n";
  $output .= ','.($final_free)/1024;
  $output .= ','.$swap/1024;

  $output = ltrim($output,',')."\n";
  // echo "output=$output\n";
  file_put_contents($csv_filename,$output,FILE_APPEND); 
  // echo "end of get_meminfo_fields\n";
  // var_dump($csv_header_written);
}
?>

