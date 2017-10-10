<?php

namespace wbarcovsky\yii2\request_docs\helpers;

class StructureHelper
{
    public static function getStructure($data)
    {
        $result = [];
        if (is_array($data) && isset($data[0]))  {
            $workData = $data[0];
        } else {
            $workData = $data;
        }
        foreach ($workData as $key => $value) {
            if (is_string($value) || is_integer($value) || is_float($value) || is_bool($value)) {
                $result[$key] = gettype($value);
            }
            if (is_array($value)) {
                $structure = self::getStructure($value);
                foreach ($structure as $field => $type) {
                    $result["{$key}.{$field}"] = $type;
                }
            }
        }
        return $result;
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