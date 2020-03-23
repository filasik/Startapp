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
            $this->actionRegistration();

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
                if ($this->user->isInRole(Config::ROLE_ADMIN)) {
                    $this->redirect('admin');
                }
            } else {
                $this->setFlashMessage('Zadané login údaje nebyly správné', 'warning');
            }

        }

        $this->setHeaderText('Přihlášení uživatele');
    }


    /**
     * Akce pro registraci uzivatele
     *
     * @throws \Exception
     */
    public function actionRegistration(): void
    {
        // Odeslal uzivatel formular registrace?
        if ($this->request->getPost('profilSubmit')) {

            // Overení dat formuláre presunu do modelu
            // Delam to z toho duvodu, ze toto overeni bude spolecne jak pro registraci tak upravu profilu
            // Vyhnu se tak nezadouci duplicite kodu
            $formData = $this->request->getPost();
            $result = $this->userModel->verifyProfilFormData($formData);

            if ($result === true) {
                // Vytvorim uzivatele
                $dataToSave = [];
                $dataToSave['email'] = $formData['profilEmail'];
                $dataToSave['password'] = md5($formData['profilPass']);
                $newUserId = $this->userModel->save($dataToSave);
                // Struktura moji zkladni tabulky v db test, tabulka user, obsahuje stale jen zakldni atributy
                // Vaše už by měla být doplněna min. o atributy jmeno a prijmeni
                // @todo - dodelejte si (doplnte) ulozeni techto atributu

                // Vytvorim jeho vazbu na roli
                // @todo: zatim reseno jen pro pedagogy
                $dataToSave = [];
                $dataToSave['user_id'] = $newUserId;
                $dataToSave['role_id'] = Config::ROLE_PEDAGOG_ID;
                $this->userModel->save($dataToSave, UserModel::TABLE_USER_JOIN_ROLE_NAME);

                // Presmeruju uzivatele na stranku prihlaseni
                $this->redirect('sign/login');

            } else {
                $this->setFlashMessage(UserModel::$profilFormStates[$result], 'warning');
            }

        }


        // Nastavím mód profilového formuláře jako registrace
        // Formulář budu totiž jinde využívat jak pro registraci, tak jej bude mít uživatel k dispozici pro změnu svého profilu
        $this->addToTemplate('profilFormMode', Config::PROFIL_FORM_REG_MODE);

        $this->setHeaderText('Registrace uživatele');
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