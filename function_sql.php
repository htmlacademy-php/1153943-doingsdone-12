<?php

    require_once 'function.php';
    $errors = [];

    // соединяемся с бд
    function connect() {
        $con = mysqli_connect("localhost", "root", "root", "schema");
        mysqli_set_charset($con, "utf8");

        if (!$con) {
            $error = mysqli_error($con);
            throw new Exception('Ошибка SQL запроса ' . $error);
        }

        return $con;
    }

    // реализация поиска слов
    function getSearchTasks($con, $search){
        $u_id = $_SESSION['user']['id'];
        $sql = 'SELECT * FROM tasks WHERE user_id = "' . $u_id . '" AND MATCH(title) AGAINST (? IN BOOLEAN MODE)';

        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, 's', $search);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);

        $task_arr = mysqli_fetch_all($res, MYSQLI_ASSOC);

        return $task_arr;
    }

    // получаем ссылку на массив задач
    function getSqlTaskList($safeCategory, $user) {
        $tasks = "SELECT * FROM tasks WHERE user_id = ".$user;

        if($safeCategory) {
            $tasks = "SELECT * FROM tasks WHERE list_id =".$safeCategory." AND user_id = ".$user;
        }

        return $tasks;
    }

    // получаем готовый массив скл из бд
    function getSqlArr($inquiry, $con) {

        $result = mysqli_query($con, $inquiry);

        if (!$result) {
            $error = mysqli_error($con);
            throw new Exception('Ошибка SQL запроса ' . $error);
        }

        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    // проверяем наличие category_id в строке
    function currentProjectId() {
        $connect = connect();
        $safeCategory = mysqli_real_escape_string($connect, $_GET['category_id']);

        $currentProjectId = $safeCategory ?? '';
        $currentProjectId = mysqli_real_escape_string($connect, (string)$currentProjectId);

        return $currentProjectId;
    }

    // получаем имя
    function nameUser() {
        $name = 'User';

        $connect = connect();
        $userId = $_SESSION['user']['id'];
        $sqlName = "SELECT name FROM users WHERE id = ".$userId;
        $arrNameUser = getSqlArr($sqlName, $connect);

        foreach ($arrNameUser as $user) {
            $name = $user['name'];
        }

        return $name;
    }

    // делаем полный массив всех данных из бд
    function sqlInquiry() {
        $connect = connect();
        $userId = $_SESSION['user']['id'];

        $safeCompleted = mysqli_real_escape_string($connect, $_GET['show_completed']);
        $safeCategory = mysqli_real_escape_string($connect, $_GET['category_id']);

        $sqlTasks = getSqlTaskList($safeCategory, $userId);
        $sqlList = "SELECT * FROM list WHERE user_id = ".$userId;
        $sqlTasksCount = "SELECT * FROM tasks WHERE user_id = ".$userId;

        try {
            $arrCategory = getSqlArr($sqlList, $connect);
            $arrCaseSheet = getSqlArr($sqlTasks, $connect);
            $arrCaseSheetCount = getSqlArr($sqlTasksCount, $connect);

            if (isset($_GET['search'])) {
                $connect = connect();
                $search = trim(filter_input(INPUT_GET, 'search', FILTER_SANITIZE_SPECIAL_CHARS));

                if (!empty($search)) {
                    $arrCaseSheet = getSearchTasks($connect, $search);
                }
            }
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }

        if ($connect) {
            mysqli_close($connect);
        }

        list($arrCaseSheet, $arrCategory) = updateArray($arrCaseSheet, $arrCategory, $arrCaseSheetCount, $safeCompleted);

        return updateArray($arrCaseSheet, $arrCategory, $arrCaseSheetCount, $safeCompleted);
    }
