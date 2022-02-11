<?php

require 'settings.php';

$errors = [];

if (checkSession()) {
    $searchSql = htmlspecialchars(trim(filter_input(INPUT_GET, 'search')));

    $tasks = getTask();

    if (!empty($_GET['type_list'])) {
        $tasks = getTypeListTask($_GET['type_list']);
    }

    if (!empty($_GET['task_id']) && !empty($_GET['check'])) {
        performTask($_GET['task_id'], $_GET['check']);
    }

    if (!empty($_GET['sort_date'])) {
        $tasks = showTasksByDate($_SESSION['id'], $_GET['sort_date']);
    }

    if (!empty($_GET['search'])) {
        $tasks = getSearchTasks($_GET['search']);
    }

    $pageContent = include_template('main.php', [
        'tasks' => $tasks,
        'searchSql' => $searchSql,
    ]);
} else {
    $pageContent = include_template('guest.php', []);
}

$layoutContent = include_template('layout.php', [
    'content' => $pageContent,
    'title' => 'Дела в порядке',
]);

print($layoutContent);
