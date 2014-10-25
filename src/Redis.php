<?php

namespace Aztech\Events\Bus\Plugins\Redis;

use Aztech\Events\Bus\Events;
use Aztech\Events\Bus\GenericPluginFactory;

class Redis
{
    public static function loadPlugin($name = 'redis')
    {
        Events::addPlugin($name, new GenericPluginFactory(function () {
            return new RedisChannelProvider();
        }, new RedisOptionsDescriptor()));
    }
}
