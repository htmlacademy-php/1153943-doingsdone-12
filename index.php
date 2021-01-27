<?php

    require_once 'helpers.php';

    $show_complete_tasks = rand(0, 1);
    
    $arrCategory = ['Входящие', 'Учеба', 'Работа', 'Домашние дела', 'Авто'];

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
            'date' => '28.01.2021',
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

    // function getCountFiltration($arr, $name) {
    //     $count = 0;

    //     for($i = 0; $i < count($arr); $i++) {
    //         if($arr[$i]['category'] == $name) {
    //             $count++; 
    //         }
    //     }

    //     return $count;
    // };

    function getCountFiltration($arr, $name) {
        $count = 0;
        $i = 0;

        foreach($arr as $task) {
            if($task['category'] == $name[$i]) {
                $count++; 
            }
        }

        $i++;

        return $count;
    };

    $countFilter = getCountFiltration($arrCaseSheet, $arrCategory);

    // time <module3-task2>

    function timeTask($date){
        $timeNow = time();
        $timeTask = strtotime($date);

        $total = $timeTask - $timeNow;

        return floor($total / 3600);
    };

    // Шаблонизация <module3-task1>

    $page_content = include_template('main.php', ['getCountFiltration' => $countFilter, 'arrCategory' => $arrCategory, 'arrCaseSheet' => $arrCaseSheet, 'show_complete_tasks' => $show_complete_tasks]);
    $layout_content = include_template('layout.php', ['content' => $page_content, 'title' => 'Дела в порядке', 'user' => 'Константин']);

    print($layout_content);
?>
