# 📬 XKCD Comic Mailer

A PHP-based daily email comic subscription system for [XKCD](https://xkcd.com/), built using file-based storage, HTML email formatting, Mailpit for testing, and CRON automation.  
This project was inspired by the rtCamp Associate Software Engineer assignment and rebuilt as a personal showcase.

---

## 📌 Features

- ✅ **Email subscription with verification (6-digit code)**
- ✅ **Unsubscribe flow with verification code**
- ✅ **HTML email format with latest XKCD comic**
- ✅ **Daily CRON job using `setup_cron.sh`**
- ✅ **No external database – emails stored in `registered_emails.txt`**
- ✅ **Responsive UI with theme toggle**
- ✅ **Tested using PHP 8.3 and Mailpit SMTP server**

---

## 🖼️ Screenshots

> 📁 All screenshots can be found inside the `/screenshots` folder.

---

## 🛠️ Tech Stack

- **PHP 8.3** (No frameworks)
- **Mailpit** (SMTP testing)
- **Git Bash** (for CRON simulation on Windows)
- **HTML + CSS** (Custom dark/light UI)

---

## 🧪 How to Run (Windows)

1. Clone this repo:
   ```bash
   git clone https://github.com/Ani811625/xkcd-comic-mailer
   cd xkcd-comic-mailer/src
   ```

2. Start Mailpit SMTP server:
   ```bash
   mailpit
   ```

3. Launch `index.php` in your local server (e.g., XAMPP or PHP CLI).

4. To send the daily comic manually:
   ```bash
   php cron.php
   ```

5. To auto-schedule via CRON:
   ```bash
   bash setup_cron.sh
   ```

---

## 📂 Project Structure

```
xkcd-comic-mailer/
└── src/
    ├── index.php              # Subscription page
    ├── unsubscribe.php        # Unsubscription page
    ├── cron.php               # Sends XKCD comics to emails
    ├── functions.php          # All helper functions
    ├── setup_cron.sh          # CRON job automation
    ├── registered_emails.txt  # Stores subscribed emails
```

---

## ⚠️ Disclaimer

> This project is for **educational/demo purposes only** and was developed as part of a hiring challenge simulation.

---

## 🙌 Acknowledgements

- [XKCD](https://xkcd.com/) for their open comic archive
- [rtCamp](https://rtcamp.com/) for the original assignment
- [Mailpit](https://github.com/axllent/mailpit) for email testing
