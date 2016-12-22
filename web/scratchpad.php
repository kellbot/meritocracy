<?php 

$mongo = new Mongo();
$db = $mongo->selectDB('test');
$coll = $db->selectCollection('sessions');
$cursor = $coll->find()->tailable(true);
while (true) {
    if ($cursor->hasNext()) {
        $doc = $cursor->getNext();
        print_r($doc);
    } else {
        sleep(1);
    }
}
