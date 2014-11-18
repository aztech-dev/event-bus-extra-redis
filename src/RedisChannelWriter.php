<?php

namespace Aztech\Events\Bus\Plugins\Redis;

use Aztech\Events\Event;
use Aztech\Events\Bus\Channel\ChannelWriter;
use Predis\Client;

class RedisChannelWriter implements ChannelWriter
{

    private $client;

    private $key = null;

    /**
     * @param string $eventKey
     */
    public function __construct(Client $redisClient, $eventKey)
    {
        if (empty($eventKey)) {
            throw new \InvalidArgumentException('Event key must be provided.');
        }

        $this->client = $redisClient;
        $this->key = $eventKey;
    }

    /**
     * (non-PHPdoc)
     * @see \Aztech\Events\Bus\Channel\ChannelWriter::write()
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function write(Event $event, $serializedEvent)
    {
        $this->client->rpush($this->key, $serializedEvent);
    }

    /**
     * (non-PHPdoc)
     * @see \Aztech\Events\Bus\Channel\ChannelWriter::dispose()
     * @codeCoverageIgnore function is null-op
     */
    public function dispose()
    { }
}
