<?php

namespace FpDbTest;

use Exception;
use mysqli;

class Database implements DatabaseInterface
{
    private const SPECIFIERS= [
        'd' => true,
        'f' => true,
        'a' => true,
        '#' => true,
    ];

    private const SPECIAL_SYMBOL = '-1';

    public function __construct(
        readonly private mysqli $mysqli
    ) {
    }

    /**
     * @throws Exception
     */
    public function buildQuery(string $query, array $args = []): string
    {
        $result = '';
        $iter = 0;
        for ($i=0; $i<strlen($query); $i++) {

            // Работа с блоками
            if ('{' === $query[$i]) {
                $block = '';
                for (; $i<strlen($query); $i++) {
                    if ('{' === $query[$i]) {
                        continue;
                    }
                    if ('}' === $query[$i]) {
                        $i++;
                        break;
                    }
                    $block .= $query[$i];
                }
                // Если параметр является специальным значением, то пропускаем блок
                if (self::SPECIAL_SYMBOL === $args[$iter]) {
                    continue;
                }

                // Заменяем идентификаторы на значения
                $resultBlock = '';
                for ($z=0; $z<strlen($block); $z++) {
                    if ('?' === $block[$z]) {
                        $template = array_key_exists($block[$z+1], self::SPECIFIERS) ?  $block[$z].$block[$z+1] : '?';
                        $value = array_key_exists($iter, $args) ? $args[$iter]: '';
                        $iter++;
                        $i = 2=== strlen($template) ? $i+2 : $i+1;
                        $z = 2=== strlen($template) ? $z+2 : $z+1;
                        $resultBlock .= $this->toType(template: $template, value: $value);
                        continue;
                    }
                    $resultBlock .= $block[$z];
                }

                $result .= trim($resultBlock);
            }
            // Если шаблон обработан весь
            if ($i>=strlen($query)) {
                break;
            }

            // Заменяем идентификаторы на значения (работа со строкой шаблона)
            if ('?' === $query[$i]) {
                $template = array_key_exists($query[$i+1], self::SPECIFIERS) ?  $query[$i].$query[$i+1] : '?';
                $value = array_key_exists($iter, $args) ? $args[$iter]: '';
                $iter++;
                $i = 2=== strlen($template) ? $i+2 : $i+1;
                $result .= $this->toType(template: $template, value: $value);
            }
            // Если шаблон обработан весь
            if ($i>=strlen($query)) {
                break;
            }

            $result .= $query[$i];
        }

        return trim($result);
    }

    /**
     * @throws Exception
     */
    public function skip(): string
    {
        return self::SPECIAL_SYMBOL;
    }

    /**
     * @throws Exception
     */
    private function toType(string $template, $value): string
    {
        if ('?' === $template) {
            if (is_string($value)) {
                return "'$value'";
            }
        }

        if ('?#' === $template) {

            if (is_array($value)) {
                foreach ($value as &$val) {
                    $val = "`$val`";
                }

                return implode(', ', $value);
            }

            if (is_string($value)) {
                return "`$value`";
            }


            throw new Exception('Некорректный тип значения для шаблона ?#');
        }

        if ('?d' === $template) {
            return (int) $value.'';
        }

        if ('?f' === $template) {
            return (float) $value.'';
        }

        if ('?a' === $template) {
            if (!is_array($value)) {
                throw new Exception('Для шаблона ?a значение должно быть массивом');
            }

            if (0 === array_key_first($value)) {
                return implode(', ', $value);
            }

            $r = [];
            foreach ($value as $key => $v) {
                if (is_null($v)) {
                    $r[] = "`$key` = NULL";
                    continue;
                }

                if (is_numeric($v)) {
                    $r[] = "`$key` = $v";
                    continue;
                }

                $r[] = "`$key` = '$v'";
            }

            return implode(', ', $r);
        }

        return '';
    }
}
