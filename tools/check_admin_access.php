<?php
// Simple script to login as admin and check access to routes
$base = 'http://127.0.0.1:8000';
$cookie = sys_get_temp_dir() . '/laravel_test_cookie.txt';
@unlink($cookie);

function curl_get($url, $cookie) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
    curl_setopt($ch, CURLOPT_USERAGENT, 'AdminAccessChecker/1.0');
    $body = curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);
    return [$body, $info];
}

function curl_post($url, $postFields, $cookie) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postFields));
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
    curl_setopt($ch, CURLOPT_USERAGENT, 'AdminAccessChecker/1.0');
    $body = curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);
    return [$body, $info];
}

echo "Checking server at $base\n";
// GET login page to extract CSRF token
list($loginBody, $loginInfo) = curl_get($base . '/login', $cookie);
if ($loginInfo['http_code'] >= 400) {
    echo "Failed to GET /login (HTTP {$loginInfo['http_code']}). Is the server running?\n";
    exit(1);
}

if (!preg_match('/name="_token" value="([^"]+)"/', $loginBody, $m)) {
    echo "Couldn't find CSRF token on /login page.\n";
    // still continue attempt: laravel may accept without token for api, but unlikely
    $token = null;
} else {
    $token = $m[1];
}

$email = 'admin@local.test';
$password = 'secret';
$post = ['email' => $email, 'password' => $password];
if ($token) $post['_token'] = $token;

list($postBody, $postInfo) = curl_post($base . '/login', $post, $cookie);
echo "Login attempt HTTP code: {$postInfo['http_code']}\n";

// Now test routes
$routes = [
    '/',
    '/dashboard',
    '/drivers',
    '/vehicles',
    '/routes',
    '/trips',
    '/deliveries',
    '/incidents',
    '/driver/trips',
    '/client/trips',
    '/profile',
];

$results = [];
foreach ($routes as $r) {
    list($b, $i) = curl_get($base . $r, $cookie);
    $results[$r] = $i['http_code'];
}

echo "\nRoute access results (HTTP codes) for $email:\n";
foreach ($results as $r => $code) {
    echo str_pad($r, 20) . " => $code\n";
}

// Clean up cookie file
@unlink($cookie);

echo "\nDone.\n";
