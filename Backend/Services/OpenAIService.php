<?php

require_once __DIR__ . '/../config/openai.php';

class OpenAIService
{
    /**
     * Parse a free-text health log into structured fields.
     *
     * Expected JSON object from the model:
     * {
     *   "sleep_hours": number or null,
     *   "steps_count": number or null,
     *   "exercise_minutes": number or null,
     *   "caffeine_cups": number or null,
     *   "water_liters": number or null,
     *   "mood_score": number or null
     * }
     */
    public static function parseEntryText(string $rawText): ?array
    {
        $system = <<<SYS
You are an assistant that parses daily health and habit logs.
The user will give you a free-text sentence describing their day, for example:
"walked 25 min, 2 coffees, slept at 01:30, drank 0.5L water, mood 7/10".

You MUST always return ONLY ONE JSON object with this exact schema:

{
  "sleep_hours": number or null,
  "steps_count": number or null,
  "exercise_minutes": number or null,
  "caffeine_cups": number or null,
  "water_liters": number or null,
  "mood_score": number or null
}

Rules:
- If a value is not mentioned, use null.
- "walked 25 min" or any exercise time -> "exercise_minutes"
- "steps", "10k steps", etc. -> "steps_count"
- "coffee", "espresso", etc. -> "caffeine_cups"
- "water", "liters", "L" -> "water_liters" in liters
- Sleep can be reported like "slept 6.5 hours" or "slept at 01:30 and woke at 08:00".
  From times, estimate total sleep_hours.
- Mood from phrases like "mood 7/10" -> mood_score = 7.

Return ONLY the JSON object, no extra text, no explanation.
SYS;

        $userContent = $rawText;

        $ch = curl_init('https://api.openai.com/v1/chat/completions');
        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: ' . 'Bearer ' . OPENAI_API_KEY,
            ],
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode([
                'model'    => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'system', 'content' => $system],
                    ['role' => 'user',   'content' => $userContent],
                ],
                'temperature' => 0,
            ]),
            CURLOPT_RETURNTRANSFER => true,
        ]);

        $resp      = curl_exec($ch);
        $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($resp === false) {
            error_log('OpenAI curl error: ' . $curlError);
            return null;
        }

        if ($httpCode !== 200) {
            error_log('OpenAI HTTP error: ' . $httpCode . ' body: ' . $resp);
            return null;
        }

        $j = json_decode($resp, true);
        if (!isset($j['choices'][0]['message']['content'])) {
            error_log('OpenAI response missing choices[0].message.content');
            return null;
        }

        $content = $j['choices'][0]['message']['content'];

        // Model returns JSON as text; decode it
        $parsed = json_decode($content, true);
        if (!is_array($parsed)) {
            error_log('OpenAI content is not valid JSON: ' . $content);
            return null;
        }

        // Normalize keys and types
        $result = [
            'sleep_hours'      => isset($parsed['sleep_hours'])      ? (float)$parsed['sleep_hours']      : null,
            'steps_count'      => isset($parsed['steps_count'])      ? (int)$parsed['steps_count']        : null,
            'exercise_minutes' => isset($parsed['exercise_minutes']) ? (int)$parsed['exercise_minutes']   : null,
            'caffeine_cups'    => isset($parsed['caffeine_cups'])    ? (int)$parsed['caffeine_cups']      : null,
            'water_liters'     => isset($parsed['water_liters'])     ? (float)$parsed['water_liters']     : null,
            'mood_score'       => isset($parsed['mood_score'])       ? (int)$parsed['mood_score']         : null,
        ];

        return $result;
    }
}
