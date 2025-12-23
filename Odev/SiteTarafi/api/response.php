<?php
function success($data = []) {
    echo json_encode([
        "status" => "ok",
        "data" => $data
    ]);
    exit;
}

function error($message, $code = 400) {
    http_response_code($code);
    echo json_encode([
        "status" => "error",
        "message" => $message
    ]);
    exit;
}
