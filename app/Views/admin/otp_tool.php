<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CampusVoice OTP Admin Tool</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&family=Source+Serif+4:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --ink: #0f172a;
            --paper: #f8fafc;
            --panel: #ffffff;
            --brand: #0f766e;
            --accent: #ea580c;
            --muted: #64748b;
            --line: #e2e8f0;
            --ok: #166534;
            --bad: #b91c1c;
            --shadow: 0 16px 36px rgba(15, 23, 42, 0.12);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Space Grotesk', sans-serif;
            color: var(--ink);
            min-height: 100vh;
            background:
                radial-gradient(circle at 10% 15%, rgba(15, 118, 110, 0.15), transparent 40%),
                radial-gradient(circle at 90% 0%, rgba(234, 88, 12, 0.16), transparent 30%),
                linear-gradient(170deg, #f7fbff 0%, #fefaf4 100%);
        }

        .page {
            max-width: 1120px;
            margin: 0 auto;
            padding: 28px 18px 40px;
        }

        .hero {
            background: linear-gradient(120deg, #0f766e, #115e59);
            color: #fff;
            border-radius: 18px;
            box-shadow: var(--shadow);
            padding: 26px 22px;
            animation: rise-in 500ms ease-out;
        }

        .hero h1 {
            margin: 0 0 10px;
            font-size: clamp(1.3rem, 2.8vw, 2rem);
            letter-spacing: 0.2px;
        }

        .hero p {
            margin: 0;
            color: rgba(255, 255, 255, 0.9);
            max-width: 780px;
            line-height: 1.6;
            font-family: 'Source Serif 4', serif;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
            margin-top: 18px;
        }

        .card {
            border-radius: 16px;
            border: 1px solid var(--line);
            background: var(--panel);
            box-shadow: var(--shadow);
            overflow: hidden;
            animation: rise-in 540ms ease-out;
        }

        .card-header {
            border-bottom: 1px solid var(--line);
            background: #f8fafc;
            padding: 14px 16px;
        }

        .card-header h2 {
            margin: 0;
            font-size: 1rem;
            font-weight: 700;
        }

        .card-body {
            padding: 14px 16px 16px;
        }

        .help {
            margin: 0 0 14px;
            color: var(--muted);
            font-size: 0.9rem;
            line-height: 1.5;
            font-family: 'Source Serif 4', serif;
        }

        label {
            display: block;
            font-size: 0.84rem;
            margin-bottom: 5px;
            font-weight: 600;
        }

        input {
            width: 100%;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            padding: 10px 12px;
            margin-bottom: 12px;
            font: inherit;
            color: var(--ink);
            background: #fff;
            transition: border-color 180ms ease, box-shadow 180ms ease;
        }

        input:focus {
            outline: none;
            border-color: #0f766e;
            box-shadow: 0 0 0 4px rgba(15, 118, 110, 0.15);
        }

        .btn {
            border: none;
            border-radius: 10px;
            padding: 10px 14px;
            font: inherit;
            font-weight: 600;
            cursor: pointer;
            transition: transform 140ms ease, opacity 180ms ease;
        }

        .btn:active {
            transform: translateY(1px);
        }

        .btn-teal {
            background: var(--brand);
            color: #fff;
        }

        .btn-orange {
            background: var(--accent);
            color: #fff;
        }

        .result {
            margin-top: 14px;
            border: 1px dashed #cbd5e1;
            background: #f8fafc;
            border-radius: 12px;
            padding: 12px;
            min-height: 68px;
            white-space: pre-wrap;
            font-size: 0.83rem;
            line-height: 1.5;
            color: #1e293b;
            font-family: Consolas, 'Courier New', monospace;
        }

        .status {
            margin-top: 6px;
            font-size: 0.84rem;
            font-weight: 600;
        }

        .status.ok {
            color: var(--ok);
        }

        .status.bad {
            color: var(--bad);
        }

        .meta {
            margin-top: 14px;
            background: #fff7ed;
            border: 1px solid #fed7aa;
            color: #9a3412;
            border-radius: 12px;
            padding: 12px 14px;
            font-size: 0.86rem;
            line-height: 1.5;
        }

        @keyframes rise-in {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
<main class="page">
    <section class="hero">
        <h1>CampusVoice OTP Control Panel</h1>
        <p>
            This tool tests the password reset OTP API used by web and mobile clients.
            Start with request OTP, then verify code, then reset password.
        </p>
    </section>

    <section class="grid">
        <article class="card">
            <div class="card-header">
                <h2>1) Request OTP</h2>
            </div>
            <div class="card-body">
                <p class="help">Sends a 6-digit OTP to the Gmail address if the account exists.</p>
                <label for="request-email">Email</label>
                <input id="request-email" type="email" placeholder="student@example.com">
                <button id="btn-request" class="btn btn-teal" type="button">Send OTP</button>
                <div id="request-status" class="status"></div>
                <div id="request-result" class="result">Waiting for request...</div>
            </div>
        </article>

        <article class="card">
            <div class="card-header">
                <h2>2) Verify OTP</h2>
            </div>
            <div class="card-body">
                <p class="help">Checks if OTP code is valid and not expired.</p>
                <label for="verify-email">Email</label>
                <input id="verify-email" type="email" placeholder="student@example.com">
                <label for="verify-otp">OTP</label>
                <input id="verify-otp" type="text" maxlength="6" placeholder="123456">
                <button id="btn-verify" class="btn btn-orange" type="button">Verify OTP</button>
                <div id="verify-status" class="status"></div>
                <div id="verify-result" class="result">Waiting for verification...</div>
            </div>
        </article>

        <article class="card">
            <div class="card-header">
                <h2>3) Reset Password</h2>
            </div>
            <div class="card-body">
                <p class="help">Resets password with email, OTP, and matching new passwords.</p>
                <label for="reset-email">Email</label>
                <input id="reset-email" type="email" placeholder="student@example.com">
                <label for="reset-otp">OTP</label>
                <input id="reset-otp" type="text" maxlength="6" placeholder="123456">
                <label for="reset-new-pass">New Password</label>
                <input id="reset-new-pass" type="password" placeholder="Minimum 8 characters">
                <label for="reset-confirm-pass">Confirm Password</label>
                <input id="reset-confirm-pass" type="password" placeholder="Repeat new password">
                <button id="btn-reset" class="btn btn-teal" type="button">Reset Password</button>
                <div id="reset-status" class="status"></div>
                <div id="reset-result" class="result">Waiting for reset...</div>
            </div>
        </article>
    </section>

    <section class="meta">
        Gmail SMTP reminder: set email.fromEmail, email.SMTPUser, and email.SMTPPass in your .env.
        For Gmail, SMTPPass must be a Google App Password.
    </section>
</main>

<script>
    const apiBase = <?= json_encode(rtrim(site_url('api'), '/')) ?>;

    async function postJson(url, payload, statusId, resultId) {
        const statusEl = document.getElementById(statusId);
        const resultEl = document.getElementById(resultId);

        statusEl.className = 'status';
        statusEl.textContent = 'Sending...';
        resultEl.textContent = 'Loading...';

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            const text = await response.text();
            let data = text;
            try {
                data = JSON.parse(text);
            } catch (e) {
                // Keep raw string if response is not JSON.
            }

            if (response.ok) {
                statusEl.classList.add('ok');
                statusEl.textContent = 'Success (' + response.status + ')';
            } else {
                statusEl.classList.add('bad');
                statusEl.textContent = 'Failed (' + response.status + ')';
            }

            resultEl.textContent = typeof data === 'string' ? data : JSON.stringify(data, null, 2);
        } catch (err) {
            statusEl.classList.add('bad');
            statusEl.textContent = 'Network Error';
            resultEl.textContent = err.message;
        }
    }

    document.getElementById('btn-request').addEventListener('click', function () {
        postJson(
            apiBase + '/auth/password/otp/request',
            {
                email: document.getElementById('request-email').value.trim()
            },
            'request-status',
            'request-result'
        );
    });

    document.getElementById('btn-verify').addEventListener('click', function () {
        postJson(
            apiBase + '/auth/password/otp/verify',
            {
                email: document.getElementById('verify-email').value.trim(),
                otp: document.getElementById('verify-otp').value.trim()
            },
            'verify-status',
            'verify-result'
        );
    });

    document.getElementById('btn-reset').addEventListener('click', function () {
        postJson(
            apiBase + '/auth/password/reset',
            {
                email: document.getElementById('reset-email').value.trim(),
                otp: document.getElementById('reset-otp').value.trim(),
                new_password: document.getElementById('reset-new-pass').value,
                confirm_password: document.getElementById('reset-confirm-pass').value
            },
            'reset-status',
            'reset-result'
        );
    });
</script>
</body>
</html>
