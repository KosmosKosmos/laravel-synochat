<?php

namespace KosmosKosmos\SynoChat\Logging;

use KosmosKosmos\SynoChat\Handler\SynologyChatHandler;
use Monolog\Logger;

class SynologyChatLogger {

    public function __invoke(array $config) {
        $logger = new Logger("custom");
        return $logger->pushHandler(new SynologyChatHandler($config['token'], $config['url'], $config['level']));
    }

}
