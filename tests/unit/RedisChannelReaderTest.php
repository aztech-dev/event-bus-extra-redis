<?php

namespace Aztech\Events\Bus\Plugins\Redis\Tests;

use Aztech\Events\Bus\Plugins\Redis\RedisChannelReader;
use Aztech\Events\Bus\Channel\Message;
class RedisChannelReaderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testEmptyEventKeyThrowsExceptionOnCreate()
    {
        new RedisChannelReader($this->getMock('\Predis\Client'), '');
    }

    public function testAcknowledgeReturnsFalseWhenMessageHasNoCorrelationData()
    {
        $reader = new RedisChannelReader($this->getMock('\Predis\Client'), 'aztech:events:bus:plugins:redis');
        $message = new Message('', 'hello');

        $this->assertFalse($reader->acknowledge($message));
    }

    public function testReadAckReturnsUncorrelatedMessageWhenNoProcessingKeyIsSet()
    {
        $redis = $this->getMock('\Predis\Client', [ 'rpop' ]);

        $redis->expects($this->once())
            ->method('rpop')
            ->willReturn('hello');

        $reader = new RedisChannelReader($redis, 'aztech:events:bus:plugins:redis');
        $message = $reader->readAck();

        $this->assertEmpty($message->getCorrelationData());
        $this->assertEquals('hello', $message->getData());
        $this->assertFalse($reader->acknowledge($message));
    }

    public function testDisposeTransfersUnackedMessagesToQueue()
    {
        $redis = $this->getMock('\Predis\Client', [ 'rpoplpush' ]);

        $redis->expects($this->at(0))
            ->method('rpoplpush')
            ->with('queue', 'processing')
            ->willReturn('hello');

        $redis->expects($this->at(1))
            ->method('rpoplpush')
            ->with('processing', 'queue')
            ->will($this->onConsecutiveCalls(true, false));

        $reader = new RedisChannelReader($redis, 'queue', 'processing');

        $message = $reader->readAck();
        $reader->dispose();

        $this->assertEquals('hello', $message->getCorrelationData());
        $this->assertEquals('hello', $message->getData());
        $this->assertFalse($reader->acknowledge($message));
    }
}