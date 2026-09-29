<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class WhatsAppDocsController extends Controller
{
    /**
     * Display comprehensive in-admin WhatsApp documentation and user manual.
     */
    public function index(): View
    {
        $isDev = config('whatsapp.dev_mode', false) || (request()->getHost() === 'egyptna.org' || str_ends_with(request()->getHost(), '.egyptna.org'));
        $devWhitelist = config('whatsapp.dev_whitelist', []);

        return view('whatsapp.docs', compact('isDev', 'devWhitelist'));
    }
}
