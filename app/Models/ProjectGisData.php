<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectGisData extends Model
{
    protected $table = 'project_gis_data';

    protected $primaryKey = 'gisdataid';

    public $timestamps = false;

    protected $fillable = [
        'projectid',
        'layername',
        'geometry',
        'attributes'
    ];
}