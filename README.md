# base.module.logger.service

<table>
<tr>
<td>
<a href="https://github.com/Liventin/base.module">Bitrix Base Module</a>
</td>
</tr>
</table>

install | update

```
"require": {
    "liventin/base.module.logger.service": "@stable"
}
```
redirect (optional)
```
"extra": {
  "service-redirect": {
    "liventin/base.module.logger.service": "module.name",
  }
}
```

Use
```php
<?php
namespace Name\Space;

use Base\Module\Service\Logger\ModuleLogger;

class SomeClass
{
    use ModuleLogger;
    
    public static function onUserAdd(array $fields): void
    {
        ...
        $this->logger()->critical('test');
        ...
    }
}

```