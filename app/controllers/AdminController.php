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
        $id = $this->request->getUrlPart(Config::ID_KEY);

        if ($action === 'updateUser') {
            $this->actionUpdateUser($id);
        } elseif ($action === 'updateUserRole') {
            $this->actionUpdateUserRole($id);
        } elseif ($action === 'deleteUser') {
            $this->actionDeleteUser($id);
        } else {
            $this->actionDefault();
        }

        $this->render();
    }


    /**
     * Metoda nám bude vypisovat tabulku s uzivateli
     *
     * @throws \Exception
     */
    public function actionDefault(): void
    {
        $this->setHeaderText('Admin - správa uživatelů');
        // Vytahnu si z DB vsechny uzivatele
        $users = $this->userModel->getAllUsers();
        $this->addToTemplate('users', $users);
        Debugger::barDump($users, 'uzivatele');
    }


    /**
     * Metoda upavy profilu uzivatele
     *
     * @param $id
     * @throws \Exception
     */
    public function actionUpdateUser($id): void
    {
        // Nejprve si vytáhneme data o uzivateli z DB
        $myUser = $this->userModel->getUserById($id);

        // Doslo k chybě - uzivatel s tímto ID nebyl nalezen
        if (!$myUser) {
            $this->setFlashMessage('Uživatel s tímto ID nebyl nalezen', 'danger');
            $this->addToTemplate('showForm', false);

        } else {
            // Uzivatel podle ID nalezen - pokracuju touto vetvi
            // Nejprve overim, jestli nebyl odeslan formular upravy profilu
            if ($this->request->getPost('profilSubmit')) {
                $formData = $this->request->getPost();
                // Volam metodtu, kterou jsme si uz pripravili u registrace
                $result = $this->userModel->verifyProfilFormData($formData);

                // Data byla vyplnena spravne
                if ($result === true) {
                    // Vytvorim pole upravenych dat pro ulozeni do DB
                    $dataToSave = [];
                    $dataToSave['email'] = $formData['profilEmail'];
                    // @todo - doplnte si chybejici atributy jmeno a prijmeni

                    // Menilo se heslo? Bude se menit v DB?
                    // Staci se "zeptat" zdali bylo vyplneno prislusne formularove policko
                    // O validaci hesla se nám uz postarala metoda UserModel::verifyProfilFormData()
                    if ($formData['profilPass']) {
                        $dataToSave['password'] = md5($formData['profilPass']);
                    }

                    // Ulozim db DB
                    $this->userModel->update($formData['userId'], $dataToSave);
                    $this->setFlashMessage('Data byla úspěšně upravena', 'success');

                    // Obnovim data upravovaneho uzivatele novymi!
                    // Musim to udelat proto, ze po odeslani zustavam na teze strance s formularem
                    // A zatim admina nikam nepresmerovavam
                    $myUser = $this->userModel->getUserById($formData['userId']);
                } else {
                    // Data nebyla vyplnena spravne
                    $this->setFlashMessage(UserModel::$profilFormStates[$result], 'warning');
                }

            }

            $this->addToTemplate('showForm', true);
            $this->addToTemplate('profilFormMode', Config::PROFIL_FORM_USER_MODE);
            $this->addToTemplate('myUser', $myUser);
        }


        $this->setHeaderText('Admin - úprava profilu uživatele');
    }


    /**
     * Akce úpravy rolí uzivatele
     *
     * @param $id
     * @throws \Exception
     */
    public function actionUpdateUserRole($id): void
    {
        // @todo - implementace zpracování formuláře nastavení rolí
        $this->setHeaderText('Admin - úprava rolí uživatele');
        $roles = $this->userModel->getAllRoles();
    }


    /**
     * Procesni akce odstraneni uzivatele
     * Nemá šablonu, jen odpovádí na požadavky podle typu requestu
     *
     * @param $id
     * @throws \Exception
     */
    public function actionDeleteUser($id): void
    {
        // Nejprve si vytáhneme data o uzivateli z DB
        $myUser = $this->userModel->getUserById($id);

        // Doslo k chybě - uzivatel s tímto ID nebyl nalezen
        if (!$myUser) {
            Debugger::log('Chyba - uživatel s ID: '.$id. ' neexistuje');
            // Pokud se jedna o Ajax volani - musim predat informaci o chybe
            if ($this->request->isAjax()) {
                // Response odpovedi bude obsahovat status error a rovnou odesilam odpoved
                // Server uz nic dalsho nedela
                echo json_encode(['status' => 'error']);
                $this->sendResponse();
            }
            // U neajaxoveho pozadavku jen presmeruju
            $this->redirect('admin/default');
        }

        $this->userModel->delete($id);

        // Pokud je pozadavek ajaxový ukoncuju vsechny dalsi akce odeslanim response
        if ($this->request->isAjax()) {
            echo json_encode(['status' => 'success']);
            $this->sendResponse();
        }
        // Pokud je to klasický požadavek provedu přesměrování na default akci
        $this->redirect('admin/default');

    }


}