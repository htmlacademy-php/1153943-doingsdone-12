<div class="content">
    <section class="content__side">
        <h2 class="content__side-heading">Проекты</h2>

        <nav class="main-navigation">
            <ul class="main-navigation__list">
                <?php foreach ($arrCategory as $category): ?>

                    <li class="main-navigation__list-item <?=$_GET['category_id'] == $category['id'] ? 'main-navigation__list-item--active' : ''?>">
                        <a class="main-navigation__list-item-link" href = "<?=$category['url']?>" ><?=$category['title']?></a>
                        <span class="main-navigation__list-item-count"><?=$category['count']?></span>
                    </li>

                <?php endforeach; ?>
            </ul>
        </nav>

        <a class="button button--transparent button--plus content__side-button" href="add.php">Добавить проект</a>
    </section>

    <main class="content__main">
        <h2 class="content__main-heading">Добавление задачи</h2>

        <form class="form"  action="add.php" method="POST" autocomplete="off" enctype="multipart/form-data">
            <div class="form__row">
                <label class="form__label" for="name">Название <sup>*</sup></label>

                <input class="form__input <?=isset($errors['name']) ? 'form__input--error' : ''?>" type="text" name="name" id="name" value="<?=isset($_POST['name']) ? $_POST['name'] : ''?>" placeholder="Введите название">
                <p class="form__message"><?=$errors['name'] ?></p>
            </div>

            <div class="form__row">
                <label class="form__label" for="project">Проект <sup>*</sup></label>

                <select class="form__input form__input--select" name="project" id="project">
                    <?php foreach ($arrCategory as $category): ?>
                        <option value="<?= $category['id'] ?>"><?= $category['title'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form__row">
                <label class="form__label" for="date">Дата выполнения</label>

                <input class="form__input form__input--date <?=isset($errors['date']) ? 'form__input--error' : ''?>" type="text" name="date" id="date" value="<?=isset($_POST['date']) ? $_POST['date'] : ''?>" placeholder="Введите дату в формате ГГГГ-ММ-ДД">
                <p class="form__message"><?=$errors['date'] ?></p>
            </div>

            <div class="form__row">
                <label class="form__label" for="file">Файл</label>

                <div class="form__input-file">
                    <input class="visually-hidden" type="file" name="file" id="file" value="<?=isset($_POST['file']) ? $_POST['file'] : ''?>">

                    <label class="button button--transparent" for="file">
                        <span>Выберите файл</span>
                    </label>
                </div>
            </div>

            <div class="form__row form__row--controls">
                <?php if(!empty($errors)): ?>
                    <p class="error-message">Пожалуйста, исправьте ошибки в форме</p>
                <?php endif; ?>
                <input class="button" type="submit" name="submit" id="submit" value="<?=isset($_POST['submit']) ? 'Добавить' : 'Добавить'?>">
            </div>
        </form>
    </main>
</div>
