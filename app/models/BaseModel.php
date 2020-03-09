<?php

namespace app\models;

use app\conf\Config;
use phpboom\services\Db;

/**
 * Základní třída, ze které budou dědit vsechny modelové třídy
 * Zajišťuje připojení k databázi
 * Obsahuje konstruktorem předanou službu Db, která se připojí k databázi a
 * obsahuje pomocné metody pro obsluhu databázové tabulky(tabulek)
 *
 * Class BaseModel
 * @package app\models
 */
abstract class BaseModel {


    /** @var Db */
    private $db;
    
    
    /**
     * @param Db $db
     */
    public function __construct(Db $db) 
    {
        $this->db = $db;
    }


    /**
     * @return Db
     */
    public function getDb(): Db
    {
        // Pokud jeste neni pripojen k databazi, tak pripojim
        $this->db->connection(Config::HOST, Config::USER, Config::PASSWORD, Config::DB);
        return $this->db;
    }
            
}
