<?php
    require_once 'helpers.php';
    require_once 'function_sql.php';
    require_once 'function.php';

    $show_complete_tasks = rand(0, 1);

    const USER_ID = 2;

    $errors = [];

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

    $current_project_id = $safeCategory ?? '';
    $current_project_id = mysqli_real_escape_string($connect, (string)$current_project_id);

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

    $page_content = include_template('main.php', ['arrCategory' => $arrCategory, 'arrCaseSheet' => $arrCaseSheet, 'show_complete_tasks' => $show_complete_tasks]);

    if ($current_project_id) {
        if (!validate_project_id($current_project_id)) {
            $page_content = include_template('404.php', []);
        }
    }

    $layout_content = include_template('layout.php', ['content' => $page_content, 'title' => 'Дела в порядке', 'user' => $nameUser, 'errors' => $errors]);

    print($layout_content);
