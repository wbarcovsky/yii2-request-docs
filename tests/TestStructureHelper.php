<?php

namespace wbarcovsky\yii2\request_docs\tests;

use PHPUnit\Framework\TestCase;
use wbarcovsky\yii2\request_docs\helpers\StructureHelper;

class TestStructureHelper extends TestCase
{
    public function testArray()
    {
        $data = [
            'field0' => 1,
            'field1' => true,
            'field2' => 'Hello, world!',
            'field3' => [
                'a' => 'a',
                'b' => 'b',
                'c' => 'c',
            ],
            'field4' => [
                [
                    'x' => ['ss' => 1, 'ww' => 11],
                    'y' => 2,
                    'z' => 3,
                ],
                [
                    'x' => ['ss' => 44, 'ww' => 444],
                    'y' => 5,
                    'z' => 6,
                ],
            ],
        ];
        $result = StructureHelper::getStructure($data);

        $this->assertEquals($result, [
            'field0' => 'integer',
            'field1' => 'boolean',
            'field2' => 'string',
            'field3.a' => 'string',
            'field3.b' => 'string',
            'field3.c' => 'string',
            'field4.x.ss' => 'integer',
            'field4.x.ww' => 'integer',
            'field4.y' => 'integer',
            'field4.z' => 'integer',
        ]);
    }

    public function testHash()
    {
        $hash1 = StructureHelper::getStructureHash([
            'a' => 1,
            'b' => 2,
            'c' => 3,
        ]);
        $hash2 = StructureHelper::getStructureHash([
            'c' => 7,
            'b' => 5,
            'a' => 2,
        ]);
        $hash3 = StructureHelper::getStructureHash([
            'a' => '1',
            'b' => 2,
            'c' => 3,
        ]);
        $hash4 = StructureHelper::getStructureHash([
            'a' => 1,
            'b' => 2,
        ]);
        $this->assertNotEmpty($hash1);
        $this->assertNotEmpty($hash2);
        $this->assertNotEmpty($hash3);
        $this->assertNotEmpty($hash4);

        $this->assertEquals($hash1, $hash2);
        $this->assertNotEquals($hash1, $hash3);
        $this->assertNotEquals($hash1, $hash4);
    }
}