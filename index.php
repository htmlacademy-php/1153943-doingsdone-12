<?php
    require_once 'helpers.php';

    $show_complete_tasks = rand(0, 1);

    $con = mysqli_connect("localhost", "root", "root", "schema");
    mysqli_set_charset($con, "utf8");

    const USER_ID = 2;

    $sqlList = "SELECT * FROM list WHERE user_id = ".USER_ID;

    if($_GET['category_id']) {
        $sqlTasks = "SELECT * FROM tasks WHERE list_id =".$_GET['category_id']." AND user_id = ".USER_ID;
    } else {
        $sqlTasks = "SELECT * FROM tasks WHERE user_id = ".USER_ID;
    }

    $sqlTasksCount = "SELECT * FROM tasks WHERE user_id = ".USER_ID;

    $sqlName = "SELECT name FROM users WHERE id = ".USER_ID;

    function getSqlArr($inquiry, $connect) {

        $result = mysqli_query($connect, $inquiry);

        if (!$result) {
            $error = mysqli_error($connect);
            throw new Exception("Ошибка MySQL: " . $error);
        }

        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    try {
        $arrCategory = getSqlArr($sqlList, $con);
        $arrCaseSheet = getSqlArr($sqlTasks, $con);
        $arrCaseSheetCount = getSqlArr($sqlTasksCount, $con);
        $arrNameUser = getSqlArr($sqlName, $con);
    } catch (Exception $e) {
        echo 'Выброшено исключение: ', $e->getMessage();
    }

    foreach ($arrNameUser as $user) {
        $nameUser = $user['name'];
    }

    if ($con) {
        mysqli_close($con);
    }

    function getCountTasks($caseSheet, $category, $complete_tasks) {
        $count = 0;

        foreach ($caseSheet as $task) {

            if($task['list_id'] == $category['id'] && !$task['is_done'] && !$complete_tasks || $task['list_id'] == $category['id'] && $complete_tasks) {
                $count++;
            }
        }

        return $count;
    };

    function getTimeTask($date){
        $timeNow = time();
        $timeTask = strtotime($date['date_deadline']);

        $result = ($timeTask - $timeNow) / 3600;

        if ($result < 24 && $date['date_deadline'] && !$date['is_done']) {
            return true;
        }

        return false;
    };

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
    };

    list($arrCaseSheet, $arrCategory) = updateArray($arrCaseSheet, $arrCategory, $arrCaseSheetCount, $show_complete_tasks);
    updateArray($arrCaseSheet, $arrCategory, $arrCaseSheetCount, $show_complete_tasks);

    $page_content = include_template('main.php', ['arrCategory' => $arrCategory, 'arrCaseSheet' => $arrCaseSheet, 'show_complete_tasks' => $show_complete_tasks]);
    $layout_content = include_template('layout.php', ['content' => $page_content, 'title' => 'Дела в порядке', 'user' => $nameUser]);

    print($layout_content);
?>
