<?php

namespace Tests\Feature;

use App\Support\SiteSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class WebsiteControlTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        File::delete(SiteSettings::path());
        SiteSettings::flush();
        config(['maintenance.secret' => 'team-secret', 'plans.plans.gold.commas_product_id' => 'GoLd1']);
    }

    protected function tearDown(): void
    {
        File::delete(SiteSettings::path());
        SiteSettings::flush();
        parent::tearDown();
    }

    public function test_website_page_lists_plans_links_and_connections(): void
    {
        $this->withSession(['is_admin' => true])
            ->get('/admin/website')->assertOk()
            ->assertSee('Live for everyone')
            ->assertSee('/accept-checkout?plan=gold', false)
            ->assertSee('/onboardingform?plan=gold', false)
            ->assertSee('GoLd1')
            ->assertSee('Commas API key')
            ->assertSee('?preview=team-secret', false);
    }

    public function test_admin_can_switch_maintenance_on_and_off(): void
    {
        $this->withSession(['is_admin' => true]);

        $this->post('/admin/website/maintenance', ['on' => 1])->assertSessionHas('success');
        SiteSettings::flush();
        $this->get('/')->assertStatus(503)->assertSee("We'll be back soon", false);
        $this->get('/admin/website')->assertOk()->assertSee('Maintenance mode');   // admin still reachable

        $this->post('/admin/website/maintenance', ['on' => 0])->assertSessionHas('success');
        SiteSettings::flush();
        $this->get('/')->assertOk();
    }
}
