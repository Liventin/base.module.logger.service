<?php

namespace Base\Module\Src\Logger;


use Base\Module\Options\TabLogger\DebugLevel;
use Base\Module\Service\LazyService;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Diag\FileLogger as bxFileLogger;
use Bitrix\Main\Diag\Helper;
use Bitrix\Main\Type\DateTime;
use Psr\Log\LogLevel;
use Bitrix\Main\Diag\LogFormatter;

#[LazyService(serviceCode: 'base.module.logger.service', constructorParams: [
    'fileName' => '',
    'maxSize' => null,
    'moduleId' => LazyService::MODULE_ID,
])]
class ModuleLoggerService extends bxFileLogger
{
    public const LOG_DELIMITER = '----------';
    public const HEAD_DELIMITER = ' | ';

    public function __construct(string $fileName, int $maxSize = null, string $moduleId = null)
    {
        if (empty($moduleId)) {
            $moduleId = LazyService::MODULE_ID;
        }

        if (empty($fileName)) {
            $fileName = dirname(__DIR__, 4) . '/' . $moduleId . '/log/logger.log';
        }

        CheckDirPath($fileName);

        parent::__construct($fileName, $maxSize);

        $this->setLevel(Option::get($moduleId, DebugLevel::getId(), LogLevel::ERROR));
        $this->setFormatter(new LogFormatter(false, 50));
    }

    protected function logMessage(string $level, string $message): void
    {
        $this->addPrefix($level, $message);
        parent::logMessage($level, $message);
    }

    protected function addPrefix(string $level, string &$message): void
    {
        $trace = Helper::getBackTrace(1, 0, 4);
        $fileLine = $trace[0]['file'] . ':' . $trace[0]['line'];

        $now = (new DateTime())->format('Y-m-d H:i:s');

        $delim = static::HEAD_DELIMITER;

        $message = static::LOG_DELIMITER . "\n" . strtoupper($level) . $delim . $now . $delim . $fileLine .
            "\n" . $message . "\n";
    }
}