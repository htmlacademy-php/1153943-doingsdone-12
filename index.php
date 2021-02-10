<?php

    require_once 'helpers.php';

    $nameUser = 'Константин';

    $show_complete_tasks = rand(0, 1);

    $arrCategory = [
        ['id' => 1,
        'name' => "Входящие"],
        ['id' => 2,
        'name' => "Учеба"],
        ['id' => 3,
        'name' => "Работа"],
        ['id' => 4,
        'name' => "Домашние дела"],
        ['id' => 5,
        'name' => "Авто"]
    ];

    $arrCaseSheet = [
        [
            'id' => 1,
            'name' => 'Собеседование в IT компании',
            'date' => '01.12.2019',
            'category' => 'Работа',
            'isDone' => false,
        ],
        [
            'id' => 2,
            'name' => 'Выполнить тестовое задание',
            'date' => '30.01.2021',
            'category' => 'Работа',
            'isDone' => false,
        ],
        [
            'id' => 3,
            'name' => 'Сделать задание первого раздела',
            'date' => '28.01.2021',
            'category' => 'Учеба',
            'isDone' => true,
        ],
        [
            'id' => 4,
            'name' => 'Встреча с другом',
            'date' => '05.02.2021',
            'category' => 'Входящие',
            'isDone' => false,
        ],
        [
            'id' => 5,
            'name' => 'Купить корм для кота',
            'date' => null,
            'category' => 'Домашние дела',
            'isDone' => false,
        ],
        [
            'id' => 6,
            'name' => 'Заказать пиццу',
            'date' => null,
            'category' => 'Домашние дела',
            'isDone' => false,
        ],
    ];

    function getCountTasks($caseSheet, $category) {
        $count = 0;

        foreach ($caseSheet as $task) {
            if($task['category'] == $category) {
                $count++;
            }
        }

        return $count;
    };

    function getTimeTask($date){
        $timeNow = time();
        $timeTask = strtotime($date['date']);

        $result = ($timeTask - $timeNow) / 3600;

        if ($result < 24 && $date['date'] && !$date['isDone']) {
            return true;
        }
        return false;
    };

    // функция добавляет в массив счетчик задач, время и ставит фильтр текста

    function updateArray($caseSheet, $category) {
        foreach ($caseSheet as $key => $tasks) {
            $caseSheet[$key]['dateImportant'] = getTimeTask($caseSheet[$key]);

            $caseSheet[$key]['name'] = htmlspecialchars($caseSheet[$key]['name']);
            $caseSheet[$key]['category'] = htmlspecialchars($caseSheet[$key]['category']);
        }

        foreach ($category as $key => $taskLists) {
            $category[$key]['count'] = getCountTasks($caseSheet, $category[$key]['name']);

            $category[$key]['name'] = htmlspecialchars($category[$key]['name']);
        }

        return [$caseSheet, $category];
    };

    list($arrCaseSheet, $arrCategory) = updateArray($arrCaseSheet, $arrCategory);
    updateArray($arrCaseSheet, $arrCategory);

    $page_content = include_template('main.php', ['arrCategory' => $arrCategory, 'arrCaseSheet' => $arrCaseSheet, 'show_complete_tasks' => $show_complete_tasks]);
    $layout_content = include_template('layout.php', ['content' => $page_content, 'title' => 'Дела в порядке', 'user' => $nameUser]);

    print($layout_content);
?>
