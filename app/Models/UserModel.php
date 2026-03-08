<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserModel extends Model
{
    protected $table = 'users';

    protected $primaryKey = 'userid';
    public $incrementing = false;
    protected $keyType = 'char';

    public $timestamps = false;

    protected $fillable = [
        'userid',
        'username',
        'userpass',
        'useremail',
        'useraddress',
        'userphno',
        'deptid',
        'roleid'

    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'roleid', 'roleid');
    }
    public function department()
    {
        return $this->belongsTo(Department::class, 'deptid', 'deptid');
    }
}
