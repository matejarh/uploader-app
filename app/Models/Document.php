<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    protected $fillable = [
        'user_id',
        'company_id',
        'key',
        'file_name',
        'year',
        'file_path',
        'file_mime_type',
        'folder',
    ];

    protected $appends = [
        'file_url',
        'human_readable_created_at',
        'formated_date',
    ];

    protected $casts = [
        'processed' => 'boolean',
    ];

    protected $with = ['company'];

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

        static::deleting(function ($document) {
            Storage::delete($document->file_path);
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
     * Get the company that owns the document.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
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

    public function getFormatedDateAttribute()
    {
        return $this->created_at->format('d.m.Y');
    }

    /**
     * Scope a query to filter documents based on user email, name, file name, company name or year.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $filters
     * @return void
     */
    public function scopeFilter($query, array $filters): void
    {
        $query->when($filters['najdi'] ?? null, function ($query, $najdi) {
            $phrases = explode('+', $najdi);
            foreach ($phrases as $phrase) {
                $phrase = str($phrase)->squish();
                $query->where(function ($query) use ($phrase) {
                    $query->whereHas('user', function ($query) use ($phrase) {
                        $query->where('email', 'like', '%' . $phrase . '%')
                              ->orWhere('name', 'like', '%' . $phrase . '%')
                              ->orWhere('company_name', 'like', '%' . $phrase . '%');
                    })->orWhereHas('company', function ($query) use ($phrase) {
                        $query->where('company_name', 'like', '%' . $phrase . '%');
                    })->orWhere(function ($query) use ($phrase) {
                        $query->where('file_name', 'like', '%' . $phrase . '%')
                              ->orWhere('year', 'like', '%' . $phrase . '%')
                              ->orWhere('folder', 'like', '%' . $phrase . '%');
                    });
                });
            }
        });
    }
}
