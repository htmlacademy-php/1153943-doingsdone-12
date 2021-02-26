<?php
    session_start();

    $_SESSION = [];

    require_once 'helpers.php';
    require_once 'function_sql.php';
    require_once 'function.php';

    // получаем массив пользователей из бд
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

    // добавляем пользователя если все ок
    function addUser($con, $email, $password, $nameUser){

        $safeEmail = mysqli_real_escape_string($con, trim($email));
        $safePassword = mysqli_real_escape_string($con, trim($password));
        $safeNameUser = mysqli_real_escape_string($con, trim($nameUser));

        $passwordHash = password_hash($safePassword, PASSWORD_DEFAULT);

        $sqlAddUser = "INSERT INTO users (email, password, name) VALUES ('$safeEmail', '$passwordHash', '$safeNameUser')";

        $result = mysqli_query($con, $sqlAddUser);

        if (!$result) {
            $error = mysqli_error($con);
            $errors[] =  "Ошибка MySQL User" . $error;
        }
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

    function validateEmail($email, $users) {
        $connect = connect();

        $safeSubmit = mysqli_real_escape_string($connect, $_POST['submit']);

        if($safeSubmit) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                return "Введите корректный email";
            }

            foreach ($users as $user) {
                if ($user['email'] === $email) {
                    return 'Пользователь с этим Email уже зарегистрирован';
                }
            }
        }
    }

    function validateErrors() {
        $connect = connect();
        $errors = [];

        $safeEmail = mysqli_real_escape_string($connect, $_POST['email']);

        $requiredFields = ['email', 'password', 'name'];

        $users = getArrSql($connect);

        foreach ($requiredFields as $fields) {
            $errors[$fields] = validateFilled($fields);
        }

        if (!empty($safeEmail)) {
            $errors['email'] = validateEmail($safeEmail, $users);
        }

        return $errors;
    }

    function checkIn() {
        $connect = connect();

        $safeEmail = mysqli_real_escape_string($connect, $_POST['email']);
        $safePass = mysqli_real_escape_string($connect, $_POST['password']);
        $safeName = mysqli_real_escape_string($connect, $_POST['name']);

        addUser($connect, $safeEmail, $safePass, $safeName);
        header('Location: /auth.php');
        exit;
    }

    // регистрируем если все ок
    function getFormRegistration(){
        $connect = connect();

        $safeSubmit = mysqli_real_escape_string($connect, $_POST['submit']);

        if($safeSubmit) {
            $errors = implode(validateErrors());

            if (empty($errors)) {
                checkIn();
            }
        }
    }

    $errors = validateErrors();
    $formRegistration = getFormRegistration();

    $registrationContent = include_template('register.php', ['errors' => $errors,]);

    $layoutContent = include_template('layout.php', [
        'content' => $registrationContent,
        'title' => "Дела в порядке",
    ]);

    print($layoutContent);
