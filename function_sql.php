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

    function showTasksByDate ($user_id, $tab) {
        if ($tab === 'today') {
            $sql = "SELECT * FROM tasks WHERE user_id = ".$user_id ." AND date_deadline = CURDATE()";
        } elseif ($tab === 'tomorrow') {
            $sql = "SELECT * FROM tasks WHERE user_id = ".$user_id ." AND date_deadline = ADDDATE(CURDATE(),INTERVAL 1 DAY)";
        } elseif ($tab === 'expired') {
            $sql = "SELECT * FROM tasks WHERE user_id = ".$user_id ." AND date_deadline < CURDATE()";
        }

        return $sql;
    }

    function showTasksByCategory ($user_id, $safeCategory) {

        $sql = "SELECT * FROM tasks WHERE list_id =".$safeCategory." AND user_id = ".$user_id;

        return $sql;
    }

    // получаем ссылку на массив задач
    function getSqlTaskList($safeCategory, $user, $safeTime) {
        $tasks = "SELECT * FROM tasks WHERE user_id = ".$user;

        if(!$safeTime && $safeCategory) {
            $tasks = showTasksByCategory($user, $safeCategory);
        }

        if($safeTime && !$safeCategory){
            $tasks = showTasksByDate($user, $safeTime);
        }

        return $tasks;
    }

    // получаем готовый массив скл из бд
    function getSqlArr($inquiry, $con) {

        $result = mysqli_query($con, $inquiry);

        if (!$result) {
            $error = mysqli_error($con);
            var_dump('Ошибка SQL запроса ' . $error);
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

    function showTask() {
        $connect = connect();
        $userId = $_SESSION['user']['id'];

        $safeCategory = mysqli_real_escape_string($connect, $_GET['category_id']);
        $safeTime = mysqli_real_escape_string($connect, $_GET['sort_date']);
        $safeSearch = mysqli_real_escape_string($connect, $_GET['search']);

        $sqlTasks = getSqlTaskList($safeCategory, $userId, $safeTime);
        $arrCaseSheet = getSqlArr($sqlTasks, $connect);

        if (isset($safeSearch)) {
            $search = trim($safeSearch);

            if (!empty($search)) {
                $arrCaseSheet = getSearchTasks($connect, $search);
            }
        }

        return $arrCaseSheet;
    }

    function showTaskCount() {
        $connect = connect();
        $userId = $_SESSION['user']['id'];

        $sqlTasksCount = "SELECT * FROM tasks WHERE user_id = ".$userId;
        $arrCaseSheetCount = getSqlArr($sqlTasksCount, $connect);

        return $arrCaseSheetCount;
    }

    function showTaskList() {
        $connect = connect();
        $userId = $_SESSION['user']['id'];

        $sqlList = "SELECT * FROM list WHERE user_id = ".$userId;

        $arrCategory = getSqlArr($sqlList, $connect);

        return $arrCategory;
    }

    // делаем полный массив всех данных из бд
    function sqlInquiry() {
        $connect = connect();

        $safeCompleted = mysqli_real_escape_string($connect, $_GET['show_completed']);

        $arrCategory = showTaskList();
        $arrCaseSheetCount = showTaskCount();
        $arrCaseSheet = showTask();

        list($arrCaseSheet, $arrCategory) = updateArray($arrCaseSheet, $arrCategory, $arrCaseSheetCount, $safeCompleted);

        return updateArray($arrCaseSheet, $arrCategory, $arrCaseSheetCount, $safeCompleted);
    }
