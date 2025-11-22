<?php
// Frontend framework detection script
// Accepts ?url= and reports whether page likely uses React, Angular, or Vue.

function respond_with_error(string $message): void
{
    http_response_code(400);
    echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>Framework Check Error</title>';
    echo '<style>body{font-family:Arial, sans-serif; margin:2rem;} .error{color:#b00020;}</style>';
    echo '</head><body><h1>Framework Check</h1><p class="error">' . htmlspecialchars($message, ENT_QUOTES) . '</p></body></html>';
    exit;
}

function normalize_url(string $url): string
{
    $trimmed = trim($url);
    if ($trimmed === '') {
        respond_with_error('No URL provided.');
    }

    if (!preg_match('#^https?://#i', $trimmed)) {
        $trimmed = 'https://' . $trimmed;
    }

    if (!filter_var($trimmed, FILTER_VALIDATE_URL)) {
        respond_with_error('Invalid URL. Please include a valid host.');
    }

    return $trimmed;
}

function fetch_page(string $url): string
{
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_USERAGENT => 'FrontendFrameworkChecker/1.0',
        CURLOPT_TIMEOUT => 15,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_MAXREDIRS => 5,
    ]);

    $body = curl_exec($ch);
    if ($body === false) {
        $error = curl_error($ch);
        curl_close($ch);
        respond_with_error('Request failed: ' . $error);
    }

    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($statusCode >= 400) {
        respond_with_error('Received HTTP status ' . $statusCode . ' from the site.');
    }

    return (string) $body;
}

function detect_frameworks(string $html): array
{
    $detections = [];

    if (preg_match('/ng-version|ng-app|ng-controller/i', $html)) {
        $detections[] = 'Angular';
    }

    if (preg_match('/__REACT_DEVTOOLS_GLOBAL_HOOK__|data-reactroot|ReactDOM\.render|react\-dom/i', $html)) {
        $detections[] = 'React';
    }

    if (preg_match('/__VUE_DEVTOOLS_GLOBAL_HOOK__|data-v-app|new Vue\s*\(|vue\.config/i', $html)) {
        $detections[] = 'Vue';
    }

    return array_values(array_unique($detections));
}

$url = $_GET['url'] ?? '';
$normalizedUrl = normalize_url($url);
$html = fetch_page($normalizedUrl);
$frameworks = detect_frameworks($html);

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Framework Check Result</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 2rem; }
        .tag { display: inline-block; background: #1976d2; color: #fff; padding: 0.35rem 0.6rem; border-radius: 4px; margin-right: 0.4rem; }
        .empty { color: #555; }
        a { color: #1976d2; }
    </style>
</head>
<body>
<h1>Framework Check Result</h1>
<p><strong>URL:</strong> <?php echo htmlspecialchars($normalizedUrl, ENT_QUOTES); ?></p>
<?php if (!empty($frameworks)): ?>
    <p>The site appears to use:</p>
    <div>
        <?php foreach ($frameworks as $framework): ?>
            <span class="tag"><?php echo htmlspecialchars($framework, ENT_QUOTES); ?></span>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p class="empty">No React, Angular, or Vue indicators were detected. The site may use a different framework or be server-rendered.</p>
<?php endif; ?>
<p><a href="formenrty.html">Check another URL</a></p>
</body>
</html>
