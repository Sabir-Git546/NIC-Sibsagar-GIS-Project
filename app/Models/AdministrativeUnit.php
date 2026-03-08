<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdministrativeUnit extends Model
{
    protected $table = 'administrative_units';
    
    protected $primaryKey = 'unitid';
    public $incrementing = true;
    protected $keyType = 'int';

    // If you want to use createdat manually, disable timestamps
    public $timestamps = false;

    protected $fillable = [
        'unitname',
        'unittype',
        'parent_unitid',
        'geometry',
    ];

    /**
     * Parent administrative unit
     */
    public function parentUnit()
    {
        return $this->belongsTo(AdministrativeUnit::class, 'parent_unitid', 'unitid');
    }

    /**
     * Child administrative units
     */
    public function childUnits()
    {
        return $this->hasMany(AdministrativeUnit::class, 'parent_unitid', 'unitid');
    }

    /**
     * Projects associated with this administrative unit
     */
    public function projects()
    {
        return $this->hasMany(Project::class, 'location_unitid', 'unitid');
    }

    /**
     * Departments associated with this administrative unit
     */
    public function departments()
    {
        return $this->hasMany(Department::class, 'unitid', 'unitid');
    }
}