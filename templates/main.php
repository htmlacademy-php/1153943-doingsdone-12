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

    <a class="button button--transparent button--plus content__side-button"
    href="addList.php" target="project_add">Добавить проект</a>
</section>

<main class="content__main">
    <h2 class="content__main-heading">Список задач</h2>

    <form class="search-form" action="index.php" method="get" autocomplete="off">
        <input class="search-form__input" type="text" name="search" value="<?= $searchSql ?>" placeholder="Поиск по задачам">

        <input class="search-form__submit" type="submit" name="" value="Искать">
    </form>

    <div class="tasks-controls">
        <nav class="tasks-switch">
            <a href="/" class="tasks-switch__item tasks-switch__item--active">Все задачи</a>
            <a href="/" class="tasks-switch__item">Повестка дня</a>
            <a href="/" class="tasks-switch__item">Завтра</a>
            <a href="/" class="tasks-switch__item">Просроченные</a>
        </nav>

        <label class="checkbox">

            <input class="checkbox__input visually-hidden show_completed" <?= $safeCompleted ? 'checked' : '' ?> type="checkbox">

            <span class="checkbox__text">Показывать выполненные</span>
        </label>
    </div>

    <table class="tasks">

        <?php foreach ($arrCaseSheet as $task): ?>

            <?php if(!$safeCompleted && $task['is_done']): continue?><?php endif; ?>

                <tr class="tasks__item task
                <?=$task['dateImportant'] ? 'task--important' : ''?>
                <?=$task['is_done'] ? 'task--completed' : '' ?> "type="checkbox">
                    <td class="task__select">
                        <label class="checkbox task__checkbox">
                            <input class="checkbox__input visually-hidden task__checkbox" <?=$getsafeIsDone == $task['id'] && $getsafeisCheck ? 'checked' : '' ?> type="checkbox" value="<?=$task['id']?>">
                            <span class="checkbox__text"><?=$task['title']?></span>
                        </label>
                    </td>
                    <td class="task__file">
                        <?php if (!empty($task['file'])) :?>

                            <a class="download-link" href="<?=$task['file']?>">
                                Home.psd
                            </a>

                        <?php endif; ?>
                    </td>
                    <td class="task__date"><?=$task['date_deadline']?></td>
                </tr>

        <?php endforeach; ?>

        <?php if (!empty($_GET['search']) && empty($arrCaseSheet)): ?>
            <p class="error-message">Ничего не найдено по вашему запросу</p>
        <?php endif; ?>
    </table>
</main>
