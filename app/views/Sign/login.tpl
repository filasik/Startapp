<h1><?=$headerText;?></h1>

<form method="post" action="">
  <div class="form-group">
      Email:
      <input type="email" name="loginEmail" required class="form-control">
  </div>
    
  <div class="form-group">
      Heslo:
      <input type="password" name="loginPass" required class="form-control">
  </div>

  <button type="submit" name="loginSubmit" class="btn btn-default" value="1">Přihlásit se</button>
</form>


<?php
    if ($user->isUserLogged()):
?>
<h3>Uživatel <em><?=$user->getData('email');?></em> je přihlášen</h3>
    <a href="<?=self::createLink('sign/logout');?>" class="btn btn-danger">
        Odhlásit se
    </a>
<?php
    endif;
?>