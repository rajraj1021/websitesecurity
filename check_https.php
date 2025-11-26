<?php
/**
 * CLI helper to verify whether a site can be reached securely over HTTPS.
 */

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "This script is intended to run from the command line." . PHP_EOL);
    exit(1);
}

if ($argc < 2) {
    fwrite(STDERR, "Usage: php check_https.php <https://example.com>" . PHP_EOL);
    exit(1);
}

$url = $argv[1];

function assertHttpsUrl(string $url): void
{
    $parts = parse_url($url);

    if (($parts['scheme'] ?? '') !== 'https') {
        fwrite(STDERR, "Error: URL must start with https://" . PHP_EOL);
        exit(1);
    }
}

function checkHttpsConnection(string $url): bool
{
    $ch = curl_init($url);
    $timeoutSeconds = 15;

    curl_setopt_array($ch, [
        CURLOPT_NOBODY => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 5,
        CURLOPT_TIMEOUT => $timeoutSeconds,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_USERAGENT => 'HTTPS-Checker/1.0',
    ]);

    $success = curl_exec($ch) !== false;

    if (!$success) {
        $error = curl_error($ch);
        curl_close($ch);
        fwrite(STDERR, "Failed to connect over HTTPS: {$error}" . PHP_EOL);
        return false;
    }

    $httpCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    curl_close($ch);

    if ($httpCode >= 200 && $httpCode < 400) {
        fwrite(STDOUT, "Secure HTTPS connection established. HTTP status: {$httpCode}" . PHP_EOL);
        return true;
    }

    fwrite(STDERR, "HTTPS reached but returned HTTP status: {$httpCode}" . PHP_EOL);
    return false;
}

assertHttpsUrl($url);
$result = checkHttpsConnection($url);

exit($result ? 0 : 2);
