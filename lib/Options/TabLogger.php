<?php

namespace Base\Module\Options;

use Base\Module\Service\Options\Tab;
use Bitrix\Main\Localization\Loc;

class TabLogger implements Tab
{

    public static function getId(): string
    {
        return 'logger';
    }

    public static function getName(): string
    {
        return Loc::getMessage('MODULE_TAB_LOGGER_TITLE');
    }

    public static function getSort(): int
    {
        return 10000;
    }
}