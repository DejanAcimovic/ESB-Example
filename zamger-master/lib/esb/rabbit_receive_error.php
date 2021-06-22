<?php

require_once __DIR__ . '../../../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Wire\AMQPTable;

require("lib/config.php");
require("lib/dblayer.php");
$connection = new AMQPStreamConnection($conf_mqhost, $conf_mqport, $conf_mquser, $conf_mqpassword);
$channel = $connection->channel();

$channel->queue_declare('errorHandlingQueue', false, true, false, false, false, new AMQPTable(array(
    'x-dead-letter-exchange' => 'demo.errorHandling.exchange',
    'x-dead-letter-routing-key' => 'demo.errorHandling.binding'
 )));
echo " [*] Waiting for messages. To exit press";

$callback = function ($msg) {
    try {
        echo 'Primljena poruka';
        throw new Exception("Error while proccessing message");
    } catch (Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";   
        $msg->nack();
    } 
};

$channel->basic_consume('errorHandlingQueue', '', false, false, false, false, $callback);

while ($channel->is_open()) {
    $channel->wait();
}

$channel->close();
$connection->close();
?>