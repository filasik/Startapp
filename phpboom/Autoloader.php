<?php

/**
 * Autoloader v návrhovém vzoru Singleton!
 * A: https://www.interval.cz/clanky/oop-v-php-vzor-singleton/
 *
 * Základní třída, která nám pomůže s vkládáním obsahu dalších našich PHP tříd z daných souborů
 * Nebudeme tak muset v kódu používat otravné volání příkazů require nebo include (srovnej s require_once)
 * B: https://www.php.net/manual/en/function.require-once.php
 * Všimněte si, že jediné použití příkazu require v projektu, je jen při úvodním vložení této třídy v index.php
 * O zbytek už se postará tento Autoloader a jeho metoda autoload()
 */
class Autoloader {


    /**
     * Privátní vlastnost (proměnná) třídy s výchozí hodnotou NULL
     * Je definována jako statická
     * Anotace (@var) oznacuje datový typ vlastnosti
     * @var null|Autoloader
     */
    private static $instance = NULL;
    
    
    
    /**
     * Všiměnte si přístupového modifikátoru: private
     * A: https://www.w3schools.com/php/php_oop_access_modifiers.asp
     *
     * V případě použití u magické metody (__construct) to znamená,
     * že instance této třídy lze vytvořit vně třídy jen voláním statické metody getInstance()
     * Označení vzoru: důkladný Singleton
     */
    private function __construct() 
    {
        // registrace speciální funkce, která se za nás bude starat o načítání PHP tříd ze souborů (#strict)
        // funkce a třídy, jejichž název začíná zpětným lomítkem používají tzv. globální jmenný prostor
        \spl_autoload_register(array($this, 'autoload'));
    }
    
    
    /**
     * Metoda vrátí jednu kopii instance třídy Autoloader
     *
     * @return self
     */
    public static function getInstance(): self
    {
        if (self::$instance === NULL) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    
    /**
     * Výše zaregistrovaná metoda, která bude vkládat naše PHP třídy z danych souborů
     * Toto řešení předpokládá shodu jmenného prostoru třídy s adresářovou strukturou (#user)
     * Na projektech vyssi urovne, se již tento princip nepouziva
     * Více informací v třídě Register
     *
     * @param string $class @example: \phpboom\services\Register
     * @throws \Exception
     */
    public function autoload($class): void
    {
        // Nejprve převedeme celý namespace volané třídy na odpovídající PHP soubor,
        // protoze autoload vklada soubor

        // Vysvetleni nasledujiciho radku
        // dirname(__DIR__): Absolutní cesta souborovým systémem ke složce současného souboru (Autoloader)
        // @example: C:\web\www\startapp\phpboom\services\Register.php
        $classFile = dirname(__DIR__) . '/' . $class . '.php';


        // Dále přepíšeme proměnnou $classFile - všechna zpětná lomítka změníme na klasická
        $classFile = str_replace("\\", '/', $classFile);
        // Ziskame tak klasickou cestu
        // @example: C:/web/www/startapp/phpboom/services/Register.php
        
        if (file_exists($classFile)) {
            require $classFile;
        } else {
            // V případě, že neexistuje žádaný PHP soubor třídy,
            // aplikace vyhodí základní vyjímku a bude zastavena (#recommended)
            throw new \Exception('Cannot find class: '. $classFile);
        }
    }
}