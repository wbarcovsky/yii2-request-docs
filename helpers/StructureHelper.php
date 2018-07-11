<?php

namespace wbarcovsky\yii2\request_docs\helpers;

class StructureHelper
{
    public static function getStructure($data, $key = null)
    {
        $result = [];
        $workData = $data;
        if (is_array($data) && isset($data[0]) && self::isObject($data[0])) {
            $workData = $data[0];
        }
        if (!is_array($workData)) {
            throw new \Exception('HEY! ' . $key . json_encode($workData));
        }
        foreach ($workData as $key => $value) {
            if (is_string($value) || is_integer($value) || is_float($value) || is_bool($value)) {
                $result[$key] = gettype($value);
            } elseif (self::isObject($value)) {
                $structure = self::getStructure($value, "{$key}");
                foreach ($structure as $field => $type) {
                    $result["{$key}.{$field}"] = $type;
                }
            } elseif (is_array($value) && isset($value[0]) && self::isObject($value[0])) {
                $structure = self::getStructure($value[0], "{$key}");
                foreach ($structure as $field => $type) {
                    $result["{$key}.{$field}"] = $type;
                }
            } elseif (is_array($value) && isset($value[0]) && !self::isObject($value[0])) {
                $result[$key] = 'array';
            }
        }
        return $result;
    }

    protected static function isObject($data)
    {
        return is_array($data) && !isset($data[0]);
    }

    public static function getStructureHash($data)
    {
        $fields = self::getStructure($data);
        sort($fields);
        return md5(json_encode($fields));
    }

    public static function jsonPrettyPrint($data, $reduce = true)
    {
        $short = is_array($data) && isset($data[0]) && count($data) > 3 && $reduce;
        $workData = $short ? [$data[0], $data[1], $data[2]] : $data;
        if ($reduce) {
            array_walk_recursive($workData, function (&$item) {
                if (is_string($item)) {
                    if (strlen($item) > 100) {
                        $item = substr($item, 0, 100) . '...';
                    }
                    $item = mb_encode_numericentity($item, [0x80, 0xffff, 0, 0xffff], 'UTF-8');
                }
            });
        }
        $result = mb_decode_numericentity(json_encode($workData, JSON_PRETTY_PRINT), [
            0x80,
            0xffff,
            0,
            0xffff,
        ], 'UTF-8');
        if ($short) {
            $result = substr_replace($result, PHP_EOL . '...', strlen($result) - 2, 0);
        }
        return $result;
    }

}