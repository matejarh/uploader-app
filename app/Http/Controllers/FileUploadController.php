<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use App\Models\Document; // Updated model for file records
use Illuminate\Support\Facades\Notification;
use App\Notifications\FileUploaded; // Notification class

class FileUploadController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:pdf,doc,docx|max:2048',
        ]);

        $userFolder = 'dokumenti/' . auth()->user()->email;
        $file = $request->file('file');
        $fileName = $file->getClientOriginalName();
        $filePath = $userFolder . '/' . $fileName;

        // Calculate the hash of the uploaded file
        $fileHash = hash_file('sha256', $file->getRealPath());

        // Check if a file with the same hash already exists in the user's folder
        $existingFiles = Storage::files($userFolder);
        foreach ($existingFiles as $existingFile) {
            if (hash_file('sha256', Storage::path($existingFile)) === $fileHash) {
                return Redirect::back()->withErrors(['file' => 'Datoteka z enako vsebino že obstaja v shrambi.']);
            }
        }

        $storedFilePath = $file->storeAs($userFolder, $fileName);
        $fileMimeType = $file->getMimeType();

        // Save file record to database
        $document = Document::create([
            'user_id' => auth()->id(),
            'file_name' => $fileName,
            'file_path' => $storedFilePath,
            'file_mime_type' => $fileMimeType,
        ]);

        // Send notification to admin
        Notification::route('mail', 'admin@example.com')->notify(new FileUploaded($document));

        session()->flash('flash.banner', 'Dokument je bil uspešno naložen!');
        session()->flash('flash.bannerStyle', 'success');

        return Redirect::back()->with([
            'success' => true,
            'file_path' => $storedFilePath,
        ]);
    }
}
