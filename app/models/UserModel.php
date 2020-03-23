<?php

namespace app\models;


use app\conf\Config;
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


    public static $profilFormStates = [
        0 => 'Hesla se neshodují',
        1 => 'Email je již používán jiným účtem, zvolte prosím jiný',
        2 => 'Zadaný tajný klíč není platný'
    ];


    /**
     * Vrátí všechny uživatale v poli
     * Metoda nevraci role uzivatele
     * Uměli bychom doplnit tento prikaz aby vracel take role?
     * Třeba oddelene čarkou? GROUP_CONCAT()??
     *
     * @return array|null
     * @throws \Exception
     */
    public function getAllUsers(): ?array
    {
        $sql = "SELECT * FROM ".self::TABLE_USER_NAME;
        $this->getDb()->executeQuery($sql);
        $data = [];
        while($row = $this->getDb()->getRows()) {
            $data[] = $row;
        }
        return $data;
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
        $data = [];
        while($row = $this->getDb()->getRows()) {
            $data[] = $row;
        }
        return $data;
    }


    /**
     * Uloží nového uživatele/roli/vazbu_na_roli
     *
     * @param array $data
     * @param string $table
     * @return mixed
     */
    public function save($data, $table = self::TABLE_USER_NAME)
    {
        $this->getDb()->insertRecords($table, $data);
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


    /**
     * Metoda pro overeni dat profiloveho formulare
     *
     * @param array $data
     * @return bool|mixed
     * @throws \Exception
     */
    public function verifyProfilFormData(array $data)
    {
        $isRegistration = $data['profilMode'] === Config::PROFIL_FORM_REG_MODE;
        // Dvojí konstrola znamená, že ikdyž jsem zkontroloval shodu hesel pomocí javascriptu u klienta
        // Provedu stejnou kontrolu na straně serveru
        // Hesla jsou u registrace povinná u zmeny profilu volitelná
        if ($data['profilPass'] && $data['profilPass'] !== $data['profilPassConfirm']) {
            return 0;
        }

        // Kontrola emailu, který musí být v db jedinečný
        // 1. Hledán uzivatele se stejným emailem
        $user = $this->getUserByEmail($data['profilEmail']);

        // 2. Uzivatel s takovým emailem jiz existuje
        if ($user) {
            // 3. Pokud je to registrace - je to automaticky chyba
            if ($isRegistration) {
                return 1;
            }

            // 4. Pokud je to uprava profilu, chyba nastava v tom pripade, ze si uzivatel meni email na email,
            // ktery je ale obsazeny nekym jinym
            if ((int) $data['userId'] !== (int) $user['id']) {
                return 1;
            }
        }

        // Kontrola tajneho klice, ktery se zadava jen u registrace
        if ($isRegistration) {
            // Dvoji kontrola - bud je to pedagog - ti maji vsichni stejny klic
            // Nebo je to rodic a ti maji klic pridelen u svych deti
            // Zatim si zpracujeme pouze pedagogy
            // @todo: implementovat registraci rodice az budeme mit prislusnou strukturu db pro deti
            if ($data['profilVerifyCode'] !== Config::PEDAGOG_VERIFY_CODE_VALUE) {
                return 2;
            }
        }

        // Vsechno je v cajku, metoda vraci true
        return true;
    }

}
