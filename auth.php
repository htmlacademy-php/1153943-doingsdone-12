<?php
    require_once 'helpers.php';
    require_once 'function_sql.php';
    require_once 'function.php';

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

    // проверяем что данные емаила и пароля есть
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

    function validateFilled($name) {
        $connect = connect();

        $safeSubmit = mysqli_real_escape_string($connect, $_POST['submit']);

        if($safeSubmit) {
            if (empty($_POST[$name])) {
                return "Это поле должно быть заполнено";
            }
        }
    }

    function validateEmail($email) {
        $connect = connect();

        $safeSubmit = mysqli_real_escape_string($connect, $_POST['submit']);

        if($safeSubmit) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                return "Введите корректный email";
            }
        }
    }

    function checkEmail() {
        $connect = connect();
        $safeEmail = mysqli_real_escape_string($connect, trim($_POST['email']));

        $sql = "SELECT * FROM users WHERE email = '$safeEmail'";
        $result = mysqli_query($connect, $sql);

        $user = $result ? mysqli_fetch_array($result, MYSQLI_ASSOC) : null;

        return $user;
    }

    function validateErrors() {
        $connect = connect();
        $errors = [];

        $safeEmail = mysqli_real_escape_string($connect, $_POST['email']);
        $safePassword = mysqli_real_escape_string($connect, trim($_POST['password']));

        $requiredFields = ['email', 'password'];

        $users = getArrSql($connect);

        $user = checkEmail();

        foreach ($requiredFields as $fields) {
            $errors[$fields] = validateFilled($fields);
        }

        if (!empty($safeEmail)) {
            $errors['email'] = validateEmail($safeEmail);
        }

        if (!empty($safeEmail) && !empty($safePassword)) {
            if (!checkAuth($users) && !$user) {
                $errors['email'] = 'Данные не верны';
            }
        }

        return $errors;
    }

    function signIn() {
        $user = checkEmail();

        session_start();
        $_SESSION['user'] = $user;
        header('Location: /index.php');
        exit;
    }

    // проверяем поля входа
    function getFormAuth(){
        $connect = connect();

        $safeSubmit = mysqli_real_escape_string($connect, $_POST['submit']);

        if ($safeSubmit) {
            $errors = implode(validateErrors());

            if (empty($errors)) {
                signIn();
            }
        }
    }

    $getFormAuth = getFormAuth();

    $errors = validateErrors();

    $authContent = include_template('addAuth.php', [
        'errors' => $errors,
    ]);

    $layoutContent = include_template('layout.php', [
        'content' => $authContent,
        'title' => "Дела в порядке",
    ]);

    print($layoutContent);
