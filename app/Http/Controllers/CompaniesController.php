<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class CompaniesController extends Controller
{
    public function __construct()
    {
        $this->middleware = [
            'index' => ['permission:view any company'],
            'show' => ['permission:view own companies', 'permission:view any company'],
            'store' => ['permission:view own companies', 'permission:view any company'],
            'update' => ['permission:view own companies', 'permission:view any company'],
            'destroy' => ['permission:delete any company', 'permission:delete own companies'],
        ];
    }

    /**
     * Display a listing of the companies.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        if (Auth::user()->can('view any company') || Auth::user()->can('view own companies')) {
            $query = Company::with('user')->filter($request->only(['najdi']))->latest();

            // Restrict query to the authenticated user's documents if they don't have permission to view all documents
            if (!Auth::user()->can('view all companies')) {
                $query->where('user_id', Auth::id());
            }

            $companies = $query->paginate(10)->onEachSide(3);

            return Inertia::render('Companies/Index', [
                'companies' => $companies,
                'filters' => $request->only(['najdi']),
                'links' => $companies->links('vendor.pagination.tailwind-table', [
                    'onEachSide' => 3,
                ])->toHtml(),
            ]);
        }

        abort(403, __('Unauthorized action.'));
    }

    /**
     * Get a list of companies for the current user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserCompanies()
    {

        if (Auth::user()->hasRole(['admin', 'super-admin'])) {
            $companies = Company::select('id', 'company_name')->get();
        } else {
            $companies = Company::where('user_id', Auth::id())->select('id', 'company_name')->get();
        }

        return response()->json($companies);
    }

    /**
     * Display the specified company.
     *
     * @param \App\Models\Company $company
     * @return \Inertia\Response
     */
    public function show(Company $company)
    {
        if (Auth::user()->can('view any company') || Auth::user()->id === $company->user_id) {
            return Inertia::render('Companies/Show', [
                'company' => $company,
            ]);
        }

        abort(403, __('Unauthorized action.'));
    }

    /**
     * Store a newly created company in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'company_name' => ['required', 'string', 'max:255', 'unique:companies'],
            'company_address' => ['nullable', 'string', 'max:255'],
            /* 'company_city' => ['required', 'string', 'max:255'],
            'company_country' => ['required', 'string', 'max:255'],
            'company_vat' => ['required', 'string', 'max:255'],
            'company_phone' => ['required', 'string', 'max:255'],
            'company_email' => ['required', 'string', 'email', 'max:255'], */
        ]);

        Company::create([
            'company_name' => $request->company_name,
            'company_address' => $request->company_address,
            /* 'company_city' => $request->company_city,
            'company_country' => $request->company_country,
            'company_vat' => $request->company_vat,
            'company_phone' => $request->company_phone,
            'company_email' => $request->company_email, */
            'user_id' => $request->user_id ?: Auth::id(),
        ]);

        return redirect()->route('companies.index');
    }

    /**
     * Update the specified company in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Company $company
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255', Rule::unique('companies')->ignore($company->id)],
            'company_address' => ['nullable', 'string', 'max:255'],
            'photo' => ['nullable', 'mimes:jpg,jpeg,png,svg', 'max:2048'],
            /* 'company_city' => ['required', 'string', 'max:255'],
            'company_country' => ['required', 'string', 'max:255'],
            'company_vat' => ['required', 'string', 'max:255'],
            'company_phone' => ['required', 'string', 'max:255'],
            'company_email' => ['required', 'string', 'email', 'max:255'], */
        ]);

        // dd($validated);
        if (isset($request->photo)) {
            $company->updateLogo($request->photo);
        }

        $company->update([
            'company_name' => $request->company_name,
            'company_address' => $request->company_address,
            /* 'company_city' => $request->company_city,
            'company_country' => $request->company_country,
            'company_vat' => $request->company_vat,
            'company_phone' => $request->company_phone,
            'company_email' => $request->company_email, */
        ]);

        return redirect()->route('companies.index');
    }

    /**
     * Remove the specified company from storage.
     *
     * @param \App\Models\Company $company
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Company $company)
    {
        if (Auth::user()->can('delete any company') || Auth::user()->id === $company->user_id) {
            $company->deleteLogo();
            $company->delete();

            return redirect()->route('companies.index');
        }

        abort(403, __('Unauthorized action.'));
    }

    /**
     * Remove specific company logo.
     *
     * @param \App\Models\Company $company
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyLogo(Company $company)
    {
        if (Auth::user()->can('delete any company') || Auth::user()->id === $company->user_id) {
            $company->deleteLogo();

            return redirect()->route('companies.index');
        }

        abort(403, __('Unauthorized action.'));
    }
}
