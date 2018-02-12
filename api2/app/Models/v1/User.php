<?php  
    namespace App\Models\v1;

    use Illuminate\Database\Eloquent\Model;
    
    use Illuminate\Auth\Authenticatable;
    use Laravel\Lumen\Auth\Authorizable;
    use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
    use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

    class User extends Model implements AuthenticatableContract, AuthorizableContract 
    {

        use Authenticatable, Authorizable;


        public static function findOrfail($id) {
            return $id;
        }
    }
?>