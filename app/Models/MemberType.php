<?php


namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ModelExtender;

class MemberType extends Model 
{
    use SoftDeletes,ModelExtender;
    protected $auto_fillable = ["created_by", "updated_by"];
    protected $table = 'registration_type_of_membership';
    protected $guarded = ['type_of_membership_id'];
    protected $primaryKey = 'type_of_membership_id';

  
}
