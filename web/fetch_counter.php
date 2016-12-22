<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 1);
$m = new MongoClient(); // connect
$db = $m->selectDB("merits");
$collection = new MongoCollection($db, 'sessions');
$cursor = $collection->find();
$merits = 0;
foreach ($cursor as $doc) {
    $merits = $merits + $doc["merits_earned"];
}

$response = array('merits'=> floor($merits), 'cadence'=> $doc["cadence"]);
echo json_encode($response);

?>
