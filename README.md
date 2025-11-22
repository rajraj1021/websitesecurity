This is Web Security Checker Tool

## HTTPS connection check script

Use `check_https.php` to verify whether a website responds securely over HTTPS when the URL starts with `https://`.

### Requirements
- PHP with cURL extension enabled

### Usage
```bash
php check_https.php https://example.com
```

The script exits with code 0 on a successful HTTPS response (HTTP 2xx/3xx), or a non-zero code with an error message otherwise.
