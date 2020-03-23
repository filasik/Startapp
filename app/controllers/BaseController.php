<?php

namespace app\controllers;

use app\components\Menu;
use app\conf\Config;
use app\models\UserModel;
use Exception;
use phpboom\security\User;
use phpboom\services\Db;
use phpboom\services\Register;
use phpboom\services\Request;
use Tracy\Debugger;


/**
 * Základní třída ze které budou dědit všechny ostatní kontrolery (#recommended)
 * Tj. obsahuje funkcioalitu, která bude pro všechny zbývající kontrolery společná
 * Z této třídy nelze vytvořit objekt pomocí operátoru new, protože je deklarována jako abstraktní
 * B: https://www.w3schools.com/php/php_oop_classes_abstract.asp
 *
 * Class BaseController
 * @package app\controllers
 */
abstract class BaseController {


    /** @var Register */
    protected $register;

    /** @var Request */
    protected $request;
    
    /** @var array */
    protected $toTemplate = [];

    /** @var array */
    protected $flashMessages = [];
    
    /** @var Menu */
    protected $menu;

    /** @var User */
    public $user;


    /**
     * BaseController constructor.
     * @param Register $register
     * @throws Exception
     */
    public function __construct(Register $register) 
    {
        $this->register = $register;
        $this->request = $this->register->getService(Request::class);

        // Aplikace bude overovat uzivatele a jeho roli v aplikaci
        // Pro tento ucel bude slouzit Objekt tridy User, ktery potrebuje pristup do databaze
        $userModel = new UserModel($this->register->getService(Db::class));
        $this->user = new User($userModel);
        if ($this->user->isUserLogged()) {
            Debugger::barDump($this->user->getData(), 'Logged user data');
            Debugger::barDump($this->user->getRoles(), 'role');
        }

        // Volani metody zivotniho cyklu Controlleru
        $this->manageAction();
    }


    /**
     * Metoda, kterou může přepsat kazdy Controller
     * Metoda definuje zivotni cyklus Controlleru
     */
    protected function manageAction()
    {
        // Zde jde spolecne volani
        $this->setMenu();
    }


    /**
     * Nastavení menu aplikace (#user)
     */
    private function setMenu(): void
    {
        $this->menu = new Menu();
        foreach (Config::$myitems as $fileName => $title) {
            $this->menu->addItem($fileName, $title);
        }

        // Doplnim o menu admina
        if ($this->user->isInRole(Config::ROLE_ADMIN)) {
            foreach (Config::$adminItems as $fileName => $title) {
                $this->menu->addItem($fileName, $title);
            }
        }

        /** @var Request $request */
        $request = $this->register->getService(Request::class);
        $controllerName = $request->getUrlPart(Config::CONTROLLER_KEY);
        $this->menu->setActiveItem($controllerName);
    }


    /**
     * Metoda pro přidání proměnné do šablony (vrtsva view)
     *
     * @param string $key
     * @param string $value
     */
    protected function addToTemplate($key, $value): void
    {
        $this->toTemplate[$key] = $value;
    }

    
    /**
     * Pomocná metoda - Nastavení titulku stránky
     *
     * @param string $title
     */
    public function setTitle($title): void
    {
        $this->addToTemplate('title', $title);
    }


    /**
     * Pomocná metoda - Nastavení doplňku titulku stránky
     *
     * @param $text
     */
    public function setHeaderText($text): void
    {
        $this->addToTemplate('headerText', $text);
    }


    /**
     * Flash zprávy do šablony
     * @example: Info pro uzivatele pri akcich
     *
     * @param string $msg
     * @param string $type
     */
    protected function setFlashMessage($msg, $type = 'info'): void
    {
        $this->flashMessages[] = ['msg' => $msg, 'type' => $type];
    }


    /**
     * Důležitá metoda
     * Umožňuje nám přesměrovat požadavek klienta na konkrétní část aplikace
     *
     * @param string $param
     * @param int $statusCode
     */
    protected function redirect($param = '', $statusCode = 303): void
    {
        header('Location: ' . Config::BASEURL . $param, true, $statusCode);
        exit();
    }


    /**
     * Implementace vykreslení šablony (#user)
     * Metoda vlastně ukoncuje proces Requestu klienta
     * Sestavuje pro nej odpověd (Response) - nasledne server posila Response klientovi
     *
     * @throws Exception
     */
    protected function render(): void
    {
        $this->setTitle(Config::WEBMAINTITLE);

        // Zjistěte, k čemu se používá tato členská funkce PHP
        extract($this->toTemplate, EXTR_OVERWRITE);

        // Vytvoreni promennych pouzitelnych v sablone
        $user = $this->user;
        $menu = $this->menu;
        $baseUrl = Config::BASEURL;
        $flashes = $this->flashMessages;

        // BarDump pro konrtrolu
        Debugger::barDump($flashes, 'flash zprávy');
        
        $template = $this->getTemplate();
        if (!file_exists($template)) {
            Debugger::log('Chyba aplikace - šablona  neexistuje:'. $template, 'critical');
            $this->redirect('404.php');
            //throw new \Exception('Chyba aplikace - šablona  neexistuje');
        }

        // Finalni sestaveni odpovědi klientovi
        // Zahlavi
        require_once './inc/head.inc';

        // Sablona akce
        require_once $template;

        // Zapati
        require_once './inc/bottom.inc';

        // Odeslání response klientovi
        $this->sendResponse();
    }
    
    /**
     * Pomocna metoda
     * Vrati cestu k sablone pozadovaneho Controlleru a pozadovane Akce
     *
     * @return string
     */
    private function getTemplate(): string
    {
        /** @var Request $request */
        $request = $this->register->getService(Request::class);

        $controllerName = ucfirst($request->getUrlPart(Config::CONTROLLER_KEY));
        if (!$controllerName) {
            $controllerName = Config::DEFAULT_CONTROLLER;
        }
        
        $actionName = $request->getUrlPart(Config::ACTION_KEY);
        if (!$actionName) {
            $actionName = Config::DEFAULT_ACTION;
        }
        
        $template = Config::VIEWSFOOLDER . '/' . $controllerName . '/' . $actionName . '.tpl';
        return $template;        
    }


    /**
     * Odeslani response klientovi (#user)
     * Mohli bychom doplnit o další funkcionalitu - @example: logování?
     *
     * @param array $param
     */
    protected function sendResponse(array $param = []): void
    {
        // Put your code here :-)
        exit;
    }


    /**
     * Pomocna metoda na tvorbu odkazů v šablonách
     * Voláme ji staticky self::createLink($path)
     *
     * @param $path
     * @return string
     */
    public static function createLink($path): string
    {
        return Config::BASEURL . $path;
    }
    
}
