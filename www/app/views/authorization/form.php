<form method="POST" class="loginForm">
<?  if (isset($viewdata_errors) AND (!empty($viewdata_errors))) : ?>
        <div class="loginFormErrors">
        <?  foreach($viewdata_errors as $error) : ?>
                <p><?=$error?></p>
        <?  endforeach; ?>
        </div>
<?  endif; ?>
    <div class="loginFormRow">
        Имя пользователя: <br />
        <input type="text" name="username" value="<?=(isset($viewdata_form_fields["username"]) ? $viewdata_form_fields["username"] : "")?>" />
    </div>
    <div class="loginFormRow">
        Пароль: <br />
        <input type="password" name="password" />
    </div>
    <div class="loginFormRow">
        <button>Войти</button>
    </div>
</form>