<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Content = 'content';
    case Operations = 'operations';
    case Sales = 'sales';
    case Foreman = 'foreman';
    case Bookkeeper = 'bookkeeper';
    case Marketing = 'marketing';
    case Dispatch = 'dispatch';

    public function getLabel(): string
    {
        return match ($this) {
            self::Admin => 'Administrator',
            self::Content => 'Content Editor',
            self::Operations => 'Operations Manager',
            self::Sales => 'Sales & Estimator',
            self::Foreman => 'Crew Leader / Foreman',
            self::Bookkeeper => 'Accountant / Bookkeeper',
            self::Marketing => 'Marketing & SEO',
            self::Dispatch => 'Customer Support / Dispatch',
        };
    }

    /** Resources/settings this role may access. */
    public function canManageSettings(): bool
    {
        return $this === self::Admin;
    }
}
