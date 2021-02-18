<?php

    function connect() {
        $con = mysqli_connect("localhost", "root", "root", "schema");
        mysqli_set_charset($con, "utf8");

        if (!$con) {
            $error = mysqli_error($con);
            throw new Exception('Ошибка SQL запроса ' . $error);
        }

        return $con;
    }

    function getSqlTaskList($safeCategory, $user) {
        $tasks = "SELECT * FROM tasks WHERE user_id = ".$user;

        if($safeCategory) {
            $tasks = "SELECT * FROM tasks WHERE list_id =".$safeCategory." AND user_id = ".$user;
        }

        return $tasks;
    }

    function getSqlArr($inquiry, $con) {

        $result = mysqli_query($con, $inquiry);

        if (!$result) {
            $error = mysqli_error($con);
            throw new Exception('Ошибка SQL запроса ' . $error);
        }

        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
