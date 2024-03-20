<?php

const ERROR_MESSAGES = [
    '401' => 'Ошибка авторизации',
    '403' => 'Доступ запрещён. Email уже занят',
    '404' => 'Объект не найден',
    '409' => 'Недопустимое имя файла (папки)',
    '460' => 'Ошибка загрузки файла',
];

const STATUS_MESSAGES = [
    200 => 'HTTP/1.0 200 OK',
    401 => 'HTTP/1.0 401 Unauthorized',
    403 => 'HTTP/1.0 403 Forbidden',
    404 => 'HTTP/1.0 404 Not Found',
    409 => 'HTTP/1.0 409 Conflict',
];
