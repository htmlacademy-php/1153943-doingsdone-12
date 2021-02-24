<?php
    session_start();

    require_once 'helpers.php';
    require_once 'function_sql.php';
    require_once 'function.php';

    //Функция рандомного показа выполненных задач

    // Сюда приходят ошибки
    $errors = [];

    // id пользователя если он авторизирован
    if(isset($_SESSION['user'])) {
        $userId = $_SESSION['user']['id'];
    }

    // проверяем существует ли проект
    function projectExistenceCheck($projectId){
        $result = false;

        $connect = connect();

        if ($projectId) {

            $sql = "SELECT * FROM `list` WHERE `id` = " . (string)$projectId;
            $sqlResult = mysqli_query($connect, $sql);

            if ($sqlResult && isset($sqlResult->num_rows) && $sqlResult->num_rows > 0) {
                $result = true;
            }
        }

        return $result;
    }

    // смотрим значение номера проекта
    function valueIntCheck($valueName){
        $result = true;

        if (!filter_input(INPUT_GET, $valueName, FILTER_SANITIZE_NUMBER_INT)) {
            $result = false;
        }

        return $result;
    }

    // проверяем идентификатор проекта
    function validateProjectId($projectId){
        $result = false;

        if (valueIntCheck('category_id') && projectExistenceCheck($projectId)) {
            $result = true;
        }

        return $result;
    }

    // sql[0] это задач
   // sql[1] это список
    if(isset($_SESSION['user'])) {
        $sql = sqlInquiry();
        $currentProjectId = currentProjectId();
    }

    // получаем данные об имени
    if(isset($_SESSION['user'])) {
        $nameUser = nameUser();
    } else {
        $nameUser = 'Гость';
    }

    function getsafeComplited() {
        $connect = connect();

        return mysqli_real_escape_string($connect, $_GET['show_completed']);
    }

    function getsafeIsDone() {
        $connect = connect();

        return mysqli_real_escape_string($connect, $_GET['task_id']);
    }

    function getsafeisCheck() {
        $connect = connect();

        return mysqli_real_escape_string($connect, $_GET['check']);
    }

    $safeCompleted = getsafeComplited();
    $getsafeIsDone = getsafeIsDone();
    $getsafeisCheck = getsafeisCheck();
    $searchSql = htmlspecialchars(trim(filter_input(INPUT_GET, 'search')));

    function updateIsDone($connect, $userId, $task){

        if (!$connect) {
            $error = mysqli_connect_error();

            $errors[] = "Ошибка подключения к базе данных " . $error;
        }

        $safeUserId = mysqli_real_escape_string($connect, $userId);
        
        if(!$task["is_done"]) {
            $sqlAddTask = "UPDATE tasks SET is_done = 1 WHERE id = '$safeUserId'";
        } else {
            $sqlAddTask = "UPDATE tasks SET is_done = 0 WHERE id = '$safeUserId'";
        }

        $result = mysqli_query($connect, $sqlAddTask);

        if (!$result) {
            $error = mysqli_error($connect);
            $errors[] =  "Ошибка MySQL" . $error;
        }
    }

    foreach ($sql[0] as $task) {
        if ($getsafeIsDone == $task['id'] && $getsafeisCheck) {
            $connect = connect();
            updateIsDone($connect, $task['id'], $task);
            header('Location: /index.php');
            exit;
        }
    }

    // смотрим пользователь авторизирован или нет и показываем содержимое
    if(isset($_SESSION['user'])){
        $pageContent = include_template('main.php', ['arrCategory' => $sql[1], 'arrCaseSheet' => $sql[0], 'safeCompleted' => $safeCompleted, 'getsafeIsDone' => $getsafeIsDone, 'getsafeisCheck' => $getsafeisCheck, 'searchSql' => $searchSql, 'task_arr' => $task_arr,]);

        if ($currentProjectId) {
            if (!validateProjectId($currentProjectId)) {
                $pageContent = include_template('404.php', []);
            }
        }

    } else {
        $pageContent = include_template('guest.php', []);
    }

    $layoutContent = include_template('layout.php', ['content' => $pageContent, 'title' => 'Дела в порядке', 'user' => $nameUser, 'errors' => $errors]);

    print($layoutContent);
