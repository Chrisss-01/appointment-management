<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;

class PublicLandingRoutesTest extends TestCase
{
    public function test_the_landing_page_renders_successfully(): void
    {
        $response = $this->get('/');

        $response->assertOk();
    }

    public function test_the_public_schedule_route_resolves_and_passes_is_open_to_the_view(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 24, 10, 0, 0));

        try {
            $response = $this->get(route('public.schedule'));

            $response
                ->assertOk()
                ->assertViewIs('information.schedule')
                ->assertViewHas('isOpen', true);
        } finally {
            Carbon::setTestNow();
        }
    }

    public function test_the_privacy_policy_route_resolves_successfully(): void
    {
        $response = $this->get(route('legal.privacy'));

        $response
            ->assertOk()
            ->assertViewIs('legal.privacy');
    }

    public function test_the_terms_of_service_route_resolves_successfully(): void
    {
        $response = $this->get(route('legal.terms'));

        $response
            ->assertOk()
            ->assertViewIs('legal.terms');
    }

    public function test_the_password_reset_request_route_resolves_successfully(): void
    {
        $response = $this->get(route('password.request'));

        $response
            ->assertOk()
            ->assertViewIs('auth.forgot-password');
    }
}
