<?php

namespace wbarcovsky\yii2\request_docs\helpers;


class StringHelper
{
    /**
     * Определить EOL
     *
     * Для многострочного $string текста определить символ перевода строки (end-of-line). Если в тексте разные типы
     * перевода, то вернет первое попавшееся.
     *
     * @see https://en.wikipedia.org/wiki/Newline
     * @param string $string Входная строка
     * @param string $defaultEOL Перенос строк по умолчанию
     * @return string
     */
    public static function detectEOL($string, $defaultEOL = PHP_EOL)
    {
        if (preg_match('~(?<eol>\r\n|\n\r|\n|\r)~u', $string, $matches)) {
            return $matches['eol'];
        }
        return $defaultEOL;
    }
}
