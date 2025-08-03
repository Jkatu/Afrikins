<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

/**
 * Simple redirect helper with optional message via query string.
 */
function redirect_with($url, $msg = '') {
    if ($msg) {
        $separator = str_contains($url, '?') ? '&' : '?';
        $url .= $separator . 'msg=' . urlencode($msg);
    }
    header("Location: $url");
    exit;
}

// === open SQLite and configure ===
$dbPath = 'E:/classdb.db'; // adjust if the DB is somewhere else
$db = new SQLite3($dbPath);

// concurrency & foreign key settings
$db->busyTimeout(5000);                    // wait up to 5s for locked DB
$db->exec('PRAGMA journal_mode = WAL;');   // better concurrent performance
$db->exec('PRAGMA foreign_keys = ON;');    // enforce FKs

/**
 * Retry helper for operations that may hit "database is locked".
 * $fn should perform the action and return truthy on success, false on failure.
 */
function with_retry(callable $fn, int $attempts = 5, int $baseDelayMs = 100) {
    global $db;
    for ($i = 0; $i < $attempts; $i++) {
        $res = $fn();
        if ($res !== false) {
            return $res;
        }
        $err = strtolower($db->lastErrorMsg() ?: '');
        if (str_contains($err, 'database is locked')) {
            // exponential backoff
            usleep($baseDelayMs * 1000 * (1 << $i));
            continue;
        }
        // other error: stop retrying
        break;
    }
    return false;
}

// --- MESSAGE FORM ---
if (isset($_POST['send_message'])) {
    $allnames = trim($_POST['allnames'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($allnames === '' || $email === '' || $phone === '' || $subject === '' || $message === '') {
        echo "All fields are required.";
        exit;
    }

    $ok = with_retry(function () use ($db, $allnames, $email, $phone, $subject, $message) {
        $db->exec('BEGIN;');
        $stmt = $db->prepare('INSERT INTO messages (fullname, email, phone, subject, message) VALUES (:fullname, :email, :phone, :subject, :message)');
        $stmt->bindValue(':fullname', $allnames, SQLITE3_TEXT);
        $stmt->bindValue(':email', $email, SQLITE3_TEXT);
        $stmt->bindValue(':phone', $phone, SQLITE3_TEXT);
        $stmt->bindValue(':subject', $subject, SQLITE3_TEXT);
        $stmt->bindValue(':message', $message, SQLITE3_TEXT);
        $res = $stmt->execute();
        if ($res) {
            $db->exec('COMMIT;');
            return true;
        } else {
            $db->exec('ROLLBACK;');
            return false;
        }
    });

    if ($ok) {
        redirect_with('../contacts.php', 'Message sent successfully.');
    } else {
        echo "Error inserting message: " . $db->lastErrorMsg();
        exit;
    }
}

// --- SIGNUP ---
if (isset($_POST['signup'])) {
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $genderId = intval($_POST['genderId'] ?? 0);
    $roleId = intval($_POST['roleId'] ?? 0);
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($fullname === '' || $email === '' || $phone === '' || $genderId <= 0 || $roleId <= 0 || $username === '' || $password === '' || $confirm_password === '') {
        echo "All fields are required.";
        exit;
    }

    if ($password !== $confirm_password) {
        echo "Passwords do not match.";
        exit;
    }

    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format.";
        exit;
    }

    // uniqueness check: email or username
    $chk = $db->prepare('SELECT userId FROM users WHERE email = :email OR username = :username LIMIT 1');
    $chk->bindValue(':email', $email, SQLITE3_TEXT);
    $chk->bindValue(':username', $username, SQLITE3_TEXT);
    $existing = $chk->execute();
    if ($existing && $existing->fetchArray(SQLITE3_ASSOC)) {
        echo "Email or username already exists.";
        exit;
    }

    $hashed_password = password_hash($confirm_password, PASSWORD_DEFAULT);
    if ($hashed_password === false) {
        echo "Password hashing failed.";
        exit;
    }

    $token = mt_rand(1000, 9999);

    $signupResult = with_retry(function () use ($db, $fullname, $email, $phone, $genderId, $roleId, $username, $hashed_password, $token) {
        $db->exec('BEGIN;');
        $stmt = $db->prepare('INSERT INTO users (fullname, email, phone, genderId, roleId, username, password, token, status) VALUES (:fullname, :email, :phone, :genderId, :roleId, :username, :password, :token, 0)');
        $stmt->bindValue(':fullname', $fullname, SQLITE3_TEXT);
        $stmt->bindValue(':email', $email, SQLITE3_TEXT);
        $stmt->bindValue(':phone', $phone, SQLITE3_TEXT);
        $stmt->bindValue(':genderId', $genderId, SQLITE3_INTEGER);
        $stmt->bindValue(':roleId', $roleId, SQLITE3_INTEGER);
        $stmt->bindValue(':username', $username, SQLITE3_TEXT);
        $stmt->bindValue(':password', $hashed_password, SQLITE3_TEXT);
        $stmt->bindValue(':token', $token, SQLITE3_TEXT);
        $res = $stmt->execute();
        if ($res) {
            $db->exec('COMMIT;');
            return true;
        } else {
            $db->exec('ROLLBACK;');
            return false;
        }
    });

    if ($signupResult) {
        if (function_exists('SendMail')) {
            SendMail($email, "Welcome to Afrikins", "Hi $fullname, thank you for signing up! Your verification code is: <strong>$token</strong>");
        }
        redirect_with('../persons.php', 'Registration successful.');
    } else {
        echo "Error: " . $db->lastErrorMsg();
        exit;
    }
}

// --- DELETE USER ---
if (isset($_GET['delete_user'])) {
    $userId = intval($_GET['delete_user']);
    if ($userId <= 0) {
        echo "User ID is required.";
        exit;
    }

    $del = with_retry(function () use ($db, $userId) {
        $db->exec('BEGIN;');
        $stmt = $db->prepare('DELETE FROM users WHERE userId = :userId');
        $stmt->bindValue(':userId', $userId, SQLITE3_INTEGER);
        $res = $stmt->execute();
        if ($res) {
            $db->exec('COMMIT;');
            return true;
        } else {
            $db->exec('ROLLBACK;');
            return false;
        }
    });

    if ($del) {
        redirect_with('../persons.php', 'deleted');
    } else {
        echo "Error deleting user: " . $db->lastErrorMsg();
        exit;
    }
}

// --- UPDATE USER ---
if (isset($_POST['update_user'])) {
    $fullname = ucwords(strtolower(trim($_POST['fullname'] ?? '')));
    $email = strtolower(trim($_POST['email'] ?? ''));
    $phone = trim($_POST['phone'] ?? '');
    $genderId = intval($_POST['genderId'] ?? 0);
    $roleId = intval($_POST['roleId'] ?? 0);
    $username = strtolower(trim($_POST['username'] ?? ''));
    $userId = intval($_POST['userId'] ?? 0);

    if ($fullname === '' || $email === '' || $phone === '' || $genderId <= 0 || $roleId <= 0 || $username === '' || $userId <= 0) {
        echo "All fields are required.";
        exit;
    }

    $update = with_retry(function () use ($db, $fullname, $email, $phone, $genderId, $roleId, $username, $userId) {
        $db->exec('BEGIN;');
        $stmt = $db->prepare('UPDATE users SET fullname = :fullname, email = :email, phone = :phone, genderId = :genderId, roleId = :roleId, username = :username WHERE userId = :userId');
        $stmt->bindValue(':fullname', $fullname, SQLITE3_TEXT);
        $stmt->bindValue(':email', $email, SQLITE3_TEXT);
        $stmt->bindValue(':phone', $phone, SQLITE3_TEXT);
        $stmt->bindValue(':genderId', $genderId, SQLITE3_INTEGER);
        $stmt->bindValue(':roleId', $roleId, SQLITE3_INTEGER);
        $stmt->bindValue(':username', $username, SQLITE3_TEXT);
        $stmt->bindValue(':userId', $userId, SQLITE3_INTEGER);
        $res = $stmt->execute();
        if ($res) {
            $db->exec('COMMIT;');
            return true;
        } else {
            $db->exec('ROLLBACK;');
            return false;
        }
    });

    if ($update) {
        redirect_with('../persons.php', 'updated');
    } else {
        echo "Error updating user: " . $db->lastErrorMsg();
        exit;
    }
}

// --- SIGNIN ---
if (isset($_POST['signin'])) {
    $entered_username = trim($_POST['username'] ?? '');
    $entered_passphrase = $_POST['passphrase'] ?? '';

    if ($entered_username === '' || $entered_passphrase === '') {
        echo "Username and passphrase are required.";
        exit;
    }

    $stmt = $db->prepare('SELECT * FROM users WHERE username = :username LIMIT 1');
    $stmt->bindValue(':username', $entered_username, SQLITE3_TEXT);
    $res = $stmt->execute();
    $user = $res ? $res->fetchArray(SQLITE3_ASSOC) : false;

    if ($user) {
        if (password_verify($entered_passphrase, $user['password'])) {
            $_SESSION['consort'] = $user;
            redirect_with('../persons.php');
        } else {
            unset($_SESSION['consort']);
            echo "Invalid passphrase.";
        }
    } else {
        unset($_SESSION['consort']);
        echo "Username not found.";
    }
}

// --- LOGOUT ---
if (isset($_GET['logout'])) {
    session_destroy();
    unset($_SESSION['consort']);
    redirect_with('../signin.php', 'logged_out');
}
