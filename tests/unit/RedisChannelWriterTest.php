<?php

namespace Aztech\Events\Bus\Plugins\Redis\Tests;

use Aztech\Events\Bus\Channel\Message;
use Aztech\Events\Bus\Plugins\Redis\RedisChannelWriter;

class RedisChannelWriterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testEmptyEventKeyThrowsExceptionOnCreate()
    {
        (new RedisChannelWriter($this->getMock('\Predis\Client'), ''));
    }

}