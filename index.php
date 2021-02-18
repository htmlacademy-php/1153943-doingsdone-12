<?php
    require_once 'helpers.php';
    require_once 'function_sql.php';
    require_once 'function.php';

    $show_complete_tasks = rand(0, 1);

    const USER_ID = 2;

    $errors = [];

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

    function valueIntCheck($valueName){
        $result = true;

        if (!filter_input(INPUT_GET, $valueName, FILTER_SANITIZE_NUMBER_INT)) {
            $result = false;
        }

        return $result;
    }

    function validateProjectId($projectId){
        $result = false;

        if (valueIntCheck('category_id') && projectExistenceCheck($projectId)) {
            $result = true;
        }

        return $result;
    }

    $connect = connect();

    $currentProjectId = $safeCategory ?? '';
    $currentProjectId = mysqli_real_escape_string($connect, (string)$currentProjectId);

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

    if ($connect) {
        mysqli_close($connect);
    }

    list($arrCaseSheet, $arrCategory) = updateArray($arrCaseSheet, $arrCategory, $arrCaseSheetCount, $show_complete_tasks);
    updateArray($arrCaseSheet, $arrCategory, $arrCaseSheetCount, $show_complete_tasks);

    $nameUser = nameUser($arrNameUser);

    $pageContent = include_template('main.php', ['arrCategory' => $arrCategory, 'arrCaseSheet' => $arrCaseSheet, 'show_complete_tasks' => $show_complete_tasks]);

    if ($currentProjectId) {
        if (!validateProjectId($currentProjectId)) {
            $pageContent = include_template('404.php', []);
        }
    }

    $layoutContent = include_template('layout.php', ['content' => $pageContent, 'title' => 'Дела в порядке', 'user' => $nameUser, 'errors' => $errors]);

    print($layoutContent);
