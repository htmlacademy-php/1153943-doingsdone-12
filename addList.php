<?php
    session_start();

    require_once 'helpers.php';
    require_once 'function_sql.php';
    require_once 'function.php';

    $errors = [];

    // добавляет проект
    function addTaskList($connect, $userId, $taskList) {
        if (!$connect) {
            $error = mysqli_connect_error();

            $errors[] = "Ошибка подключения к базе данных " . $error;
        }

        $sqlAddList = "INSERT INTO list (user_id, title) VALUES ('$userId', '$taskList')";

        $result = mysqli_query($connect, $sqlAddList);

        if (!$result) {
            $error = mysqli_error($connect);
            $errors[] =  "Ошибка MySQL" . $error;
        }
    }

    //проверяем поля для задачи
    if($_POST['submit']) {
        $connect = connect();
        $safeName = mysqli_real_escape_string($connect, trim($_POST['name']));

        if(empty($safeName)) {
            $errors['name'] = 'Поле не заполнено';
        }

        if(empty($errors)) {
            addTaskList($connect, $_SESSION['user']['id'],  $safeName);
            header('Location: /index.php');
            exit;
        }
    }

    $nameUser = nameUser();
    $sql = sqlInquiry();

    $taskListContent = include_template('addTaskList.php', ['arrCategory' => $sql[1], 'errors' => $errors]);
    $layoutContent = include_template('layout.php', [
        'content' => $taskListContent,
        'title' => "Дела в порядке",
        'user' => $nameUser,
        'errors' => $errors,
    ]);

    print($layoutContent);
