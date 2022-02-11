<?php

require_once dirname(__DIR__) . '/config/connect.php';

/**
 * проверка на пустоту
 * @param $name 'название' поля
 * @return bool|string
 */

function validateFilled($name): bool|string
{
    if (empty($_POST[$name])) {
        return "Это поле должно быть заполнено";
    }

    return false;
}

/**
 * Добавляет файл
 * @param $fileName $_FILE
 * @param $fileUrl 'название' пути
 * @return bool|string
 */

function validateFiles($fileName, $fileUrl): bool|string
{
    if (!empty($fileName[$fileUrl]) && $fileName[$fileUrl]['error'] !== 4) {
        $file_name = $fileName[$fileUrl]['name'];
        $file_path = dirname(__DIR__) . '/uploads/';

        move_uploaded_file($fileName[$fileUrl]['tmp_name'], $file_path . $file_name);

        return '/uploads/' . $file_name;
    }

    return false;
}

/**
 * Проверяем валидность даты
 * @param $date
 * @return bool|string
 */

function isDateValid($date): bool|string
{
    if ($_POST[$date]) {

        if (!is_date_valid($_POST[$date]) && $_POST[$date] !== '') {
            return 'Неверный формат даты';
        }

        if (strtotime($_POST[$date]) + 86400 < time()) {
            return 'Некорректная дата';
        }
    }
    return false;
}

/**
 * Проверяем поля на ошибки
 * @return array|string
 */

function validateAddTask(): array|string
{
    $errors = [];

    $requiredFields = ['name', 'project'];

    foreach ($requiredFields as $field) {

        $checkEmptiness = validateFilled($field);

        if ($checkEmptiness) {
            $errors[$field] = validateFilled($field);
        }
    }

    $validDate = isDateValid('date');

    if ($validDate) {
        $errors['date'] = $validDate;
    }

    return $errors;
}

/**
 * Добавляет задачу
 * @param $Id 'id' пользователя
 * @param $post $_POST
 * @param null $file $_FILE
 * @return bool|string
 */

function addTask($Id, $post, $file = NULL): bool|string
{
    $con = mysqli_connect(HOST, USER, PASS, NAME);
    mysqli_set_charset($con, "utf8");

    if (!$con) {
        print(sprintf('Ошибка подключения: %s', mysqli_connect_error()));
    }

    $title = mysqli_real_escape_string($con, $post['name']);
    $list = mysqli_real_escape_string($con, $post['project']);

    if ($post['date']) {
        $date = mysqli_real_escape_string($con, $post['date']);

        $sql = "INSERT INTO `tasks` (user_id, list_id, title, date_deadline, file) VALUES ('$Id', '$list', '$title', '$date', '$file')";
    } else {
        $sql = "INSERT INTO `tasks` (user_id, list_id, title, file) VALUES ('$Id', '$list', '$title', '$file')";
    }

    if (mysqli_query($con, $sql)) {
        var_dump("Данные успешно добавлены");
    } else {
        var_dump("Ошибка: " . mysqli_error($con));
    }

    return false;
}
