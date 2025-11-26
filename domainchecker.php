<?php
function renderPage(string $body, string $title = 'Domain Age Checker'): void
{
    echo "<!DOCTYPE html>\n<html lang=\"en\">\n<head>\n    <meta charset=\"UTF-8\">\n    <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">\n    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n    <title>" . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . "</title>\n    <script src=\"https://code.jquery.com/jquery-3.6.0.min.js\" integrity=\"sha256-K+ctZQ+YdBV/OGJySlcF6lFqC9bYdY+4K4e72qYFAmE=\" crossorigin=\"anonymous\"></script>\n    <style>\n        :root {\n            --primary: #1976d2;\n            --primary-dark: #125a9c;\n            --border: #e1e5eb;\n            --error: #b00020;\n            --muted: #5c6b7a;\n        }\n        * { box-sizing: border-box; }\n        body {\n            margin: 0;\n            font-family: Arial, sans-serif;\n            background: #f5f7fb;\n            color: #1d232a;\n            min-height: 100vh;\n            display: flex;\n            align-items: center;\n            justify-content: center;\n            padding: 1rem;\n        }\n        .card {\n            background: #fff;\n            width: 900px;\n            max-width: 100%;\n            border-radius: 12px;\n            padding: 1.75rem 2rem;\n            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);\n        }\n        h1 {\n            margin: 0 0 0.35rem;\n            font-size: 1.5rem;\n        }\n        p {\n            margin: 0 0 1rem;\n            color: var(--muted);\n        }\n        .result {\n            margin-top: 1rem;\n            border: 1px solid var(--border);\n            border-radius: 12px;\n            padding: 1rem 1.25rem;\n            background: linear-gradient(135deg, #f9fbff 0%, #f3f5fa 100%);\n        }\n        .label {\n            font-weight: 600;\n            color: #0f172a;\n        }\n        .error {\n            color: var(--error);\n            font-weight: 600;\n        }\n        .actions {\n            display: flex;\n            gap: 0.75rem;\n            flex-wrap: wrap;\n            margin-top: 1.25rem;\n        }\n        .button {\n            padding: 0.8rem 1.1rem;\n            border-radius: 8px;\n            border: 1px solid var(--border);\n            background: #fff;\n            cursor: pointer;\n            font-weight: 600;\n            transition: box-shadow 0.2s ease, background 0.2s ease, border 0.2s ease;\n        }\n        .button.primary {\n            background: var(--primary);\n            color: #fff;\n            border-color: var(--primary);\n        }\n        .button.primary:hover { background: var(--primary-dark); }\n        .button:hover { box-shadow: 0 2px 8px rgba(0,0,0,0.08); }\n        code {\n            background: #f2f4f7;\n            padding: 0.1rem 0.35rem;\n            border-radius: 4px;\n        }\n        form {\n            margin-top: 1rem;\n            display: grid;\n            gap: 0.75rem;\n        }\n        label {\n            font-weight: 600;\n            color: #0f172a;\n        }\n        input[type=text] {\n            padding: 0.85rem 0.9rem;\n            border-radius: 10px;\n            border: 1px solid var(--border);\n            font-size: 1rem;\n            transition: border 0.2s ease, box-shadow 0.2s ease;\n        }\n        input[type=text]:focus {\n            outline: none;\n            border-color: var(--primary);\n            box-shadow: 0 0 0 3px rgba(25, 118, 210, 0.12);\n        }\n        .table {\n            width: 100%;\n            border-collapse: collapse;\n        }\n        .table tr + tr td {\n            border-top: 1px solid var(--border);\n        }\n        .table td {\n            padding: 0.75rem 0;\n        }\n        .value {\n            font-weight: 600;\n            color: #0f172a;\n        }\n        .muted { color: var(--muted); }
    </style>\n</head>\n<body>\n<div class=\"card\">\n    $body\n</div>\n<script>\n    $(function() {\n        $('#retry').on('click', function() { window.location.href = 'domainchecker.php'; });\n        $('#load-original').on('click', function() {\n            const original = $(this).data('url');\n            if (original) window.location.href = original;\n        });\n        $('#domain-form').on('submit', function(e) {\n            const input = $('#url');\n            const value = input.val().trim();\n            if (!value) {\n                e.preventDefault();\n                input.focus();\n                alert('Please enter a website URL to check.');\n            }\n        });\n    });\n</script>\n</body>\n</html>";
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
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_HTTPHEADER => [
            'Accept: application/rdap+json, application/json',
            'User-Agent: Domain-Age-Checker/1.2'
        ],
    ]);

    $body = curl_exec($ch);
    $errNo = curl_errno($ch);
    $err = curl_error($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($body === false) {
        $message = $errNo === CURLE_COULDNT_CONNECT
            ? 'Unable to reach RDAP service. Please check your network or proxy settings.'
            : 'Request failed: ' . $err;
        return ['error' => $message];
    }
    if ($status >= 400) {
        $detail = $status === 403
            ? 'Access to RDAP service was denied. This can happen behind restricted networks; try again on an unrestricted connection.'
            : 'RDAP lookup returned HTTP ' . $status;
        return ['error' => $detail];
    }

    $json = json_decode($body, true);
    if (!is_array($json)) {
        return ['error' => 'Unable to parse RDAP response'];
    }
    return ['data' => $json];
}

function findEventDate(array $rdap, array $actions): ?string
{
    if (!isset($rdap['events']) || !is_array($rdap['events'])) {
        return null;
    }
    foreach ($rdap['events'] as $event) {
        if (!is_array($event)) {
            continue;
        }
        $action = strtolower($event['eventAction'] ?? '');
        if (in_array($action, $actions, true) && !empty($event['eventDate'])) {
            return $event['eventDate'];
        }
    }
    return null;
}

function formatUtc(?string $date): ?string
{
    if ($date === null) {
        return null;
    }
    try {
        $dt = new DateTimeImmutable($date);
        return $dt->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d H:i:s \U\T\C');
    } catch (Exception $e) {
        return null;
    }
}

function formatAge(?string $date): ?string
{
    if ($date === null) {
        return null;
    }
    try {
        $registered = new DateTimeImmutable($date);
        $now = new DateTimeImmutable('now', new DateTimeZone('UTC'));
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
    } catch (Exception $e) {
        return null;
    }
}

$url = normalizeUrl($_GET['url'] ?? $_POST['url'] ?? null);
if ($url === null) {
    $body = '<h1>Domain Age Checker</h1>'
        . '<p>Enter any website URL below to calculate its domain age using live RDAP records.</p>'
        . '<form id="domain-form" method="get" action="domainchecker.php">'
        . '<label for="url">Website URL</label>'
        . '<input type="text" id="url" name="url" placeholder="https://example.com" autocomplete="url" required>'
        . '<div class="actions">'
        . '<button type="submit" class="button primary">Check domain age</button>'
        . '<button type="reset" class="button">Clear</button>'
        . '</div>'
        . '</form>'
        . '<p class="muted">The tool uses public RDAP data to determine creation, update, and expiration dates.</p>';

    renderPage($body);
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
$createdRaw = findEventDate($rdap, ['registration', 'registered', 'creation', 'created']);
$updatedRaw = findEventDate($rdap, ['last changed', 'last update of rdap database', 'last update']);
$expiryRaw = findEventDate($rdap, ['expiration', 'expiry', 'expired']);

$created = formatUtc($createdRaw);
$updated = formatUtc($updatedRaw);
$expiry = formatUtc($expiryRaw);
$age = formatAge($createdRaw);

$domainEscaped = htmlspecialchars($domain, ENT_QUOTES, 'UTF-8');
$urlEscaped = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');

$rows = [
    'Domain Name' => $domainEscaped,
    'Domain Created on' => $created ?? '<span class="muted">Not available</span>',
    'Domain Age' => $age ?? '<span class="muted">Not available</span>',
    'Domain Updated Date' => $updated ?? '<span class="muted">Not available</span>',
    'Domain Expiration' => $expiry ?? '<span class="muted">Not available</span>',
];

$tableRows = '';
foreach ($rows as $label => $value) {
    $tableRows .= '<tr><td class="label">' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . ':</td><td class="value">' . $value . '</td></tr>';
}

$body = '<h1>Domain Age Result</h1>'
    . '<p>We checked the RDAP record for <code>' . $domainEscaped . '</code>.</p>'
    . '<div class="result">'
    . '<table class="table" role="presentation">' . $tableRows . '</table>'
    . '</div>'
    . '<div class="actions">'
    . '<button class="button" id="retry">Check another domain</button>'
    . '<button class="button primary" id="load-original" data-url="' . $urlEscaped . '">Open website</button>'
    . '</div>';

renderPage($body, 'Domain Age Result');
