<?php
header('Content-Type: application/json');

// Caminho absoluto para a pasta de uploads
$target_dir = __DIR__ . '/';
$target_file = $target_dir . basename($_FILES["file"]["name"]);

// URL pública para acessar a imagem
$public_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") .
    "://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']) . '/' . basename($_FILES["file"]["name"]);

// Validação do tipo de arquivo
$allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
$ext = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
if (!in_array($ext, $allowed)) {
    http_response_code(400);
    echo json_encode(['error' => 'Tipo de arquivo não permitido']);
    exit;
}

if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
    echo json_encode(['location' => $public_url]);
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Falha no upload']);
} 