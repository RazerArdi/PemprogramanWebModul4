<?php
// Routes/LowonganRoutes.php
require_once 'Controllers/LowonganController.php';

$lowonganController = new LowonganController();

$request_method = $_SERVER['REQUEST_METHOD'];

switch ($request_method) {
    case 'GET':
        if (isset($_GET['id'])) {
            // Jika ID diberikan, tampilkan lowongan berdasarkan ID
            echo $lowonganController->getLowonganById($_GET['id']);
        } else {
            // Jika tidak ada ID, tampilkan semua lowongan
            echo $lowonganController->getAllLowongan();
        }
        break;

    case 'POST':
        // Membuat lowongan baru
        $data = json_decode(file_get_contents("php://input"), true);
        echo $lowonganController->createLowongan($data);
        break;

    case 'PUT':
        // Memperbarui lowongan
        $data = json_decode(file_get_contents("php://input"), true);
        echo $lowonganController->updateLowongan($data);
        break;

    default:
        echo json_encode(["message" => "Invalid request method"]);
        break;
}

