This is Web Security Checker Tool
##<<<<<<< codex/check-https-connection-for-website-7u6ni4
## HTTPS connection check script
Use `check_https.php` to verify whether a website responds securely over HTTPS when the URL starts with `https://`.
### Requirements
- PHP with cURL extension enabled
=======

## HTTPS connection check script

Use `check_https.php` to verify whether a website responds securely over HTTPS when the URL starts with `https://`.

### Requirements
- PHP with cURL extension enabled

##>>>>>>> main
### Usage
```bash
php check_https.php https://example.com
```
##<<<<<<< codex/check-https-connection-for-website-7u6ni4
The script exits with code 0 on a successful HTTPS response (HTTP 2xx/3xx), or a non-zero code with an error message otherwise.

## Frontend framework checker
Use `formenrty.html` and `checkfrontend.php` to detect whether a site likely runs React, Angular, or Vue on the frontend.
### Requirements
- PHP with cURL extension enabled
- A PHP-capable web server (or `php -S localhost:8000` for local testing)
### Usage
1. Serve the files locally (e.g., `php -S localhost:8000`).
2. Open `http://localhost:8000/formenrty.html` in your browser.
3. Enter the target website URL; the form sends you to `checkfrontend.php` with the detection result.
=======

The script exits with code 0 on a successful HTTPS response (HTTP 2xx/3xx), or a non-zero code with an error message otherwise.
##>>>>>>> main
