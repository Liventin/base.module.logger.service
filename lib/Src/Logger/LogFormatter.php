<?php

namespace Base\Module\Src\Logger;


use Bitrix\Main\Diag\LogFormatter as bxLogFormatter;
use Bitrix\Main\Type\Date;
use Throwable;

class LogFormatter extends bxLogFormatter
{
    protected function castToString($value, $placeholder = null, int $deep = 0): string
    {
        $type = gettype($value);

        return match ($type) {
            'string' => $deep === 0 ? $value : '\'' . $value . '\'',
            'boolean' => $value ? 'true' : 'false',
            'NULL' => 'null',
            'integer', 'double' => (string)$value,
            'array' => $this->formatArray($value, $deep),
            'object' => $this->formatObject($value, $deep),
            default => 'unknown type : ' . $type,
        };
//
//        if (is_string($value)) {
//            return $value;
//        }
//
//        if (is_object($value)) {
//            if ($placeholder === 'date' && $value instanceof Date) {
//                $value = $this->formatDate($value);
//            } elseif ($placeholder === 'exception' && $value instanceof Throwable) {
//                $value = $this->formatException($value);
//            } elseif (method_exists($value, '__toString')) {
//                $value = (string)$value;
//            } else {
//                $value = $this->formatMixed($value);
//            }
//        } else {
//            if ($placeholder === 'trace' && is_array($value)) {
//                $value = $this->formatTrace($value);
//            } else {
//                $value = $this->formatMixed($value);
//            }
//        }
//        return $value;
    }

    public function formatArray(array $value, int $deep = 0): string
    {
        if (is_callable($value, false, $callableName)) {
            return 'func:' . $callableName;
        }

        $arr = "[\n";
        foreach ($value as $key => $keyValue) {
            $arr .= str_repeat("\t", $deep + 1) . $key . ' => ' . $this->castToString(
                    $keyValue,
                    null,
                    $deep + 1
                ) . ",\n";
        }
        $arr .= str_repeat("\t", $deep) . ']';
        return $arr;
    }

    public function formatObject(mixed $value, int $deep= 0): string
    {
        if ($value instanceof Throwable) {
            return $this->formatException($value);
        }

        if ($value instanceof Date) {
            $formatDate = $this->formatDate($value);

            if ($deep === 0) {
                return $formatDate;
            }
            return 'new ' . get_class($value) . '(\'' . $formatDate . '\')';
        }

        $result = '[' . get_class($value) . ']';
        if (method_exists($value, '__toString')) {
            return $result . ': ' . $value;
        }

        return $result;
    }
}