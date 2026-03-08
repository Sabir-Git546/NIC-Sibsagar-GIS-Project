<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles'; 

    protected $primaryKey = 'roleid';
    public $incrementing = false;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'roleid',
        'rolename',
        'roledescription',
        'permissionid'

    ];

    public function users()
    {
        return $this->hasMany(User::class, 'roleid', 'roleid');
    }
}