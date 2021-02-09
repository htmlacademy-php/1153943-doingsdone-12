USE `schema`;

-- регистрируем пользователей

INSERT INTO users SET name = 'Константин',
                     email = 'Константин123@mail.ru',
                     password = '123qwerty';

INSERT INTO users SET name = 'Виталик',
                     email = 'Виталик456@mail.ru',
                     password = 'qwerty1';

-- Добавляем проекты для 2 пользователя

INSERT INTO list SET user_id = 2, title = "Входящие";
INSERT INTO list SET user_id = 2, title = "Учеба";
INSERT INTO list SET user_id = 2, title = "Работа";
INSERT INTO list SET user_id = 2, title = "Домашние дела";
INSERT INTO list SET user_id = 2, title = "Авто";

-- создаем задачи для 2 пользователя

INSERT INTO tasks SET user_id = 2,
                     list_id = 3,
                     title = 'Собеседование в IT компании',
                     task_deadline = '2020-12-01',
                     task_done = 0;

INSERT INTO tasks SET user_id = 2,
                     list_id = 3,
                     title = 'Выполнить тестовое задание',
                     task_deadline = '2020-11-29',
                     task_done = 0;

INSERT INTO tasks SET user_id = 2,
                     list_id = 2,
                     title = 'Сделать задание первого раздела',
                     task_deadline = '2020-12-21',
                     task_done = 1;

INSERT INTO tasks SET user_id = 2,
                     list_id = 1,
                     title = 'Встреча с другом',
                     task_deadline = '2020-12-22',
                     task_done = 0;

INSERT INTO tasks SET user_id = 2,
                     list_id = 4,
                     title = 'Купить корм для кота',
                     task_deadline = null,
                     task_done = 0;

INSERT INTO tasks SET user_id = 2,
                     list_id = 4,
                     title = 'Заказать пиццу',
                     task_deadline = null,
                     task_done = 0;

-- Получаем списки 2 пользователя

SELECT * FROM list WHERE user_id = 2;

-- Получаем задачи для одного проекта для пользователя 2

SELECT * FROM tasks WHERE user_id = 2 AND list_id = 3;

-- Помечаем задачу как выполненную

UPDATE tasks SET task_done = 1 WHERE id = 2;

-- обновить название задачи по её идентификатору

UPDATE tasks SET title = 'Сходить в магазин' WHERE id = 5;
