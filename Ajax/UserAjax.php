<?php

namespace User\Ajax;

class UserAjax extends \Core\AjaxController
{
    public function register($username, $password)
    {
        $user = new \User\User();
        $user->registerClassic($username, $password);
        \Authorization\Authorization::login($username, $password);

    }
}
