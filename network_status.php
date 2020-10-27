<?php
  // I've been running this file as a cron job every 15 minutes.
  // All the nodes that are on the wrong version are at the top of the queue
  // So when it gets down below 100 (line 33) it may be that the problem has been fixed.
  // Change the email addresses on lines 35 & 38

  $apiUrl = 'https://nodeapi.energi.network/';
	$stuck = 0; $active = 0;
  
  $message = json_encode(
        array('jsonrpc' => '2.0', 'id' => 15, 'method' => 'masternode_listMasternodes')
  );
  $requestHeaders = [
        'Content-type: application/json'
  ];

  $ch = curl_init($apiUrl);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $message);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $requestHeaders);
    
  $response = curl_exec($ch);
  curl_close($ch);

  $json = json_decode($response, true);

  foreach($json['result'] as $p) {
      if ($p['IsActive'] == '0') { $stuck++; }
      else { break; }
  }
    
  echo '<p>There are currently '.$stuck.' nodes stuck on the network';
  if ($stuck < 100) {
      $to = "youremail@goeshere.com";
      $subject = "Energi Network Moving";
      $txt = "There are currently less than 100 nodes stuck so network might be working";
      $headers = "From: youremail@goeshere.com" . "\r\n";
      mail($to,$subject,$txt,$headers);
  }
?>
