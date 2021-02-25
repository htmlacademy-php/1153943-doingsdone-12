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

    //проверяем поля для задачи
    function getFormAddList(){
        $connect = connect();
        $errors = [];

        $safeSubmit = mysqli_real_escape_string($connect, $_POST['submit']);

        if ($safeSubmit) {
            $safeName = mysqli_real_escape_string($connect, trim($_POST['name']));

            if (empty($safeName)) {
                $errors['name'] = 'Поле не заполнено';
            }

            if (count($errors)) {
                return $errors;
            }

            if (empty($errors)) {
                addTaskList($connect, $_SESSION['user']['id'], $safeName);
                header('Location: /index.php');
                exit;
            }
        }
    }

    $formAddList = getFormAddList();

    $nameUser = nameUser();
    $sql = sqlInquiry();

    $taskListContent = include_template('addTaskList.php', ['arrCategory' => $sql[1], 'errors' => $formAddList]);
    $layoutContent = include_template('layout.php', [
        'content' => $taskListContent,
        'title' => "Дела в порядке",
        'user' => $nameUser,
        'errors' => $formAddList,
    ]);

    print($layoutContent);
