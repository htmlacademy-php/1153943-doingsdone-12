<?php
    // считает количество задач в каждом проекте
    function getCountTasks($caseSheet, $category, $complete_tasks) {
        $count = 0;

        foreach ($caseSheet as $task) {

            if($task['list_id'] == $category['id'] &&
                !$task['is_done'] &&
                !$complete_tasks ||
                $task['list_id'] == $category['id'] &&
                $complete_tasks) {
                $count++;
            }
        }

        return $count;
    }

    // проверяет когда задача становится срочной
    function getTimeTask($date){
        $timeNow = time();
        $timeTask = strtotime($date['date_deadline']);

        $result = ($timeTask - $timeNow) / 3600;

        if ($result < 24 && $date['date_deadline'] && !$date['is_done']) {
            return true;
        }

        return false;
    }

    // дополняем массив из бд

    function updateArray($caseSheet, $category, $countArr, $complete_tasks) {

        foreach ($caseSheet as $key => $tasks) {
            $url = "/" . 'index.php' . "?" . $caseSheet[$key]['id'];
            $caseSheet[$key]['url'] = $url;

            $caseSheet[$key]['dateImportant'] = getTimeTask($caseSheet[$key]);

            $caseSheet[$key]['title'] = htmlspecialchars($caseSheet[$key]['title']);
            $caseSheet[$key]['category'] = htmlspecialchars($caseSheet[$key]['category']);
        }

        foreach ($category as $key => $taskLists) {
            $url = "/" . 'index.php' . "?" . 'category_id=' . $category[$key]['id'];

            $category[$key]['count'] = getCountTasks($countArr, $taskLists, $complete_tasks);
            $category[$key]['url'] = $url;

            $category[$key]['title'] = htmlspecialchars($category[$key]['title']);
        }

        return [$caseSheet, $category];
    }
