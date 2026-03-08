<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $table = 'projects';

    protected $primaryKey = 'projectid';
    public $incrementing = True;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
    'projectname',
    'description',
    'status',
    'deptid',
    'location_unitid',
    'createdby'
];

    public function department()
    {
        return $this->belongsTo(Department::class, 'deptid', 'deptid');
    }

    public function locationUnit()
    {
        return $this->belongsTo(AdministrativeUnit::class, 'location_unitid', 'unitid');
    }

    
}
