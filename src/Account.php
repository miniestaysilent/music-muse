<?php
    namespace TeamCherry\MusicMuse;

    use \Exception;
    // use Syeda\Classproject\Database;
    // use Syeda\Classproject\Validator;
    // use Syeda\Classproject;

    use TeamCherry\MusicMuse\Database;
    use TeamCherry\MusicMuse\Validator;
    use TeamCherry\MusicMuse;

use Validator as GlobalValidator;

    class Account extends Database{
        public $errors = [];
        public $response = [];

        public function __construct(){
            try{
                parent::__construct();

                $db = new Database();   
                
                if(!$db){
                    throw new Exception("No database available.");
                }
                else{
                    $this -> connection = $db -> connection;
                }
            }
            catch(Exception $exc){
                exit($exc -> getMessage());
            }
        }

        public function create($email, $password){
            // Execute query to create user with email and password

            $create_query = 'INSERT INTO "Account (
                email, 
                password,
                reset,
                active,
                created
                VALUES(?,?,?,TRUE,NOW())
            )"';

            //'::' is used to call static functions
            if(Validator::validateEmail($email) == false){
                // Email is not in a valid format
                $this -> errors['email'] = "Email address is not valid.";
            }

            if(Validator::validatePassword($password) == false){
                // Password is not valid
                $this -> errors['password'] = "Password does not meet requirements.";
                }

                // If there are errors, return the response
                if(count($this -> errors) > 0){
                    return $this -> response['success'] = false;
                    return $this -> response['errors'] = $this -> errors;
                    // print_r($this -> response);

                    return $this -> response;
                }

                // If there are no errors

                //For resetting password and hashing using MD5
                $reset = md5(time().random_int(0,5000));
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                
                // Creaate a mySQL prepared statement
                $statement = $this -> connection -> prepare($create_query);
                //Binding parameters to query
                $statement -> bind_param("sss", $email, $password, $reset);

                if($statement -> execute()){
                    $this -> response['success'] = 1;
                }
                else {
                    $this -> response['success'] = 0;
                    $this -> errors['Falied to execute.'];
                    $this -> response['errors'] = $this -> errors;
                }

                return $this -> response;
        }

        // For resetting password
        public function update($email, $reset){

        }

        public function getAccount(){

        }

        public function deactivate(){

        }
    }
?>