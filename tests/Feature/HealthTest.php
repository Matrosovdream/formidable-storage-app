<?php

it('exposes /health and reports database OK', function () {
    if (! extension_loaded('redis')) {
        $this->markTestSkipped('phpredis extension not installed; /health depends on Redis.');
    }

    $response = $this->getJson('/health');

    $response->assertJsonStructure(['database', 'redis', 'storage']);
    expect($response->json('database'))->toBe('OK');
});
