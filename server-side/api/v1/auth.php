<?php
/**
 * Проверка токена API
 */
$headers = getallheaders();
if (!isset($headers['Authorization']) || $headers['Authorization'] !== API_TOKEN) {
    header('Content-type: application/json');
    echo json_encode(array(
        "status"  => "error",
        "message" => "Токен API неверный или отсутствует"
    ));
    exit();
}