<?php

namespace Aztech\Events\Bus\Plugins\Redis;

use Aztech\Events\Bus\Events;
use Aztech\Events\Bus\GenericPluginFactory;

class Redis
{
    public static function loadPlugin($name = 'redis')
    {
        $events = new Events();

        $events->addPlugin($name, new GenericPluginFactory(function () {
            return new RedisChannelProvider();
        }, new RedisOptionsDescriptor()));
    }
}
