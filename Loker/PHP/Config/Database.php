<?php
// Database configuration
class Database {
    private $host = "localhost";
    private $db_name = "lowongan_kerja";
    private $username = "root";
    private $password = "";
    private $port = 3306; // Sesuaikan dengan port MySQL jika tidak default
    public $conn;

    // Constructor untuk koneksi ke database
    public function __construct() {
        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->db_name, $this->port);
        

    }

    // Fungsi untuk mendapatkan koneksi
    public function getConnection() {
        return $this->conn;
    }
}

?>
