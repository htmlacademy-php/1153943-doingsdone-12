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

    function validateFilled($name) {
        $connect = connect();

        $safeSubmit = mysqli_real_escape_string($connect, $_POST['submit']);

        if($safeSubmit) {
            if (empty($_POST[$name])) {
                return "Это поле должно быть заполнено";
            }
        }
    }

    function validateFiles() {
        $fileUrl = '';

        if (is_uploaded_file($_FILES['file']['tmp_name'])) {
            $fileName = $_FILES['file']['name'];
            $filePath = __DIR__ . '/uploads/';
            $fileUrl = '/uploads/' . $fileName;

            move_uploaded_file($_FILES['file']['tmp_name'], $filePath . $fileName);

        }

        return $fileUrl;
    }

    function isDateValid($date) {
        if (!is_date_valid($date) && $date !== NULL && $date !== '') {
            return 'Неверный формат даты';
        }

        if (strtotime($date) + 86400 < time() && $date) {
            return 'Некорректная дата';
        }
    }

    function validateErrors() {
        $connect = connect();
        $errors = [];

        $requiredFields = ['name', 'project'];

        $safeSubmit = mysqli_real_escape_string($connect, $_POST['submit']);
        $safeDate = mysqli_real_escape_string($connect, $_POST['date']);

        if ($safeSubmit) {

            foreach ($requiredFields as $fields) {
                $errors[$fields] = validateFilled($fields);
            }

            if(!empty($safeDate)) {
                $errors['date'] = isDateValid($safeDate);
            }
        }

        return $errors;
    }

    function addTaskList() {
        $connect = connect();

        $safeProject = mysqli_real_escape_string($connect, $_POST['project']);
        $safeName = mysqli_real_escape_string($connect, $_POST['name']);
        $safeDate = mysqli_real_escape_string($connect, $_POST['date']);
        $fileUrl = validateFiles();

        addTask($connect, $_SESSION['user']['id'], $safeProject, $safeName, $safeDate, $fileUrl);
        header('Location: /index.php');
        exit;
    }

    function getFormAdd(){
        $connect = connect();

        $safeSubmit = mysqli_real_escape_string($connect, $_POST['submit']);

        if ($safeSubmit) {
            $errors = implode(validateErrors());

            if (empty($errors)) {
                addTaskList();
            }
        }
    }

    $formAdd = getFormAdd();
    $errors = validateErrors();

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
    ]);

    print($layoutContent);
