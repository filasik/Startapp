<?php

namespace app;

// Následující řádky importují třídy použité níže v kódu
// B: https://www.php.net/manual/en/language.namespaces.importing.php
use app\conf\Config;
use app\controllers\BaseController;
use phpboom\services\Register;
use phpboom\services\Request;


/**
 * Třída, která spouští naši aplikaci (#user)
 *
 * Class Application
 * @package app
 */
class Application {


    /** @var Register */
    private $register;
    
    /** @var null|string  */
    private $controllerName = null;


    /**
     * Základní magická metoda, volaná automaticky při vytvoření objektu třídy (#strict)
     * A: https://www.w3schools.com/php/php_oop_constructor.asp
     *
     * Application constructor.
     * @param Register $register
     * @throws \Exception
     */
    public function __construct(Register $register)
    {
        $this->register = $register;
        $this->setControllerName();
    }


    /**
     * Privátní metoda - "volatelná" v kódu pouze uvnitř třídy samotné
     * Úkolem metody je uložit do své interní vlastnosti $controllerName název aktuálního Controlleru
     *
     * @throws \Exception
     */
    private function setControllerName(): void
    {
        // Z Registru získáme službu (objekt třídy) Request (reprezentuje požadavek klienta)
        /** @var Request $request */
        $request = $this->register->getService(Request::class);

        // Obsahuje URL requestu klienta cast nazvu controlleru?
        if ($request->getUrlPart(Config::CONTROLLER_KEY) !== false) {

            $controllerName = $request->getUrlPart(Config::CONTROLLER_KEY);
            $controllerWithNamespace = self::getControllerWithNamespace($controllerName);
            
            if (class_exists($controllerWithNamespace)) {
                $this->controllerName = $controllerName;
            } else {
                //throw new \Exception('Controller neexistuje: '.$controllerName);
                $this->controllerName = Config::ERROR_CONTROLLER;
            }      
        } else {
            $this->controllerName = Config::DEFAULT_CONTROLLER;
        }
    }


    /**
     * Pomocná statická metoda, která nám vrací celý namespace daného Controlleru
     *
     * @param string $controllerName
     * @return string
     */
    public static function getControllerWithNamespace($controllerName): string
    {
        return "app\\controllers\\".ucfirst($controllerName). 'Controller';
    }


    /**
     * Metoda spustí aplikaci tak, že předá další řízení načtenému Controlleru (#recommended)
     *
     * @return BaseController
     */
    public function run(): BaseController
    {
        $controller = self::getControllerWithNamespace($this->controllerName);
        return new $controller($this->register);
    }
}
