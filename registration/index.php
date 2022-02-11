<?php

require_once dirname(__DIR__) . '/settings.php';

$errors = [];

if ($_POST) {
    $errors = validateRegister();

    if (empty($errors)) {

        addUser($_POST);

        header('Location: /auth/');
        exit;
    }
}

$registrationContent = include_template('registration.php', [
    'errors' => $errors,
]);

$layoutContent = include_template('layout.php', [
    'content' => $registrationContent,
    'title' => "Дела в порядке",
]);

print($layoutContent);
