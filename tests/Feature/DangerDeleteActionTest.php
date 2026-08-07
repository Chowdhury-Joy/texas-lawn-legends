<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Filament\Resources\Pages\Pages\EditPage;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Models\Page;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use ReflectionMethod;
use Tests\TestCase;

/**
 * The footer "danger zone" Delete action on Edit screens. These screens render
 * form actions through custom Blade views that echo each action directly, so
 * the action never receives a record from Filament's schema layer — it has to
 * be bound in HasPrimarySaveAndDangerDelete itself, or rendering fatals on
 * DeleteAction's `hidden(fn (Model $record) => ...)` closure.
 */
class DangerDeleteActionTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => UserRole::Admin]);
    }

    /**
     * getFormActions() is protected; reach it the way the Blade view does.
     *
     * @return array<mixed>
     */
    private function formActions(object $page): array
    {
        $method = new ReflectionMethod($page, 'getFormActions');
        $method->setAccessible(true);

        return $method->invoke($page);
    }

    private function deleteAction(object $page): ?DeleteAction
    {
        foreach ($this->formActions($page) as $action) {
            if ($action instanceof DeleteAction) {
                return $action;
            }
        }

        return null;
    }

    public function test_page_edit_screen_renders_without_fataling(): void
    {
        $page = Page::create([
            'title' => 'Deletable',
            'slug' => 'deletable',
            'is_published' => true,
            'blocks' => [],
        ]);

        $html = $this->actingAs($this->admin())
            ->get("/admin/pages/{$page->slug}/edit")
            ->assertStatus(200)
            ->getContent();

        $this->assertStringContainsString('Delete', $html);
    }

    public function test_delete_action_has_the_record_bound(): void
    {
        $page = Page::create([
            'title' => 'Bound',
            'slug' => 'bound',
            'is_published' => true,
            'blocks' => [],
        ]);

        $component = Livewire::actingAs($this->admin())
            ->test(EditPage::class, ['record' => $page->slug])
            ->assertSuccessful();

        $action = $this->deleteAction($component->instance());

        $this->assertNotNull($action, 'Edit screen should expose a footer Delete action.');
        $this->assertTrue($action->getRecord()->is($page), 'Delete action must carry the edited record.');
        $this->assertFalse($action->isHidden(), 'Delete should be available for a normal record.');
    }

    public function test_user_edit_shows_delete_for_another_user(): void
    {
        $other = User::factory()->create(['role' => UserRole::Content]);

        $component = Livewire::actingAs($this->admin())
            ->test(EditUser::class, ['record' => $other->getKey()])
            ->assertSuccessful();

        $action = $this->deleteAction($component->instance());

        $this->assertNotNull($action);
        $this->assertTrue($action->isVisible(), 'Admins should be able to delete another user.');
    }

    /**
     * The override that previously used `parent::` — an admin must not be able
     * to delete their own account out from under themselves.
     */
    public function test_user_edit_hides_delete_for_yourself(): void
    {
        $admin = $this->admin();

        $component = Livewire::actingAs($admin)
            ->test(EditUser::class, ['record' => $admin->getKey()])
            ->assertSuccessful();

        $action = $this->deleteAction($component->instance());

        $this->assertNotNull($action);
        $this->assertFalse($action->isVisible(), 'You must not be offered Delete on your own account.');
    }
}
