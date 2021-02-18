<?php

    require_once 'helpers.php';
    require_once 'function_sql.php';
    require_once 'function.php';

    $required_fields = ['name', 'project'];

    $show_complete_tasks = rand(0, 1);

    const USER_ID = 2;

    $errorsSql = [];
    $errors = [];

    $connect = connect();

    function addTask($connect, $userId, $taskList, $taskName, $date, $fileUrl){

        if (!$connect) {
            $error = mysqli_connect_error();

            $errors[] = "Ошибка подключения к базе данных " . $error;
        }

        $safeTaskName = mysqli_real_escape_string($connect, $taskName);
        $safeFileUrl = mysqli_real_escape_string($connect, $fileUrl);

        if ($date === NULL || $date === '') {
            $sqlAddTask = "INSERT INTO tasks (user_id, list_id, title, file) VALUES ($userId, $taskList, '$safeTaskName', '$safeFileUrl')";
        } else {
            $sqlAddTask = "INSERT INTO tasks (user_id, list_id, title, date_deadline, file) VALUES ($userId, $taskList, '$safeTaskName', '$date', '$safeFileUrl')";
        }

        $result = mysqli_query($connect, $sqlAddTask);

        if (!$result) {
            $error = mysqli_error($connect);
            $errors[] =  "Ошибка MySQL" . $error;
        }
    }

    if($_POST['submit']) {
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                $errorsSql[$field] = 'Поле не заполнено';
            }
        }

        if(!is_date_valid($_POST['date']) && $_POST['date'] !== NULL && $_POST['date'] !== '') {
            $errorsSql['date'] = 'Неверный формат даты';
        }

        if(strtotime($_POST['date']) + 86400 < time() && $_POST['date']) {
            $errorsSql['date'] = 'Некорректная дата';
        }

        if (is_uploaded_file($_FILES['file']['tmp_name'])) {
            $fileName = $_FILES['file']['name'];
            $filePath = __DIR__ . '/uploads/';
            $fileUrl = '/uploads/' . $fileName;

            move_uploaded_file($_FILES['file']['tmp_name'], $filePath . $fileName);
            print("<a href='$fileUrl'>$fileName</a>");

        } else {
            $fileUrl = '';
        }
        var_dump($errorsSql);

        if (count($errorsSql)) {
            var_dump($errorsSql);
        }

        if(empty($errorsSql)) {
            addTask($connect, 2, $_POST['project'], $_POST['name'], $_POST['date'], $fileUrl);
            header('Location: /index.php');
            exit;
        }
    }

    $safeCategory = mysqli_real_escape_string($connect, $_GET['category_id']);

    $sqlTasks = getSqlTaskList($safeCategory, USER_ID);
    $sqlList = "SELECT * FROM list WHERE user_id = ".USER_ID;
    $sqlTasksCount = "SELECT * FROM tasks WHERE user_id = ".USER_ID;
    $sqlName = "SELECT name FROM users WHERE id = ".USER_ID;

    try {
        $arrCategory = getSqlArr($sqlList, $connect);
        $arrCaseSheet = getSqlArr($sqlTasks, $connect);
        $arrCaseSheetCount = getSqlArr($sqlTasksCount, $connect);
        $arrNameUser = getSqlArr($sqlName, $connect);
    } catch (Exception $e) {
        $errors[] = $e->getMessage();
    }

    list($arrCaseSheet, $arrCategory) = updateArray($arrCaseSheet, $arrCategory, $arrCaseSheetCount, $show_complete_tasks);
    updateArray($arrCaseSheet, $arrCategory, $arrCaseSheetCount, $show_complete_tasks);

    $nameUser = nameUser($arrNameUser);

    $taskContent = include_template('addTask.php', [
        'arrCategory' => $arrCategory,
        'errors' => $errorsSql,
    ]);

    $layoutContent = include_template('layout.php', [
        'content' => $taskContent,
        'title' => "Дела в порядке",
        'user' => $nameUser,
        'errors' => $errors,
    ]);

    print($layoutContent);
