<?php
error_reporting(0);
ini_set('display_errors', 0);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);

$action = $data['action'] ?? ($_GET['action'] ?? 'generate');
$prompt = trim($data['prompt'] ?? ($_GET['prompt'] ?? ''));
$text = trim($data['text'] ?? '');
$apiKey = trim($data['apiKey'] ?? '');
$groqKey = trim($data['groqKey'] ?? '');

function callGroqAPI($userPrompt, $sysRole = "", $key = "") {
    if (empty($key)) return null;
    $url = "https://api.groq.com/openai/v1/chat/completions";
    
    $payload = json_encode([
        "model" => "llama-3.3-70b-versatile",
        "messages" => [
            ["role" => "system", "content" => $sysRole ?: "Você é um redator profissional e especialista em análise de documentos."],
            ["role" => "user", "content" => $userPrompt]
        ],
        "temperature" => 0.5
    ]);

    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Bearer " . $key
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 12);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $res = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200 && $res) {
            $json = json_decode($res, true);
            if (isset($json['choices'][0]['message']['content'])) {
                return trim($json['choices'][0]['message']['content']);
            }
        }
    }
    return null;
}

function callGeminiFree($userPrompt, $key = "") {
    if (empty($key)) return null;
    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . urlencode($key);
    
    $payload = json_encode([
        "contents" => [
            ["parts" => [["text" => $userPrompt]]]
        ]
    ]);

    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 12);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $res = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200 && $res) {
            $json = json_decode($res, true);
            if (isset($json['candidates'][0]['content']['parts'][0]['text'])) {
                return trim($json['candidates'][0]['content']['parts'][0]['text']);
            }
        }
    }
    return null;
}

echo json_encode(["status" => "success", "message" => "Stirling PDF AI Backend Ready"]);
