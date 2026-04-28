<?php

namespace Base\Module\Src\Logger;

use Bitrix\Main\Diag\Helper;
use Bitrix\Main\Diag\LogFormatter as bxLogFormatter;
use Bitrix\Main\Type\DateTime;

class LogFormatter extends bxLogFormatter
{
    public function format($message, array $context = []): string
    {
        $message = (new DateTime())->format('Y-m-d H:i:s') . "\n" . $message;

        $this->addTrace($message);

        $message .= "\n" . static::DELIMITER . "\n";

        $replace = [];
        foreach ($context as $key => $val) {
            $replace['{' . $key . '}'] = $this->castToString($val, $key);
        }

        return strtr($message, $replace);
    }

    public function addTrace(string &$message): void
    {
        $trace = Helper::getBackTrace(6, 0, 5);

        foreach ($trace as $index => $params) {
            $message .= "\n#$index: " . $params['file'] . ':' . $params['line'];
        }

        $action = $trace[1];
        $message .= "\n\t" . $action['class'] . $action['type'] . $action['function'] . "()";

        if (!empty($action['args'])) {
            $message .= $this->formatArguments($action['args']);
        }
    }

    public function formatArguments($args): string
    {
        $arguments = '';
        foreach ($args as $arg) {
            $arguments .= "\n\t\t" . $this->formatArgument($arg);
        }

        return $arguments;
    }

    public function formatArgument($arg, int $deep = 2): string
    {
        $type = gettype($arg);

        return match ($type) {
            'boolean' => $arg ? 'true' : 'false',
            'NULL' => 'null',
            'integer', 'double' => (string)$arg,
            'string' => $this->formatStringArgument($arg),
            'array' => $this->formatArrayArgument($arg, $deep),
            'object' => $type . ': [' . get_class($arg) . ']',
            'resource' => $type . ': ' . get_resource_type($arg),
            default => 'unknown type',
        };
    }

    public function formatStringArgument(string $arg): string
    {
        if (is_callable($arg, false, $callableName)) {
            return 'func:' . $callableName;
        }

        if (class_exists($arg, false)) {
            return 'class:' . $arg;
        }

        if (interface_exists($arg, false)) {
            return 'interface:' . $arg;
        }

        if (mb_strlen($arg) > $this->argMaxChars) {
            return '"' . mb_substr($arg, 0, $this->argMaxChars / 2) . '...' . mb_substr(
                    $arg,
                    -$this->argMaxChars / 2
                ) . '" (' . mb_strlen($arg) . ')';
        }

        return '"' . $arg . '"';
    }

    public function formatArrayArgument(array $arg, int $deep = 0): string
    {
        if (is_callable($arg, false, $callableName)) {
            return 'func:' . $callableName;
        }

        $arr = "[\n";
        foreach ($arg as $key => $value) {
            $arr .= str_repeat("\t", $deep+1) . $key . ' => ' . $this->formatArgument($value, $deep+1) . ",\n";
        }
        $arr .= str_repeat("\t", $deep).']';
        return $arr;
    }
}