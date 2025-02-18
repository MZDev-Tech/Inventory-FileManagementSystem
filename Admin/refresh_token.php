<?php
session_name("ADMIN_SESSION");
session_start();
require '../connection.php';
require '../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$secret_key = "Zarnat12$&10";
$issuer = "http://localhost";
$audience = "http://localhost";
$issued_at = time();

// Check if the access token is available
if (isset($_COOKIE['access_token'])) {
    $token = $_COOKIE['access_token'];
    try {
        //decode the token
        $decoded_token = JWT::decode($token, new Key($secret_key, 'HS256'));
        //update admin status to active when token refresh
        $user_id = $decoded_token->data->id;
        $query = "UPDATE user SET status='active' WHERE id='$user_id'";
        mysqli_query($con, $query);
        // Generate a new expiration time (1 hour)
        $new_expTime = time() + 3600;
        // Create a new JWT payload with the updated expiration time
        $payload = [
            'iss' => $issuer,
            'aud' => $audience,
            'ist' => $issued_at,
            'exp' => $new_expTime,
            'data' => $decoded_token->data, //keep same user data
        ];

        //encode the new jwt token
        $new_token = JWT::encode($payload, $secret_key, 'HS256');
        setcookie('access_token', $new_token, time() + 3600, '/', "", false, true);
        //return success response
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Token not found in cookie']);
}
