<?php
    require_once 'helpers.php';

    $show_complete_tasks = rand(0, 1);

    const USER_ID = 2;

    $errors = [];

    function connect () {
        $con = mysqli_connect("localhost", "root", "root", "schema");
        mysqli_set_charset($con, "utf8");

        if (!$con) {
            $error = mysqli_error($con);
            throw new Exception('Ошибка SQL запроса ' . $error);
        }

        return $con;
    }

    function getSqlTaskList($safeCategory, $user) {
        $tasks = "SELECT * FROM tasks WHERE user_id = ".$user;

        if($safeCategory) {
            $tasks = "SELECT * FROM tasks WHERE list_id =".$safeCategory." AND user_id = ".$user;
        }

        return $tasks;
    }

    function getSqlArr($inquiry, $con) {

        $result = mysqli_query($con, $inquiry);

        if (!$result) {
            $error = mysqli_error($con);
            throw new Exception('Ошибка SQL запроса ' . $error);
        }

        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    function nameUser($arr) {
        $name = 'User';

        foreach ($arr as $user) {
            $name = $user['name'];
        }

        return $name;
    }

    function getCountTasks($caseSheet, $category, $complete_tasks) {
        $count = 0;

        foreach ($caseSheet as $task) {

            if($task['list_id'] == $category['id'] && !$task['is_done'] && !$complete_tasks || $task['list_id'] == $category['id'] && $complete_tasks) {
                $count++;
            }
        }

        return $count;
    }

    function getTimeTask($date){
        $timeNow = time();
        $timeTask = strtotime($date['date_deadline']);

        $result = ($timeTask - $timeNow) / 3600;

        if ($result < 24 && $date['date_deadline'] && !$date['is_done']) {
            return true;
        }

        return false;
    }

    function updateArray($caseSheet, $category, $countArr, $complete_tasks) {

        foreach ($caseSheet as $key => $tasks) {
            $caseSheet[$key]['dateImportant'] = getTimeTask($caseSheet[$key]);

            $caseSheet[$key]['title'] = htmlspecialchars($caseSheet[$key]['title']);
            $caseSheet[$key]['category'] = htmlspecialchars($caseSheet[$key]['category']);
        }

        foreach ($category as $key => $taskLists) {
            $params = $_GET;

            $params['category_id'] = $category[$key]['id'];

            $scriptname = pathinfo(__FILE__, PATHINFO_BASENAME);
            $query = http_build_query($params);
            $url = "/" . $scriptname . "?" . $query;


            $category[$key]['count'] = getCountTasks($countArr, $taskLists, $complete_tasks);
            $category[$key]['url'] = $url;

            $category[$key]['title'] = htmlspecialchars($category[$key]['title']);
        }

        return [$caseSheet, $category];
    }

    function project_existence_check($project_id){
        $result = false;

        $connect = connect();

        if ($project_id) {

            $sql = "SELECT * FROM `list` WHERE `id` = " . (string)$project_id;
            $sql_result = mysqli_query($connect, $sql);

            if ($sql_result && isset($sql_result->num_rows) && $sql_result->num_rows > 0) {
                $result = true;
            }
        }

        return $result;
    }

    function value_int_check($value_name){
        $result = true;

        if (!filter_input(INPUT_GET, $value_name, FILTER_SANITIZE_NUMBER_INT)) {
            $result = false;
        }

        return $result;
    }

    function validate_project_id($project_id){
        $result = false;

        if (value_int_check('category_id') && project_existence_check($project_id)) {
            $result = true;
        }

        return $result;
    }

    $connect = connect();
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

    $nameUser = nameUser($arrNameUser);

    $current_project_id = $safeCategory ?? '';
    $current_project_id = mysqli_real_escape_string($connect, (string)$current_project_id);

    if ($connect) {
        mysqli_close($connect);
    }

    list($arrCaseSheet, $arrCategory) = updateArray($arrCaseSheet, $arrCategory, $arrCaseSheetCount, $show_complete_tasks);
    updateArray($arrCaseSheet, $arrCategory, $arrCaseSheetCount, $show_complete_tasks);

    $page_content = include_template('main.php', ['arrCategory' => $arrCategory, 'arrCaseSheet' => $arrCaseSheet, 'show_complete_tasks' => $show_complete_tasks]);

    if ($current_project_id) {
        if (!validate_project_id($current_project_id)) {
            $page_content = include_template('404.php', []);
        }
    }

    $layout_content = include_template('layout.php', ['content' => $page_content, 'title' => 'Дела в порядке', 'user' => $nameUser, 'errors' => $errors]);

    print($layout_content);
