<?php
$text = isset($_POST['text']) ? $_POST['text'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Convert Case</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f9fafb;
            color: #111827;
        }
        header {
            padding: 24px;
            text-align: center;
            background: linear-gradient(135deg, #111827, #1f2937);
            color: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.12);
        }
        main {
            max-width: 1100px;
            margin: 32px auto;
            padding: 0 16px 48px;
        }
        .card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            border: 1px solid #e5e7eb;
        }
        label { font-weight: 600; display: block; margin-bottom: 8px; }
        textarea {
            width: 100%;
            min-height: 240px;
            resize: vertical;
            padding: 14px;
            border-radius: 10px;
            border: 1px solid #d1d5db;
            font-size: 16px;
            line-height: 1.5;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        textarea:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
        }
        .actions {
            margin-top: 14px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .actions button {
            border: none;
            padding: 10px 14px;
            border-radius: 8px;
            background: #2563eb;
            color: white;
            cursor: pointer;
            font-weight: 600;
            transition: transform 0.12s ease, box-shadow 0.12s ease;
        }
        .actions button.secondary { background: #111827; }
        .actions button:hover { transform: translateY(-1px); box-shadow: 0 8px 16px rgba(0,0,0,0.12); }
        .actions button:active { transform: translateY(0); box-shadow: none; }
        .stats {
            margin-top: 14px;
            font-size: 14px;
            color: #4b5563;
        }
        .toolbar-title { font-weight: 700; margin-top: 8px; margin-bottom: 6px; }
        .back-link { display: inline-block; margin-bottom: 12px; color: #2563eb; font-weight: 600; text-decoration: none; }
        .back-link:hover { text-decoration: underline; }
        .status { margin-top: 8px; color: #065f46; font-weight: 600; }
    </style>
</head>
<body>
<header>
    <h1>Case Converter</h1>
    <p>Transform your text instantly with the buttons below.</p>
</header>
<main>
    <div class="card">
        <a class="back-link" href="caseenrty.html">&larr; Back to entry form</a>
        <label for="text">Text to convert</label>
        <textarea id="text" name="text" placeholder="Paste any text here..."><?php echo htmlspecialchars($text, ENT_QUOTES, 'UTF-8'); ?></textarea>
        <div class="toolbar-title">Choose an action:</div>
        <div class="actions">
            <button type="button" data-action="sentence">Sentence case</button>
            <button type="button" data-action="lower">lower case</button>
            <button type="button" data-action="upper">upper case</button>
            <button type="button" data-action="capitalize">Capitalize Case</button>
            <button type="button" data-action="title">Title Case</button>

            <button type="button" data-action="alternate">aLtErNaTe Case</button>
            <button type="button" data-action="inverse">Inverse Case</button>
            <button type="button" data-action="toggle">Toggle Case</button>

            <button type="button" data-action="alternate">aLtErNaTe Case</button>
            <button type="button" data-action="inverse">Inverse Case</button>
            <button type="button" data-action="toggle">Toggle Case</button>


            <button type="button" data-action="alternate">aLtErNaTe Case</button>
            <button type="button" data-action="inverse">Inverse Case</button>
            <button type="button" data-action="toggle">Toggle Case</button>


            <button type="button" data-action="alternate">aLtErNaTe Case</button>
            <button type="button" data-action="inverse">Inverse Case</button>
            <button type="button" data-action="toggle">Toggle Case</button>

            <button type="button" data-action="inverse">Inverse Case</button>




            <button type="button" data-action="copy" class="secondary">Copy To Clipboard</button>
            <button type="button" data-action="hyphen">Hyphen</button>
            <button type="button" data-action="underscore">Underscore</button>
            <button type="button" data-action="reverse">Reverse</button>
            <button type="button" data-action="clear" class="secondary">Clear</button>
        </div>
        <div class="stats" id="counts">Character Count: 0 | Word Count: 0 | Sentence Count: 0 | Line Count: 0</div>
        <div class="status" id="status"></div>
    </div>
</main>
<script>
    function sentenceCase(text) {
        const lowered = text.toLowerCase();
        return lowered.replace(/(^\s*[a-zA-Z])|([.!?]\s*[a-zA-Z])/g, (match) => match.toUpperCase());
    }

    function capitalizeCase(text) {
        if (!text) return '';
        const lowered = text.toLowerCase();
        return lowered.charAt(0).toUpperCase() + lowered.slice(1);
    }

    function titleCase(text) {
        return text.toLowerCase().replace(/\b([a-z])/g, (match, p1) => p1.toUpperCase());
    }


    function alternateCase(text) {
        let makeUpper = true;
        return text.split('').map((ch) => {
            if (/[a-zA-Z]/.test(ch)) {
                const updated = makeUpper ? ch.toUpperCase() : ch.toLowerCase();
                makeUpper = !makeUpper;
                return updated;
            }
            return ch;
        }).join('');
    }


    function inverseCase(text) {
        return text.split('').map((ch) => {
            if (ch >= 'a' && ch <= 'z') return ch.toUpperCase();
            if (ch >= 'A' && ch <= 'Z') return ch.toLowerCase();
            return ch;
        }).join('');
    }


    function toggleCase(text) {
        if (!text) return '';

        const hasUpper = /[A-Z]/.test(text);
        const hasLower = /[a-z]/.test(text);

        if (hasUpper && hasLower) {
            return inverseCase(text);
        }

        return hasLower ? text.toUpperCase() : text.toLowerCase();
    }


    function updateCounts(text) {
        const characters = text.length;
        const words = text.trim() ? text.trim().split(/\s+/).filter(Boolean).length : 0;
        const sentences = text.trim() ? (text.match(/[^.!?]+[.!?]+|[^.!?]+$/g) || []).length : 0;
        const lines = text.length ? text.split(/\n/).length : 0;
        $('#counts').text(`Character Count: ${characters} | Word Count: ${words} | Sentence Count: ${sentences} | Line Count: ${lines}`);
    }

    function convert(action) {
        const textarea = $('#text');
        let value = textarea.val();
        let status = '';

        switch(action) {
            case 'sentence':
                value = sentenceCase(value);
                status = 'Converted to Sentence case.';
                break;
            case 'lower':
                value = value.toLowerCase();
                status = 'Converted to lower case.';
                break;
            case 'upper':
                value = value.toUpperCase();
                status = 'Converted to upper case.';
                break;
            case 'capitalize':
                value = capitalizeCase(value);
                status = 'Converted to Capitalize Case.';
                break;
            case 'title':
                value = titleCase(value);
                status = 'Converted to Title Case.';
                break;

            case 'alternate':
                value = alternateCase(value);
                status = 'Converted to aLtErNaTe Case.';
                break;

            case 'inverse':
                value = inverseCase(value);
                status = 'Converted to Inverse Case.';
                break;

            case 'toggle':
                value = toggleCase(value);
                status = 'Toggled case for the text.';
                break;

            case 'copy':
                navigator.clipboard.writeText(value).then(() => {
                    $('#status').text('Copied to clipboard.');
                }).catch(() => {
                    $('#status').text('Unable to copy to clipboard in this browser.');
                });
                return;
            case 'hyphen':
                value = value.replace(/\s+/g, '-');
                status = 'Spaces replaced with hyphens.';
                break;
            case 'underscore':
                value = value.replace(/\s+/g, '_');
                status = 'Spaces replaced with underscores.';
                break;
            case 'reverse':
                value = value.split('').reverse().join('');
                status = 'Text reversed.';
                break;
            case 'clear':
                value = '';
                status = 'Textarea cleared.';
                break;
            default:
                break;
        }

        textarea.val(value);
        updateCounts(value);
        $('#status').text(status);
    }

    $(document).ready(function() {
        const initialText = $('#text').val();
        updateCounts(initialText);

        $('.actions button').on('click', function() {
            const action = $(this).data('action');
            convert(action);
        });

        $('#text').on('input', function() {
            updateCounts($(this).val());
        });
    });
</script>
</body>
</html>
