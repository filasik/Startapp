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
        //BASEURL = 'http://localhost/startapp/',
        BASEURL = 'http://startapp.test/', // Vychozi URL aplikace
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
     * V systému budou jen 3, pro rychlejsi provoz je tak muzeme udrzovat i zde
     */
    public const ROLE_ADMIN = 'admin';
    public const ROLE_PEDAGOG = 'pedagog';
    public const ROLE_RODIC = 'rodic';

    public const ROLE_ADMIN_ID = 1;
    public const ROLE_PEDAGOG_ID = 2;
    public const ROLE_RODIC_ID = 3;



    /**
     * Mod pro vyuziti registracniho formulare
     */
    public CONST PROFIL_FORM_REG_MODE = 'reg'; // Registrace
    public CONST PROFIL_FORM_USER_MODE = 'user'; // Úprava profilu uzivatele


    // Univerzalni tajny klic pro registraci pedagogu
    public CONST PEDAGOG_VERIFY_CODE_VALUE = '3s5df4sd35f4sA78#';
}
