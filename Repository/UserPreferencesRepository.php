<?php
namespace User\Repository;
use Core\Database\DB;

class UserPreferencesRepository
{

    public function getByUserId(int $userId)
    {
        return DB::funquery("SELECT * FROM user_preferences WHERE id_user = ?", [$userId])->toAssocArray(fn($x)=>$x->name);
    }

    public function updatePreference(int $userId, string $name, string $value)
    {
        DB::query("INSERT INTO user_preferences (id_user, name, value, changed) VALUES (?, ?, ?, NOW()) ON DUPLICATE KEY UPDATE value = ?", [$userId, $name, $value, $value]);
    }
}