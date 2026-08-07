<?php

namespace App\Services;

use App\Enums\ProductPart;
use App\Enums\UserRole;
use App\Models\Setting;
use App\Models\TrialWorkspace;
use App\Models\User;
use App\Support\Niche\NicheLoader;
use App\Support\Niche\NicheResolver;
use App\Support\Trial\TrialHost;
use App\Support\Trial\TrialWorkspaceContext;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;

/**
 * Provisions an isolated V3 trial workspace (Step 2).
 */
final class TrialProvisioner
{
    public function __construct(
        private readonly NicheLoader $nicheLoader,
    ) {}

    /**
     * @param  array{name: string, email: string, password?: string|null, google_id?: string|null}  $owner
     * @return array{0: User, 1: TrialWorkspace}
     */
    public function provision(string $nicheId, array $owner, ?string $slug = null): array
    {
        if (! TrialHost::enabled()) {
            throw new RuntimeException('Trial provisioning requires APP_TRIAL_HOST=true.');
        }

        $packs = config('niche.packs', []);

        if (! isset($packs[$nicheId]) || ! is_string($packs[$nicheId])) {
            throw new InvalidArgumentException("Unknown niche pack [{$nicheId}].");
        }

        $email = Str::lower(trim($owner['email']));
        $name = trim($owner['name']);
        $slug = TrialHost::makeUniqueSlug($slug ?? Str::before($email, '@'));

        if (User::withoutTrialWorkspaceScope()->where('email', $email)->exists()) {
            throw new InvalidArgumentException('An account with this email already exists.');
        }

        $workspace = TrialHost::createWorkspace($nicheId, $slug);

        $user = TrialWorkspaceContext::run($workspace, function () use ($nicheId, $owner, $email, $name, $workspace): User {
            $this->nicheLoader->provisionWorkspace($nicheId, preserveEmails: [$email]);

            Setting::set('product_part', (string) ProductPart::Ops->value, 'integer', 'product');
            return User::query()->create([
                'name' => $name !== '' ? $name : 'Trial Owner',
                'email' => $email,
                'role' => UserRole::Admin,
                'google_id' => $owner['google_id'] ?? null,
                'email_verified_at' => now(),
                'password' => filled($owner['password'] ?? null)
                    ? (string) $owner['password']
                    : Str::random(40),
                'trial_workspace_id' => $workspace->id,
            ]);
        });

        $workspace->update(['owner_user_id' => $user->id]);
        NicheResolver::flush();

        return [$user->fresh(), $workspace->fresh()];
    }
}
