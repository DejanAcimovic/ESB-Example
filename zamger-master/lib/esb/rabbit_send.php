<?php

require_once __DIR__ . '../../../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

function sendMessage($object, $queueName){
    
    require("lib/config.php");
    echo $conf_mqhost . " " . $conf_mqport . " " . $conf_mquser . " " . $conf_mqpassword . "\n";
    $connection = new AMQPStreamConnection($conf_mqhost, $conf_mqport, $conf_mquser, $conf_mqpassword);
    $channel = $connection->channel();
    
    $channel->queue_declare($queueName, false, true, false, false);    

    $msg = new AMQPMessage(json_encode($object));
    echo "xxxxx" . $msg->body . "\n";
    $channel->basic_publish($msg, '', $queueName);
    
    echo " [x] Sent " . $message . "\n";
    
    $channel->close();
    $connection->close();
}
?>