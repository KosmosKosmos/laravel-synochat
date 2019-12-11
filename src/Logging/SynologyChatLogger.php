<?php

namespace KosmosKosmos\SynoChat\Logging;

use Illuminate\Support\Facades\Log;
use KosmosKosmos\SynoChat\Handler\SynologyChatHandler;
use Monolog\Logger;

class SynologyChatLogger {

    public function __invoke(array $config) {
        $logger = new Logger("custom");
        Log::info("invoke!");
        return $logger->pushHandler(new SynologyChatHandler($config['token'], $config['url'], $config['level']));
    }

}
