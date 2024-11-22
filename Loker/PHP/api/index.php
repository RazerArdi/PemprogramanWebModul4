<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '../Config/Database.php';
require_once '../Models/Lowongan.php';

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    // Respond to pre-flight request
    http_response_code(200);
    exit();
}


// Mendapatkan HTTP Method
$method = $_SERVER['REQUEST_METHOD'];

// Mengaktifkan CORS
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

// Jika request adalah OPTIONS, tanggapi untuk mengizinkan pre-flight request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit(0);
}

$database = new Database();
$db = $database->getConnection();
$lowongan = new Lowongan($db);

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $lowongan->id = $_GET['id'];
            $stmt = $lowongan->getSingle();
            
            if ($stmt->num_rows > 0) {
                $row = $stmt->fetch_assoc();
                $lowongan_item = array(
                    'id' => $row['id'],
                    'title' => $row['title'],
                    'description' => $row['description'],
                    'location' => $row['location'],
                    'created_at' => $row['created_at'],
                    'updated_at' => $row['updated_at']
                );
                echo json_encode($lowongan_item);
            } else {
                echo json_encode(array('message' => 'Lowongan not found.'));
            }
        } else {
            $stmt = $lowongan->getAll();
            if ($stmt->num_rows > 0) {
                $lowongan_arr = array();
                $lowongan_arr['lowongan'] = array();
                
                while ($row = $stmt->fetch_assoc()) {
                    extract($row);
                    $lowongan_item = array(
                        'id' => $id,
                        'title' => $title,
                        'description' => $description,
                        'location' => $location,
                        'created_at' => $created_at,
                        'updated_at' => $updated_at
                    );
                    array_push($lowongan_arr['lowongan'], $lowongan_item);
                }
                echo json_encode($lowongan_arr);
            } else {
                echo json_encode(array('message' => 'No lowongan found.'));
            }
        }
        break;

    case 'POST':
        // Get raw posted data
        $data = json_decode(file_get_contents("php://input"), true);
        
        // Validate data
        if(isset($data['title']) && isset($data['description']) && isset($data['location'])) {
            // Set values
            $lowongan->title = $data['title'];
            $lowongan->description = $data['description'];
            $lowongan->location = $data['location'];
            
            // Create lowongan
            if($lowongan->create()) {
                http_response_code(201); // Created
                echo json_encode(array("message" => "Lowongan successfully created."));
            } else {
                http_response_code(503); // Service Unavailable
                echo json_encode(array("message" => "Failed to create lowongan."));
            }
        } else {
            http_response_code(400); // Bad Request
            echo json_encode(array("message" => "Failed to create lowongan. Data incomplete."));
        }
        break;

        case 'PUT':
            // Get raw PUT data
            $data = json_decode(file_get_contents("php://input"), true); // Decode the PUT data into an array
            
            if (is_null($data)) {
                echo json_encode(array("message" => "Invalid data. Failed to decode JSON."));
                return;
            }
        
            // Get the ID from the URL
            $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $uri_segments = explode('/', $uri);
            $id = end($uri_segments); // Get the last segment of the URL (ID)
        
            if (is_numeric($id)) {
                // Check if the ID exists in the database
                $lowongan->id = $id;
                
                // Set the properties from the PUT data
                if (isset($data['title'])) {
                    $lowongan->title = $data['title'];
                }
                if (isset($data['description'])) {
                    $lowongan->description = $data['description'];
                }
                if (isset($data['location'])) {
                    $lowongan->location = $data['location'];
                }
        
                // Attempt to update the lowongan
                if ($lowongan->update()) {
                    echo json_encode(array("message" => "Lowongan updated successfully."));
                } else {
                    echo json_encode(array("message" => "Failed to update lowongan."));
                }
            } else {
                echo json_encode(array("message" => "Invalid ID provided."));
            }
            break;        
    case 'DELETE':
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri_segments = explode('/', $uri);
        $id = end($uri_segments);
        
        if(is_numeric($id)) {
            $lowongan->id = $id;
            if($lowongan->delete()) {
                echo json_encode(array("message" => "Lowongan deleted successfully."));
            } else {
                echo json_encode(array("message" => "Failed to delete lowongan."));
            }
        } else {
            echo json_encode(array("message" => "Invalid ID provided."));
        }
        break;

    default:
        http_response_code(405); // Method Not Allowed
        echo json_encode(array("message" => "Method Not Allowed"));
        break;
}
?>