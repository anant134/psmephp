<?php


namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ModelExtender;

class RegistrationType extends Model 
{
    use SoftDeletes,ModelExtender;
    protected $auto_fillable = ["created_by", "updated_by"];
    protected $table = 'registration_temp_personal_information';
    protected $guarded = ['professional_credentials_id'];

  
}
