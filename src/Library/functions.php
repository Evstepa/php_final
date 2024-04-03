<?php

/**
 * Отправка email
 *
 * @param string  $to
 * @param string  $from
 * @param string  $title
 * @param string  $message
 */
function sendMessageMail(string $to, string $from, string $title, string $message): array
{
    $subject = $title;

    $headers = "Content-type: text/html; charset=\"utf-8\"\r\n";
    $headers .= "From: " . $from . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Date: " . date('D, d M Y h:i:s O') . "\r\n";
    $headers .= 'X-Mailer: PHP/' . phpversion();

    $resault = mail($to, $subject, $message, $headers);

    if (!$resault)
        return [
            'body' => 'Ошибка отправки сообщения',
            'status' => 401,
        ];
    else
        return [
            'body' => 'Сообщение успешно отправлено',
            'status' => 200,
        ];
}

/**
 * Формирование и выполнение запроса к БД на вставку или изменение данных
 *
 * @param array $data
 * @return boolean
 */
function getSqlCreateUpdate(array $data): bool
{
    $connect = $data['state'];
    $query = $data['query'];
    $table = $data['table'];
    $obj = $data['obj']->extractData();
    $key = isset($data['key']) ? $data['key'] : '';

    if (isset($key)) {
        $obj = array_filter($obj, function ($value, $key) {
            return !(is_null($value) || $key == 'role');
        }, ARRAY_FILTER_USE_BOTH);
    }

    $objKeys = array_keys($obj);
    $objValues = array_values($obj);

    switch ($query) {
        case 'INSERT':
            $columns = implode(', ', $objKeys);
            $questions = array_fill(0, count($objKeys), '?');
            $placeholders = implode(', ', $questions);
            $sql = sprintf('INSERT INTO `%s` (%s) VALUES (%s)', $table, $columns, $placeholders);
            break;
        case 'UPDATE':
            $columnNames = array_map(function ($key) {
                return "`{$key}` = ?";
            }, $objKeys);
            $columns = implode(', ', $columnNames);
            array_push($objValues, $key[array_key_first($key)]);
            $sql = sprintf('UPDATE `%s` SET %s WHERE `%s`= ?', $table, $columns, array_key_first($key));
            break;
        default:
            break;
    }

    $state = $connect->prepare($sql);
    $answer = $state->execute($objValues);

    return $answer;
}
