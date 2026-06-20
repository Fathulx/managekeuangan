<?php
// api/session_db.php
// File ini harus di-include SETELAH koneksi.php, dan SEBELUM kamu pakai $_SESSION

class DBSessionHandler implements SessionHandlerInterface {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function open($savePath, $sessionName): bool {
        return true;
    }

    public function close(): bool {
        return true;
    }

    public function read($id): string {
        $id = mysqli_real_escape_string($this->conn, $id);
        $result = mysqli_query($this->conn, "SELECT data FROM sessions WHERE id = '$id'");
        if ($row = mysqli_fetch_assoc($result)) {
            return $row['data'];
        }
        return "";
    }

    public function write($id, $data): bool {
        $id = mysqli_real_escape_string($this->conn, $id);
        $data = mysqli_real_escape_string($this->conn, $data);
        $time = time();
        return mysqli_query($this->conn, "REPLACE INTO sessions (id, data, last_access) VALUES ('$id', '$data', $time)");
    }

    public function destroy($id): bool {
        $id = mysqli_real_escape_string($this->conn, $id);
        mysqli_query($this->conn, "DELETE FROM sessions WHERE id = '$id'");
        return true;
    }

    public function gc($max_lifetime): int|false {
        $old = time() - $max_lifetime;
        mysqli_query($this->conn, "DELETE FROM sessions WHERE last_access < $old");
        return true;
    }
}

session_set_save_handler(new DBSessionHandler($conn), true);
session_start();