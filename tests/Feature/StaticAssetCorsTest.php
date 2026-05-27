<?php

it('serves static assets with CORS headers', function () {
    // Create a temporary file in js-static for testing
    $testDir = resource_path('js-static/test-folder');
    if (!is_dir($testDir)) {
        mkdir($testDir, 0755, true);
    }
    
    $testFile = realpath($testDir) . DIRECTORY_SEPARATOR . 'test.js';
    file_put_contents($testFile, 'console.log("hello test");');

    try {
        $response = $this->get('/js/test-folder/test.js');

        $response->assertStatus(200);
        $response->assertHeader('Access-Control-Allow-Origin', '*');
        $response->assertHeader('Content-Type', 'application/javascript');
        
        // Assert it is a binary file response serving the correct file
        expect($response->baseResponse)->toBeInstanceOf(\Symfony\Component\HttpFoundation\BinaryFileResponse::class);
        expect(realpath($response->baseResponse->getFile()->getPathname()))->toBe(realpath($testFile));
        
        // Test OPTIONS preflight
        $optionsResponse = $this->options('/js/test-folder/test.js');
        $optionsResponse->assertStatus(200);
        $optionsResponse->assertHeader('Access-Control-Allow-Origin', '*');
    } finally {
        // Clean up
        if (file_exists($testFile)) {
            unlink($testFile);
        }
        if (is_dir($testDir)) {
            rmdir($testDir);
        }
    }
});

it('returns 404 for non-existent assets or directory traversal', function () {
    $response1 = $this->get('/js/non-existent-asset-12345.js');
    $response1->assertStatus(404);

    $response2 = $this->get('/js/../routes/web.php');
    $response2->assertStatus(404);
});

