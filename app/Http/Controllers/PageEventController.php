<?php

namespace App\Http\Controllers;

use App\Models\PageEvent;
use Illuminate\Http\Request;

class PageEventController extends Controller
{
    /**
     * Store a front-end event (CTA click, form submit, etc.).
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'action'    => 'required|string|max:100',
            'path'      => 'nullable|string|max:255',
            'referrer'  => 'nullable|string|max:255',
            'meta'      => 'nullable|array',
        ]);

        PageEvent::create([
            'action'     => $data['action'],
            'path'       => $data['path'] ?? $request->path(),
            'referrer'   => $data['referrer'] ?? $request->header('referer'),
            'ip'         => $request->ip(),
            'user_agent' => substr($request->userAgent() ?? '', 0, 500),
            'meta'       => $data['meta'] ?? [],
        ]);

        return response()->json(['ok' => true]);
    }
}
