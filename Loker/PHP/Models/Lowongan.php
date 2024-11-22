<?php
class Lowongan {
    private $conn;
    private $table_name = "lowongan";

    public $id;
    public $title;
    public $description;
    public $location;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Mengambil semua lowongan
    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY created_at DESC";

        if ($stmt = $this->conn->prepare($query)) {
            $stmt->execute();
            $result = $stmt->get_result();
            return $result;
        } else {
            return null;  // Query preparation failed
        }
    }

    // Mengambil lowongan berdasarkan ID
    public function getSingle() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";

        if ($stmt = $this->conn->prepare($query)) {
            $stmt->bind_param("i", $this->id); // "i" untuk integer
            $stmt->execute();
            $result = $stmt->get_result();

            return $result;
        } else {
            return null;  // Query preparation failed
        }
    }

    // Fungsi untuk membuat lowongan baru
    public function create() {
        // Query untuk insert data
        $query = "INSERT INTO " . $this->table_name . " (title, description, location) VALUES (?, ?, ?)";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        // Sanitize input
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->location = htmlspecialchars(strip_tags($this->location));
        
        // Bind parameters
        $stmt->bind_param("sss", $this->title, $this->description, $this->location);
        
        // Execute query
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Fungsi untuk memperbarui lowongan
    public function update() {
        $query = "UPDATE " . $this->table_name . " SET title = ?, description = ?, location = ? WHERE id = ?";

        if ($stmt = $this->conn->prepare($query)) {
            $stmt->bind_param("sssi", $this->title, $this->description, $this->location, $this->id);

            if ($stmt->execute()) {
                return true;
            }
            return false;
        } else {
            return false;
        }
    }

// Fungsi untuk menghapus lowongan
public function delete() {
    // First, check if the lowongan with the provided ID exists
    $check_query = "SELECT id FROM " . $this->table_name . " WHERE id = ?";
    
    if ($stmt = $this->conn->prepare($check_query)) {
        $stmt->bind_param("i", $this->id);  // "i" for integer
        $stmt->execute();
        $stmt->store_result();

        // If no rows are returned, the ID does not exist
        if ($stmt->num_rows == 0) {
            return array("error" => "Lowongan not found.");
        }
    } else {
        return array("error" => "Query preparation failed: " . $this->conn->error);
    }

    // If the lowongan exists, proceed with the delete query
    $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";

    if ($stmt = $this->conn->prepare($query)) {
        $stmt->bind_param("i", $this->id);  // "i" for integer

        if ($stmt->execute()) {
            // Check the affected rows to ensure the deletion occurred
            if ($stmt->affected_rows > 0) {
                return array("message" => "Lowongan deleted successfully.");
            } else {
                // This should be handled if for some reason the deletion didn't happen
                return array("error" => "Lowongan deletion failed, no rows affected.");
            }
        }
        return array("error" => "Failed to delete lowongan: " . $this->conn->error);
    } else {
        return array("error" => "Query preparation failed: " . $this->conn->error);
    }
}
}

