<!--
Pomocná proměnná $isRegistration pro přehlednější řízení formulaře
-->
<?php $isRegistration = ($profilFormMode === app\conf\Config::PROFIL_FORM_REG_MODE); ?>


<form method="post" action="" id="profilForm">
    <div class="form-group">
        Jméno:
        <input type="text" name="profilName" required class="form-control" maxlength="30">
    </div>

    <div class="form-group">
        Příjmení:
        <input type="text" name="profilSurname" required class="form-control" maxlength="50">
    </div>

    <div class="form-group">
        Email:
        <input type="email" name="profilEmail" required value="<?=@$myUser['email'];?>" class="form-control" maxlength="100">
    </div>

    <div class="well" id="passwordSection">
        <div class="form-group">
            Heslo:
            <input type="password" id="profilPass" name="profilPass" <?php if ($isRegistration):?>required<?php endif;?> class="form-control" minlength="5">
        </div>

        <div class="form-group">
            Heslo znovu pro ověření:
            <input type="password" id="profilPassConfirm" name="profilPassConfirm" <?php if ($isRegistration):?>required<?php endif;?> class="form-control" minlength="5">
        </div>
    </div>


    <?php if ($isRegistration): ?>
        <div class="form-group">
            Tajný klíč:
            <input type="text" name="profilVerifyCode" required class="form-control">
        </div>
    <?php endif;?>

    <input type="hidden" name="profilMode" value="<?=$profilFormMode;?>">


    <input type="hidden" name="userId" value="<?=@$myUser['id'];?>">



    <?php $submitBtnCaption = 'Registrovat se';?>
    <?php if (!$isRegistration): ?>
        <?php $submitBtnCaption = 'Upravit profil';?>
    <?php endif;?>

    <button type="submit" name="profilSubmit" class="btn btn-warning" value="1">
        <?=$submitBtnCaption;?>
    </button>
</form>