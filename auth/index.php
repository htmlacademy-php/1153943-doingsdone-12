<?php
require_once dirname(__DIR__) . '/settings.php';

$errors = [];

if ($_POST) {
    $errors = validateLogin();
}

$authContent = include_template('auth.php', [
    'errors' => $errors,
]);

$layoutContent = include_template('layout.php', [
    'content' => $authContent,
    'title' => "Дела в порядке",
]);

print($layoutContent);
