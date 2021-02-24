<?php
    require_once 'helpers.php';
    require_once 'function_sql.php';
    require_once 'function.php';

    $required_fields = ['email', 'password'];
    $errors = [];

    function getArrSql($connect){
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

    // проверяем верификацию пароля
    function checkPass($clientPass, $userHass) {
        $pass = false;

        if (password_verify($clientPass, $userHass)) {
            $pass = true;
        }

        return $pass;
    }

    // проверяем что данные есть емаила и пароля
    function checkAuth($client) {
        $connect = connect();

        $safeEmail = mysqli_real_escape_string($connect, trim($_POST['email']));
        $safePassword = mysqli_real_escape_string($connect, trim($_POST['password']));

        $auth = false;
        foreach ($client as $user) {
            if ($user['email'] === $safeEmail && checkPass($safePassword, $user['password'])) {
                $auth = true;
                break;
            }
        }

        return $auth;
    }

    // проверяем поля входа
    if($_POST['submit']) {
        $connect = connect();
        $users = getArrSql($connect);

        $safeEmail = mysqli_real_escape_string($connect, trim($_POST['email']));
        $safePassword = mysqli_real_escape_string($connect, trim($_POST['password']));

        foreach ($required_fields as $fields) {
            if (empty($_POST[$fields])) {
                $errors[$fields] = 'Данные не заполнены';
            }
        }

        if (filter_var($safeEmail, FILTER_VALIDATE_EMAIL) === false) {
            $errors['email'] = 'Введите корректный Email';
        }

        if(empty($errors)) {
            $sql = "SELECT * FROM users WHERE email = '$safeEmail'";
            $result = mysqli_query($connect, $sql);

            $user = $result ? mysqli_fetch_array($result, MYSQLI_ASSOC) : null;

            if(!empty($safeEmail) && !empty($safePassword)) {
                if(checkAuth($users) && $user) {
                    session_start();
                    $_SESSION['user'] = $user;
                    header('Location: /index.php');
                    exit;
                } else {
                    $errors['email'] = 'Данные не верны';
                }
            }
        }
    }

    $authContent = include_template('addAuth.php', [
        'errors' => $errors,
    ]);

    $layoutContent = include_template('layout.php', [
        'content' => $authContent,
        'title' => "Дела в порядке",
        'errors' => $errors,
    ]);

    print($layoutContent);
