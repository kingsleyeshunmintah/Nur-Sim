<?php
// ai.php — AI provider abstraction (Anthropic + OpenAI)

declare(strict_types=1);

function call_ai(string $system_prompt, array $messages): string {
    return AI_PROVIDER === 'anthropic'
        ? call_anthropic($system_prompt, $messages)
        : (AI_PROVIDER === 'gemini'
            ? call_gemini($system_prompt, $messages)
            : call_openai($system_prompt, $messages)
        );
}

function call_anthropic(string $system_prompt, array $messages): string {
    if (!ANTHROPIC_API_KEY) {
        throw new RuntimeException('ANTHROPIC_API_KEY is not configured in .env');
    }

    $payload = json_encode([
        'model'      => 'claude-opus-4-6',
        'max_tokens' => 512,
        'system'     => $system_prompt,
        'messages'   => $messages,
    ]);

    $ch = curl_init('https://api.anthropic.com/v1/messages');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $payload,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'x-api-key: ' . ANTHROPIC_API_KEY,
            'anthropic-version: 2023-06-01',
        ],
    ]);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code !== 200) {
        throw new RuntimeException("Anthropic API error ($http_code): $response");
    }

    $data = json_decode($response, true);
    return $data['content'][0]['text'] ?? 'No response received.';
}

function call_openai(string $system_prompt, array $messages): string {
    if (!OPENAI_API_KEY) {
        throw new RuntimeException('OPENAI_API_KEY is not configured in .env');
    }

    $oai_messages = array_merge(
        [['role' => 'system', 'content' => $system_prompt]],
        $messages
    );

    $payload = json_encode([
        'model'      => 'gpt-4o',
        'max_tokens' => 512,
        'messages'   => $oai_messages,
    ]);

    $ch = curl_init('https://api.openai.com/v1/chat/completions');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $payload,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . OPENAI_API_KEY,
        ],
    ]);

    $response  = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code !== 200) {
        throw new RuntimeException("OpenAI API error ($http_code): $response");
    }

    $data = json_decode($response, true);
    return $data['choices'][0]['message']['content'] ?? 'No response received.';
}

function call_gemini(string $system_prompt, array $messages): string {
    if (!GEMINI_API_KEY) {
        throw new RuntimeException('GEMINI_API_KEY is not configured in .env');
    }

    // Convert messages to Gemini format
    $contents = [];

    // Add system prompt as first user message
    $contents[] = [
        'parts' => [
            ['text' => $system_prompt]
        ]
    ];

    // Add conversation messages
    foreach ($messages as $message) {
        $contents[] = [
            'parts' => [
                ['text' => $message['content']]
            ]
        ];
    }

    $payload = json_encode([
        'contents' => $contents,
        'generationConfig' => [
            'temperature' => 0.7,
            'maxOutputTokens' => 512,
        ]
    ]);

    $url = 'https://generativelanguage.googleapis.com/v1beta/models/' . urlencode(GEMINI_MODEL) . ':generateContent';
    $ch  = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $payload,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'x-goog-api-key: ' . GEMINI_API_KEY,
        ],
    ]);

    $response  = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code !== 200) {
        throw new RuntimeException("Gemini API error ($http_code): $response");
    }

    $data = json_decode($response, true);
    return $data['candidates'][0]['content']['parts'][0]['text'] ?? 'No response received.';
}
