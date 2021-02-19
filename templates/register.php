<div class="content">
    <section class="content__side">
      <p class="content__side-info">Если у вас уже есть аккаунт, авторизуйтесь на сайте</p>

      <a class="button button--transparent content__side-button" href="form-authorization.html">Войти</a>
    </section>

    <main class="content__main">
      <h2 class="content__main-heading">Регистрация аккаунта</h2>

      <form class="form" action="registration.php" method="POST" autocomplete="off">
        <div class="form__row">
          <label class="form__label" for="email">E-mail <sup>*</sup></label>

          <input class="form__input <?=!empty($errors['email']) ? 'form__input--error' : ''?>" type="text" name="email" id="email" value="" placeholder="Введите e-mail">

          <?php if(!empty($errors['email'])): ?>
            <p class="form__message">E-mail введён некорректно</p>
          <?php endif; ?>
        </div>

        <div class="form__row">
          <label class="form__label" for="password">Пароль <sup>*</sup></label>

          <input class="form__input <?=!empty($errors['password']) ? 'form__input--error' : ''?>" type="password" name="password" id="password" value="" placeholder="Введите пароль">

          <?php if(!empty($errors['password'])): ?>
              <p class="form__message">Password введён некорректно</p>
          <?php endif; ?>
        </div>

        <div class="form__row">
          <label class="form__label" for="name">Имя <sup>*</sup></label>

          <input class="form__input <?=!empty($errors['name']) ? 'form__input--error' : ''?>" type="text" name="name" id="name" value="" placeholder="Введите имя">

          <?php if(!empty($errors['name'])): ?>
              <p class="form__message">Имя введено некорректно</p>
          <?php endif; ?>
        </div>

        <div class="form__row form__row--controls">
            <?php if(!empty($errors)): ?>
                <p class="error-message">Пожалуйста, исправьте ошибки в форме</p>
            <?php endif; ?>
          <input class="button" type="submit" name="submit" value="<?=isset($_POST['submit']) ? 'Зарегистрироваться' : 'Зарегистрироваться'?>">
        </div>
      </form>
    </main>
 </div>
