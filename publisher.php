<?php


require_once __DIR__ . '/vendor/autoload.php';

$host = 'rat-01.rmq2.cloudamqp.com';
$port = 5672;
$user = 'rgigcmhd';
$pass = 'cdlYCEMQg7-TwPxUAiPONXCvxhExSLSJ';
$vhost = 'rgigcmhd';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;

$exchange = 'subscribers';
$queue = 'Zouheir_subscriberss';

$connection = new AMQPStreamConnection($host, $port, $user, $pass, $vhost);
$channel = $connection->channel();

$channel->queue_declare($queue, false, true, false, false);



$channel->exchange_declare($exchange, AMQPExchangeType::DIRECT, false, true, false);

$channel->queue_bind($queue, $exchange);

$faker = Faker\Factory::create();

$limit = 10;
$iteration = 0;

while ($iteration < $limit) {
    $messageBody = json_encode([
        'name' => $faker->name,
        'email' => $faker->email,
        'address' => $faker->address,
        'subscriber' => true
    ]);
    $message = new AMQPMessage($messageBody, [
        'content_type' => 'application/json',
        'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT
    ]);
    $channel->basic_publish($message, $exchange);
    $iteration++;
}
$channel->close();
$connection->close();
