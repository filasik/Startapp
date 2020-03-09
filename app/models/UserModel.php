<?php

namespace app\models;


use Tracy\Debugger;

/**
 * Model pro obsluhu uzivatelů aplikace
 *
 * Class UserModel
 * @package app\models
 */
class UserModel extends BaseModel {


    public const TABLE_USER_NAME = 'user';
    public const TABLE_ROLE_NAME = 'role';
    public const TABLE_USER_JOIN_ROLE_NAME = 'user_role';


    /**
     * Vrátí všechny uživatale v poli
     *
     * @return array|null
     * @throws \Exception
     */
    public function getAllUsers(): ?array
    {
        $sql = "SELECT * FROM ".self::TABLE_USER_NAME;
        $this->getDb()->executeQuery($sql);
        return $this->getDb()->getRows();
    }


    /**
     * Vrátí uživatele podle jeho ID
     *
     * @param $id
     * @return array|null
     * @throws \Exception
     */
    public function getUserById($id): ?array
    {
        $id = (int) $this->getDb()->sanitizeData($id);
        $sql = "SELECT * FROM ".self::TABLE_USER_NAME." WHERE id = $id";
        $this->getDb()->executeQuery($sql);
        return $this->getDb()->getRows();
    }


    /**
     * Vrati uzivatele podle emailu
     *
     * @param $email
     * @return array|null
     * @throws \Exception
     */
    public function getUserByEmail($email): ?array
    {
        $email = $this->getDb()->sanitizeData($email);
        $sql = "SELECT * FROM ".self::TABLE_USER_NAME." WHERE email = '$email'";
        $this->getDb()->executeQuery($sql);
        return $this->getDb()->getRows();
    }


    /**
     * Vrati vsechny role uzivatele
     *
     * @param $id
     * @return array|null
     * @throws \Exception
     */
    public function getUserRolesByUserId($id): ?array
    {
        $sql = "SELECT role, role_name
                FROM ".self::TABLE_ROLE_NAME."
                INNER JOIN ".self::TABLE_USER_JOIN_ROLE_NAME." ON id = role_id
                WHERE user_id = $id";
        $this->getDb()->executeQuery($sql);
        return $this->getDb()->getRows();
    }



    /**
     * Uloží nového uživatele
     *
     * @param array $data
     * @return mixed
     */
    public function save($data)
    {
        $this->getDb()->insertRecords(self::TABLE_USER_NAME, $data);
        return $this->getDb()->getLastInsertID();
    }


    /**
     * Odstraní uživatele
     *
     * @param int $id
     * @return bool|int
     * @throws \Exception
     */
    public function delete($id) 
    {
        $cond = "id = ".$id;
        return $this->getDb()->deleteRecords(self::TABLE_USER_NAME, $cond, 1);
    }


    /**
     * Upraví záznam uživatele v databázi
     *
     * @param int $id
     * @param array $data
     * @return bool|int
     * @throws \Exception
     */
    public function update($id, $data) 
    {
        $cond = "id = ".$id;
        return $this->getDb()->updateRecords(self::TABLE_USER_NAME, $data, $cond);
    }
}
