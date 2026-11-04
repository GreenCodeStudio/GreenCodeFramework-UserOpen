<?php

namespace User;
use User\Repository\UserRepository;

class User
{

    public function registerClassic($username, $password)
    {
        new UserRepository()->insertFull(
            ['name'=>$username,'createdStamp'=>date('Y-m-d H:i:s')],
            ['contact_type'=>'email','contact_data'=>$username],
            ['login_method'=>'password', 'login_data_1'=>password_hash($password,PASSWORD_BCRYPT)]
    );
    }
}
