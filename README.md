
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
Use `domainchecker.php` (or the helper entry page `domainenrty.html`) to calculate a domain's age based on its RDAP registration date.
### Requirements
- PHP with cURL extension enabled
- A PHP-capable web server (or `php -S localhost:8000` for local testing)
### Usage
1. Serve the files locally (e.g., `php -S localhost:8000`).
2. Open `http://localhost:8000/domainchecker.php` (or `domainenrty.html` if you prefer the separate entry page).
3. Enter a website URL and submit to view the registration, update, expiration dates, and computed age pulled from RDAP.

# This is Web Security Checker Tool

## HTTPS connection check script
Use `check_https.php` to verify whether a website responds securely over HTTPS when the URL starts with `https://`.

### Requirements
- PHP with cURL extension enabled

### Domain Age Checker
Use `domain_age.php` to check the domain creation date, expiry date, and overall domain age.

### Case Converter
Use `convertcase.php` to convert text into:
- Uppercase
- Lowercase
- Alternate Case
- Inverse Case
- Toggle Case
- Hyphen / Underscore
- Reverse Text
- Sentence/Word/Character Count

### How to Run
Upload the PHP files into a server that supports PHP, then open each tool in your browser:

