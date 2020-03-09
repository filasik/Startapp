<?php

namespace phpboom\services;

use app\conf\Config;


/**
 * Request - pro aplikaci v zásadě nejdůležitější služba
 * Poskytuje rozhranní pro získání globálních hodnot, předávaných metodou get (URL) nebo post (Formuláře)
 * Zajišťuje základní ošeření těchto hodnot pomocí filtračních funkcí
 * Poskytuje pohdlný přístup pomocí jednoduchých veřejných metod
 *
 * Class Request
 * @package phpboom\services
 */
class Request {


    /**
     * Obsahuje casti URL podle masky z Configu
     * @var array
     */
    private $urlParts = [];

    /**
     * Obsahuje dalsi casti URL parametru následující za otaznikem (tzv. query)
     * @var array
     */
    private $urlQueryParams = [];


    /** @var array */
    private $post;


    /**
     * Request constructor.
     * Vola metodu nastaveni casti URL podle nasi masky
     * Uklada data predane metodou POST
     */
    public function __construct() 
    {
        $this->setUrlParts();
        $this->post = filter_input_array(INPUT_POST);
    }


    /**
     * Parsuje části URL podle masky a ulozi je do vlastnoti $urlParts
     * $urlParts ulozi jako pole obsahujici dvojice (klic => hodnota): controller => user, action => edit, id => 1
     *
     * Komentar budeme doplnovat pro pripad URL:
     * http://localhost/startapp/user/edit/1?param1=10&antoherParam=2
     */
    private function setUrlParts(): void
    {
        $url = filter_input(INPUT_SERVER, 'REQUEST_URI');
        // Chcete-li si vypsat aktuálního hodnotu proměnné $url muzete pouzit funkci var_dump()
        //var_dump($url);die;

        // Volam clenskou funkci parse_url(), která nam pomuze s rozdelenim URL na casti
        $parseUrl = parse_url($url);
        // Obsah proměnné $parseUrl: array(2) path => "/startapp/user/edit/1", query => "param1=10&antoherParam=2"
        //var_dump($parseUrl);

        // Odebereme lomitko na zacatku hodnoty s klicem 'path'
        $path = substr($parseUrl['path'], 1);

        // Pokud pracujeme na localhostu proměnná $path bude vypadat takto: startapp/user/edit/1
        // To startapp nám tam nevyhovuje, protože my to považujeme za nazev serveru
        // Nicméně PHP to vyhodnotilo jako součást REQUEST_URI, protoze nazev serveru je pro neho jen 'localhost'

        // Prevedeme retezec $path na prvky pole
        $pathBits = explode('/', $path);
        //var_dump($pathBits);die;

        // Vyresime problem s nazvem slozky projektu v castech URL
        // Pokud prvni prvek pole je shodny s nazvem slozky projektu
        if (isset($pathBits[0]) && $pathBits[0] === Config::PROJECT_LOCAL_FOLDER) {
            // Odstranim tento prvek z pole
            unset($pathBits[0]);
            // Nastavim indexovani oriznuteho pole znovu na 0
            $pathBits = array_values($pathBits);
        }
        //var_dump($pathBits);die;

        // Cyklem budu prochazet vsechny polozky pole a ukladat je podle masky
        foreach ($pathBits as $key => $bit) {
            if (trim($bit) !== '') {
                $this->urlParts[Config::$urlmask[$key]] = $bit;
            }
        }

        // Obsahuje url také část query?
        // Pokud ano, nastavim take $urlParams
        if (isset($parseUrl['query'])) {
            $this->setUrlQueryParams($parseUrl['query']);
        }
    }


    /**
     * Nastavi query parametry rozdelene do dvojic nazev_parametru => hodnota_parametru
     *
     * @param string $params - napr: param1=10&antoherParam=2
     */
    public function setUrlQueryParams($params): void
    {
        // Rozdelim jednotlive parametry podle oddelovace '&' na polozky pole
        $paramsBits = explode('&', $params);

        // Projdu vsechny polozky pole
        // V prvnim pruchodu bude v nasem prikladu hodnota $paramsBit obsahovat 'param1=10'
        foreach ($paramsBits as $paramsBit) {

            // Vytvorim dvojici klic a hodnota
            [$key, $value] = explode('=', $paramsBit);

            // Postupne pridavam do vlastnosti tridy $urlParams (pole dvojic nazev_parametru => hodnota_par)
            $this->urlQueryParams[$key] = $value;
        }
    }
    
    
    /**
     * Vrati cast URL podle masky
     *
     * @param string $mask
     * @return mixed
     */
    public function getUrlPart($mask)
    {
        if (array_key_exists($mask, $this->urlParts)) {
            return $this->urlParts[$mask];
        }
        return false;
    }


    /**
     * Vrati hodnotu query parametru
     *
     * @param string $param
     * @return bool|mixed
     */
    public function getQueryParam($param)
    {
        if (array_key_exists($param, $this->urlQueryParams)) {
            return $this->urlQueryParams[$param];
        }
        return false;
    }


    /**
     * Vrati hodnoty z pole post
     *
     * @param string|null $index
     * @return mixed
     */
    public function getPost($index = null)
    {
        // Pokud nebyl zadan zadny index, vracim cele pole POST
        if ($index === null) {
            return $this->post;
        }

        // Byl zadan index, takze chci hodnotu konkretniho klice (indexu)
        // Pri kontrole existence klice v poli, muzete pouzit bud funkce isset($this->post[$index])
        // Nebo rychlejsi a efektivnejsi funkce array_key_exists()
        if (is_array($this->post) && array_key_exists($index, $this->post)) {
            return filter_var($this->post[$index], FILTER_SANITIZE_STRING);            
        }

        // Byla vyzadana hodnota daneho indexu, ale pole $this->post ji neobsahuje :-/
        return false;
    }
    
}
