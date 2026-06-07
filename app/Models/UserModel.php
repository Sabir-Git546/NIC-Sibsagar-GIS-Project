<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class UserModel extends Authenticatable
{
    protected $table = 'users';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'userid',
        'username',
        'password',
        'email',
        'deptid',
        'roleid'
    ];

    protected $hidden = [
        'password',
    ];

    // Tell Laravel which column stores hashed password
    public function getAuthPassword()
    {
        return $this->password;
    }

    // ROLE RELATION
    public function role()
    {
        return $this->belongsTo(Role::class, 'roleid', 'roleid');
    }

    // DEPARTMENT RELATION
    public function department()
    {
        return $this->belongsTo(Department::class, 'deptid', 'deptid');
    }
}