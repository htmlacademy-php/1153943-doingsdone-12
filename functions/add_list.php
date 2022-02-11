<?php

require_once dirname(__DIR__) .'/config/connect.php';

/**
 * Функция Проверяет поле на заполненность
 * @param $name "имя" пользователя
 * @return string|bool
 * */

function validateName($name): bool|string
{
    if (empty(trim($name))) {
        return 'Поле не заполнено';
    }

    return false;
}

/**
 * Функция добавляет задачу
 * @param $userId "id" пользователя
 * @param $postName "имя" пользователя
 * @return int|string
 * */

function addList($userId, $postName): int|string
{
    $con = mysqli_connect(HOST, USER, PASS, NAME);
    mysqli_set_charset($con, "utf8");

    if (!$con) {
        print(sprintf('Ошибка подключения: %s', mysqli_connect_error()));
    }

    $safeName = mysqli_real_escape_string($con, trim($postName));

    $sql = "INSERT INTO list (user_id, title) VALUES ('$userId', '$safeName')";

    if (mysqli_query($con, $sql)) {
        var_dump("Данные успешно добавлены");
    } else {
        var_dump("Ошибка: " . mysqli_error($con));
    }

    return mysqli_insert_id($con);
}
