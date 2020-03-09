<!DOCTYPE html>
<!--
Informace:
V šablonách se používá i PHP
Nicméně nejde o žádné velké programování, převážně jen výpis proměnných, použití podmínkových bloků
a cyklů pro průchod proměnými typu array

----------- Zvláštnosti PHP syntaxe v šablonách --------------

PHP umožňuje v šablonách "přehlednější" nebo jednodužší zápis. Obecně se k tomu používají
tzv. šablonovací systémy (např. Smarty nebo Latte), ale ty tento startapp nevyužívá. 
V šablonách používáme čisté PHP a držíme se pokud možno striktně těchto pravidel:
A: https://platesphp.com/v3/templates/syntax/
-->
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="description" content="Popis stránky">
        <title><?=$title;?> | <?=$headerText;?></title>
        
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <!-- Optional theme -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">        
    </head>
    <body>
        
        <nav class="navbar navbar-inverse navbar-fixed-top">
          <div class="container">
            <div class="navbar-header">
              <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
              </button>
                <a class="navbar-brand" href="#"><?=\app\conf\Config::WEBMAINTITLE;?></a>
            </div>
            <div id="navbar" class="collapse navbar-collapse">
              <ul class="nav navbar-nav">

                  <!-- Výpis položek menu -->
                <?php foreach ($menu->getItems() as $controller => $title): ?>
                    <?php $active = '';?>

                    <?php if ($controller === $menu->getActiveItem()):?>
                        <?php $active = "class='active'";?>
                    <?php endif;?>

                    <li <?=$active;?>>
                        <a href="<?=\app\conf\Config::BASEURL;?><?=$controller;?>">
                            <?=$title;?>
                        </a>
                    </li>
                <?php endforeach;?>
                  
              </ul>
            </div><!--/.nav-collapse -->
          </div>
        </nav>        
        
        <!-- Otevření obalu - končí v souboru bottom.inc -->
        <div class="container">
            <br><br><br>

            <?php if ($flashes):?>
                <?php foreach ($flashes as $flash):?>
                    <div class="alert alert-<?=$flash['type']?>" role="alert"><?=$flash['msg']?></div>
                <?php endforeach;?>
            <?php endif;?>
            
