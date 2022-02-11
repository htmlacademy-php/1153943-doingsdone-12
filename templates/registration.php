<section class="content__side">
  <p class="content__side-info">Если у вас уже есть аккаунт, авторизуйтесь на сайте</p>

  <a class="button button--transparent content__side-button" href="auth.php">Войти</a>
</section>

<main class="content__main">
  <h2 class="content__main-heading">Регистрация аккаунта</h2>

  <form class="form" action="/registration/" method="POST" autocomplete="off">
    <div class="form__row">
      <label class="form__label" for="email">E-mail <sup>*</sup></label>

      <input class="form__input <?=!empty($errors['email']) ? 'form__input--error' : ''?>" type="email" name="email" id="email" value="<?=!empty($_POST['email']) ? $_POST['email'] : ''?>" placeholder="Введите e-mail">
      <?php if(!empty($errors['email'])): ?>
        <p class="form__message"><?=$errors['email']?></p>
      <?php endif; ?>
    </div>

    <div class="form__row">
      <label class="form__label" for="password">Пароль <sup>*</sup></label>

      <input class="form__input <?=!empty($errors['password']) ? 'form__input--error' : ''?>" type="password" name="password" id="password" value="<?=!empty($_POST['password']) ? $_POST['password'] : ''?>" placeholder="Введите пароль">

      <?php if(!empty($errors['password'])): ?>
          <p class="form__message"><?=$errors['password']?></p>
      <?php endif; ?>

    </div>

    <div class="form__row">
      <label class="form__label" for="name">Имя <sup>*</sup></label>

      <input class="form__input <?=!empty($errors['name']) ? 'form__input--error' : ''?>" type="text" name="name" id="name" value="<?=!empty($_POST['name']) ? $_POST['name'] : ''?>" placeholder="Введите имя">

      <?php if(!empty($errors['name'])): ?>
          <p class="form__message"><?=$errors['name']?></p>
      <?php endif; ?>
    </div>

    <div class="form__row form__row--controls">
        <?php if(!empty($errors)): ?>
            <p class="error-message">Пожалуйста, исправьте ошибки в форме</p>
        <?php endif; ?>

      <input class="button" type="submit" name="submit" value="Зарегистрироваться">
    </div>
  </form>
</main>
