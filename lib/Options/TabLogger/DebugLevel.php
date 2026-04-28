<?php

namespace Base\Module\Options\TabLogger;


use Base\Module\Exception\ModuleException;
use Base\Module\Options\TabLogger;
use Base\Module\Service\Container;
use Base\Module\Service\Options\Option;
use Base\Module\Service\Options\OptionsService;
use Base\Module\Src\Options\Providers\SelectBoxProvider;
use Bitrix\Main\Localization\Loc;
use Psr\Log\LogLevel;

class DebugLevel implements Option
{

    public static function getId(): string
    {
        return 'sample_option_text';
    }

    public static function getName(): string
    {
        return Loc::getMessage('MODULE_OPTION_LOGGER_LEVEL_TITLE');
    }

    public static function getType(): string
    {
        return 'selectbox';
    }

    public static function getTabId(): string
    {
        return TabLogger::getId();
    }

    public static function getSort(): int
    {
        return 100;
    }

    /**
     * @return array
     * @throws ModuleException
     */
    public static function getParams(): array
    {
        /** @var OptionsService $srvOptions */
        $srvOptions = Container::get(OptionsService::SERVICE_CODE);
        /** @var SelectBoxProvider $provider */
        $provider = $srvOptions->getProvider(self::getType());
        return $provider
            ->setItems([
                LogLevel::EMERGENCY => LogLevel::EMERGENCY,
                LogLevel::ALERT => LogLevel::ALERT,
                LogLevel::CRITICAL => LogLevel::CRITICAL,
                LogLevel::ERROR => LogLevel::ERROR,
                LogLevel::WARNING => LogLevel::WARNING,
                LogLevel::NOTICE => LogLevel::NOTICE,
                LogLevel::INFO => LogLevel::INFO,
                LogLevel::DEBUG => LogLevel::DEBUG,
            ])
            ->setDefault(LogLevel::ERROR)
            ->getParamsToArray();
    }
}