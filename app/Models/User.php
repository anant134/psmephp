<?php


namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;



use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
class User extends Model implements JWTSubject, AuthenticatableContract, AuthorizableContract
{
    use SoftDeletes,ModelExtender;
    protected $auto_fillable = ["created_by", "updated_by"];
    protected $table = 'system_user';
    protected $guarded = ['id'];
    protected $hidden = [
        'password',
    ];
    public function province()
    {
        return $this->belongsTo(Province::class, 'province', 'id');
    }
    public function cites()
    {
        return $this->belongsTo(City::class, 'city', 'id');
    }
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
    public function getAuthIdentifierName(){ return 'id';}
    public function getAuthIdentifier(){return $this->id;
    }
    public function getAuthPassword(){
        return $this->password;
    }
    public function getRememberToken(){$this->remember_token;}
    public function setRememberToken($value){$this->remember_token = $value;}
    public function getRememberTokenName(){ return 'remember_token';}

    public function can($abilities, $arguments = array()){
        return true;
    }
    public function validateCredentials(UserContract $user, array $credentials)
    {
        $plain = $credentials['password'];

        return $this->hasher->check($plain, $user->getAuthPassword());
    }
}
