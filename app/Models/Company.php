<?php

namespace App\Models;

use App\Traits\HasLogo;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasLogo;

    protected $fillable = [
        'user_id',
        'company_name',
        'company_address',
    ];


    protected $appends = [
        'logo_photo_url',
        'company_slug',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($company) {
            $company->documents->each->delete();
        });
    }

    /**
     * Get the company slug attribute.
     *
     * @return string
     */
    public function getCompanySlugAttribute()
    {
        return str(explode(',', $this->company_name)[0])->slug('-');
    }

    /**
     * Get the company's documents.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Get the company's user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }


    /**
     * Scope a query to filter companies based on user email, name, file name, company name.
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

                    })->orWhere(function ($query) use ($phrase) {
                        $query->where('company_name', 'like', '%' . $phrase . '%')
                              ->orWhere('company_address', 'like', '%' . $phrase . '%');
                    });
                });
            }
        });
    }


}
