<?php

// --- CORS para preflight ---
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: Content-Type");
    header("Access-Control-Allow-Methods: POST, OPTIONS");
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
    exit();
}

// --- Cabeceras generales ---
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json");

require __DIR__ . '/PHPMailer/src/Exception.php';
require __DIR__ . '/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/PHPMailer/src/SMTP.php';
require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Cargar variables de entorno
// $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
// $dotenv->load();

$data = json_decode(file_get_contents("php://input"), true);

$nombre = $data['nombre'] ?? '';
$email = $data['email'] ?? '';
$mensaje = $data['mensaje'] ?? '';

if (empty($nombre) || empty($email) || empty($mensaje)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Faltan campos obligatorios']);
    exit();
}

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = getenv('GMAIL_USER');
    $mail->Password = getenv('GMAIL_PASS');
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom(getenv('GMAIL_USER'), 'Formulario Web');
    $mail->addAddress(getenv('GMAIL_USER'));
    $mail->Subject = 'Nuevo mensaje desde tu formulario';
    $mail->Body = "Nombre: $nombre\nEmail: $email\nMensaje:\n$mensaje";

    $mail->send();
    echo json_encode(['status' => 'ok']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $mail->ErrorInfo
    ]);
}
