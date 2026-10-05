<?php

namespace Tests\Feature;

use App\Models\City;
use App\Services\CityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_root_redirects_to_default_city(): void
    {
        City::create(['name' => '上海', 'slug' => 'shanghai']);

        $this->get('/')->assertRedirect('/shanghai');
    }

    public function test_city_home_renders_brand_and_remembers_city(): void
    {
        City::create(['name' => '上海', 'slug' => 'shanghai']);

        $this->get('/shanghai')
            ->assertOk()
            ->assertSee('roavilo-glm')
            ->assertCookie(CityService::COOKIE_NAME);
    }
}
