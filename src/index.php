<?php
require_once __DIR__ . '/functions.php';
$message = "";

// Handle email submission
if (isset($_POST['email']) && !isset($_POST['verification_code'])) {
    $email = trim($_POST['email']);
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $code = generateVerificationCode();
        storeCode($email, $code);
        sendVerificationEmail($email, $code);
        $message = "<div class='alert success'>Verification code sent to $email</div>";
    } else {
        $message = "<div class='alert error'>Invalid email address.</div>";
    }
}

// Handle verification code submission
if (isset($_POST['verification_code'])) {
    $email = trim($_POST['email']);
    $code = trim($_POST['verification_code']);
    if (verifyCode($email, $code)) {
        registerEmail($email);
        $message = "<div class='alert success'>$email has been successfully registered!</div>";
    } else {
        $message = "<div class='alert error'>Verification failed. Incorrect code.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>rtCamp XKCD Comics</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Quicksand:wght@500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0ff0fc;
            --secondary: #a259f7;
            --accent: #ff26a9;
            --bg-gradient: linear-gradient(135deg, #18122B 0%, #22223b 100%);
            --glass-bg: rgba(30, 30, 60, 0.85);
            --glass-blur: 18px;
            --shadow: 0 8px 32px 0 rgba(0, 255, 255, 0.08);
            --border-radius: 24px;
            --input-bg: rgba(40, 40, 70, 0.85);
            --input-border: #2d2d4d;
            --input-focus: var(--primary);
            --text-color: #e0e0fa;
            --text-dark: #fff;
            --nav-bg: linear-gradient(90deg, #18122B 0%, #a259f7 100%);
            --footer-bg: #18122B;
            --neon-glow: 0 0 8px var(--primary), 0 0 16px var(--secondary);
            --neon-btn: 0 0 8px var(--accent), 0 0 16px var(--primary);
        }
        body {
            margin: 0;
            font-family: 'Montserrat', 'Quicksand', Arial, sans-serif;
            background: var(--bg-gradient);
            color: var(--text-color);
            min-height: 100vh;
            transition: background 0.5s, color 0.5s;
            position: relative;
            overflow-x: hidden;
        }
        body.dark {
            background: var(--bg-gradient);
            color: var(--text-dark);
        }
        .bg-art {
            position: fixed;
            top: 0; left: 0; width: 100vw; height: 100vh;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
        }
        nav, .container, footer {
            position: relative;
            z-index: 2;
        }
        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 48px;
            background: var(--nav-bg);
            color: white;
            box-shadow: var(--shadow);
            border-bottom-left-radius: 18px;
            border-bottom-right-radius: 18px;
            font-family: 'Montserrat', Arial, sans-serif;
        }
        .logo {
            font-size: 2em;
            font-weight: 700;
            letter-spacing: 2px;
            display: flex;
            align-items: center;
            color: var(--primary);
        }
        .logo span {
            margin-left: 10px;
            font-weight: 400;
            font-size: 0.7em;
            opacity: 0.7;
            color: var(--accent);
        }
        nav a {
            color: var(--primary);
            text-decoration: none;
            margin: 0 18px;
            font-weight: 700;
            font-size: 1.1em;
            position: relative;
            transition: color 0.3s;
            font-family: 'Quicksand', Arial, sans-serif;
        }
        nav a::after {
            content: '';
            display: block;
            width: 0;
            height: 2.5px;
            background: var(--accent);
            transition: width 0.3s;
            position: absolute;
            left: 0;
            bottom: -4px;
            box-shadow: 0 0 8px var(--accent);
        }
        nav a:hover {
            color: var(--accent);
        }
        nav a:hover::after {
            width: 100%;
        }
        .toggle {
            cursor: pointer;
            padding: 10px 22px;
            background: rgba(20,20,40,0.18);
            border: 1.5px solid var(--primary);
            border-radius: 50px;
            font-weight: 700;
            font-size: 1.1em;
            box-shadow: var(--neon-glow);
            transition: background 0.3s, color 0.3s, box-shadow 0.3s;
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--primary);
        }
        .toggle:hover {
            background: var(--accent);
            color: #fff;
            border-color: var(--accent);
            box-shadow: 0 0 16px var(--accent);
        }
        .container {
            max-width: 420px;
            margin: 70px auto 0 auto;
            background: var(--glass-bg);
            backdrop-filter: blur(var(--glass-blur));
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 48px 32px 32px 32px;
            animation: fadeInUp 1.2s cubic-bezier(.39,.575,.565,1.000);
            display: flex;
            flex-direction: column;
            gap: 0;
            align-items: center;
        }
        body.dark .container {
            background: rgba(30,30,60,0.98);
        }
        h1, h2 {
            text-align: center;
            color: var(--primary);
            font-weight: 700;
            margin-bottom: 18px;
            letter-spacing: 1px;
            font-family: 'Montserrat', Arial, sans-serif;
        }
        body.dark h1, body.dark h2 {
            color: var(--accent);
        }
        .form-group {
            margin-bottom: 28px;
            position: relative;
            width: 100%;
            display: flex;
            flex-direction: column-reverse;
            align-items: stretch;
        }
        .floating-label {
            position: absolute;
            left: 24px;
            top: 18px;
            color: #aaa;
            font-size: 1.08em;
            pointer-events: none;
            transition: 0.25s cubic-bezier(.4,0,.2,1) all;
            background: transparent;
            font-family: 'Quicksand', Arial, sans-serif;
        }
        input[type="email"], input[type="text"] {
            width: 100%;
            box-sizing: border-box;
            padding: 22px 24px 10px 24px;
            border-radius: 16px;
            border: 1.5px solid var(--input-border);
            background: var(--input-bg);
            font-size: 1.13em;
            color: var(--text-color);
            outline: none;
            transition: border 0.3s, background 0.3s, box-shadow 0.3s;
            font-family: 'Quicksand', Arial, sans-serif;
            box-shadow: 0 0 8px #0ff0fc33;
        }
        input:focus {
            border-color: var(--primary);
            background: #232946;
            box-shadow: 0 0 16px var(--primary);
            color: var(--primary);
        }
        input:focus + .floating-label,
        input:not(:placeholder-shown) + .floating-label {
            top: -14px;
            left: 16px;
            font-size: 0.93em;
            color: var(--accent);
            background: var(--glass-bg);
            padding: 0 8px;
            border-radius: 8px;
            box-shadow: 0 0 8px var(--accent);
        }
        body.dark input {
            background: rgba(30,30,60,0.85);
            color: var(--text-dark);
            border-color: #444;
        }
        button {
            width: 100%;
            background: linear-gradient(90deg, var(--primary) 0%, var(--accent) 100%);
            color: #fff;
            border: none;
            border-radius: 16px;
            padding: 18px 0;
            font-size: 1.13em;
            font-weight: 700;
            cursor: pointer;
            box-shadow: var(--neon-btn);
            transition: background 0.3s, transform 0.2s, box-shadow 0.3s;
            margin-top: 8px;
            font-family: 'Montserrat', Arial, sans-serif;
            letter-spacing: 1px;
            animation: popIn 0.7s cubic-bezier(.39,.575,.565,1.000);
        }
        button:hover {
            background: linear-gradient(90deg, var(--accent) 0%, var(--primary) 100%);
            transform: translateY(-2px) scale(1.04);
            box-shadow: 0 0 32px var(--accent), 0 0 16px var(--primary);
        }
        .alert {
            padding: 16px 20px;
            margin-bottom: 24px;
            border-radius: 14px;
            font-weight: 700;
            font-size: 1.08em;
            box-shadow: 0 0 8px var(--primary);
            font-family: 'Montserrat', Arial, sans-serif;
            animation: fadeIn 0.7s cubic-bezier(.39,.575,.565,1.000);
            background: #232946cc;
            color: var(--primary);
            border: 1.5px solid var(--primary);
        }
        .success {
            background: #232946cc;
            color: var(--primary);
            border: 1.5px solid var(--primary);
        }
        .error {
            background: #232946cc;
            color: var(--accent);
            border: 1.5px solid var(--accent);
        }
        footer {
            text-align: center;
            margin: 60px 0 20px 0;
            color: #888;
            font-size: 1em;
            background: var(--footer-bg);
            color: #fff;
            padding: 18px 0 10px 0;
            border-top-left-radius: 18px;
            border-top-right-radius: 18px;
            box-shadow: 0 0 16px var(--primary);
            font-family: 'Montserrat', Arial, sans-serif;
        }
        @keyframes fadeIn {
            0% {opacity: 0; transform: translateY(30px);}
            100% {opacity: 1; transform: translateY(0);}
        }
        @keyframes fadeInUp {
            0% {opacity: 0; transform: translateY(60px) scale(0.98);}
            100% {opacity: 1; transform: translateY(0) scale(1);}
        }
        @keyframes popIn {
            0% {opacity: 0; transform: scale(0.95);}
            100% {opacity: 1; transform: scale(1);}
        }
        @media (max-width: 600px) {
            .container {
                padding: 24px 4vw 20px 4vw;
                margin: 32px 0 0 0;
                max-width: 98vw;
            }
            nav {
                flex-direction: column;
                padding: 16px 4vw;
                gap: 10px;
            }
        }
    </style>
    <script>
        function toggleTheme() {
            document.body.classList.toggle("dark");
            localStorage.setItem('theme', document.body.classList.contains('dark') ? 'dark' : 'light');
        }
        window.onload = function() {
            if(localStorage.getItem('theme') === 'dark') {
                document.body.classList.add('dark');
            }
        }
    </script>
</head>
<body>
    <div class="bg-art">
        <!-- SVG Neon Abstract Art -->
        <svg width="100%" height="100%" viewBox="0 0 1440 900" fill="none" xmlns="http://www.w3.org/2000/svg" style="position:absolute;top:0;left:0;z-index:0;">
            <defs>
                <radialGradient id="neon1" cx="50%" cy="50%" r="80%" fx="50%" fy="50%">
                    <stop offset="0%" stop-color="#0ff0fc" stop-opacity="0.7"/>
                    <stop offset="100%" stop-color="#18122B" stop-opacity="0"/>
                </radialGradient>
                <radialGradient id="neon2" cx="50%" cy="50%" r="80%" fx="50%" fy="50%">
                    <stop offset="0%" stop-color="#a259f7" stop-opacity="0.5"/>
                    <stop offset="100%" stop-color="#18122B" stop-opacity="0"/>
                </radialGradient>
                <radialGradient id="neon3" cx="50%" cy="50%" r="80%" fx="50%" fy="50%">
                    <stop offset="0%" stop-color="#ff26a9" stop-opacity="0.4"/>
                    <stop offset="100%" stop-color="#18122B" stop-opacity="0"/>
                </radialGradient>
            </defs>
            <ellipse cx="1200" cy="120" rx="320" ry="120" fill="url(#neon1)"/>
            <ellipse cx="300" cy="800" rx="400" ry="140" fill="url(#neon2)"/>
            <ellipse cx="900" cy="600" rx="180" ry="80" fill="url(#neon3)"/>
            <ellipse cx="200" cy="200" rx="120" ry="60" fill="url(#neon1)"/>
        </svg>
    </div>
    <nav>
        <div class="logo">
            <img src="https://imgs.xkcd.com/static/terrible_small_logo.png" alt="XKCD Logo" style="height:36px;width:36px;vertical-align:middle;border-radius:8px;box-shadow:0 0 8px var(--primary);margin-right:10px;">
            XKCD <span>by rtCamp</span>
        </div>
        <div>
            <a href="index.php">Subscribe</a>
            <a href="unsubscribe.php">Unsubscribe</a>
        </div>
        <div class="toggle" onclick="toggleTheme()">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M21 12.79A9 9 0 1111.21 3a7 7 0 109.79 9.79z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <span>Theme</span>
        </div>
    </nav>
    <div class="container">
        <h1>Subscribe to XKCD Comics</h1>
        <?php echo $message; ?>
        <form method="POST" autocomplete="off" style="margin-bottom: 24px; width:100%;">
            <div class="form-group">
                <input type="email" name="email" id="email-sub" required placeholder=" " autocomplete="email">
                <label for="email-sub" class="floating-label">Email Address</label>
            </div>
            <button type="submit" id="submit-email">Submit</button>
        </form>
        <h2 style="margin-top:24px;">Verify Your Email</h2>
        <form method="POST" autocomplete="off" style="width:100%;">
            <div class="form-group">
                <input type="text" name="verification_code" id="verification_code" maxlength="6" required placeholder=" ">
                <label for="verification_code" class="floating-label">Verification Code</label>
            </div>
            <div class="form-group">
                <input type="email" name="email" id="email-ver" required placeholder=" " autocomplete="email">
                <label for="email-ver" class="floating-label">Email Address</label>
            </div>
            <button type="submit" id="submit-verification">Verify</button>
        </form>
    </div>
    <footer>
        <p>© <?php echo date('Y'); ?> rtCamp XKCD Project • Designed by Aniruddha</p>
    </footer>
</body>
</html>