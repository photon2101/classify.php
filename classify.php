<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// 1️⃣ Read API key header
$headers = getallheaders();
$apiKey = $headers['X-API-Key'] ?? '';

// 2️⃣ Read API key from environment variable
$validKey = getenv('API_KEY'); // set this in Vercel dashboard

if (!$validKey) {
    http_response_code(500);
    echo json_encode(['error' => 'API key not configured']);
    exit;
}

// 3️⃣ Verify the key
if ($apiKey !== $validKey) {
    http_response_code(403);
    echo json_encode(['error' => 'Invalid API key']);
    exit;
}

// 4️⃣ Read user agent from request body
$input = json_decode(file_get_contents('php://input'), true);
$userAgent = strtolower($input['user_agent'] ?? $_SERVER['HTTP_USER_AGENT'] ?? '');

// 5️⃣ Detect bots
$botPatterns = ['bot', 'crawl', 'spider', 'preview', 'fetch', 'telegrambot'];
$isBot = false;
foreach ($botPatterns as $pattern) {
    if (strpos($userAgent, $pattern) !== false) {
        $isBot = true;
        break;
    }
}

// 6️⃣ Respond with classification
echo json_encode([
    'visitor_type' => $isBot ? 'bot' : 'human'
]);
exit;
