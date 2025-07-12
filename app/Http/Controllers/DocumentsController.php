<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class DocumentsController extends Controller
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
     * Display a listing of the documents.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        // Create a query to filter and sort documents
        $query = Document::with('user')->with('company')->filter($request->only(['najdi']))->latest();

        // Restrict query to the authenticated user's documents if they don't have permission to view all documents
        if (!Auth::user()->can('view all documents')) {
            $query->where('user_id', Auth::id());
        }

        // Paginate the results
        $documents = $query->paginate(10)->onEachSide(3);

        // Render the documents index view with the documents and filters
        return Inertia::render('Documents/Index', [
            'documents' => $documents,
            'filters' => $request->only(['najdi']),
            'links' => $documents->links('vendor.pagination.tailwind-table', [
                'onEachSide' => 3,
            ])->toHtml(),
            'canDeleteDocuments' => Auth::user()->can('delete any document'),
            'canDeleteOwnDocuments' => Auth::user()->can('delete own documents'),
        ]);
    }

    /**
     * Display the specified document.
     *
     * @param \App\Models\Document $document
     * @return \Inertia\Response|\Illuminate\Http\Response
     */
    public function show(Document $document)
    {
        // Check if the authenticated user can view the document
        if (Auth::user()->can('view all documents') || (Auth::user()->id === $document->user_id && Auth::user()->can('view own documents'))) {
            return Inertia::render('Documents/Show', [
                'document' => $document,
            ]);
        }

        // Abort with a 403 status if the user is not authorized
        abort(403, __('Unauthorized action.'));
    }

    /**
     * Download the specified document.
     *
     * @param \App\Models\Document $document
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|\Illuminate\Http\Response
     */
    public function download(Document $document)
    {
        // Check if the authenticated user can download the document
        if (Auth::user()->can('view all documents') || (Auth::user()->id === $document->user_id && Auth::user()->can('view own documents'))) {
            return Storage::download($document->file_path, $document->file_name);
        }

        // Abort with a 403 status if the user is not authorized
        abort(403, __('Unauthorized action.'));
    }

    /**
     * Remove the specified document from storage.
     *
     * @param \App\Models\Document $document
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function destroy(Document $document)
    {
        // Check if the authenticated user can delete the document
        if (Auth::user()->can('delete any document') || (Auth::user()->id === $document->user_id && Auth::user()->can('delete own documents'))) {
            // Delete the file from storage
            Storage::delete($document->file_path);
            // Delete the document record from the database
            $document->delete();

            session()->flash('flash.banner', 'Dokument je bil uspešno pobrisan!');
            session()->flash('flash.bannerStyle', 'success');

            // Redirect to the documents index with a success message
            return redirect()->route('documents.index')->with('success', 'Document deleted successfully.');
        }

        // Abort with a 403 status if the user is not authorized
        abort(403, __('Unauthorized action.'));
    }

    /**
     * Update the specified document in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Document $document
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Document $document)
    {
        // Check if the authenticated user can update the document
        if (Auth::user()->can('update any document') || (Auth::user()->id === $document->user_id && Auth::user()->can('update own documents'))) {
            // Update the document record with the request data
            /* $document->update($request->all()); */
            $document->processed = !$document->processed;
            $document->save();

            session()->flash('flash.banner', 'Dokument je bil uspešno posodobljen!');
            session()->flash('flash.bannerStyle', 'success');

            // Redirect to the document show page with a success message
            // return redirect()->route('documents.index', $document)->with('success', 'Document updated successfully.');
            return back()->with('success', 'Document updated successfully.');
        }

        // Abort with a 403 status if the user is not authorized
        abort(403, __('Unauthorized action.'));
    }
}
