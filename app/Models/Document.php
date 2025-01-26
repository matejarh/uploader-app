<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    protected $fillable = [
        'user_id',
        'key',
        'file_name',
        'file_path',
        'file_mime_type',
        'folder',
    ];

    protected $appends = [
        'file_url',
        'human_readable_created_at',
    ];

    public function getRouteKeyName()
    {
        return 'key';
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($document) {
            $document->key = hash('sha256', time() . $document->file_name);
        });
    }

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
                $query->where('file_name', 'like', '%' . $najdi . '%')->orWhere('folder', 'like', '%' . $najdi . '%');
            });
        });
    }
}
