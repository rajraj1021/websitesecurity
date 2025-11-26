<?php
function renderPage(string $body, string $title = 'Domain Age Checker'): void
{
    echo "<!DOCTYPE html>\n<html lang=\"en\">\n<head>\n    <meta charset=\"UTF-8\">\n    <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">\n    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n    <title>" . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . "</title>\n    <script src=\"https://code.jquery.com/jquery-3.6.0.min.js\" integrity=\"sha256-K+ctZQ+YdBV/OGJySlcF6lFqC9bYdY+4K4e72qYFAmE=\" crossorigin=\"anonymous\"></script>\n    <style>\n        :root {\n            --primary: #1976d2;\n            --primary-dark: #125a9c;\n            --border: #e1e5eb;\n            --error: #b00020;\n            --muted: #5c6b7a;\n        }\n        * { box-sizing: border-box; }\n        body {\n            margin: 0;\n            font-family: Arial, sans-serif;\n            background: #f5f7fb;\n            color: #1d232a;\n            min-height: 100vh;\n            display: flex;\n            align-items: center;\n            justify-content: center;\n            padding: 1rem;\n        }\n        .card {\n            background: #fff;\n            width: 800px;\n            max-width: 100%;\n            border-radius: 12px;\n            padding: 1.75rem 2rem;\n            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);\n        }\n        h1 {\n            margin: 0 0 0.35rem;\n            font-size: 1.5rem;\n        }\n        p {\n            margin: 0 0 1rem;\n            color: var(--muted);\n        }\n        .result {\n            margin-top: 1rem;\n            border: 1px solid var(--border);\n            border-radius: 8px;\n            padding: 1rem;\n            background: #fafcff;\n        }\n        .label {\n            font-weight: 600;\n        }\n        .error {\n            color: var(--error);\n            font-weight: 600;\n        }\n        .actions {\n            display: flex;\n            gap: 0.75rem;\n            flex-wrap: wrap;\n            margin-top: 1.25rem;\n        }\n        .button {\n            padding: 0.8rem 1.1rem;\n            border-radius: 8px;\n            border: 1px solid var(--border);\n            background: #fff;\n            cursor: pointer;\n            font-weight: 600;\n        }\n        .button.primary {\n            background: var(--primary);\n            color: #fff;\n            border-color: var(--primary);\n        }\n        .button.primary:hover { background: var(--primary-dark); }\n        .button:hover { box-shadow: 0 2px 8px rgba(0,0,0,0.08); }\n        code {\n            background: #f2f4f7;\n            padding: 0.1rem 0.35rem;\n            border-radius: 4px;\n        }\n    </style>\n</head>\n<body>\n<div class=\"card\">\n    $body\n</div>\n<script>\n    $(function() {\n        $('#retry').on('click', function() { window.history.back(); });\n        $('#load-original').on('click', function() {\n            const original = $(this).data('url');\n            if (original) window.location.href = original;\n        });\n    });\n</script>\n</body>\n</html>";
}

function normalizeUrl(?string $url): ?string
{
    if ($url === null) {
        return null;
    }
    $trimmed = trim($url);
    if ($trimmed === '') {
        return null;
    }
    if (!preg_match('#^https?://#i', $trimmed)) {
        $trimmed = 'https://' . $trimmed;
    }
    return filter_var($trimmed, FILTER_VALIDATE_URL) ? $trimmed : null;
}

function extractDomain(string $url): ?string
{
    $parts = parse_url($url);
    if (!isset($parts['host'])) {
        return null;
    }
    $host = $parts['host'];
    if (function_exists('idn_to_ascii')) {
        $ascii = idn_to_ascii($host, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46);
        if ($ascii !== false) {
            $host = $ascii;
        }
    }
    return strtolower($host);
}

function fetchRdap(string $domain): array
{
    $endpoint = 'https://rdap.org/domain/' . rawurlencode($domain);
    $ch = curl_init($endpoint);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_HTTPHEADER => [
            'Accept: application/json',
            'User-Agent: Domain-Age-Checker/1.0'
        ],
    ]);

    $body = curl_exec($ch);
    $err = curl_error($ch);
    $status = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    curl_close($ch);

    if ($body === false) {
        return ['error' => 'Request failed: ' . $err];
    }
    if ($status >= 400) {
        return ['error' => 'RDAP lookup returned HTTP ' . $status];
    }

    $json = json_decode($body, true);
    if (!is_array($json)) {
        return ['error' => 'Unable to parse RDAP response'];
    }
    return ['data' => $json];
}

function findRegistrationDate(array $rdap): ?string
{
    if (!isset($rdap['events']) || !is_array($rdap['events'])) {
        return null;
    }
    foreach ($rdap['events'] as $event) {
        if (!is_array($event)) {
            continue;
        }
        $action = $event['eventAction'] ?? '';
        if (in_array($action, ['registration', 'registered'], true) && !empty($event['eventDate'])) {
            return $event['eventDate'];
        }
    }
    return null;
}

function formatAge(DateTimeImmutable $registered, DateTimeImmutable $now): string
{
    $diff = $registered->diff($now);
    $parts = [];
    $map = [
        'y' => 'year',
        'm' => 'month',
        'd' => 'day',
    ];
    foreach ($map as $prop => $label) {
        if ($diff->$prop > 0) {
            $parts[] = $diff->$prop . ' ' . $label . ($diff->$prop === 1 ? '' : 's');
        }
    }
    if (!$parts) {
        return 'Less than a day old';
    }
    return implode(', ', $parts);
}

$url = normalizeUrl($_GET['url'] ?? $_POST['url'] ?? null);
if ($url === null) {
    renderPage('<h1 class="error">No URL provided</h1><p>Please go back and enter a valid website URL.</p><div class="actions"><button class="button" id="retry">Go back</button></div>');
    exit;
}

$domain = extractDomain($url);
if ($domain === null) {
    renderPage('<h1 class="error">Invalid URL</h1><p>Unable to read the domain from the provided URL. Try again with a standard hostname like <code>https://example.com</code>.</p><div class="actions"><button class="button" id="retry">Go back</button></div>');
    exit;
}

$response = fetchRdap($domain);
if (isset($response['error'])) {
    $escaped = htmlspecialchars($response['error'], ENT_QUOTES, 'UTF-8');
    renderPage("<h1 class=\"error\">Lookup failed</h1><p>$escaped</p><div class=\"actions\"><button class=\"button\" id=\"retry\">Try another domain</button></div>");
    exit;
}

$rdap = $response['data'];
$registrationDate = findRegistrationDate($rdap);
if ($registrationDate === null) {
    renderPage('<h1>Domain Age</h1><p class="error">Registration date not available from RDAP for <code>' . htmlspecialchars($domain, ENT_QUOTES, 'UTF-8') . '</code>.</p><div class="actions"><button class="button" id="retry">Check another domain</button><button class="button" id="load-original" data-url="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '">Open website</button></div>');
    exit;
}

try {
    $registered = new DateTimeImmutable($registrationDate);
    $now = new DateTimeImmutable('now', new DateTimeZone('UTC'));
    $age = formatAge($registered, $now);
    $formattedDate = $registered->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d H:i:s \U\T\C');
} catch (Exception $e) {
    renderPage('<h1 class="error">Date parsing error</h1><p>Could not interpret registration date returned by RDAP.</p><div class="actions"><button class="button" id="retry">Try another domain</button></div>');
    exit;
}

$domainEscaped = htmlspecialchars($domain, ENT_QUOTES, 'UTF-8');
$urlEscaped = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
$body = "<h1>Domain Age Result</h1>\n<p>We checked the RDAP registration record for <code>$domainEscaped</code>.</p>\n<div class=\"result\">\n    <p><span class=\"label\">Registration date:</span> $formattedDate</p>\n    <p><span class=\"label\">Domain age:</span> $age</p>\n</div>\n<div class=\"actions\">\n    <button class=\"button\" id=\"retry\">Check another domain</button>\n    <button class=\"button primary\" id=\"load-original\" data-url=\"$urlEscaped\">Open website</button>\n</div>";

renderPage($body, 'Domain Age Result');
