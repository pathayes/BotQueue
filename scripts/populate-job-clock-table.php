<?
  require("../extensions/global.php");
  $start_time = microtime(true);
  
  //delete our dev db.
  db()->execute("TRUNCATE TABLE job_clock");
  
  $rs = db()->query("SELECT * FROM jobs");
  while ($row = $rs->fetch_assoc())
  {
    $log = new JobClockEntry();
    $log->set('job_id', $row['id']);
    $log->set('bot_id', $row['bot_id']);
    $log->set('user_id', $row['user_id']);
    $log->set('queue_id', $row['queue_id']);
    
    if ($row['status'] == 'complete' || $row['status'] == 'failure' || $row['status'] == 'qa')
    {
      $log->set('start_date', $row['taken_time']);
      $log->set('end_date', $row['finished_time']);
      $log->set('status', 'complete');
      $log->save();
    }
    else if ($row['status'] == 'taken' || $row['status'] == 'slicing')
    {
      $log->set('start_date', $row['taken_time']);
      $log->set('status', 'working');
      $log->save();
    }
  }
  
  //finished!!!!
  echo "\nPopulated job clock log in " . round((microtime(true) - $start_time), 2) . " seconds.\n";
?>