<?php


namespace app\controllers;

use app\conf\Config;
use app\models\UserModel;
use phpboom\services\Db;
use phpboom\services\Register;


/**
 * Registrace a login
 *
 * Class SignController
 * @package app\controllers
 */
class SignController extends BaseController {
      
    
    /** @var UserModel */ 
    private $userModel;


    /**
     * SignController constructor.
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
        // Volani rodicovske metody
        parent::manageAction();

        $action = $this->request->getUrlPart(Config::ACTION_KEY);
        // $id = $this->request->getUrlPart(Config::ID_KEY);
              
        if ($action === 'login') {
            $this->actionLogin();

        } elseif ($action === 'logout') {
            $this->actionLogout();

        } elseif ($action === 'registration') {
            // @todo: doplnit actionMetodu a sablony

        } elseif ($action === 'send-password') {
            // @todo: doplnit actionMetodu a sablony

        } else {
            $this->actionDefault();
        }

        $this->render();
    }


    /**
     * Vychozi akce Controlleru
     * Neni potreba - presmeruje klienta na login aplikace
     */
    public function actionDefault(): void
    {
        $this->redirect('sign/login');
    }


    /**
     * Akce pro login uzivatele
     *
     * @throws \Exception
     */
    public function actionLogin(): void
    {
        // Odeslal uzivatel formular loginu?
        if ($this->request->getPost('loginSubmit')) {

            // Data z formulare
            $formData = $this->request->getPost();
            $email = $formData['loginEmail'];
            $password = $formData['loginPass'];

            // Zkusime zalogovat uzivatele
            if ($this->user->login($email, $password)) {
                // Zde bychom si mohli implementovat aktulizaci casu posledniho prihlaseni uzivatele
                // A dale presmerovat klienta do zabezpecene casti aplikace
                // $this->redirect('sign/login');
            } else {
                $this->setFlashMessage('Zadané login údaje nebyly správné', 'warning');
            }

        }

        $this->setHeaderText('Přihlášení uživatele');
    }


    /**
     * Akce odhlaseni uzivatele
     * Nepotrebuje sablonu protoze jen presmerovava dale
     */
    public function actionLogout(): void
    {
        $this->user->logout();
        $this->redirect('sign');
    }


}