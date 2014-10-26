<?php

namespace Aztech\Events\Bus\Plugins\Redis;

use Predis\Client;
use Aztech\Events\Bus\Channel\AcknowledgeableChannelReader;
use Aztech\Events\Bus\Channel\Message;

class RedisChannelReader implements AcknowledgeableChannelReader
{

    private $client;

    private $key = null;

    private $processingKey = null;

    public function __construct(Client $redisClient, $eventKey, $processingKey = null)
    {
        if (empty($eventKey)) {
            throw new \InvalidArgumentException('Event key must be provided.');
        }

        $this->client = $redisClient;
        $this->key = $eventKey;
        $this->processingKey = $processingKey;
    }

    public function read()
    {
        return $this->client->rpop(array(
            $this->key
        ), 0);
    }

    public function readAck()
    {
        if (! empty($this->processingKey)) {
            $data = $this->client->rpoplpush($this->key, $this->processingKey);
            $message = new Message($data, $data);
        }
        else {
            $data = $this->read();
            $message = new Message("", $data);
        }

        return $message;
    }

    public function acknowledge(Message $ack)
    {
        if ($ack->getCorrelationData() == "") {
            return false;
        }

        return $this->client->lpop($this->processingKey, 0, $ack->getCorrelationData()) === 1;
    }

    public function dispose()
    {
        while ($this->client->rpoplpush($this->processingKey, $this->key)) {
            continue;
        }
    }
}
