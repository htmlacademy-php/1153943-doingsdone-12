<?php

    require_once 'helpers.php';

    $show_complete_tasks = rand(0, 1);

    $con = mysqli_connect("localhost", "root", "root", "schema");
    mysqli_set_charset($con, "utf8");

    $sqlList = "SELECT * FROM list WHERE user_id = 2";
    $sqlTasks = "SELECT * FROM tasks WHERE user_id = 2";
    $sqlName = "SELECT name FROM users WHERE id = 2";

    $result = mysqli_query($con, $sqlList);
    $resultTasks = mysqli_query($con, $sqlTasks);
    $resultName = mysqli_query($con, $sqlName);

    $arrCategory = mysqli_fetch_all($result, MYSQLI_ASSOC);
    $arrCaseSheet = mysqli_fetch_all($resultTasks, MYSQLI_ASSOC);
    $arrNameUser = mysqli_fetch_all($resultName, MYSQLI_ASSOC);

    foreach ($arrNameUser as $user) {
        $nameUser = $user['name'];
    }

    if (!$result) {
        $error = mysqli_error($con);
        print("Ошибка MySQL: " . $error);
    }

    function getCountTasks($caseSheet, $category) {
        $count = 0;

        foreach ($caseSheet as $task) {

            if($task['list_id'] == $category['id']) {
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
        foreach ($caseSheet as $key => $tasks) {
            $caseSheet[$key]['dateImportant'] = getTimeTask($caseSheet[$key]);

            $caseSheet[$key]['title'] = htmlspecialchars($caseSheet[$key]['title']);
            $caseSheet[$key]['category'] = htmlspecialchars($caseSheet[$key]['category']);
        }

        foreach ($category as $key => $taskLists) {
            $category[$key]['count'] = getCountTasks($caseSheet, $taskLists);

            $category[$key]['title'] = htmlspecialchars($category[$key]['title']);
        }

        return [$caseSheet, $category];
    };

    list($arrCaseSheet, $arrCategory) = updateArray($arrCaseSheet, $arrCategory);
    updateArray($arrCaseSheet, $arrCategory);

    $page_content = include_template('main.php', ['arrCategory' => $arrCategory, 'arrCaseSheet' => $arrCaseSheet, 'show_complete_tasks' => $show_complete_tasks]);
    $layout_content = include_template('layout.php', ['content' => $page_content, 'title' => 'Дела в порядке', 'user' => $nameUser]);

    print($layout_content);
?>
