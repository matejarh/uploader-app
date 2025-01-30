<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use App\Notifications\FileUploaded;

class FileUploadController extends Controller
{
    /**
     * Handle the file upload request.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function upload(Request $request)
    {
        $this->validateRequest($request);
        $userFolder = $this->getUserFolder();
        $file = $request->file('file');
        $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $file->getClientOriginalName());
        // $fileName = $file->getClientOriginalName();
        $filePath = "$userFolder/$fileName";

        if ($this->fileExists($file, $userFolder)) {
            return Redirect::back()->withErrors(['file' => __('A file with the same content already exists in storage.')]);
        }

        $storedFilePath = $this->storeFile($file, $userFolder, $fileName, $request->folder);
        $document = $this->saveFileRecord($file, $storedFilePath, $request->folder);

        $this->notifyAdmin($document);

        session()->flash('flash.banner', __('Document uploaded successfully!'));
        session()->flash('flash.bannerStyle', 'success');

        return Redirect::back()->with([
            'success' => true,
            'file_path' => $storedFilePath,
        ]);
    }

    /**
     * Validate the upload request.
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    protected function validateRequest(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:pdf,doc,docx|max:2048',
        ]);
    }

    /**
     * Get the user's folder path.
     *
     * @return string
     */
    protected function getUserFolder()
    {
        return 'dokumenti/' . auth()->user()->email;
    }

    /**
     * Check if a file with the same hash already exists in the user's folder.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $userFolder
     * @return bool
     */
    protected function fileExists($file, $userFolder)
    {
        $fileHash = hash_file('sha256', $file->getRealPath());
        $existingFiles = Storage::files($userFolder);

        foreach ($existingFiles as $existingFile) {
            if (hash_file('sha256', Storage::path($existingFile)) === $fileHash) {
                return true;
            }
        }

        return false;
    }

    /**
     * Store the uploaded file in the specified folder.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $userFolder
     * @param string $fileName
     * @return string
     */
    protected function storeFile($file, $userFolder, $fileName, $folder = 'inbox')
    {
        return $file->storeAs("$userFolder/$folder", $fileName, 'local');
    }

    /**
     * Save the file record to the database.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $storedFilePath
     * @return \App\Models\Document
     */
    protected function saveFileRecord($file, $storedFilePath, $folder = 'inbox')
    {
        return Document::create([
            'user_id' => auth()->id(),
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $storedFilePath,
            'file_mime_type' => $file->getMimeType(),
            'folder' => $folder,
        ]);
    }

    /**
     * Notify the admin about the uploaded file.
     *
     * @param \App\Models\Document $document
     * @return void
     */
    protected function notifyAdmin($document)
    {
        Notification::route('mail', env('ADMIN_EMAIL', 'matej.arh@gmail.com'))->notify(new FileUploaded($document));
    }
}
