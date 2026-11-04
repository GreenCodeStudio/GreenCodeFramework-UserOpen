<?php

namespace User\Repository;
use Core\Database\DB;
use Core\Repository;
use Exception;


class UserRepository extends Repository
{

    public function __construct()
    {
        $this->archiveMode = static::ArchiveMode_OnlyExisting;
    }

    public function getByUsername(string $username, bool $getSecretData = false)
    {
        if ($getSecretData) {
            return DB::get("SELECT user.id,user.name, user_login_method.login_data_1 as password FROM user 
    JOIN user_contact ON user_contact.user_id = user.id 
               LEFT JOIN user_login_method ON user_login_method.user_id = user.id AND user_login_method.login_method = 'password'
               
               WHERE contact_data = ? ", [$username])[0] ?? null;
        } else {
            return DB::get("SELECT user.id,user.name FROM user JOIN user_contact ON user_contact.user_id = user.id WHERE contact_data = ? ", [$username])[0] ?? null;
        }
    }

    public function defaultTable(): string
    {
        return "user";
    }

    public function insertFull(array $user, array $contact, array $loginMethod)
    {
        try{
            DB::beginTransaction();
            $userId = $this->insert($user);
            $contact['user_id'] = $userId;
            DB::insert('user_contact', $contact);
            $loginMethod['user_id'] = $userId;
            DB::insert('user_login_method', $loginMethod);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    public function getPermissions(int $userId)
    {
        return [];
    }
}
