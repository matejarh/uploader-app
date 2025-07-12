<?php

namespace App\Http\Controllers;

use App\Models\Company;
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

        $userFolder = $this->getUserFolder($request->company_id);
        $yearFolder = $this->getYearFolder();
        $file = $request->file('file');
        $fileName = $file->getClientOriginalName();

        if ($this->fileExists($file, $userFolder, $yearFolder)) {
            return Redirect::back()->withErrors(['file' => __('A file with the same content already exists in storage.')]);
        }

        $storedFilePath = $this->storeFile($file, $userFolder, $yearFolder, $fileName, $request->folder);
        $document = $this->saveFileRecord($file, $storedFilePath, $request->folder, $request->company_id);

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
            'file' => [
                'required',
                'mimes:pdf,doc,docx,xls,xlsx,csv,txt,xml',
                'max:2048',
                function ($attribute, $value, $fail) use ($request) {
                    $userFolder = $this->getUserFolder($request->company_id);
                    $yearFolder = $this->getYearFolder();
                    $fileName = $request->file('file')->getClientOriginalName();
                    if (Storage::exists("$userFolder/$yearFolder/$fileName")) {
                        $fail(__('A file with the same name already exists in your folder.'));
                    }
                },
            ],
            'folder' => ['required', 'in:prejeti,izdani,trr,kadrovske zadeve,pogodbe,ostalo'],
            'company_id' => ['required', 'exists:companies,id'],
        ]);
    }

    /**
     * Get the user's folder path.
     *
     * @return string
     */
    protected function getUserFolder($companyId)
    {
        $company_slug = Company::find($companyId)->company_slug;
        return 'dokumenti/' . $company_slug;
        // return 'dokumenti/' . auth()->user()->company_slug;
    }

    /**
     * Get the current year folder path.
     *
     * @return string
     */
    protected function getYearFolder()
    {
        return "Leto " . date('Y');
    }

    /**
     * Check if a file with the same hash already exists in the user's folder or any subfolder.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $userFolder
     * @param string $yearFolder
     * @return bool
     */
    protected function fileExists($file, $userFolder, $yearFolder)
    {
        $fileHash = hash_file('sha256', $file->getRealPath());
        $existingFiles = Storage::allFiles($userFolder);

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
     * @param string $yearFolder
     * @param string $fileName
     * @param string $folder
     * @return string
     */
    protected function storeFile($file, $userFolder, $yearFolder, $fileName, $folder)
    {
        return $file->storeAs("$userFolder/$yearFolder/$folder", $fileName, 'local');
    }

    /**
     * Save the file record to the database.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $storedFilePath
     * @param string $folder
     * @return \App\Models\Document
     */
    protected function saveFileRecord($file, $storedFilePath, $folder, $companyId)
    {
        $company = Company::find($companyId);

        if (auth()->user()->hasRole(['admin', 'super-admin'])) {
            $user_id = $company->user_id;
        } else {
            $user_id = auth()->id();
        }

        return Document::create([
            'user_id' => $user_id,
            'company_id' => $companyId,
            'file_name' => $file->getClientOriginalName(),
            'year' => $this->getYearFolder(),
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
        Notification::route('mail', config('app.admin_email'))->notify(new FileUploaded($document));
    }
}
