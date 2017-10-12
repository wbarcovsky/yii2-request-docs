<?php

namespace wbarcovsky\yii2\request_docs\tests;

use PHPUnit\Framework\TestCase;
use wbarcovsky\yii2\request_docs\components\RequestDocs;

class RequestDocsComponentTest extends TestCase
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
        if (is_dir($this->getDataPath() . '/fill_info/')) {
            $files = glob($this->getDataPath() . '/fill_info/');
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
        $file = $this->getDataPath() . '/GET__api-user-id.4ace4c.json';
        $this->assertFileExists($file);
        $data = json_decode(file_get_contents($file), true);
        $this->assertEquals($data['method'], 'GET');
        $this->assertEquals($data['url'], 'api/user/:id');
        $this->assertEquals($data['params']['state'], 'string');
        $this->assertEquals($data['params']['object.x'], 'double');
        $this->assertEquals($data['params']['object.y'], 'double');
        $this->assertEquals($data['result']['success'], 'boolean');
        $this->assertEquals($data['result']['test'], 'integer');

        // Check zip file
        $zipFile = $this->getDataPath() . '/full_info/GET__api-user-id.4ace4c.zip';
        $this->assertFileExists($zipFile);
        $data = json_decode(file_get_contents("zip://{$zipFile}#data.json"), true);
        $this->assertEquals($data['method'], 'GET');
        $this->assertEquals($data['url'], 'api/user/:id');
        $this->assertNotEmpty($data['result']);
        $this->assertNotEmpty($data['params']);

        // Check load requests
        $docs2 = new RequestDocs([
            'storeFolder' => $this->getDataPath(),
            'autoLoadRequests' => true,
        ]);
        $this->assertNotEmpty($docs2->getRequests());
        $request = $docs2->getRequests()[0];
        $this->assertNotEmpty($request->hash);
        $this->assertNotEmpty($request->url);
        $this->assertNotEmpty($request->getParams());
        $this->assertNotEmpty($request->getResult());
    }

    protected function getDataPath()
    {
        return __DIR__ . '/data';
    }
}