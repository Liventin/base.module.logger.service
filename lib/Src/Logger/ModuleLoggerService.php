<?php

namespace Base\Module\Src\Logger;


use Base\Module\Options\TabLogger\DebugLevel;
use Base\Module\Service\LazyService;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Diag\FileLogger as bxFileLogger;
use Psr\Log\LogLevel;

#[LazyService(serviceCode: 'base.module.logger.service', constructorParams: [
    'fileName' => '',
    'maxSize' => null,
    'moduleId' => LazyService::MODULE_ID,
])]
class ModuleLoggerService extends bxFileLogger
{
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
        $this->setFormatter(new LogFormatter(true));
    }

    protected function logMessage(string $level, string $message): void
    {
        $message = strtoupper($level) . ' | ' . $message;
        parent::logMessage($level, $message);
    }
}