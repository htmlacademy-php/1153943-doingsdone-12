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

    // добавляем проверку полей
    function getFormRegistration(){
        $connect = connect();
        $errors = [];

        $users = getArrSql($connect);
        $requiredFields = ['email', 'password', 'name'];

        $safeSubmit = mysqli_real_escape_string($connect, $_POST['submit']);
        $safeEmail = mysqli_real_escape_string($connect, $_POST['email']);
        $safePass = mysqli_real_escape_string($connect, $_POST['password']);
        $safeName = mysqli_real_escape_string($connect, $_POST['name']);

        if($safeSubmit) {
            foreach ($requiredFields as $fields) {
                if (empty($_POST[$fields])) {
                    $errors[$fields] = 'Поле не заполнено';
                }
            }

            if (!empty($safeEmail)) {
                if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) === false) {
                    $errors['email'] = 'Введите корректный Email';
                }

                foreach ($users as $user) {
                    if ($user['email'] === $safeEmail) {
                        $errors['email'] = 'Пользователь с этим Email уже зарегистрирован';
                    }
                }
            }

            if (count($errors)) {
                return $errors;
            }

            if(empty($errorsSql)) {
                addUser($connect, $safeEmail, $safePass, $safeName);
                header('Location: /auth.php');
                exit;
            }
        }
    }

    $formRegistration = getFormRegistration();

    $registrationContent = include_template('register.php', ['errors' => $formRegistration,]);

    $layoutContent = include_template('layout.php', [
        'content' => $registrationContent,
        'title' => "Дела в порядке",
        'errors' => $formRegistration,
    ]);

    print($layoutContent);
