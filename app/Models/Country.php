<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\FileManager\app\Models\File;

class Country extends Model
{
   protected $table = 'countries';

   protected $fillable = [
       'name',
       'flag_symbol',
       'code',
       'flag_id'
   ];

    public function flag(): BelongsTo
    {
         return $this->belongsTo(File::class, 'flag_id');
    }
}
