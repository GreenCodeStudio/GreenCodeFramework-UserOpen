<?php

namespace User;

use Authorization\Authorization;
use MKrawczyk\FunQuery\FunQuery;
use stdClass;
use User\Repository\UserPreferencesRepository;

class UserPreferences
{
    public function getAllPossible()
    {
        $root = [];
        $modules = scandir(__DIR__.'/../');
        foreach ($modules as $module) {
            if ($module == '.' || $module == '..') {
                continue;
            }
            $filename = __DIR__.'/../'.$module.'/userPreferences.xml';
            if (is_file($filename)) {
                $xml = simplexml_load_string(file_get_contents($filename));
                foreach ($xml->children() as $element) {
                    $root[] = $this->getAsStdClass($module, $element);
                }
            }
        }
        return $root;
    }

    private function getAsStdClass(string $module, \SimpleXMLElement $element)
    {
        $ret = new \StdClass();
        foreach ($element->children() as $name => $value) {
            if ($name == 'name') {
                $ret->name = $module.'.'.$value->__toString();
            } else if ($name == 'default') {
                $ret->default = $value->__toString();
            } else if ($name == 'select') {
                $ret->select = [];
                foreach ($value->children() as $option) {
                    $opt = new StdClass();
                    $opt->value = $option->__toString();
                    $ret->select[] = $opt;
                }
            } else
                $ret->$name = $value->__toString();
        }
        return $ret;
    }

    public function getByUserId(int $userId)
    {
        $forUser = (new UserPreferencesRepository())->getByUserId($userId);
        $all = $this->getAllPossible();
        foreach ($all as $preference) {
            $preference->value = $forUser[$preference->name]?->value ?? $preference->default;
        }
        return $all;

    }
    public function getByUserIdShort(int $userId){
        return FunQuery::create($this->getByUserId($userId))->toAssocArray(fn($x)=>$x->name, fn($x)=>$x->value);
    }

    public function updatePreference(int $userId, string $name, string $value)
    {
        (new UserPreferencesRepository())->updatePreference($userId, $name, $value);
        (new \Authorization\Authorization())->refreshUserData();
    }
}
