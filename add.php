<?php
    session_start();

    require_once 'helpers.php';
    require_once 'function_sql.php';
    require_once 'function.php';

    $required_fields = ['name', 'project'];

    $errorsSql = [];
    $errors = [];

    // добавляет задачу
    function addTask($connect, $userId, $taskList, $taskName, $date, $fileUrl){

        if (!$connect) {
            $error = mysqli_connect_error();

            $errors[] = "Ошибка подключения к базе данных " . $error;
        }

        $safeTaskName = mysqli_real_escape_string($connect, $taskName);
        $safeFileUrl = mysqli_real_escape_string($connect, $fileUrl);

        if (empty($date)) {
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
        $connect = connect();

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

        } else {
            $fileUrl = '';
        }

        if (count($errorsSql)) {
            $errors[] = implode(", ", $errorsSql);
        }

        if(empty($errorsSql)) {
            addTask($connect, $_SESSION['user']['id'], $_POST['project'], $_POST['name'], $_POST['date'], $fileUrl);
            header('Location: /index.php');
            exit;
        }
    }

    $nameUser = nameUser();
    $sql = sqlInquiry();

    $taskContent = include_template('addTask.php', [
        'arrCategory' => $sql[1],
        'errors' => $errorsSql,
    ]);

    $layoutContent = include_template('layout.php', [
        'content' => $taskContent,
        'title' => "Дела в порядке",
        'user' => $nameUser,
        'errors' => $errors,
    ]);

    print($layoutContent);
