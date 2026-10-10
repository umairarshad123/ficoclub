<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\SiteContent;
use App\Support\SiteSettings;
use Illuminate\Http\Request;

/** Admin → Site Content: homepage text, announcement, sales switch, referral codes, SEO. */
class SiteContentController extends Controller
{
    public function edit()
    {
        return view('admin.site-content', [
            'c'          => SiteContent::all(),
            'customized' => SiteSettings::get('content') !== null,
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'sales_open'           => 'nullable|boolean',
            'sales_paused_message' => 'required|string|max:200',

            'announce_on'    => 'nullable|boolean',
            'announce_text'  => 'nullable|string|max:140',
            'announce_link'  => 'nullable|url|max:300',
            'announce_style' => 'required|in:green,gold,red,navy',

            'ticker'         => 'required|string|max:1500',

            'hero_eyebrow'     => 'required|string|max:120',
            'hero_line1'       => 'required|string|max:60',
            'hero_green1'      => 'nullable|string|max:60',
            'hero_mid'         => 'nullable|string|max:60',
            'hero_green2'      => 'nullable|string|max:60',
            'hero_lead'        => 'required|string|max:400',
            'hero_lead_strong' => 'nullable|string|max:40',
            'hero_cta1'        => 'required|string|max:40',
            'hero_cta2'        => 'required|string|max:40',

            'stats'            => 'required|array|size:5',
            'stats.*.count'    => 'required|integer|min:0|max:100000000',
            'stats.*.prefix'   => 'nullable|string|max:4',
            'stats.*.suffix'   => 'nullable|string|max:10',
            'stats.*.label'    => 'required|string|max:80',

            'booking_url'     => 'required|url|max:300',
            'seo_title'       => 'required|string|max:70',
            'seo_description' => 'required|string|max:170',

            'referral_codes'  => 'nullable|string|max:300',
        ]);

        SiteContent::save([
            'sales_open'           => $request->boolean('sales_open'),
            'sales_paused_message' => $data['sales_paused_message'],
            'announce_on'          => $request->boolean('announce_on'),
            'announce_text'        => (string) ($data['announce_text'] ?? ''),
            'announce_link'        => (string) ($data['announce_link'] ?? ''),
            'announce_style'       => $data['announce_style'],
            'ticker'               => array_values(array_filter(array_map('trim', preg_split('/\R/', $data['ticker'])), 'strlen')),
            'hero_eyebrow'         => $data['hero_eyebrow'],
            'hero_line1'           => $data['hero_line1'],
            'hero_green1'          => (string) ($data['hero_green1'] ?? ''),
            'hero_mid'             => (string) ($data['hero_mid'] ?? ''),
            'hero_green2'          => (string) ($data['hero_green2'] ?? ''),
            'hero_lead'            => $data['hero_lead'],
            'hero_lead_strong'     => (string) ($data['hero_lead_strong'] ?? ''),
            'hero_cta1'            => $data['hero_cta1'],
            'hero_cta2'            => $data['hero_cta2'],
            'stats'                => array_map(fn ($s) => [
                'count'  => (int) $s['count'],
                'prefix' => (string) ($s['prefix'] ?? ''),
                'suffix' => (string) ($s['suffix'] ?? ''),
                'label'  => $s['label'],
            ], array_values($data['stats'])),
            'booking_url'          => $data['booking_url'],
            'seo_title'            => $data['seo_title'],
            'seo_description'      => $data['seo_description'],
            'referral_codes'       => preg_split('/[\s,]+/', strtoupper((string) ($data['referral_codes'] ?? '')), -1, PREG_SPLIT_NO_EMPTY),
        ]);

        return back()->with('success', 'Website content saved — changes are live now.');
    }

    public function reset()
    {
        SiteSettings::set('content', null);

        return back()->with('success', 'Website content reset to the original text.');
    }
}
