<?php
namespace TeamCherry\MusicMuse;

use \Exception;
use TeamCherry\MusicMuse\Database;
use TeamCherry\MusicMuse\Validator;

class Account extends Database {
    public $errors = [];
    public $response = ['success' => false, 'errors' => []];

    public function __construct() {
        try {
            parent::__construct();
            if (!$this->connection) {
                throw new Exception("No database connection available.");
            }
        } catch (Exception $exc) {
            exit($exc->getMessage());
        }
    }

    public function create($username, $email, $password) {
        $this->errors = [];
        $this->response = ['success' => false, 'errors' => []];

        if (!Validator::validateEmail($email)) {
            $this->errors['email'] = "Email address is not valid.";
        }

        if (!Validator::validatePassword($password)) {
            $this->errors['password'] = "Password does not meet requirements.";
        }

        if (count($this->errors) > 0) {
            $this->response['errors'] = $this->errors;
            return $this->response;
        }

        $reset = md5(time() . random_int(0, 5000));
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $create_query = 'INSERT INTO Account (
            username,
            email,
            password_hashed,
            reset,
            created,
            last_seen,
            active
        ) VALUES (?, ?, ?, ?, NOW(), NOW(), TRUE)';
        
        $statement = $this->connection->prepare($create_query);
        $statement->bind_param("ssss", $username, $email, $hashed, $reset);

        if ($statement->execute()) {
            $this->response['success'] = true;
        } else {
            $this->response['errors']['signup'] = "Failed to create account.";
        }

        return $this->response;
    }

    // For resetting password
    public function update($email, $reset) {
        // Implement update logic here
    }

    // For Log in
    public function getAccountByEmail($email) {
        $query = 'SELECT * FROM Account WHERE email = ?';
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function deactivate() {
        // Implement deactivate logic here
    }
}
?>