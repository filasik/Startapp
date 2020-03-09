<?php

namespace phpboom\security;

use app\models\UserModel;


/**
 * Trida Uzivatele aplikace (#user)
 * Pokud mame aplikaci s uzivatelskou zonou, je potreba podobne implementace
 *
 * Class User
 * @package phpboom\security
 */
class User {

    private const SESSION_BASE_KEY = 'phpboom_user_session';
    private const SESSION_ACTIVITY_KEY = 'phpboom_last_activity';

    public const MAX_TIME_USER_INACTIVITY = '-30 minutes';


    /** @var array */
    private $data = [];

    /** @var array */
    private $roles = [];

    /** @var bool */
    private $isLogged = false;

    /** @var UserModel */
    private $userModel;


    /**
     * User constructor.
     * @param UserModel $userModel
     * @throws \Exception
     */
    public function __construct(UserModel $userModel)
    {
        $this->userModel = $userModel;
        $this->setUserSession();
    }


    /**
     * Metoda nastaveni sezeni (Session)
     * A: https://www.php.net/manual/en/reserved.variables.session.php
     *
     * @param array $data
     * @return bool
     * @throws \Exception
     */
    private function setUserSession(array $data = []): bool
    {
        // Nastartování Session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Je uz uzivatel prihlasen?
        if (!$data && isset($_SESSION[self::SESSION_BASE_KEY])) {
            $data = $_SESSION[self::SESSION_BASE_KEY];

            // Kontroluju, jestli posledni aktivita nepresahla povoleny casovy limit
            $checkActivity = new \DateTime(self::MAX_TIME_USER_INACTIVITY);
            $lastActivity = $data[self::SESSION_ACTIVITY_KEY];
            // Limit presahl stanovenou dobu
            if ($lastActivity < $checkActivity) {
                $this->logout(); // Odhlaseni
                $data = []; // Resetuji vstupni data
            }
        }

        // Data jsou OK
        if ($data) {
            // Pokud obsahuji heslo - vymazu
            if (array_key_exists('password', $data)) {
                unset($data['password']);
            }
            // S kazdym nastavenim dat pridavam cas posledni aktivity
            $data[self::SESSION_ACTIVITY_KEY] = new \DateTime();

            // Ulozim data do Session
            $_SESSION[self::SESSION_BASE_KEY] = $data;

            // Oznacim uzivatele jako prihlaseneho
            $this->isLogged = true;

            // Objektu User nastavim data do vlastnosti data a budu k nim tak moci dale pristupovat
            $this->setData($data);

            // Konec metody pokud vse OK - User is logged
            return true;
        }

        // Konec metody - User is not logged
        return false;
    }


    /**
     * @param array $data
     */
    private function setData(array $data): void
    {
        $this->data = $data;
    }


    /**
     * @param null $key
     * @return array|bool|mixed
     */
    public function getData($key = null)
    {
        if ($key === null) {
            return $this->data;
        }

        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }

        return false;
    }


    /**
     * Pomocna metoda pro prime ziskani ID uzivatele
     *
     * @param string $key
     * @return bool|int
     */
    public function getId($key = 'id')
    {
        if (array_key_exists($key, $this->data)) {
            return (int) $this->data[$key];
        }
        return false;
    }


    /**
     * @return bool
     */
    public function isUserLogged(): bool
    {
        return $this->isLogged;
    }


    /**
     * Metoda ziska a nastavi uzivateli jeho role
     *
     * @throws \Exception
     */
    private function setRoles(): void
    {
        $roles = $this->userModel->getUserRolesByUserId($this->getId());
        foreach ($roles as $role) {
            $this->roles[$role['role']] = $role['roleName'];
        }
    }


    /**
     * Vrati seznam roli uzivatele
     * Pokud jeste nejsou nastaveny, zavola metodu setRoles()
     *
     * @return array
     * @throws \Exception
     */
    private function getRoles(): array
    {
        if ($this->isUserLogged() && !$this->roles) {
            $this->setRoles();
        }
        return $this->roles;
    }


    /**
     * Metoda overeni, jestli je uzivatel v dane roli
     *
     * @param $role
     * @return bool
     * @throws \Exception
     */
    public function isInRole($role): bool
    {
        return array_key_exists($role, $this->getRoles());
    }


    /**
     * Login metoda
     *
     * @param $email
     * @param $password
     * @return bool
     * @throws \Exception
     */
    public function login($email, $password): bool
    {
        $this->logout();

        $result = $this->userModel->getUserByEmail($email);
        $password = md5($password);

        if ($result && $result['password'] === $password) {
            // Proces zalogovani uzivatele
            $this->setUserSession($result);

        } else {
            $this->isLogged = false;
        }

        return $this->isLogged;
    }


    /**
     * Logout uzivatele
     */
    public function logout(): void
    {
        unset($_SESSION[self::SESSION_BASE_KEY]);
        $this->isLogged = false;
    }



}
