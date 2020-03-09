<?php
// Jmenný prostor třídy
// Je uveden nad definicí třídy
// A: http://jakpsatphp.cz/Namespaces/
namespace phpboom\services;


use app\conf\Config;
use http\Header;
use Tracy\Debugger;

/**
 * Register je pomocná třída vhodná pro jednodušší projekty (#user)
 * Máme ji navrženu jako kontejner pro služby aplikace, které si ukládá v jedné kopii,
 * a poskytuje je pro každá volání
 * Např. Mailer, Databáze, Request, Response atd.
 *
 * Class Register
 * @package phpboom\services
 */
class Register {

    /**
     * Pole vytvořených služeb
     * @var array
     */
    private $services = [];
    
    
    /**
     * Metoda inicializuje a následne vraci objekt konkretni sluzby (#user)
     * @param string $className - @example: phpboom\services\Request
     * @return object
     */
    public function getService($className)
    {
        if (!\array_key_exists($className, $this->services)) {
            // Zachytime moznou vyjimku ve volani neexistujici sluzby
            try {
                $this->services[$className] = new $className;
            } catch (\Exception $e) {
                // Došlo k zachycení chyby (vyjimky)
                // Loguju
                Debugger::log($e, 'critical');
                // Natvrdo :-)
                header('Location: ' . Config::BASEURL . '500.php');
                exit;
            }
        }
        return $this->services[$className];
    }
}
