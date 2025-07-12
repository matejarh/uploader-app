<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait HasLogo
{
    /**
     * Update the company's logo.
     *
     * @param  \Illuminate\Http\UploadedFile  $photo
     * @param  string  $storagePath
     * @return void
     */
    public function updateLogo(UploadedFile $photo, $storagePath = 'company-logos')
    {
        tap($this->logo_photo_path, function ($previous) use ($photo, $storagePath) {
            $this->forceFill([
                'logo_photo_path' => $photo->storePublicly(
                    $storagePath, ['disk' => $this->logoPhotoDisk()]
                ),
            ])->save();

            if ($previous) {
                Storage::disk($this->logoPhotoDisk())->delete($previous);
            }
        });
    }

    /**
     * Delete the company's logo.
     *
     * @return void
     */
    public function deleteLogo()
    {
        if (is_null($this->logo_photo_path)) {
            return;
        }

        Storage::disk($this->logoPhotoDisk())->delete($this->logo_photo_path);

        $this->forceFill([
            'logo_photo_path' => null,
        ])->save();
    }

    /**
     * Get the URL to the company's logo.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function logoPhotoUrl(): Attribute
    {
        return Attribute::get(function (): string {
            return $this->logo_photo_path
                    ? Storage::disk($this->logoPhotoDisk())->url($this->logo_photo_path)
                    : $this->defaultLogoUrl();
        });
    }

    /**
     * Get the default logo URL if no logo has been uploaded.
     *
     * @return string
     */
    protected function defaultLogoUrl()
    {
        $name = trim(collect(explode(' ', $this->company_slug))->map(function ($segment) {
            return mb_substr($segment, 0, 1);
        })->join(' '));

        // return 'https://ui-avatars.com/api/?name='.urlencode($name).'&color=7F9CF5&background=EBF4FF';

        return "https://api.dicebear.com/8.x/rings/svg?seed=" . urlencode($this->company_slug) . ""; // icons | pixel-art | ident ...  check https://www.dicebear.com/styles/
    }

    /**
     * Get the disk that logos should be stored on.
     *
     * @return string
     */
    protected function logoPhotoDisk()
    {
        return 'public';
    }
}
