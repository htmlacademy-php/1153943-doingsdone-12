<?php

/**
 * Считает кол-во задач
 * @param $caseSheet
 * @param $category
 * @param $complete_tasks
 * @return int
 */

function getCountTasks($caseSheet, $category): int
{
    $count = 0;

    foreach ($caseSheet as $task) {

        if ($task['list_id'] == $category['id'] && !$task['is_done']) {

            $count++;
        }
    }

    return $count;
}

/**
 * Считает кол-во задач
 * @param $date
 * @return bool
 */

function getTimeTask($task): bool
{
    $timeNow = time();
    $timeTask = strtotime($task['date_deadline']);

    $result = ($timeTask - $timeNow) / 3600;

    if ($result < 24 && $task['date_deadline'] && !$task['is_done']) {
        return true;
    }

    return false;
}
