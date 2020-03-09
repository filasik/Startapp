<?php

namespace app\conf;


/**
 * Třída obsahující přístup ke statickým konfiguračním vlastnostem aplikace
 * Volají se staticky Config::($)NAZEV_KONSTANTY
 */
class Config {

    /**
     * Zakladni titulek aplikace
     */
    public CONST
        WEBMAINTITLE = 'PHPBoom';

    /**
     * Oznaceni slozky aplikace
     */
    public CONST
        PROJECT_LOCAL_FOLDER = 'startapp';


    /**
     * Controllery a Akce
     */
    public CONST
        ERROR_CONTROLLER = 'Error',
        DEFAULT_CONTROLLER = 'Default',
        DEFAULT_ACTION = 'default';
    
    
    public CONST
        BASEURL = 'http://localhost/startapp/', // Vychozi URL aplikace
        VIEWSFOOLDER = './app/views'; // Vychozi cesta k souborum šablon


    // Udaje pro pripojeni k databazovemu systemu
    public CONST
        HOST = 'localhost', // Domenove jmeno nebo IP adresa serveru
        USER = 'root', // Db user
        PASSWORD = 'root1234', // Db user password
        DB = 'test'; // Jmeno databaze

    /**
     * Nazvy masek URL
     */
    public CONST
        CONTROLLER_KEY = 'controller',
        ACTION_KEY = 'action',
        ID_KEY = 'id';



    // Maska URL - pomaha nam prelozit jednotlive casti URL
    // Podobnou masku pouziva vetsina znamych PHP frameworku
    public static $urlmask = [self::CONTROLLER_KEY, self::ACTION_KEY, self::ID_KEY];
    
    
     /**
     * Polozky menu (klic => hodnota)
     * Klic je nazev controlleru, hodnota jeho uživatelsky nazev
     * @var array 
     */
    public static $myitems = [
        'default' => 'Home',
        'sign' => 'Login/Registrace',
        'user' => 'Uživatelé'
    ];


    /**
     * Uzivatelske role
     */
    public const ROLE_ADMIN = 'admin';
}
