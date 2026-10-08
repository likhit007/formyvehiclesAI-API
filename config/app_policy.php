<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Terms and Conditions Text
    |--------------------------------------------------------------------------
    |
    | The default text returned when querying application terms (type = 1).
    |
    */
    'terms' => env('APP_TERMS_TEXT', "Terms and Conditions\n\n1. Acceptance of Terms\nBy accessing and using this application, you accept and agree to be bound by the terms and provisions of this agreement.\n\n2. User Responsibilities\nYou are responsible for maintaining the confidentiality of your account information and for all activities that occur under your account.\n\n3. Vehicle Information\nYou agree to provide accurate and updated information regarding any vehicles registered or tracked within the app.\n\n4. Modifications\nWe reserve the right to modify these terms at any time without prior notice. Your continued use of the application indicates your acceptance of any amendments.\n\n5. Termination\nWe reserve the right to suspend or terminate access to our services at our sole discretion, without notice, for conduct that violates these terms."),

    /*
    |--------------------------------------------------------------------------
    | Privacy Policy Text
    |--------------------------------------------------------------------------
    |
    | The default text returned when querying application privacy policy (type = 2).
    |
    */
    'privacy_policy' => env('APP_PRIVACY_POLICY_TEXT', "Privacy Policy\n\n1. Information Collection\nWe collect information necessary to provide and improve our services, including name, mobile number, state, and vehicle details.\n\n2. Use of Information\nCollected information is used for authentication, account management, vehicle tracking, and customer communication.\n\n3. Data Protection\nWe implement standard security measures to safeguard your personal data from unauthorized access, disclosure, or destruction.\n\n4. Third-Party Sharing\nWe do not sell or rent your personal information to third parties. Data is shared only with trusted providers essential for service delivery.\n\n5. Your Rights\nYou retain the right to review, update, or request the deletion of your account data by contacting our support team."),
];
