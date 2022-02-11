<?php

require_once dirname(__DIR__) . '/config/connect.php';

/**
 * Проверяем что почта есть в бд
 * @param $email $_POST['email']
 * @return bool|array
 */

function checkEmail($email): bool|array
{

    $con = mysqli_connect(HOST, USER, PASS, NAME);

    $sql = "SELECT id, email, password, name FROM users WHERE email=? LIMIT 1";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        return false;
    }

    return $user;
}

/**
 * Проверяем поля на ошибки и если их нет входим на сайт
 * @return array
 */

function validateLogin(): array
{
    $errors = [];

    $authorized = false;

    $requiredFields = ['email', 'password'];

    $con = mysqli_connect(HOST, USER, PASS, NAME);
    mysqli_set_charset($con, "utf8");

    if (!$con) {
        print(sprintf('Ошибка подключения: %s', mysqli_connect_error()));
    }

    $email = mysqli_real_escape_string($con, trim($_POST['email']));
    $password = mysqli_real_escape_string($con, trim($_POST['password']));

    foreach ($requiredFields as $fields) {

        if (empty($_POST[$fields])) {
            $errors[$fields] = 'Заполните все поля';
        }
    }

    $user = checkEmail($email);

    if (!$user) {
        $errors['email'] = 'Почта не найдена';
    }

    if ($user && $user['email'] === $email && password_verify($password, $user['password'])) {
        $authorized = true;
    } else {
        $errors['password'] = 'Пароль не верен';
    }

    if ($authorized && empty($errors)) {
        session_start();

        setcookie('visit', $user['email'], time() + (60 * 60 * 24 * 30), '/');

        $_SESSION['name'] = $user['name'];
        $_SESSION['id'] = $user['id'];

        header('Location: /');
        exit;
    }

    return $errors;
}
