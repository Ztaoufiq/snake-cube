<?php

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;

$host = 'rat-01.rmq2.cloudamqp.com';
$port = 5672;
$user = 'rgigcmhd';
$pass = 'cdlYCEMQg7-TwPxUAiPONXCvxhExSLSJ';
$vhost = 'rgigcmhd';

$exchange = 'subscribers';
$queue = 'Zouheir_subscribersss';

$connection = new AMQPStreamConnection($host, $port, $user, $pass, $vhost);
$channel = $connection->channel();
$channel->queue_declare($queue, false, true, false, false);
$consumerTag = 'local.imac.consumer';
$channel->exchange_declare($exchange, AMQPExchangeType::DIRECT, false, true, false);

$channel->queue_bind($queue, $exchange);

function process_message(AMQPMessage $message)
{
    $messageBody = json_decode($message->body);

    $email = $messageBody->email;
    file_put_contents(dirname(__DIR__) . '/data/' . $email . '.json', $messageBody);

    $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);

}

$channel->basic_consume($queue, $consumerTag, false, false, false, false, 'process_message');

function shutdown($channel, $connection)
{
    $channel->close();
    $connection->close();
}

register_shutdown_function('shutdown', $channel, $connection);

// Loop as long as the channel has callbacks registered
while ($channel->is_consuming()) {
    $channel->wait();
}