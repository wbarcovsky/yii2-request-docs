<?php

namespace wbarcovsky\yii2\request_docs\tests;

use PHPUnit\Framework\TestCase;
use wbarcovsky\yii2\request_docs\components\RequestDocs;

class TestRequestDocsComponent extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        if (!is_dir($this->getDataPath())) {
            mkdir($this->getDataPath(), 0777);
        }
        $files = glob($this->getDataPath() . '/*');
        foreach ($files as $file) {
            if (is_file($file) && $file) {
                unlink($file);
            }
        }
        if (is_dir($this->getDataPath() . '/zip/')) {
            $files = glob($this->getDataPath() . '/zip/');
            foreach ($files as $file) {
                if (is_file($file) && $file) {
                    unlink($file);
                }
            }
        }
    }

    public function testCreateRequest()
    {
        $docs = new RequestDocs(['storeFolder' => $this->getDataPath()]);
        // First request
        $request1 = $docs->addRequest('GET', '/api/user/123/');
        $this->assertNotEmpty($request1);
        $request1->addParams([
            'state' => 'new',
        ]);
        $request1->addResult(['success' => true]);

        // Second request
        $request2 = $docs->addRequest('GET', '/api/user/321/');
        $this->assertNotEmpty($request2);
        $request2->addParams([
            'object' => [
                'x' => 10.1,
                'y' => 11.2,
            ],
        ]);
        $request2->addResult(['test' => 123]);

        $docs->storeRequests();
        $file = $this->getDataPath() . '/GET__api-user-id.json';
        $this->assertFileExists($file);
        $data = json_decode(file_get_contents($file), true);
        $this->assertEquals($data['method'], 'GET');
        $this->assertEquals($data['url'], 'api/user/:id');
        $this->assertEquals($data['params']['state'], 'string');
        $this->assertEquals($data['params']['object.x'], 'double');
        $this->assertEquals($data['params']['object.y'], 'double');
        $this->assertEquals($data['result']['success'], 'boolean');
        $this->assertEquals($data['result']['test'], 'integer');
    }

    protected function getDataPath()
    {
        return __DIR__ . '/data';
    }
}