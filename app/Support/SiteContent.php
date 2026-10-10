<?php

namespace App\Support;

/**
 * Website content the owner edits on Admin → Site Content (stored in
 * storage/app/site-settings.json under "content"). Every default below is
 * exactly what the site showed before this page existed, so an untouched
 * setting renders the site unchanged.
 */
class SiteContent
{
    public static function defaults(): array
    {
        return [
            // Announcement bar (top of homepage, checkout, funding pages)
            'announce_on'    => false,
            'announce_text'  => '',
            'announce_link'  => '',
            'announce_style' => 'green',   // green | gold | red | navy

            // Scrolling ticker under the announcement
            'ticker' => [
                'Repossessions Challenged', 'Collections Challenged', 'Charge-offs Challenged',
                'Bankruptcies Challenged', 'Student Loans Challenged', 'Late Payments Challenged',
                'Hard Inquiries Challenged', 'Personal Information Challenged',
            ],

            // Homepage hero
            'hero_eyebrow'     => 'We Specialize in Consumer Law, FCRA, FDCPA & Metro 2 Compliance',
            'hero_line1'       => 'Improve Your Credit',
            'hero_green1'      => 'With Professional',
            'hero_mid'         => 'Credit Analysis',
            'hero_green2'      => '& Guidance',
            'hero_lead'        => 'We help consumers review their credit reports and challenge inaccurate or unverifiable information under federal consumer protection laws.',
            'hero_lead_strong' => 'YES.',
            'hero_cta1'        => 'View Membership Plans',
            'hero_cta2'        => 'Get Free Credit Analysis',

            // Stats band (5 boxes)
            'stats' => [
                ['count' => 10000, 'prefix' => '',  'suffix' => '+',     'label' => 'Clients Served'],
                ['count' => 137,   'prefix' => '+', 'suffix' => '',      'label' => 'Average Client Score Improvement (Results Vary)'],
                ['count' => 25000, 'prefix' => '',  'suffix' => '+',     'label' => 'Credit Reports Reviewed'],
                ['count' => 15000, 'prefix' => '',  'suffix' => '+',     'label' => 'Disputes Submitted for Clients'],
                ['count' => 8,     'prefix' => '',  'suffix' => '+ Yrs', 'label' => 'Industry Experience'],
            ],

            // "Book a consultation" calendar (GHL booking widget)
            'booking_url' => 'https://api.leadconnectorhq.com/widget/booking/jdPgwX73WgVZQuCJKFlI',

            // Google search snippet for the homepage
            'seo_title'       => '850 FICO Club — Credit Is King & Cash Is Power',
            'seo_description' => 'Professional credit analysis and dispute help under the FCRA & FDCPA. Choose a credit repair plan and start today.',

            // Sales switch — off = checkout visible but no new payments
            'sales_open'           => true,
            'sales_paused_message' => 'Enrollment is temporarily paused. Please check back soon or book a free consultation.',

            // Referral partner codes accepted from ?ref=CODE links
            'referral_codes' => ['DL', 'EL', 'NL', 'EXP', 'LAL'],
        ];
    }

    public static function all(): array
    {
        return array_merge(self::defaults(), (array) SiteSettings::get('content', []));
    }

    public static function get(string $key): mixed
    {
        return self::all()[$key] ?? null;
    }

    public static function save(array $values): void
    {
        SiteSettings::set('content', array_intersect_key($values, self::defaults()));
    }

    public static function salesOpen(): bool
    {
        return (bool) self::get('sales_open');
    }

    /** Upper-cased, de-duplicated partner codes. */
    public static function referralCodes(): array
    {
        return array_values(array_unique(array_filter(array_map(
            fn ($c) => strtoupper(trim((string) $c)),
            (array) self::get('referral_codes')
        ))));
    }
}
