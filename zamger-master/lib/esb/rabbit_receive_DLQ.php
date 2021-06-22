<?php

require_once __DIR__ . '../../../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Wire\AMQPTable;
use PhpAmqpLib\Exchange\AMQPExchangeType;

require_once("lib/config.php");
require("lib/dblayer.php");
$connection = new AMQPStreamConnection($conf_mqhost, $conf_mqport, $conf_mquser, $conf_mqpassword);
$channel = $connection->channel();

$channel->queue_declare('deadLetterQueue', false, true, false, false);
$channel->queue_declare('parkingLotQueue', false, true, false, false);
echo " [*] Waiting for messages. To exit press";

$callback = function ($msg) {
    $retry_count = $msg->get('application_headers')["x-reties-count"];
    if(is_null($retry_count)){
        $retry_count = 1;
    }

    if($retry_count > 5) {
        echo "Discard message";
        $msg->getChannel()->basic_publish($msg, "demo.parkingLot.exchange", "demo.parkingLot.binding");
    }
    else {

        $headers = new AMQPTable(array(
            "x-reties-count" => $retry_count + 1
        ));
        echo "Retry:" . $retry_count;
        $msg->set('application_headers', $headers);
        echo $msg->get('application_headers')["x-reties-count"];
        $msg->getChannel()->basic_publish($msg, "demo.errorMaking.exchange", "demo.errorMaking.binding");
    }
};

$channel->basic_consume('deadLetterQueue', '', false, true, false, false, $callback);

while ($channel->is_open()) {
    $channel->wait();
}

$channel->close();
$connection->close();
?>