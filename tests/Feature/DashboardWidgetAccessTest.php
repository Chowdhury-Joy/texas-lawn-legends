<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Filament\Widgets\BusinessSnapshot;
use App\Filament\Widgets\LeadFunnel;
use App\Filament\Widgets\NeedsAttention;
use App\Filament\Widgets\RecentActivity;
use App\Filament\Widgets\UpcomingSiteVisits;
use App\Filament\Widgets\WeeklyLeadTrend;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Dashboard widgets read lead data, so they must honour the same access key
 * as LeadResource — otherwise a user blocked from /admin/leads still sees
 * customer names and pipeline values on the dashboard.
 */
class DashboardWidgetAccessTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<int, class-string>
     */
    private function widgets(): array
    {
        return [
            BusinessSnapshot::class,
            LeadFunnel::class,
            NeedsAttention::class,
            RecentActivity::class,
            UpcomingSiteVisits::class,
            WeeklyLeadTrend::class,
        ];
    }

    public function test_content_user_cannot_view_lead_widgets(): void
    {
        $this->actingAs(User::factory()->create(['role' => UserRole::Content]));

        foreach ($this->widgets() as $widget) {
            $this->assertFalse($widget::canView(), $widget.' must be hidden from Content users');
        }
    }

    public function test_operations_and_admin_users_can_view_lead_widgets(): void
    {
        foreach ([UserRole::Admin, UserRole::Operations] as $role) {
            $this->actingAs(User::factory()->create(['role' => $role]));

            foreach ($this->widgets() as $widget) {
                $this->assertTrue($widget::canView(), $widget.' must be visible to '.$role->value);
            }
        }
    }

    /**
     * Table widgets defer their rows to a follow-up Livewire request, so the
     * lead data is never in the initial HTML either way. What matters is that
     * the widget component itself is absent — that's what stops the deferred
     * fetch from ever being made.
     */
    public function test_lead_widgets_are_absent_from_the_dashboard_for_content_users(): void
    {
        Lead::factory()->create(['name' => 'Marguerite Vandersloot']);

        $this->actingAs(User::factory()->create(['role' => UserRole::Content]))
            ->get('/admin')
            ->assertStatus(200)
            ->assertDontSee('NeedsAttention')
            ->assertDontSee('UpcomingSiteVisits')
            ->assertDontSee('BusinessSnapshot')
            ->assertDontSee('Marguerite Vandersloot');
    }

    public function test_lead_widgets_are_present_on_the_dashboard_for_operations_users(): void
    {
        $this->actingAs(User::factory()->create(['role' => UserRole::Operations]))
            ->get('/admin')
            ->assertStatus(200)
            ->assertSee('NeedsAttention')
            ->assertSee('BusinessSnapshot');
    }
}
