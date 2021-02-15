<?php
    require_once 'helpers.php';

    $show_complete_tasks = rand(0, 1);

    $con = mysqli_connect("localhost", "root", "root", "schema");
    mysqli_set_charset($con, "utf8");

    const USER_ID = 2;

    $sqlList = "SELECT * FROM list WHERE user_id = ".USER_ID;

    if($_GET['title']) {
        $sqlTasks = "SELECT * FROM tasks WHERE list_id =".$_GET['title']." AND user_id = ".USER_ID;
    } else {
        $sqlTasks = "SELECT * FROM tasks WHERE user_id = ".USER_ID;
    }

    $sqlTasksCount = "SELECT * FROM tasks WHERE user_id = ".USER_ID;

    $sqlName = "SELECT name FROM users WHERE id = ".USER_ID;

    function getSqlArr($inquiry) {
        global $con;

        $result = mysqli_query($con, $inquiry);

        if (!$result) {
            $error = mysqli_error($con);
            throw new Exception("Ошибка MySQL: " . $error);
        }

        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    try {
        $arrCategory = getSqlArr($sqlList);
        $arrCaseSheet = getSqlArr($sqlTasks);
        $arrCaseSheetCount = getSqlArr($sqlTasksCount);
        $arrNameUser = getSqlArr($sqlName);
    } catch (Exception $e) {
        echo 'Выброшено исключение: ', $e->getMessage();
    }

    foreach ($arrNameUser as $user) {
        $nameUser = $user['name'];
    }

    if ($con) {

        mysqli_close($con);
    }

    function getCountTasks($caseSheet, $category) {
        global $show_complete_tasks;

        $count = 0;

        foreach ($caseSheet as $task) {

            if($task['list_id'] == $category['id'] && !$task['is_done'] && !$show_complete_tasks) {
                $count++;
            }

            if($task['list_id'] == $category['id'] && $show_complete_tasks) {
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

    // функция добавляет в массив счетчик задач, время и ставит фильтр текста

    function updateArray($caseSheet, $category) {
        global $arrCaseSheetCount;

        foreach ($caseSheet as $key => $tasks) {
            $caseSheet[$key]['dateImportant'] = getTimeTask($caseSheet[$key]);

            $caseSheet[$key]['title'] = htmlspecialchars($caseSheet[$key]['title']);
            $caseSheet[$key]['category'] = htmlspecialchars($caseSheet[$key]['category']);
        }

        foreach ($category as $key => $taskLists) {
            $params = $_GET;

            $params['title'] = $category[$key]['id'];

            $scriptname = pathinfo(__FILE__, PATHINFO_BASENAME);
            $query = http_build_query($params);
            $url = "/" . $scriptname . "?" . $query;


            $category[$key]['count'] = getCountTasks($arrCaseSheetCount, $taskLists);
            $category[$key]['url'] = $url;

            $category[$key]['title'] = htmlspecialchars($category[$key]['title']);
        }

        return [$caseSheet, $category];
    };

    list($arrCaseSheet, $arrCategory) = updateArray($arrCaseSheet, $arrCategory);
    updateArray($arrCaseSheet, $arrCategory);

    $page_content = include_template('main.php', ['arrCategory' => $arrCategory, 'arrCaseSheet' => $arrCaseSheet, 'show_complete_tasks' => $show_complete_tasks, 'url' => $url]);
    $layout_content = include_template('layout.php', ['content' => $page_content, 'title' => 'Дела в порядке', 'user' => $nameUser]);

    print($layout_content);
?>
