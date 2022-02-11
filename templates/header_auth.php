<?php

$tasks = getTask();
$categories = getList();

?>

<section class="content__side">
    <h2 class="content__side-heading">Проекты</h2>

    <nav class="main-navigation">
        <ul class="main-navigation__list">
            <?php foreach ($categories as $category): ?>
                <li class="main-navigation__list-item <?= !empty($_GET['type_list']) && $_GET['type_list'] == $category['id'] ? 'main-navigation__list-item--active' : '' ?>">
                    <a class="main-navigation__list-item-link" href="/?type_list=<?=$category['id']?>">
                        <?= $category['title'] ?>
                    </a>
                    <span class="main-navigation__list-item-count"><?= getCountTasks($tasks, $category) ?></span>
                </li>

            <?php endforeach; ?>
        </ul>
    </nav>

    <a class="button button--transparent button--plus content__side-button"
       href="/add_list/">Добавить проект</a>
</section>
