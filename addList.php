<?php
    session_start();

    require_once 'helpers.php';
    require_once 'function_sql.php';
    require_once 'function.php';

    // добавляет проект
    function addTaskList($connect, $userId, $taskList) {

        $sqlAddList = "INSERT INTO list (user_id, title) VALUES ('$userId', '$taskList')";

        $result = mysqli_query($connect, $sqlAddList);

        if (!$result) {
            $error = mysqli_error($connect);
            $errors[] =  "Ошибка MySQL" . $error;
        }
    }

    function validateName($name) {
        $connect = connect();

        $safeSubmit = mysqli_real_escape_string($connect, $_POST['submit']);

        if($safeSubmit) {
            if (empty($name)) {
                return 'Поле не заполнено';
            }
        }
    }

    function validateErrors() {
        $connect = connect();
        $errors = [];

        $safeName = mysqli_real_escape_string($connect, trim($_POST['name']));

        if (empty($safeName)) {
            $errors['name'] = validateName($safeName);
        }

        return $errors;
    }

    function signIn() {
        $connect = connect();

        $safeName = mysqli_real_escape_string($connect, trim($_POST['name']));

        addTaskList($connect, $_SESSION['user']['id'], $safeName);
        header('Location: /index.php');
        exit;
    }

    //проверяем поля для задачи
    function getFormAddList(){
        $connect = connect();

        $safeSubmit = mysqli_real_escape_string($connect, $_POST['submit']);

        if ($safeSubmit) {
            $errors = implode(validateErrors());

            if (empty($errors)) {
                signIn();
            }
        }
    }

    $formAddList = getFormAddList();
    $errors = validateErrors();

    $nameUser = nameUser();
    $sql = sqlInquiry();

    $taskListContent = include_template('addTaskList.php', ['arrCategory' => $sql[1], 'errors' => $errors]);
    $layoutContent = include_template('layout.php', [
        'content' => $taskListContent,
        'title' => "Дела в порядке",
        'user' => $nameUser,
    ]);

    print($layoutContent);
