<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __construct()
    {
        // Define middleware for permissions
        $this->middleware = [
            'index' => ['permission:view all documents'],
            'show' => ['permission:view own documents'],
            'destroy' => ['permission:delete any document', 'permission:delete own documents'],
        ];
    }

    /**
     * Show the dashboard.
     *
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $query = Document::latest();

        // Restrict query to the authenticated user's documents if they don't have permission to view all documents
        if (!Auth::user()->can('view all documents')) {
            $query->where('user_id', Auth::id());
        }

        $latestDocuments = $query->take(5)->get();

        return Inertia::render('Dashboard', [
            'latestDocuments' => $latestDocuments,
        ]);
    }
}
