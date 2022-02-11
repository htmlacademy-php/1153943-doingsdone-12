<?php

require_once dirname(__DIR__) . '/settings.php';

if (!checkSession()) {
    header('location: /auth/');
}

$errors = [];

if ($_POST) {
    $field = validateName($_POST['name']);

    if ($field) {
        $errors['name'] = $field;
    }

    if(empty($errors)) {
        addList($_SESSION['id'], $_POST['name']);

        header('Location: /');
        exit;
    }
}

$pageContent = include_template('addTaskList.php', [
    'errors' => $errors,
]);

$layoutContent = include_template('layout.php', [
    'content' => $pageContent,
    'title' => "Дела в порядке",
]);

print($layoutContent);
