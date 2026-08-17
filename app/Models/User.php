<?php

namespace App\Models;

use App\Models\Concerns\HasRoles;
use App\Notifications\ResetPasswordNotification;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    use HasRoles;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'profile_photo_path',
        'profile_photo_thumbnail_path',
        'password',
        'is_active',
        'must_change_password',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @var list<string>
     */
    protected $appends = [
        'full_name',
        'profile_photo_url',
        'profile_photo_thumbnail_url',
        'status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'must_change_password' => 'boolean',
        ];
    }

    /**
     * The user's lifecycle status, derived from `is_active` and
     * `must_change_password`: an invited user stays "pending" until they
     * log in with their one-time password and set their own, an admin can
     * deactivate an account at any time to force "inactive", and otherwise
     * the account is "active".
     *
     * @return Attribute<string, never>
     */
    protected function status(): Attribute
    {
        return Attribute::get(function (): string {
            if (! $this->is_active) {
                return 'inactive';
            }

            if ($this->must_change_password) {
                return 'pending';
            }

            return 'active';
        });
    }

    /**
     * @param  string  $token
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * @return Attribute<string, never>
     */
    protected function fullName(): Attribute
    {
        return Attribute::get(
            fn (): string => trim($this->first_name.' '.$this->last_name),
        );
    }

    /**
     * @return Attribute<string, never>
     */
    protected function name(): Attribute
    {
        return Attribute::get(fn (): string => $this->full_name);
    }

    /**
     * @return Attribute<?string, never>
     */
    protected function profilePhotoUrl(): Attribute
    {
        return Attribute::get(
            fn (): ?string => $this->profile_photo_path
                ? Storage::disk(config('media.profile_photo.disk'))->url($this->profile_photo_path)
                : null,
        );
    }

    /**
     * @return Attribute<?string, never>
     */
    protected function profilePhotoThumbnailUrl(): Attribute
    {
        return Attribute::get(
            fn (): ?string => $this->profile_photo_thumbnail_path
                ? Storage::disk(config('media.profile_photo.disk'))->url($this->profile_photo_thumbnail_path)
                : null,
        );
    }
}
