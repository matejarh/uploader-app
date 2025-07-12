<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasRoles, HasFactory, HasProfilePhoto, Notifiable, TwoFactorAuthenticatable;

    protected $guard_name = 'web';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'company_name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
        'human_readable_created_at',
        'company_slug',
        'companies_count',
    ];

    protected $with = ['roles'];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($user) {
            // Delete profile photo
            if ($user->profile_photo_path) {
                Storage::delete($user->profile_photo_path);
            }

            // Delete all documents and their files
            foreach ($user->documents as $document) {
                Storage::delete($document->file_path);
                $document->delete();
            }

            // Delete all companies
            foreach ($user->companies as $company) {
                $company->delete();
            }
        });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected function defaultProfilePhotoUrl()
    {
        return "https://api.dicebear.com/8.x/rings/svg?seed=" . urlencode($this->name) . ""; // icons | pixel-art | ident ...  check https://www.dicebear.com/styles/
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
     * Get users companies count
     */
    public function getCompaniesCountAttribute()
    {
        return $this->companies->count();
    }

    /**
     * Get the user's documents.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Get the user's companies.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function companies()
    {
        return $this->hasMany(Company::class);
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
     * Scope a query to filter users based on search phrases.
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
                    $query->where('name', 'like', '%' . $phrase . '%')
                          ->orWhere('email', 'like', '%' . $phrase . '%');
                          //->orWhere('company_name', 'like', '%' . $phrase . '%');
                })->orWhereHas('companies', function ($query) use ($phrase) {
                    $query->where('company_name', 'like', '%' . $phrase . '%');
                });
            }
        });
    }
}
