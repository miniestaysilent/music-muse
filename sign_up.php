<?php

use Dotenv\Dotenv;

require_once 'vendor/autoload.php'; // Ensure Composer autoloader is included

// Load environment variables BEFORE any classes that depend on them are instantiated
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Classes used on this page
use TeamCherry\MusicMuse\App;
use TeamCherry\MusicMuse\Account;

// Create app from App class
$app = new App(); // This will now have access to the loaded environment variables
$site_name = $app->site_name;

// Create data variables
$page_title = $site_name . "|" . "Sign Up";
$signup_errors = [];

// Loading the twig template
$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader);
$template = $twig->load('sign_up.twig');

// Checking for form submmission via POST
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    echo "Post request received.";
    //Store username in a variable
    $username = $_POST['username'];
    // Store email in a variable
    $email = $_POST['email'];
    // Store password in a variable
    $password = $_POST['password'];
    // Store confirm password in a variable
    $confirmPassword = $_POST['confirm-password'];

    // Add account variable
    $account = new Account(); // This will also have access to the loaded environment variables

    // Basic password confirmation check
    if ($password !== $confirmPassword) {
        $signup_errors['confirm-password'] = "Passwords do not match.";
    }

    // // Call the create method from Account if no confirmation error yet
    // if (empty($signup_errors)) {
    //     $account->create($username, $email, $password);
    //     if ($account->response['success'] == true) {
    //         // Account has been created
    //         // Redirect to login
    //         header("Location: login.php?signup=success");
    //         exit();
    //     } else {
    //         // Error occurred during account creation
    //         $signup_errors = $account->response['errors'];
    //     }
    // }

    // Call the create method from Account if no confirmation error yet
    if (empty($signup_errors)) {
        $account->create($username, $email, $password);
        if ($account->response['success'] == true) {
            // Account has been created
            $_SESSION['user_id'] = $user['id']; 
            $_SESSION['email'] = $user['email'];
            $_SESSION['username'] = $user['username'];
            
            $signup_success = true;

            header("Location: index.php"); // Redirect to homepage 
            exit();
        } else {
            // Error occurred during account creation
            $signup_errors = $account->response['errors'];
            $signup_success = false; // Ensure success is false
        }
    } else {
        $signup_success = false; // Ensure success is false if password confirmation fails
    }
}

// Render the output
echo $template->render([
    'title' => $page_title,
    'website_name' => $site_name,
    'errors' => $signup_errors
]);
