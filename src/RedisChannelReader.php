<?php

namespace Aztech\Events\Bus\Plugins\Redis;

use Aztech\Events\Bus\Channel\AcknowledgeableChannelReader;
use Aztech\Events\Bus\Channel\Message;
use Predis\Client;

class RedisChannelReader implements AcknowledgeableChannelReader
{

    /**
     *
     * @var Client
     */
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
        return $this->client->rpop($this->key);
    }

    public function readAck()
    {
        if (! empty($this->processingKey)) {
            return $this->getCorrelatedMessage();
        }

        return new Message("", $this->read());
    }

    public function acknowledge(Message $ack)
    {
        if ($ack->getCorrelationData() == "") {
            return false;
        }

        return $this->client->lrem($this->processingKey, 0, $ack->getCorrelationData()) === 1;
    }

    public function dispose()
    {
        while ($this->client->rpoplpush($this->processingKey, $this->key)) {
            continue;
        }
    }

    private function getCorrelatedMessage()
    {
        $data = $this->client->rpoplpush($this->key, $this->processingKey);

        return new Message($data, $data);
    }
}
