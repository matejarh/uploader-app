<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class DocumentsController extends Controller
{
    /**
     * Display a listing of the documents.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        return $this->documentIndex($request, false);
    }

    public function archiveIndex(Request $request)
    {
        return $this->documentIndex($request, true);
    }

    private function documentIndex(Request $request, bool $showArchived)
    {
        $user = Auth::user();
        $canViewAnyDocuments = $this->canViewAnyDocuments($user);

        abort_unless(
            $canViewAnyDocuments || $user->can('view own documents'),
            403,
            __('Unauthorized action.')
        );

        $query = Document::with('user')->with('company')
            ->where('archived', $showArchived)
            ->filter($request->only(['najdi']));

        if (! $canViewAnyDocuments) {
            $query->where('user_id', $user->id);
        }

        $showArchived
            ? $query->orderByDesc('archived_at')->latest('created_at')
            : $query->latest();

        $documents = $query->paginate(10)->withQueryString()->onEachSide(3);

        return Inertia::render('Documents/Index', [
            'documents' => $documents,
            'filters' => $request->only(['najdi']),
            'archived' => $showArchived,
            'currentUserId' => $user->id,
            'links' => $documents->links('vendor.pagination.tailwind-table', [
                'onEachSide' => 3,
            ])->toHtml(),
            'canDeleteAnyDocuments' => $user->can('delete any document'),
            'canDeleteOwnDocuments' => $user->can('delete own documents'),
            'canArchiveAnyDocuments' => $user->can('archive any document'),
            'canArchiveOwnDocuments' => $user->can('archive own documents'),
            'canRestoreAnyDocuments' => $user->can('restore any document'),
            'canRestoreOwnDocuments' => $user->can('restore own documents'),
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
        if ($this->canViewDocument(Auth::user(), $document)) {
            return Inertia::render('Documents/Show', [
                'document' => $document,
            ]);
        }

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
        if ($this->canViewDocument(Auth::user(), $document)) {
            return Storage::download($document->file_path, $document->file_name);
        }

        abort(403, __('Unauthorized action.'));
    }

    public function archive(Document $document)
    {
        $this->authorizeArchiveAction($document, 'archive any document', 'archive own documents');

        $document->fill([
            'archived' => true,
            'archived_at' => now(),
            'archived_by' => Auth::id(),
        ])->save();

        session()->flash('flash.banner', __('Document archived successfully.'));
        session()->flash('flash.bannerStyle', 'success');

        return back()->with('success', __('Document archived successfully.'));
    }

    public function restore(Document $document)
    {
        $this->authorizeArchiveAction($document, 'restore any document', 'restore own documents');

        $document->fill([
            'archived' => false,
            'archived_at' => null,
            'archived_by' => null,
        ])->save();

        session()->flash('flash.banner', __('Document restored successfully.'));
        session()->flash('flash.bannerStyle', 'success');

        return back()->with('success', __('Document restored successfully.'));
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
            if ($request->has('archived')) {
                abort(422, __('Use the dedicated archive or restore action.'));
            }

            $document->processed = !$document->processed;
            $document->save();

            session()->flash('flash.banner', 'Dokument je bil uspešno posodobljen!');
            session()->flash('flash.bannerStyle', 'success');

            return back()->with('success', 'Document updated successfully.');
        }

        abort(403, __('Unauthorized action.'));
    }

    private function canViewAnyDocuments(User $user): bool
    {
        return $user->can('view any document') || $user->can('view all documents');
    }

    private function canViewDocument(User $user, Document $document): bool
    {
        return $this->canViewAnyDocuments($user)
            || ($user->id === $document->user_id && $user->can('view own documents'));
    }

    private function authorizeArchiveAction(Document $document, string $anyPermission, string $ownPermission): void
    {
        $user = Auth::user();
        $canChangeDocument = $user->can($anyPermission)
            || ($user->id === $document->user_id && $user->can($ownPermission));

        abort_unless(
            $this->canViewDocument($user, $document) && $canChangeDocument,
            403,
            __('Unauthorized action.')
        );
    }
}
