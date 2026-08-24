<?php

namespace Tests\Feature;

use Tests\TestCase;

class OpenDiscoveryTest extends TestCase
{
    public function test_frontpage_redirects_to_github(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('https://github.com/OpenDiscoveryBiz/root-provider');
    }

    public function test_lookup_rejects_invalid_id(): void
    {
        $response = $this->get('/.well-known/opendiscovery/X.json');

        $response->assertStatus(400)
            ->assertJson([
                'type' => 'official',
                'error' => 'invalid_id',
            ]);
    }

    public function test_lookup_returns_country_not_supported(): void
    {
        $response = $this->get('/.well-known/opendiscovery/XX123.json');

        $response->assertStatus(404)
            ->assertJson([
                'type' => 'official',
                'error' => 'country_not_supported',
            ]);
    }

    public function test_lookup_returns_redirect_for_supported_country(): void
    {
        $response = $this->get('/.well-known/opendiscovery/DK123.json');

        $response->assertOk()
            ->assertJson([
                'type' => 'redirect',
                'id' => 'DK',
                'providers' => ['https://dk.opendiscovery.biz'],
                'ttl' => 3600,
            ]);
    }

    public function test_lookup_supports_pretty_json(): void
    {
        $response = $this->get('/.well-known/opendiscovery/DK123.json?pretty=1');

        $response->assertOk();
        $this->assertStringContainsString("\n", $response->getContent());
    }
}
