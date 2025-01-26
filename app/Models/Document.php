<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    protected $fillable = [
        'user_id',
        'file_name',
        'file_path',
        'file_mime_type',
    ];

    protected $appends = [
        'file_url',
        'human_readable_created_at',
    ];

    /**
     * Get the user that owns the document.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the URL of the file.
     *
     * @return string
     */
    public function getFileUrlAttribute()
    {
        return Storage::url($this->file_path);
    }

    /**
     * Get the created_at attribute in a human-readable format.
     *
     * @return string
     */
    public function getHumanReadableCreatedAtAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Scope a query to filter documents based on user email, name, or file name.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $filters
     * @return void
     */
    public function scopeFilter($query, array $filters): void
    {
        $query->when($filters['najdi'] ?? null, function ($query, $najdi) {
            $najdi = str($najdi)->squish();
            $query->whereHas('user', function ($query) use ($najdi) {
                $query->where('email', 'like', '%' . $najdi . '%')
                      ->orWhere('name', 'like', '%' . $najdi . '%');
            })->orWhere(function ($query) use ($najdi) {
                $query->where('file_name', 'like', '%' . $najdi . '%');
            });
        });
    }
}
