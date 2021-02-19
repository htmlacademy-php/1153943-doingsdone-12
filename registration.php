<?php

    require_once 'helpers.php';
    require_once 'function_sql.php';
    require_once 'function.php';


    $errors = [];
    $errorsSql = [];
    $requiredFields = ['email', 'password', 'name'];

    function emailCheck($connect){
        $usersInfo = [];

        $sqlUsersInfo = "SELECT * FROM users";
        $result = mysqli_query($connect, $sqlUsersInfo);

        if ($result) {
            $usersInfo = mysqli_fetch_all($result, MYSQLI_ASSOC);
        } else {
            $error = mysqli_error($connect);
            print ("Ошибка MySQL" . $error);
        }
        return $usersInfo;
    }

    $connect = connect();
    $users = emailCheck($connect);

    function addUser($con, $email, $password, $nameUser){

        if (!$con) {
            $error = mysqli_connect_error();

            $errors[] = "Ошибка подключения к базе данных " . $error;
        }

        $safeEmail = mysqli_real_escape_string($con, trim($email));
        $safePassword = mysqli_real_escape_string($con, trim($password));
        $safeNameUser = mysqli_real_escape_string($con, trim($nameUser));

        $passwordHash = password_hash($safePassword, PASSWORD_DEFAULT);

        if (password_verify($safePassword, $passwordHash)) {
            $sqlAddUser = "INSERT INTO users (email, password, name) VALUES ('$safeEmail', '$passwordHash', '$safeNameUser')";
        } else {
            echo 'Пароль неправильный.';
        }

        $result = mysqli_query($con, $sqlAddUser);

        if (!$result) {
            $error = mysqli_error($con);
            $errors[] =  "Ошибка MySQL User" . $error;
        }
    }

    if($_POST['submit']) {
        foreach ($requiredFields as $fields) {
            if (empty($_POST[$fields])) {
                $errorsSql[$fields] = 'Поле не заполнено';
            }
        }

        if (!empty($_POST['email'])) {
            if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) === false) {
                $errorsSql['email'] = 'Введите корректный Email';
            }

            foreach ($users as $user) {
                if ($user['email'] === $_POST['email']) {
                    $errorsSql['email'] = 'Пользователь с этим Email уже зарегистрирован';
                }
            }
        }

        if (count($errorsSql)) {
            $errors[] = implode(", ", $errorsSql);
        }

        if(empty($errorsSql)) {
            addUser($connect, $_POST['email'], $_POST['password'], $_POST['name']);
            header('Location: /index.php');
            exit;
        }
    }

    $registrationContent = include_template('register.php', ['errors' => $errorsSql,]);

    $layoutContent = include_template('layout.php', [
        'content' => $registrationContent,
        'title' => "Дела в порядке",
        'user' => '$nameUser',
        'errors' => $errors,
    ]);

    print($layoutContent);
