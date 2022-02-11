<?php

require_once dirname(__DIR__) . '/settings.php';

if (!checkSession()) {
    header('location: /auth/');
}

$errors = [];

if ($_POST) {
    $errors = validateAddTask();

    var_dump($_POST['date']);

    if (empty($errors)) {
        $file = null;

        if (!empty($_FILES['file']) && $_FILES['file']['error'] !== 4) {
            $file = validateFiles($_FILES, 'file');
        }

        addTask($_SESSION['id'], $_POST, $file);
        header('Location: /');
        exit;
    }
}

$taskContent = include_template('addTask.php', [
    'categories' => getList(),
    'errors' => $errors,
]);

$layoutContent = include_template('layout.php', [
    'content' => $taskContent,
    'title' => "Дела в порядке",
]);

print($layoutContent);
