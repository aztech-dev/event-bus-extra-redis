<?php

namespace Aztech\Events\Bus\Plugins\Redis\Tests;

use Aztech\Events\Bus\Events;
use Aztech\Events\Bus\Channel\AcknowledgeableChannelReader;
use Aztech\Events\Bus\Plugins\Redis\Redis;

class RedisChannelTest extends \PHPUnit_Framework_TestCase
{

    private static $pluginLoaded = false;

	protected function setUp()
	{
	    if (! self::$pluginLoaded) {
		  Redis::loadPlugin('redis');
		  self::$pluginLoaded = true;
	    }
	}

	public function testPublishedEventIsReadBack()
	{
		$redis = Events::getPlugin('redis');
		$channel = $redis->getChannelProvider()->createChannel([ 'event-key' => 'aztech:events:plugins:redis:test']);

		$reader = $channel->getReader();
		$writer = $channel->getWriter();

		$writer->write($this->getMock('\Aztech\Events\Event'), 'hello');

		$this->assertEquals('hello', $reader->read());
	}

	public function testPublishedEventIsAcknowledgeable()
	{
	    $redis = Events::getPlugin('redis');
	    $channel = $redis->getChannelProvider()->createChannel([
	        'event-key' => 'aztech:events:plugins:redis:test'
        ]);

	    /* @var $reader AcknowledgeableChannelReader */
	    $reader = $channel->getReader();
	    $writer = $channel->getWriter();

	    $this->assertInstanceOf('\Aztech\Events\Bus\Channel\AcknowledgeableChannelReader', $reader);

	    $writer->write($this->getMock('\Aztech\Events\Event'), 'hello');

	    $message = $reader->readAck();

	    $this->assertEquals('hello', $message->getData());
	    $this->assertEquals('hello', $message->getCorrelationData());

	    $this->assertTrue($reader->acknowledge($message));
	}

	public function testPassingCredentialsInOptionsSetsAuth()
	{
	    $client = $this->getMock('\Predis\Client', [ 'auth' ]);
	    $redis = Events::getPlugin('redis');

	    $client->expects($this->once())
	       ->method('auth')
	       ->with('pass');

	    $provider = $redis->getChannelProvider();

	    $provider->setClient($client);
	    $provider->createChannel([
	        'event-key' => 'aztech:events:plugins:redis:test',
	        'password' => 'pass'
        ], $client);
	}

}