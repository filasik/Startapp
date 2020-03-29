<h1><?=$headerText;?></h1>

<pre>
    <?php print_r($users);?>
</pre>

<table class="table table-hover table-striped">
    <thead>
    <tr>
        <th>ID</th>
        <th>Jméno a příjmení</th>
        <th>Email</th>
        <th>Poslední přihlášení</th>
        <th>Role</th>
        <th>Akce</th>
    </tr>
    </thead>
    <tbody>
        <?php foreach($users as $myUser):?>
            <tr>
                <td><?=$myUser['id'];?></td>
                <td>{* Vaše implementace*}</td>
                <td><?=$myUser['email'];?></td>
                <td><?=$myUser['lastLogin'];?></td>
                <td><?=$myUser['role'];?></td>
                <td>
                    <a href="<?=self::createLink('admin/updateUser/'.$myUser['id']);?>" class="btn btn-warning">
                        Upravit
                    </a>
                    <a href="<?=self::createLink('admin/updateUserRole/'.$myUser['id']);?>" class="btn btn-info">
                        Role
                    </a>
                    <a href="javascript:;" data-url="<?=self::createLink('admin/deleteUser/'.$myUser['id']);?>" class="deleteUserBtn btn btn-danger">
                        Odstranit
                    </a>
                </td>
            </tr>
        <?php endforeach;?>
    </tbody>
</table>



