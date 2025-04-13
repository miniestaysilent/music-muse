<?php
use Dotenv\Dotenv;
use TeamCherry\MusicMuse\App;
use TeamCherry\MusicMuse\Account;
use TeamCherry\MusicMuse\SessionManager;

require_once "vendor/autoload.php";

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Initialize session
// SessionManager::init();

// Create app instance
$app = new App();
$site_name = $app->site_name;

// Set page title
$page_title = $site_name . " | Login";

// Initialize error variable
$login_error = null;

// Check for signup success message
$signup_success = isset($_GET['signup']) && $_GET['signup'] === 'success';

// Load Twig template
$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader);
$template = $twig->load('login.twig');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $account = new Account();

    // Basic validation (you might want more robust validation)
    if (empty($email) || empty($password)) {
        $login_error = "Please enter both email and password.";
    } else {
        // Attempt to log in the user
        $user = $account->getAccountByEmail($email);

        if ($user) {
            // Verify the password
            if (password_verify($password, $user['password_hashed'])) {
                // Password is correct, set session and redirect
                $_SESSION['user_id'] = $user['id']; // Assuming your user table has an 'id'
                $_SESSION['email'] = $user['email'];
                $login_success = true;
                // header("Location: /"); // Redirect to homepage or dashboard
                exit();
            } else {
                // Incorrect password
                $login_error = "Incorrect password.";
            }
        } else {
            // User not found
            $login_error = "User with that email not found.";
        }
    }
}

// Render the template
echo $template->render([
    'title' => $page_title,
    'website_name' => $site_name,
    'login_error' => $login_error,
    'signup_success' => $signup_success,
]);

?>