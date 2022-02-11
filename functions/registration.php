<?php

require_once dirname(__DIR__) . '/config/connect.php';

/**
 * Проверяем существует ли емеил
 * @param $post $_POST
 * @return bool|string
 */

function getEmail($post): bool|string
{
    $con = mysqli_connect(HOST, USER, PASS, NAME);
    mysqli_set_charset($con, "utf8");

    if (!$con) {
        print(sprintf('Ошибка подключения: %s', mysqli_connect_error()));
    }

    $email = mysqli_real_escape_string($con, $post);

    $sql = "SELECT email FROM users WHERE email='$email' LIMIT 1";

    $result_query = mysqli_query($con, $sql);

    $result = mysqli_fetch_all($result_query, MYSQLI_ASSOC);

    if ($result) {
        return 'Почта уже существует';
    }

    return false;
}

/**
 * Проверяем емеил на корректность
 * @param $post $_POST
 * @return bool|string
 */

function validateEmail($post): bool|string
{
    $haveEmail = getEmail($post);

    if ($haveEmail) {
        return 'Пользователь с этим Email уже зарегистрирован';
    }

    if (!filter_var($post, FILTER_VALIDATE_EMAIL)) {
        return "Введите корректный email";
    }

    return false;
}

/**
 * Проверяем длинну поля
 * @param $key 'имя' поля
 * @param int $min 'минимальное' значение
 * @return bool|string
 */

function getLenStr($key, int $min = 3): bool|string
{
    $len = strlen(trim($_POST[$key]));

    if ($len < $min) {
        return 'Длина поля должна быть от ' . $min . ' символов';
    }

    return false;
}

/**
 * Проверяем поля на валидность
 * @return array
 */

function validateRegister(): array
{
    $errors = [];

    $requiredFields = ['email', 'password', 'name'];

    foreach ($requiredFields as $fields) {
        $checkEmptiness = validateFilled($fields);

        if ($checkEmptiness) {
            $errors[$fields] = validateFilled($fields);
        }
    }

    if (empty($errors)) {
        $checkEmail = validateEmail($_POST['email']);

        if ($checkEmail) {
            $errors['email'] = validateEmail($_POST['email']);
        }
    }

    $checkLenPass = getLenStr('password', 6);

    if ($checkLenPass) {
        $errors['password'] = getLenStr('password', 6);
    }

    $checkLenName = getLenStr('name');

    if ($checkLenName) {
        $errors['name'] = getLenStr('name');
    }

    return $errors;
}

/**
 * Добавляем пользователя
 * @param $email 'почта'
 * @param $password 'пароль'
 * @param $name 'имя'
 * @return string|bool
 */

function addUser($post): bool|string
{

    $con = mysqli_connect(HOST, USER, PASS, NAME);
    mysqli_set_charset($con, "utf8");

    if (!$con) {
        print(sprintf('Ошибка подключения: %s', mysqli_connect_error()));
    }

    $email = mysqli_real_escape_string($con, trim($post['email']));
    $password = mysqli_real_escape_string($con, trim($post['password']));
    $name = mysqli_real_escape_string($con, trim($post['name']));

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (email, password, name) VALUES ('$email', '$passwordHash', '$name')";


    if (mysqli_query($con, $sql)) {
        var_dump("Данные успешно добавлены");
    } else {
        var_dump("Ошибка: " . mysqli_error($con));
    }

    return false;
}
