<?php
    $apiUrl = 'https://nodeapi.energi.network/';
    $myAddress = strtolower("YOUR ENERGI ADDRESS");
    $rank = 1;
    $collateral = 0;

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
        if ($p['Owner'] != $myAddress) {
            $col = ( hexdec($p['Collateral']) / 1000000000000000000 );
            if ($p['IsActive'] == '1') { $collateral = $collateral + $col; }
            $rank++;
        }
        else {
            $my_collateral = ( hexdec($p['Collateral']) / 1000000000000000000 );
            $total_above = $collateral;
            $my_rank = $rank;
	}
    }

    $date = date("Y-m-d H:i");
    $minutes_to_add = floor($total_above / 10000);
    $time = new DateTime($date);
    $time->add(new DateInterval('PT' . $minutes_to_add . 'M'));
    $next_reward = $time->format('l jS M Y @ H:i');
    $frequency = ($collateral / 10000);
    $hours = floor($frequency / 60);
    $minutes = floor($frequency % 60);
    $frequency = str_pad($hours, 2, "0", STR_PAD_LEFT) . " hours " . str_pad($minutes, 2, "0", STR_PAD_LEFT) . " minutes";
    $active_collateral = $collateral + $my_collateral;

    echo "<p>My Rank: ".$my_rank."</p>";
    echo "<p>My Collateral: ".number_format($my_collateral,0)."</p>";
    echo "<p>Next Reward: ".$next_reward."</p>";
    echo "<p>Next Payout: ".(0.914 * ($my_collateral / 1000))." NRG</p>";
    echo "<p>Active Collateral: ".number_format($active_collateral,0)."</p>";
    echo "<p>Reward Frequency: ".$frequency."</p>";
?>
