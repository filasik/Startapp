<?php

namespace app\controllers;

use app\conf\Config;
use phpboom\services\Register;


/**
 * Výchozí Controller, dědící z BaseControlleru
 * A: https://www.php.net/manual/en/language.oop5.inheritance.php
 *
 * Class DefaultController
 * @package app\controllers
 */
class DefaultController extends BaseController {


    /**
     * DefaultController constructor.
     * Konstruktor vsech Controlleru, ktere dedi z BaseControlleru musí
     * mít stejně jako BaseController povinně vstupní parametr objekt Registru
     *
     * @param Register $register
     * @throws \Exception
     */
    public function __construct(Register $register) 
    {
        // Volani rodicovskeho konstruktoru
        parent::__construct($register);

        // Volani verejnych (public) nebo chráněných (protected) metod BaseControlleru
        $this->setHeaderText('Úvodní stránka');
        $this->render();
    }
    
    
    
}
