<?php

namespace App;

// use App\Notifications\VerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
        /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = ['remember_token'];

    protected $table = 'user_credentials';

    protected $primaryKey = 'id_user';

        /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'unique_id','name', 'email', 'password', 'car_type','license_plate_number','activation_token','token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password','activation_token','remember_token'
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    /**
    * @param string|array $roles
    */
    public function authorizeRoles($roles)
    {
    if (is_array($roles)) {
        return $this->hasAnyRole($roles) || 
        // response()->json(['error'=>'This action is unauthorized.',401]);
                abort(401, 'This action is unauthorized.');
    }
    return $this->hasRole($roles) || 
    // response()->json(['error'=>'This action is unauthorized.',401]);
            abort(401, 'This action is unauthorized.');
    }
    /**
    * Check multiple roles
    * @param array $roles
    */
    public function hasAnyRole($roles)
    {
    return null !== $this->roles()->whereIn('role_name', $roles)->first();
    }
    /**
    * Check one role
    * @param string $role
    */
    public function hasRole($role)
    {
    return null !== $this->roles()->where('role_name', $role)->first();
    }
}
