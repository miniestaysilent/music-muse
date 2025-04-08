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

    public function getAccount() {
        // Implement getAccount logic here
    }

    public function deactivate() {
        // Implement deactivate logic here
    }
}
?>