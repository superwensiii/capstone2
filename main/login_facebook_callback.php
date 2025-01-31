<?php
require_once '../vendor/autoload.php';
require_once '../db/connect.php'; // Include your database connection file

session_start();

$fb = new Facebook\Facebook([
    'app_id' => '666274249155260',
    'app_secret' => '60944545b891bb33e2de2e4b5a0bc3fb',
    'default_graph_version' => 'v12.0',
]);

$helper = $fb->getRedirectLoginHelper();

try {
    // Get the access token
    $accessToken = $helper->getAccessToken();

    // Get the user's profile information
    $response = $fb->get('/me?fields=id,name,email', $accessToken);
    $user = $response->getGraphUser();

    // Extract user data
    $facebookId = $user->getId();
    $name = $user->getName();
    $email = $user->getEmail();

    // Check if the user already exists in the database
    $stmt = $conn->prepare("SELECT id FROM loginauth WHERE facebook_id = ?");
    $stmt->bind_param("s", $facebookId);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        // Insert new user into the database
        $stmt = $conn->prepare("INSERT INTO loginauth (facebook_id, name, email) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $facebookId, $name, $email);
        $stmt->execute();
    }

    // Store user info in session
    $_SESSION['user_id'] = $stmt->insert_id ?? $stmt->fetch()->id; // Use the newly inserted ID or existing ID
    $_SESSION['user_name'] = $name;
    $_SESSION['user_email'] = $email;

    // Redirect to index.php
    header('Location: iindex.php');
    exit();
} catch (Exception $e) {
    // Handle error
    echo "Error: " . $e->getMessage();
    header('Location: user_login.php');
    exit();
}
?>