<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quote extends Model
{

    use SoftDeletes;

    protected $fillable = [
        'quote', 'child_id', 'backgr_with_quote', 'preset_background_id',
    ];

    public function presetBackground() {
        return $this->belongsTo('App\PresetBackground');
    }

    public function child() {
        return $this->belongsTo('App\Child');
    }
}
