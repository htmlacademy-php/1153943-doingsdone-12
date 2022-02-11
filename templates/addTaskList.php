<main class="content__main">
    <h2 class="content__main-heading">Добавление проекта</h2>

    <form class="form" action="/add_list/" method="post" autocomplete="off">
        <div class="form__row">
            <label class="form__label" for="project_name">Название <sup>*</sup></label>

            <input class="form__input <?= !empty($errors['name']) ? 'form__input--error' : '' ?>" type="text"
                   name="name" id="project_name" value="<?= !empty($_POST['name']) ? $_POST['name'] : '' ?>"
                   placeholder="Введите название проекта">
            <p class="form__message"><?= !empty($errors['name']) ? $errors['name'] : '' ?></p>
        </div>

        <div class="form__row form__row--controls">
            <input class="button" type="submit" name="submit" value="Добавить">
        </div>
    </form>
</main>
