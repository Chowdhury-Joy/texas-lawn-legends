<?php

namespace App\Support;

use App\Enums\UserRole;

/**
 * Central registry of every admin-panel area an administrator can grant or
 * revoke per user. Each entry has a stable `key`, a human label, and the
 * role presets that imply access by default.
 *
 * Used both to enforce access (via User::canAccessKey()) and to render the
 * per-user "Access" checkbox list in the User form, so the two never drift.
 */
final class AccessPermissions
{
    /**
     * @return array<string, array{label: string, group: string, roles: array<int, UserRole>}>
     */
    public static function all(): array
    {
        return [
            // ---------------- Resources ----------------
            'resource.leads' => [
                'label' => 'Leads',
                'group' => 'Data',
                'roles' => [UserRole::Admin, UserRole::Operations, UserRole::Sales, UserRole::Dispatch],
            ],
            'resource.projects' => [
                'label' => 'Projects',
                'group' => 'Data',
                'roles' => [UserRole::Admin, UserRole::Operations, UserRole::Sales, UserRole::Foreman, UserRole::Bookkeeper, UserRole::Dispatch],
            ],
            'resource.crews' => [
                'label' => 'Field Crews',
                'group' => 'Data',
                'roles' => [UserRole::Admin, UserRole::Operations, UserRole::Foreman, UserRole::Dispatch],
            ],
            'resource.invoices' => [
                'label' => 'Invoices & Billing',
                'group' => 'Data',
                'roles' => [UserRole::Admin, UserRole::Operations, UserRole::Bookkeeper],
            ],
            'resource.proposals' => [
                'label' => 'Proposals',
                'group' => 'Data',
                'roles' => [UserRole::Admin, UserRole::Operations, UserRole::Sales],
            ],
            'resource.equipment' => [
                'label' => 'Equipment',
                'group' => 'Data',
                'roles' => [UserRole::Admin, UserRole::Operations, UserRole::Foreman],
            ],
            'resource.time_entries' => [
                'label' => 'Time Entries',
                'group' => 'Data',
                'roles' => [UserRole::Admin, UserRole::Operations, UserRole::Bookkeeper, UserRole::Foreman],
            ],
            'resource.services' => [
                'label' => 'Services',
                'group' => 'Data',
                'roles' => [UserRole::Admin, UserRole::Content, UserRole::Operations, UserRole::Marketing],
            ],
            'resource.pages' => [
                'label' => 'Pages',
                'group' => 'Data',
                'roles' => [UserRole::Admin, UserRole::Content, UserRole::Operations, UserRole::Marketing],
            ],
            'resource.testimonials' => [
                'label' => 'Testimonials',
                'group' => 'Data',
                'roles' => [UserRole::Admin, UserRole::Content, UserRole::Operations, UserRole::Marketing],
            ],
            'resource.access_codes' => [
                'label' => 'Access Codes',
                'group' => 'Data',
                'roles' => [UserRole::Admin, UserRole::Operations],
            ],
            'resource.progress_photos' => [
                'label' => 'Progress Photos',
                'group' => 'Data',
                'roles' => [UserRole::Admin, UserRole::Operations, UserRole::Foreman],
            ],
            'resource.addons' => [
                'label' => 'Add-ons',
                'group' => 'Data',
                'roles' => [UserRole::Admin, UserRole::Content, UserRole::Operations],
            ],
            'resource.milestones' => [
                'label' => 'Milestones',
                'group' => 'Data',
                'roles' => [UserRole::Admin, UserRole::Operations, UserRole::Foreman],
            ],
            'resource.users' => [
                'label' => 'Users',
                'group' => 'Configuration',
                'roles' => [UserRole::Admin],
            ],
            'resource.settings' => [
                'label' => 'Settings (raw keys)',
                'group' => 'Configuration',
                'roles' => [UserRole::Admin],
            ],

            // ---------------- Settings pages ----------------
            'settings.product_parts' => [
                'label' => 'Product Parts',
                'group' => 'Site Settings',
                'roles' => [UserRole::Admin],
            ],
            'settings.industry_packs' => [
                'label' => 'Industry Packs / Demo',
                'group' => 'Site Settings',
                'roles' => [UserRole::Admin],
            ],
            'settings.homepage' => [
                'label' => 'Homepage Content',
                'group' => 'Site Settings',
                'roles' => [UserRole::Admin],
            ],
            'settings.branding' => [
                'label' => 'Branding & Theme',
                'group' => 'Site Settings',
                'roles' => [UserRole::Admin, UserRole::Marketing],
            ],
            'settings.contact' => [
                'label' => 'Contact & Footer',
                'group' => 'Site Settings',
                'roles' => [UserRole::Admin],
            ],
            'settings.operations' => [
                'label' => 'Operations Alerts',
                'group' => 'Site Settings',
                'roles' => [UserRole::Admin],
            ],
            'settings.pricing' => [
                'label' => 'Estimator & Pricing',
                'group' => 'Site Settings',
                'roles' => [UserRole::Admin],
            ],
            'settings.seo' => [
                'label' => 'SEO & Analytics',
                'group' => 'Site Settings',
                'roles' => [UserRole::Admin, UserRole::Marketing],
            ],
            'settings.advanced_layout' => [
                'label' => 'Advanced Block Layout & Grid Controls',
                'group' => 'Site Settings',
                'roles' => [UserRole::Admin],
            ],
        ];
    }

    /**
     * Keys that an admin may grant/revoke for a given role by default.
     *
     * @return array<int, string>
     */
    public static function defaultsFor(UserRole $role): array
    {
        $keys = [];

        foreach (self::all() as $key => $def) {
            if (in_array($role, $def['roles'], true)) {
                $keys[] = $key;
            }
        }

        return $keys;
    }

    /**
     * Grouped options for a checkbox list: [group => [key => label]].
     *
     * @return array<string, array<string, string>>
     */
    public static function groupedOptions(): array
    {
        $groups = [];

        foreach (self::all() as $key => $def) {
            $groups[$def['group']][$key] = $def['label'];
        }

        return $groups;
    }

    /**
     * Flat options for Filament's CheckboxList (which renders a single
     * key => label list and does not support nested optgroups in this
     * view). Grouping is preserved visually via a "Group — Label" label.
     *
     * @return array<string, string>
     */
    public static function flatOptions(): array
    {
        $options = [];

        foreach (self::all() as $key => $def) {
            $options[$key] = $def['group'].' — '.$def['label'];
        }

        return $options;
    }
}
