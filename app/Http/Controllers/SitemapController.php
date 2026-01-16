<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class SitemapController extends Controller
{
    public function index()
    {
        $urls = [
            [
                'loc' => url('/'),
                'priority' => '1.0',
            ],
            [
                'loc' => url('/about'),
                'priority' => '0.8',
            ],
            [
                'loc' => url('/services'),
                'priority' => '0.9',
            ],
            [
                'loc' => url('/projects'),
                'priority' => '0.8',
            ],
            [
                'loc' => url('/products'),
                'priority' => '0.8',
            ],
            [
                'loc' => url('/contact'),
                'priority' => '0.7',
            ],
        ];

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($urls as $url) {
            $xml .= '  <url>' . "\n";
            $xml .= '    <loc>' . $url['loc'] . '</loc>' . "\n";
            $xml .= '    <priority>' . $url['priority'] . '</priority>' . "\n";
            $xml .= '  </url>' . "\n";
        }
        $xml .= '</urlset>';

        return response($xml, 200)->header('Content-Type', 'text/xml');
    }
}
