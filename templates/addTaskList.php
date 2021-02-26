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

    <a class="button button--transparent button--plus content__side-button" href="addList.php">Добавить проект</a>
</section>

<main class="content__main">
    <h2 class="content__main-heading">Добавление проекта</h2>

    <form class="form"  action="addList.php" method="post" autocomplete="off">
        <div class="form__row">
            <label class="form__label" for="project_name">Название <sup>*</sup></label>

            <input class="form__input <?=isset($errors['name']) ? 'form__input--error' : ''?>" type="text" name="name" id="project_name" value="<?=isset($_POST['name']) ? $_POST['name'] : ''?>" placeholder="Введите название проекта">
            <p class="form__message"><?=$errors['name'] ?></p>
        </div>

        <div class="form__row form__row--controls">
            <input class="button" type="submit" name="submit" value="Добавить">
        </div>
    </form>
</main>
