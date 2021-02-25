<?php
    session_start();

    require_once 'helpers.php';
    require_once 'function_sql.php';
    require_once 'function.php';

    // добавляет задачу
    function addTask($connect, $userId, $taskList, $taskName, $date, $fileUrl){

        $safeTaskName = mysqli_real_escape_string($connect, $taskName);
        $safeFileUrl = mysqli_real_escape_string($connect, $fileUrl);

        if (empty($date)) {
            $sqlAddTask = "INSERT INTO tasks (user_id, list_id, title, file) VALUES ($userId, $taskList, '$safeTaskName', '$safeFileUrl')";
        } else {
            $sqlAddTask = "INSERT INTO tasks (user_id, list_id, title, date_deadline, file) VALUES ($userId, $taskList, '$safeTaskName', '$date', '$safeFileUrl')";
        }

        $result = mysqli_query($connect, $sqlAddTask);

        if (!$result) {
            $error = mysqli_error($connect);
            $errors[] =  "Ошибка MySQL" . $error;
        }
    }

    function getFormAdd(){
        $required_fields = ['name', 'project'];

        $errors = [];
        $connect = connect();

        $safeSubmit = mysqli_real_escape_string($connect, $_POST['submit']);
        $safeProject = mysqli_real_escape_string($connect, $_POST['project']);
        $safeName = mysqli_real_escape_string($connect, $_POST['name']);
        $safeDate = mysqli_real_escape_string($connect, $_POST['date']);

        if ($safeSubmit) {
            $connect = connect();

            foreach ($required_fields as $field) {
                if (empty($_POST[$field])) {
                    $errors[$field] = 'Поле не заполнено';
                }
            }

            if (!is_date_valid($safeDate) && $safeDate !== NULL && $safeDate !== '') {
                $errors['date'] = 'Неверный формат даты';
            }

            if (strtotime($safeDate) + 86400 < time() && $safeDate) {
                $errors['date'] = 'Некорректная дата';
            }

            if (is_uploaded_file($_FILES['file']['tmp_name'])) {
                $fileName = $_FILES['file']['name'];
                $filePath = __DIR__ . '/uploads/';
                $fileUrl = '/uploads/' . $fileName;

                move_uploaded_file($_FILES['file']['tmp_name'], $filePath . $fileName);

            } else {
                $fileUrl = '';
            }

            if (count($errors)) {
                return $errors;
            }

            if (empty($errorsSql)) {
                addTask($connect, $_SESSION['user']['id'], $safeProject, $safeName, $safeDate, $fileUrl);
                header('Location: /index.php');
                exit;
            }
        }
    }

    $errors = getFormAdd();

    $nameUser = nameUser();
    $sql = sqlInquiry();

    $taskContent = include_template('addTask.php', [
        'arrCategory' => $sql[1],
        'errors' => $errors,
    ]);

    $layoutContent = include_template('layout.php', [
        'content' => $taskContent,
        'title' => "Дела в порядке",
        'user' => $nameUser,
        'errors' => $errors,
    ]);

    print($layoutContent);
