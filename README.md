# aztech/event-bus-extra-redis

## Build status

[![Build Status](https://travis-ci.org/aztech-dev/event-bus-extra-redis.png?branch=master)](https://travis-ci.org/aztech-dev/event-bus-extra-redis)
[![Code Coverage](https://scrutinizer-ci.com/g/aztech-dev/event-bus-extra-redis/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/aztech-dev/event-bus-extra-redis/?branch=master)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/aztech-dev/event-bus-extra-redis/badges/quality-score.png?s=668e4df5ba163c804504257d4a026a0a549f220a)](https://scrutinizer-ci.com/g/aztech-dev/event-bus-extra-redis/)
[![Dependency Status](https://www.versioneye.com/user/projects/53b92a84609ff04f7f000003/badge.svg)](https://www.versioneye.com/user/projects/53b92a84609ff04f7f000003)
[![HHVM Status](http://hhvm.h4cc.de/badge/aztech/event-bus-extra-redis.png)](http://hhvm.h4cc.de/package/aztech/event-bus-extra-redis)

## Stability

[![Latest Stable Version](https://poser.pugx.org/aztech/event-bus-extra-redis/v/stable.png)](https://packagist.org/packages/aztech/event-bus-extra-redis)
[![Latest Unstable Version](https://poser.pugx.org/aztech/event-bus-extra-redis/v/unstable.png)](https://packagist.org/packages/aztech/event-bus-extra-redis)

## About

Redis database plugin for the [`aztech/event-bus` library](https://github.com/aztech-dev/event-bus). For more information on `event-bus`, see that library's readme.

## Installation

### Via Composer

Composer is the only supported way of installing *aztech/event-bus-extra-redis* . Don't know Composer yet ? [Read more about it](https://getcomposer.org/doc/00-intro.md).


`$ composer require "aztech/event-bus-extra-redis":"~1"`

## Usage

To use this plugin, you first need to register it with the Event factory. The easiest way is as follows :

```php
<?php

require_once 'vendor/autoload.php';

use \Aztech\Events\Events;
use \Aztech\Events\Bus\Plugins\Redis\Redis;

Redis::loadPlugin('redis');

$publisher = Events::createPublisher('redis');
$processor = Events::createProcessor('redis');
// ...
```

## Configuring

By default, the plugin attempts to connect to a Redis server at localhost:6379, which suits mostly development machines.

You can configure the Redis client using the options array when using the Event factory.

```php
<?php

require_once 'vendor/autoload.php';

use \Aztech\Events\Events;
use \Aztech\Events\Bus\Plugins\Redis\Redis;

Redis::loadPlugin('redis');

$publisher = Events::createPublisher('redis', [ 'host' => '192.168.1.1', 'port' => 6379);
// ...
```

The keenest readers will have noticed the options accepted are the same as those used by `\Predis\Client`.
