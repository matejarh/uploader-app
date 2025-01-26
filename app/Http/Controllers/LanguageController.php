<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Inertia\Inertia;

class LanguageController extends Controller
{
    public function switch(Request $request) : Redirector|RedirectResponse {

        $language = $request->input('language');

        session(['language' => $language]);

        //cookie('language',$language);
        //dd(cookie('language'));

        return redirect()->back()->withCookie(cookie()->forever('language', $language));
    }
}
