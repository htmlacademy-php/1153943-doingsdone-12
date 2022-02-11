<?php

require 'settings.php';

$errors = [];

if (checkSession()) {
    $searchSql = htmlspecialchars(trim(filter_input(INPUT_GET, 'search')));

    $tasks = getTask();

    if (!empty($_GET['type_list'])) {
        $tasks = getTypeListTask($_GET['type_list']);
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
