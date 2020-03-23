<?php


namespace app\controllers;

use app\conf\Config;
use app\models\UserModel;
use phpboom\services\Db;
use phpboom\services\Register;
use Tracy\Debugger;


/**
 * Správa systému - jen pro uzivatele s rolí admin
 *
 * Class AdminController
 * @package app\controllers
 */
class AdminController extends BaseController {
    
    /** @var UserModel */ 
    private $userModel;


    /**
     * AdminController constructor.
     * @param Register $register
     * @throws \Exception
     */
    public function __construct(Register $register) 
    {
        $this->userModel = new UserModel($register->getService(Db::class));
        parent::__construct($register);

    }


    /**
     * Metoda, ktera je volana konstruktorem rodice (BaseController)
     * Princip PHP: Override
     * B: https://www.youtube.com/watch?v=5tcIUn6nbVE
     */
    protected function manageAction()
    {
        // Zde kontrolujeme oprávnění uzivatele
        // Pokud není uzivatel zalogovan nebo neni v roli admin
        // tak je okamzite presmerovan pryc
        // Stane se tak drive, nez rodicovska metoda zacne vytvaret menu (#recommended)
        if (!$this->user->isInRole(Config::ROLE_ADMIN)) {
            $this->redirect('default');
        }

        // Volani rodicovske metody
        parent::manageAction();

        $action = $this->request->getUrlPart(Config::ACTION_KEY);
        // $id = $this->request->getUrlPart(Config::ID_KEY);

        // Zatim implementujeme jen jednu metodu default
        $this->actionDefault();

        $this->render();
    }


    /**
     * Metoda nám bude vypisovat tabulku s uzivateli
     *
     * @throws \Exception
     */
    public function actionDefault()
    {
        $this->setHeaderText('Admin - správa uživatelů');
        $users = $this->userModel->getAllUsers();
        $this->addToTemplate('users', $users);
        Debugger::barDump($users, 'uzivatele');
    }



}