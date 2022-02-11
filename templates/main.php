<main class="content__main">
    <h2 class="content__main-heading">Список задач</h2>

    <form class="search-form" action="/" method="get" autocomplete="off">
        <input class="search-form__input" type="text" name="search" value="<?= $searchSql ?>"
               placeholder="Поиск по задачам">

        <input class="search-form__submit" type="submit" name="" value="Искать">
    </form>

    <div class="tasks-controls">
        <nav class="tasks-switch">
            <a href="/"
               class="tasks-switch__item <?= empty($_GET['sort_date']) ? 'tasks-switch__item--active' : '' ?>">Все
                задачи</a>
            <a href="?sort_date=today"
               class="tasks-switch__item <?= !empty($_GET['sort_date']) && $_GET['sort_date'] == 'today' ? 'tasks-switch__item--active' : '' ?>">Повестка
                дня</a>
            <a href="?sort_date=tomorrow"
               class="tasks-switch__item <?= !empty($_GET['sort_date']) && $_GET['sort_date'] == 'tomorrow' ? 'tasks-switch__item--active' : '' ?>">Завтра</a>
            <a href="?sort_date=expired"
               class="tasks-switch__item <?= !empty($_GET['sort_date']) && $_GET['sort_date'] == 'expired' ? 'tasks-switch__item--active' : '' ?>">Просроченные</a>
        </nav>

        <label class="checkbox">

            <input class="checkbox__input visually-hidden show_completed"
                <?= !empty($_GET['show_completed']) ? 'checked' : '' ?> type="checkbox">

            <span class="checkbox__text">Показывать выполненные</span>
        </label>
    </div>

    <table class="tasks">

        <?php foreach ($tasks as $task): ?>

            <?php if ($task['is_done'] && empty($_GET['show_completed'])): continue ?>

            <?php endif; ?>

            <tr class="tasks__item task
                <?= getTimeTask($task) ? 'task--important' : '' ?>

                <?= $task['is_done'] ? 'task--completed' : '' ?> " type="checkbox">
                <td class="task__select">
                    <label class="checkbox task__checkbox">
                        <input class="checkbox__input visually-hidden task__checkbox" type="checkbox"
                               value="<?= $task['id'] ?>">
                        <span class="checkbox__text"><?= $task['title'] ?></span>
                    </label>
                </td>
                <td class="task__file">

                    <?php if (!empty($task['file'])) : ?>

                        <a class="download-link" href="<?= $task['file'] ?>">
                            FILE
                        </a>

                    <?php endif; ?>
                </td>
                <td class="task__date"><?= $task['date_deadline'] ?></td>
            </tr>

        <?php endforeach; ?>

        <?php if (!empty($_GET['search']) && empty($tasks)): ?>
            <p class="error-message">Ничего не найдено по вашему запросу</p>
        <?php endif; ?>
    </table>
</main>
