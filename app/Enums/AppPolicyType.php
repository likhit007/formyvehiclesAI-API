<?php

namespace App\Enums;

enum AppPolicyType: int
{
    case Terms = 1;
    case PrivacyPolicy = 2;

    /**
     * Get human-readable label for the policy type.
     */
    public function label(): string
    {
        return match ($this) {
            self::Terms => 'Terms',
            self::PrivacyPolicy => 'Privacy Policy',
        };
    }

    /**
     * Get content text for the policy type.
     */
    public function content(): string
    {
        return match ($this) {
            self::Terms => (string) config('app_policy.terms', 'Terms and Conditions'),
            self::PrivacyPolicy => (string) config('app_policy.privacy_policy', 'Privacy Policy'),
        };
    }
}
