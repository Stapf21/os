<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Upload extends CI_Controller
{
    public function tinymce()
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
            $target_dir = './assets/upload/';
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0755, true);
            }
            $target_file = $target_dir . basename($_FILES['file']['name']);
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $ext = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed)) {
                http_response_code(400);
                echo json_encode(['error' => 'Tipo de arquivo não permitido']);
                exit;
            }
            if (move_uploaded_file($_FILES['file']['tmp_name'], $target_file)) {
                echo json_encode(['location' => base_url('assets/upload/' . $_FILES['file']['name'])]);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Erro ao mover o arquivo.']);
            }
        } else {
            http_response_code(403);
            echo json_encode(['error' => 'Método não permitido.']);
        }
    }
} 