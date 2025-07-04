<?php
// 1️⃣ Generate a 6-digit verification code
function generateVerificationCode() {
    return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
}

// 2️⃣ Register email to registered_emails.txt
function registerEmail($email) {
    $file = __DIR__ . '/registered_emails.txt';
    $emails = file_exists($file) ? file($file, FILE_IGNORE_NEW_LINES) : [];
    if (!in_array($email, $emails)) {
        file_put_contents($file, $email . PHP_EOL, FILE_APPEND);
    }
}

// 3️⃣ Remove email from registered_emails.txt
function unsubscribeEmail($email) {
    $file = __DIR__ . '/registered_emails.txt';
    if (!file_exists($file)) return;
    $emails = file($file, FILE_IGNORE_NEW_LINES);
    $emails = array_filter($emails, fn($e) => trim($e) !== trim($email));
    file_put_contents($file, implode(PHP_EOL, $emails) . PHP_EOL);
}

// 4️⃣ Send verification email with code
function sendVerificationEmail($email, $code) {
    ini_set("SMTP", "localhost");
ini_set("smtp_port", "1025");

    $subject = "Your Verification Code";
    $message = "<p>Your verification code is: <strong>$code</strong></p>";
    $headers  = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: no-reply@example.com" . "\r\n";
    mail($email, $subject, $message, $headers);
}

// 5️⃣ Check if verification code matches
function verifyCode($email, $code) {
    $path = sys_get_temp_dir() . "/verify_" . md5($email);
    if (file_exists($path)) {
        $storedCode = trim(file_get_contents($path));
        return $storedCode === $code;
    }
    return false;
}

// 🔧 Helper: Store code for later verification
function storeCode($email, $code) {
    file_put_contents(sys_get_temp_dir() . "/verify_" . md5($email), $code);
}

// 6️⃣ Fetch random XKCD comic and return HTML
function fetchAndFormatXKCDData() {
    $random = rand(1, 2800);
    $url = "https://xkcd.com/$random/info.0.json";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verify if needed
    $json = curl_exec($ch);
    curl_close($ch);

    if (!$json) {
        return "<p>Sorry, could not load the XKCD comic.</p>";
    }

    $data = json_decode($json);
    if (!isset($data->img)) {
        return "<p>Comic data is missing.</p>";
    }

    $title = $data->title ?? 'XKCD Comic';
    $img = $data->img;
    $alt = $data->alt ?? '';

    return "
        <h2>$title</h2>
        <img src='$img' alt='$alt' style='max-width:100%;height:auto;'>
        <p><em>$alt</em></p>
        <p><a href='http://localhost:8000/unsubscribe.php?email=$email' id='unsubscribe-button'>Unsubscribe</a></p>
    ";
}


// 7️⃣ Send XKCD comic to all registered users
function sendXKCDUpdatesToSubscribers() {
    $file = __DIR__ . '/registered_emails.txt';
    if (!file_exists($file)) {
        echo "[LOG] Email file not found.\n";
        return;
    }

    $emails = file($file, FILE_IGNORE_NEW_LINES);
    if (empty($emails)) {
        echo "[LOG] No emails to send to.\n";
        return;
    }

    $comic = fetchAndFormatXKCDData();
    if (empty($comic)) {
        echo "[LOG] Comic content is empty.\n";
        return;
    }

    $subject = "Your XKCD Comic";
    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8\r\n";
    $headers .= "From: no-reply@example.com\r\n";

    foreach ($emails as $email) {
        echo "[LOG] Sending to: $email\n";
        mail($email, $subject, $comic, $headers);
    }
}

?>
