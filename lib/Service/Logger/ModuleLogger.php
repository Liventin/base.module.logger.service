<?php

namespace Base\Module\Service\Logger;


use Base\Module\Service\Container;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Throwable;

trait ModuleLogger
{
    private static LoggerInterface|null $logger = null;

    /**
     * @return LoggerInterface
     */
    private function logger(): LoggerInterface
    {
        try {
            if (self::$logger === null) {
                self::$logger = Container::get('base.module.logger.service');
            }
            return self::$logger;
        } catch (Throwable) {
            self::$logger = new NullLogger();
            return self::$logger;
        }
    }
}