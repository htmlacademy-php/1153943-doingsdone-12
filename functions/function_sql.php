<?php

require_once dirname(__DIR__) . '/config/connect.php';

/**
 * Функция выводит задач по искомому слову
 * @param $search
 * @return array
 */

function getSearchTasks($search): array
{
    $con = mysqli_connect(HOST, USER, PASS, NAME);
    mysqli_set_charset($con, "utf8");

    if (!$con) {
        print(sprintf('Ошибка подключения: %s', mysqli_connect_error()));
    }

    $u_id = $_SESSION['id'];
    $sql = 'SELECT * FROM tasks WHERE user_id = ? AND MATCH(title) AGAINST (? IN BOOLEAN MODE)';

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'ss', $u_id, $search);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    return mysqli_fetch_all($res, MYSQLI_ASSOC);
}

/**
 * Функция выводит спиоск задач по дате
 * @param $user_id
 * @param $tab
 * @return array
 */

function showTasksByDate($user_id, $tab): array
{
    $sql = '';

    $con = mysqli_connect(HOST, USER, PASS, NAME);
    mysqli_set_charset($con, "utf8");

    if (!$con) {
        print(sprintf('Ошибка подключения: %s', mysqli_connect_error()));
    }

    if ($tab === 'today') {
        $sql = "SELECT * FROM tasks WHERE user_id = ? AND date_deadline = CURDATE()";
    } elseif ($tab === 'tomorrow') {
        $sql = "SELECT * FROM tasks WHERE user_id = ? AND date_deadline = ADDDATE(CURDATE(),INTERVAL 1 DAY)";
    } elseif ($tab === 'expired') {
        $sql = "SELECT * FROM tasks WHERE user_id = ? AND date_deadline < CURDATE()";
    }

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 's', $user_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    return mysqli_fetch_all($res, MYSQLI_ASSOC);
}

/**
 * Функция выводит задачи
 * @return array
 * */

function getTask(): array
{
    $con = mysqli_connect(HOST, USER, PASS, NAME);
    mysqli_set_charset($con, "utf8");

    if (!$con) {
        print(sprintf('Ошибка подключения: %s', mysqli_connect_error()));
    }

    $id = $_SESSION['id'];

    $sql = "SELECT * FROM tasks WHERE user_id = ?";

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 's', $id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    return mysqli_fetch_all($res, MYSQLI_ASSOC);
}

/**
 * Функция выводит задачи по типу листа
 * @param $type
 * @return array
 */

function getTypeListTask($type): array
{
    $con = mysqli_connect(HOST, USER, PASS, NAME);
    mysqli_set_charset($con, "utf8");

    if (!$con) {
        print(sprintf('Ошибка подключения: %s', mysqli_connect_error()));
    }

    $id = $_SESSION['id'];

    $sql = "SELECT * FROM tasks WHERE user_id = ? AND list_id = ?";

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'ss', $id, $type);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    return mysqli_fetch_all($res, MYSQLI_ASSOC);
}

/**
 * Функция выводит список типы задач
 * @return array
 * */

function getList(): array
{
    $con = mysqli_connect(HOST, USER, PASS, NAME);
    mysqli_set_charset($con, "utf8");

    if (!$con) {
        print(sprintf('Ошибка подключения: %s', mysqli_connect_error()));
    }

    $id = $_SESSION['id'];

    $sql = "SELECT * FROM list WHERE user_id = ?";

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 's', $id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    return mysqli_fetch_all($res, MYSQLI_ASSOC);
}
