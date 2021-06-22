<?php

require_once __DIR__ . '../../../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

require_once("../config.php");
require_once("../dblayer.php");
echo $conf_mqhost . " " . $conf_mqport . " " . $conf_mquser . " " . $conf_mqpassword . "\n";
$connection = new AMQPStreamConnection($conf_mqhost, $conf_mqport, $conf_mquser, $conf_mqpassword);
$channel = $connection->channel();

$channel->queue_declare('personReplyQueue', false, true, false, false);

echo " [*] Waiting for messages. To exit press";

$callback = function ($msg) {
    $osoba = json_decode($msg->body);
    echo ' [x] Received ', $osoba->Name, " ", $osoba->Surname, "\n";
};

$channel->basic_consume('personReplyQueue', '', false, true, false, false, $callback);

while ($channel->is_open()) {
    $channel->wait();
}

$channel->close();
$connection->close();
?>