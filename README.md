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

## Frontend framework checker
Use `formenrty.html` and `checkfrontend.php` to detect whether a site likely runs React, Angular, or Vue on the frontend.
### Requirements
- PHP with cURL extension enabled
- A PHP-capable web server (or `php -S localhost:8000` for local testing)
### Usage
1. Serve the files locally (e.g., `php -S localhost:8000`).
2. Open `http://localhost:8000/formenrty.html` in your browser.
3. Enter the target website URL; the form sends you to `checkfrontend.php` with the detection result.

## Text case converter
Use `caseenrty.html` and `convertcase.php` to paste text and apply multiple case conversions and text utilities in the browser.
### Requirements
- A PHP-capable web server (or `php -S localhost:8000` for local testing)
### Usage
1. Serve the files locally (e.g., `php -S localhost:8000`).
2. Open `http://localhost:8000/caseenrty.html` in your browser.
3. Paste any text into the form and click **Continue to Converter**.
4. Use the buttons on `convertcase.php` to transform text into sentence, lower, upper, capitalize, title, inverse case, copy to clipboard, hyphen, underscore, reverse, or clear the textarea while live counts update.

## Domain age checker
Use `domainenrty.html` and `domainchecker.php` to calculate a domain's age based on its RDAP registration date.
### Requirements
- PHP with cURL extension enabled
- A PHP-capable web server (or `php -S localhost:8000` for local testing)
### Usage
1. Serve the files locally (e.g., `php -S localhost:8000`).
2. Open `http://localhost:8000/domainenrty.html` in your browser.
3. Enter a website URL and submit to view the registration date and computed age pulled from RDAP.
