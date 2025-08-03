<?php
// DEBUGGING / show errors during development
ini_set('display_errors', 1);
error_reporting(E_ALL);

// open SQLite DB
$dbPath = 'E:/classdb.db';
 // adjust path if it's in a different folder
$db = new SQLite3($dbPath);
$db->exec('PRAGMA foreign_keys = ON;');

// Fetch genders
$genders = [];
$roles = [];
$res = $db->query('SELECT genderId, gender FROM gender ORDER BY gender;');
while ($r = $res->fetchArray(SQLITE3_ASSOC)) {
    $genders[] = $r;
}
$res = $db->query('SELECT roleId, role FROM roles ORDER BY role;');
while ($r = $res->fetchArray(SQLITE3_ASSOC)) {
    $roles[] = $r;
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Sign Up</title>
  <style>
    body { font-family: sans-serif; max-width: 800px; margin: 1em auto; }
    form input, form select { display: block; margin: .5em 0; padding: .4em; width: 100%; max-width: 400px; }
    .row { display: flex; gap: 2rem; flex-wrap: wrap; }
    .content { flex: 2; }
    .sidebar { flex: 1; background:#f7f7f7; padding:1em; border-radius:6px; }
  </style>
</head>
<body>

  <div class="row">
    <div class="content">
      <h2>Sign Up</h2>

      <form action="proc/processes.php" method="post">
        <input type="text" name="fullname" placeholder="Enter your full name" required autofocus/>
        <input type="email" name="email" placeholder="Enter your email address" required />
        <input type="tel" name="phone" placeholder="Enter your phone number" maxlength="13" required />

        <label for="genderId">Select gender</label>
        <select name="genderId" id="genderId" required>
          <option value="">Select your gender</option>
          <?php foreach ($genders as $g): ?>
            <option value="<?= htmlspecialchars($g['genderId']) ?>"><?= htmlspecialchars($g['gender']) ?></option>
          <?php endforeach; ?>
        </select>

        <label for="roleId">Select role</label>
        <select name="roleId" id="roleId" required>
          <option value="">Select your role</option>
          <?php foreach ($roles as $r): ?>
            <?php if ($r['role'] === 'Admin') continue; ?>
            <option value="<?= htmlspecialchars($r['roleId']) ?>"><?= htmlspecialchars($r['role']) ?></option>
          <?php endforeach; ?>
        </select>

        <input type="text" name="username" placeholder="Create a username" required />
        <input type="password" name="password" placeholder="Create a password" required />
        <input type="password" name="confirm_password" placeholder="Confirm your password" required />

        <br>
        <input type="submit" name="signup" value="Sign Up" />
        <p><a href="signin.php">Already have an account? Sign In</a></p>
      </form>

      <p>HTML forms enable you to gather user input ...</p>
      <p>You are required to create the following forms on the appropriate pages:</p>
      <ul>
        <li>Contact Us form</li>
        <li>Sign Up form</li>
        <li>Sign In form</li>
      </ul>

      <h2>Learn More About Our Team and Mission</h2>
      <p>sed quia non numquam eius modi tempora ...</p>
    </div>

    <div class="sidebar">
      <h2>Side Bar</h2>
      <p>We are a team of dedicated professionals committed to delivering high-quality services and products.</p>
      <p>This is the about page. It contains information about the website, its purpose, and the team behind it. You can find details on our mission, vision, and values here. We aim to provide a comprehensive overview of our services and how we can help you achieve your goals.</p>
    </div>
  </div>

</body>
</html>
