<?php

/**
 * Проверяет авторизирован ли пользователь
 * @param $name "имя" пользователя
 * @return bool
 * */

function checkSession (): bool
{
    return !empty($_SESSION['id']) && !empty($_COOKIE['visit']);
}
