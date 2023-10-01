<?php


namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class MemberRegistrationLog extends Model 
{
    use SoftDeletes,ModelExtender;
    protected $auto_fillable = ["created_by", "updated_by"];
    protected $table = 'member_registration_log';
    protected $guarded = ['id'];
  
}
