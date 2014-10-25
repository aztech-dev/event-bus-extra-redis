<?php

namespace Aztech\Events\Bus\Plugins\Redis;

use Aztech\Events\Bus\Channel\ChannelProvider;
use Aztech\Events\Bus\Channel\ReadWriteChannel;
use Predis\Client;

class RedisChannelProvider implements ChannelProvider
{

    public function createChannel(array $options = array())
    {
        $redisClient = new Client($options);
        $redisClient->connect();

        if (isset($options['password']) && ! empty($options['password'])) {
            $redisClient->auth($options['password']);
        }

        $eventKey = $options['event-key'] . ':queue';
        $processingKey = $options['event-key'] . ':processing';

        $reader = new RedisChannelReader($redisClient, $eventKey, $processingKey);
        $writer = new RedisChannelWriter($redisClient, $eventKey, $processingKey);

        return new ReadWriteChannel($reader, $writer);
    }
}
