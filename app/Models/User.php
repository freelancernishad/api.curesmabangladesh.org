<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'mobile',
        'blood_group',
        'email',
        'gander',
        'gardiant_phone',
        'last_donate_date',
        'whatsapp_number',
        'division',
        'district',
        'thana',
        'union',
        'org',
        'password',
        'role',
        'role_id',
        'fullName',
        'relationship',
        'diagnosedForSMA',
        'symptoms',
        'typeOfSMA',
        'doctorName',
        'fatherMobile',
        'motherMobile',
        'emergencyContact',
        'presentAddress',
        'permanentAddress',
        'agreement',
        'dateOfBirth',
        'annual_cost',
        'total_cost',
        'donate_for',  // Added this line
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'diagnosedForSMA' => 'boolean',
        'symptoms' => 'boolean',
        'agreement' => 'boolean',
        'dateOfBirth' => 'date',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'org');
    }

    // Required method from JWTSubject
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    // Required method from JWTSubject
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function roles()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function permissions()
    {
        return $this->hasManyThrough(
            Permission::class,
            'role_permission', // Pivot table name
            'user_id',         // Foreign key on the pivot table related to the User model
            'role_id',         // Foreign key on the pivot table related to the Permission model
            'id',              // Local key on the User model
            'role_id'          // Local key on the pivot table related to the Permission model
        );
    }

    public function hasRole($role)
    {
        return $this->roles()->where('name', $role)->exists();
    }

    public function hasPermission($routeName)
    {
        // Get the user's roles with eager loaded permissions
        $permissions = $this->roles()->with('permissions')
            ->get()
            ->pluck('permissions')
            ->flatten();

        // Check if any of the user's permissions match the provided route name and permission name
        $checkPermission = $permissions->contains(function ($permission) use ($routeName) {
            return true;
            // Log:info($permission->name === $routeName && $permission->permission);
            // return $permission->path === $routeName && $permission->permission;
        });

        return $checkPermission;
    }


    public function user()
    {
        return $this->belongsTo(User::class, 'donate_for');
    }

}
