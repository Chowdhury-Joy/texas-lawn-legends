<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Content = 'content';
    case Operations = 'operations';

    public function getLabel(): string
    {
        return match ($this) {
            self::Admin => 'Administrator',
            self::Content => 'Content Editor',
            self::Operations => 'Operations',
        };
    }

    /** Resources/settings this role may access. */
    public function canManageSettings(): bool
    {
        return $this === self::Admin;
    }
}
