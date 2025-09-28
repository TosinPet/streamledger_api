<?php

return [
    'brokers' => env('KAFKA_BROKERS', 'localhost:9092'),
    'producer' => [
        'topic' => env('KAFKA_DEFAULT_PRODUCER_TOPIC', 'transactions'),
    ],
    'consumer' => [
        'group_id' => env('KAFKA_DEFAULT_CONSUMER_GROUP', 'laravel'),
    ],
];
