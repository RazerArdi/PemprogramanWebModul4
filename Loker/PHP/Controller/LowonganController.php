<?php
class LowonganController {
    private $db;
    private $lowongan;

    public function __construct() {
        // Mendapatkan koneksi mysqli dari Database
        $this->db = (new Database())->getConnection();
        $this->lowongan = new Lowongan($this->db);
    }

    // Menangani permintaan GET untuk mendapatkan semua lowongan
    public function getAllLowongan() {
        $result = $this->lowongan->getAll();
        $lowongan_arr = [];

        while ($row = $result->fetch_assoc()) {
            $lowongan_arr[] = $row;
        }

        return json_encode($lowongan_arr);
    }

    // Menangani permintaan GET untuk mendapatkan lowongan berdasarkan ID
    public function getLowonganById($id) {
        // Set ID untuk Lowongan
        $this->lowongan->id = $id;
        
        // Mendapatkan data berdasarkan ID
        $result = $this->lowongan->getSingle();
        $row = $result->fetch_assoc();

        if ($row) {
            // Jika ada data, kembalikan hanya satu data lowongan
            return json_encode($row);
        } else {
            // Jika tidak ditemukan lowongan dengan ID tersebut
            return json_encode(["message" => "Lowongan not found"]);
        }
    }

    // Menangani permintaan POST untuk membuat lowongan baru
    public function createLowongan($data) {
        $this->lowongan->title = $data['title'];
        $this->lowongan->description = $data['description'];
        $this->lowongan->location = $data['location'];

        if ($this->lowongan->create()) {
            return json_encode(["message" => "Lowongan created successfully"]);
        }
        return json_encode(["message" => "Failed to create Lowongan"]);
    }

    // Menangani permintaan PUT untuk memperbarui lowongan
    public function updateLowongan($data) {
        $this->lowongan->id = $data['id'];
        $this->lowongan->title = $data['title'];
        $this->lowongan->description = $data['description'];
        $this->lowongan->location = $data['location'];

        if ($this->lowongan->update()) {
            return json_encode(["message" => "Lowongan updated successfully"]);
        }
        return json_encode(["message" => "Failed to update Lowongan"]);
    }
}
