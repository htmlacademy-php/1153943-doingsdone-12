<main class="content__main">
    <h2 class="content__main-heading">Добавление задачи</h2>

    <form class="form" action="/add/" method="POST" autocomplete="off" enctype="multipart/form-data">
        <div class="form__row">
            <label class="form__label" for="name">Название <sup>*</sup></label>

            <input class="form__input <?= !empty($errors['name']) ? 'form__input--error' : '' ?>" type="text"
                   name="name"
                   id="name" value="<?= !empty($_POST['name']) ? $_POST['name'] : '' ?>" placeholder="Введите название">
            <?php if (!empty($errors['name'])) : ?>
                <p class="form__message"><?= $errors['name'] ?></p>
            <?php endif?>
        </div>

        <div class="form__row">
            <label class="form__label" for="project">Проект <sup>*</sup></label>

            <select class="form__input form__input--select <?= isset($errors['project']) ? 'form__input--error' : '' ?>"
                    name="project" id="project">
                <?php foreach ($categories as $category): ?>
                <option value="<?= $category['id'] ?>"><?= $category['title'] ?></option>
                <?php endforeach; ?>

                <?php if (!empty($errors['project'])) : ?>
                    <p class="form__message"><?= $errors['project'] ?></p>
                <?php endif?>
            </select>
        </div>

        <div class="form__row">
            <label class="form__label" for="date">Дата выполнения</label>

            <input class="form__input form__input--date <?= !empty($errors['date']) ? 'form__input--error' : '' ?>"
                   type="text" name="date" id="date" value="<?= !empty($_POST['date']) ? $_POST['date'] : '' ?>"
                   placeholder="Введите дату в формате ГГГГ-ММ-ДД">

            <?php if (!empty($errors['date'])) : ?>
                <p class="form__message"><?= $errors['date'] ?></p>
            <?php endif?>
        </div>

        <div class="form__row">
            <label class="form__label" for="file">Файл</label>

            <div class="form__input-file">
                <input class="visually-hidden" type="file" name="file" id="file"
                       value="<?= !empty($_POST['file']) ? $_POST['file'] : '' ?>">

                <label class="button button--transparent" for="file">
                    <span>Выберите файл</span>
                </label>
            </div>
        </div>

        <div class="form__row form__row--controls">
            <?php if (!empty($errors['date']) || !empty($errors['project']) || !empty($errors['name'])): ?>
                <p class="error-message">Пожалуйста, исправьте ошибки в форме</p>
            <?php endif; ?>

            <input class="button" type="submit" name="submit" id="submit" value="Добавить">
        </div>
    </form>
</main>
