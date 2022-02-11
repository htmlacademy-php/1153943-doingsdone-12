<?php
require_once dirname(__DIR__) . '/config/connect.php';

/**
 * Обновляем статус задачи
 * @param $Id 'id' задачи
 * @param $status 'статус' задачи
 * @return bool|string
 */

function performTask ($id, $status): bool
{
    $con = mysqli_connect(HOST, USER, PASS, NAME);
    mysqli_set_charset($con, "utf8");

    if (!$con) {
        print(sprintf('Ошибка подключения: %s', mysqli_connect_error()));
    }

    $sql = "UPDATE tasks SET is_done = '$status' WHERE id = '$id'";

    if (mysqli_query($con, $sql)) {
        header("Refresh:0; url=/");
    } else {
        var_dump("Error updating record: " . mysqli_error($con));
    }

    return true;
}
