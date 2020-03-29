<h1><?=$headerText;?></h1>

<a href="<?=self::createLink('admin/default/');?>" class="btn btn-sm btn-info">
    Zpět na přehled všech uživatelů
</a>

<hr>

<?php if ($showForm):?>

    <?php require_once 'app/views/Sign/inc/profilForm.tpl';?>

<?php endif;?>



