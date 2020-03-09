<?php
// @author: Jan Kubrický
// Řádkový komentář v PHP (#strict)
// V komentářích se Vám budu snažit přiblížit logiku celé stavby jádra této aplikace
// Budu zde vkládat odkazy na vybrané výukové zdroje: A - Povinný, B - Doporučený

// Budu používat také tyto 3 hashtagy:
// 1. #strict - pravidlo nebo definice vymezené striktně jazykem PHP
// 2. #recommended - pravidlo nebo definice obecně doporučované
// 3. #user - nikoli pravidlo, ale vlastní způsob implementace autora/ů aplikace

// <?php - otevírací značka pro kód v jazyce PHP (#strict)
// Vše za touto značkou (vyjma komentářů) je zpracováváno PHP interpretem
// A: https://www.php.net/manual/en/language.basic-syntax.phptags.php

/*
 * Blokový komentář v PHP (#strict)
 * Soubor index.php je výchozí bod celé aplikace a všechny požadavky jsou na něj směrovány (#recommended)
 * Viz .htaccess
 * Z hlediska doporučovaného návrhu aplikace by neměl obsahovat nic jiného než kód PHP (#recommended)
 * Dodržujeme tak pravidlo: oddělení funkční logiky (vrstvy) od prezentační vrstvy (HTML)
*/

// Aplikace je navržena a implementována s použitím OOP
// B: https://www.w3schools.com/php/php_oop_what_is.asp
// OK lets go

// Vloží obsah souboru Autoloader.php z daného umístění (#user)
require_once './phpboom/Autoloader.php';

// Vloží obsah souboru autoloadu pro nami vyuzivane knihovny dodavatelu (tretich stran)
require_once './libs/vendor/autoload.php';

// Inicializujeme knihovnu dodavatele a na localhostu povolujeme Debbuger
// Bude se starat do Debug nasich chyb
// B: https://tracy.nette.org/cs/guide
use Tracy\Debugger;
Debugger::enable(Debugger::DETECT, __DIR__.'/log');
Debugger::log('Arnie is the Running man'); // Testovaci zapis do logu


// Inicializujeme Autoloader voláním jeho statické veřejné metody getInstance() a uložíme jej v proměnné $loader
// Statické volání má vždy tvar NazevTridy::Metoda() (#strict)
$loader = Autoloader::getInstance();

// Uz jste si asi vsimli, že v PHP se proměnné označují značkou dolaru $variable (#strict)
// A: https://www.w3schools.com/php/php_variables.asp

// Vytvoříme instanci třídy Register (a uložíme ji do proměnné), která nám bude dodávat služby aplikace (#user)
// Vytvoření instance třídy se řeší pomocí operátoru new (#strict)
// Třída musí být uvedena s celým jmenným prostorem (#strict)
// Zpětná lomítka automaticky doplněnuje IDE podle jmenného prostoru uvedeného u třídy
$register = new \phpboom\services\Register();

// Vyvoříme a uložíme instanci třídy Application
$app = new \app\Application($register);

// Spustíme aplikaci voláním metoty run() objektu třídy Application
// Zajímá Vás rozdíl mezi pojmy třída, instance a objekt?
// B: https://alfredjava.wordpress.com/2008/07/08/class-vs-object-vs-instance/
$app->run();

// Chcete znát rozdíly mezi OOP a klasických procedurálním přístupem?
// B: https://www.geeksforgeeks.org/differences-between-procedural-and-object-oriented-programming/