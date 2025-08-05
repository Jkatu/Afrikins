<?php
// development: show errors (remove in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// SQLite DB path (absolute)
$dbPath = 'E:/classdb.db';
$db = new SQLite3($dbPath);
$db->exec('PRAGMA foreign_keys = ON;');

// bootstrap schema if missing
$db->exec('
CREATE TABLE IF NOT EXISTS gender (
    genderId INTEGER PRIMARY KEY AUTOINCREMENT,
    gender TEXT NOT NULL UNIQUE DEFAULT "",
    dateCreate DATETIME DEFAULT (CURRENT_TIMESTAMP),
    dateUpdate DATETIME DEFAULT (CURRENT_TIMESTAMP)
);
');
$db->exec('
CREATE TABLE IF NOT EXISTS roles (
    roleId INTEGER PRIMARY KEY AUTOINCREMENT,
    role TEXT NOT NULL UNIQUE DEFAULT "",
    dateCreate DATETIME DEFAULT (CURRENT_TIMESTAMP),
    dateUpdate DATETIME DEFAULT (CURRENT_TIMESTAMP)
);
');
$db->exec('
CREATE TABLE IF NOT EXISTS users (
    userId INTEGER PRIMARY KEY AUTOINCREMENT,
    fullname TEXT NOT NULL DEFAULT "",
    email TEXT NOT NULL UNIQUE DEFAULT "",
    phone TEXT NOT NULL DEFAULT "",
    username TEXT NOT NULL UNIQUE DEFAULT "",
    password TEXT NOT NULL DEFAULT "",
    token TEXT,
    status INTEGER NOT NULL DEFAULT 0,
    roleId INTEGER NOT NULL DEFAULT 0,
    genderId INTEGER NOT NULL DEFAULT 0,
    userCreated DATETIME DEFAULT (CURRENT_TIMESTAMP),
    userUpdated DATETIME DEFAULT (CURRENT_TIMESTAMP)
);
');

// seed if empty
if ($db->querySingle("SELECT COUNT(*) FROM gender") == 0) {
    $db->exec("
        INSERT INTO gender (gender, dateCreate, dateUpdate) VALUES
        ('Female', '2025-07-17 16:46:30', '2025-07-17 16:46:30'),
        ('Male', '2025-07-17 16:46:30', '2025-07-17 16:46:30'),
        ('Rather not say', '2025-07-17 16:46:30', '2025-07-17 16:46:30')
    ");
}
if ($db->querySingle("SELECT COUNT(*) FROM roles") == 0) {
    $db->exec("
        INSERT INTO roles (role, dateCreate, dateUpdate) VALUES
        ('Admin', '2025-07-17 16:46:30', '2025-07-17 16:46:30'),
        ('Producer', '2025-07-17 16:46:30', '2025-07-17 16:46:30'),
        ('Director', '2025-07-17 16:46:30', '2025-07-17 16:46:30'),
        ('Cinematographer', '2025-07-17 16:46:30', '2025-07-17 16:46:30'),
        ('Editor', '2025-07-17 16:46:30', '2025-07-17 16:46:30'),
        ('Researcher', '2025-07-17 16:46:30', '2025-07-17 16:46:30')
    ");
}

// load dropdown data
$genders = [];
$roles = [];
$gRes = $db->query('SELECT genderId, gender FROM gender ORDER BY gender;');
while ($row = $gRes->fetchArray(SQLITE3_ASSOC)) {
    $genders[] = $row;
}
$rRes = $db->query('SELECT roleId, role FROM roles ORDER BY role;');
while ($row = $rRes->fetchArray(SQLITE3_ASSOC)) {
    $roles[] = $row;
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Afrikins Documentary Team — Sign Up</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    :root {
      --bg: #ffffff;           /* White background */
      --card: #f0f7ff;         /* Very light blue cards */
      --radius: 12px;
      --shadow: 0 6px 20px rgba(0, 0, 0, 0.05);
      --accent: #007BFF;       /* Bootstrap-like blue */
      --text: #333333;
      --muted: #666666;
      font-family: system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, sans-serif;
    }
    * { box-sizing: border-box; }
    body {
      margin: 0;
      background: var(--bg);
      color: var(--text);
      min-height: 100vh;
      line-height: 1.5;
      padding: 2rem 1rem;
    }
    .container {
      max-width: 1100px;
      margin: 0 auto;
      display: grid;
      gap: 2rem;
      grid-template-columns: 2fr 1fr;
    }
    .card {
      background: var(--card);
      border-radius: var(--radius);
      padding: 2rem;
      box-shadow: var(--shadow);
    }
    h1, h2 { margin-top: 0; font-weight: 600; }
    .subtitle { color: var(--muted); margin-bottom: 1rem; }
    form { display: grid; gap: 1rem; }
    .grid-two {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
      gap: 1rem;
    }
    label {
      display: block;
      font-size: .85rem;
      margin-bottom: .25rem;
      text-transform: uppercase;
      letter-spacing: 1px;
      font-weight: 600;
    }
    input, select {
      background: #ffffff;
      border: 1px solid #ccc;
      padding: 12px 14px;
      border-radius: 8px;
      color: var(--text);
      font-size: 1rem;
      width: 100%;
    }
    input:focus, select:focus {
      outline: 2px solid var(--accent);
      border-color: var(--accent);
    }
    .btn {
      background: var(--accent);
      border: none;
      padding: 14px 20px;
      border-radius: 8px;
      cursor: pointer;
      font-weight: 600;
      letter-spacing: 1px;
      color: #fff;
      transition: background-color .2s;
    }
    .btn:hover { background-color: #0069d9; }
    .small { font-size: .85rem; color: var(--muted); }
    .sidebar h3 { margin-top: 0; }
    .role-note {
      font-size: .7rem;
      background: #d9ecff; /* Pale blue highlight */
      padding: 6px 10px;
      border-radius: 6px;
      display: inline-block;
      margin-bottom: 8px;
    }
    .footer {
      margin-top: 3rem;
      text-align: center;
      font-size: .8rem;
      color: var(--muted);
    }
    a { color: var(--accent); text-decoration: none; }
    .inline-link { font-size: .9rem; }
    .flex { display: flex; gap: 1rem; flex-wrap: wrap; }
    .badge {
      background: var(--accent);
      padding: 4px 10px;
      border-radius: 999px;
      font-size: .65rem;
      text-transform: uppercase;
      letter-spacing: 1px;
      font-weight: 700;
      color: #fff;
    }
</style>


</head>
<body>

  <div class="container">
    <!-- main content -->
    <div class="card">
      <div class="flex" style="justify-content:space-between; align-items:center;">
        <div>
          <h1>Afrikins Documentary Team</h1>
          <div class="subtitle">Telling Africa's stories through authentic documentaries.</div>
        </div>
        <div class="badge">Join Us</div>
      </div>

      <p>We are a collective of storytellers, researchers, and filmmakers dedicated to producing documentaries that amplify African voices, preserve heritage, and illuminate hidden narratives. Sign up to collaborate, contribute, or follow our journey.</p>

      <h2>Create your account</h2>
      <p class="small">Membership gives you access to project dashboards, collaboration tools, and early previews of upcoming documentaries.</p>

      <form action="proc/processes.php" method="post" autocomplete="off" novalidate>
        <div class="grid-two">
          <div>
            <label for="fullname">Full Name</label>
            <input type="text" name="fullname" id="fullname" placeholder="e.g., Amina Odhiambo" required autofocus>
          </div>
          <div>
            <label for="username">Username</label>
            <input type="text" name="username" id="username" placeholder="pick a handle" required>
          </div>
        </div>

        <div class="grid-two">
          <div>
            <label for="email">Email Address</label>
            <input type="email" name="email" id="email" placeholder="you@domain.com" required>
          </div>
          <div>
            <label for="phone">Phone</label>
            <input type="tel" name="phone" id="phone" placeholder="+2547XXXXXXXX" maxlength="13" required>
          </div>
        </div>

        <div class="grid-two">
          <div>
            <label for="genderId">Gender</label>
            <select name="genderId" id="genderId" required>
              <option value="">Select gender</option>
              <?php foreach ($genders as $g): ?>
                <option value="<?= htmlspecialchars($g['genderId'], ENT_QUOTES) ?>"><?= htmlspecialchars($g['gender'], ENT_QUOTES) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label for="roleId">Role / Contribution</label>
            <select name="roleId" id="roleId" required>
              <option value="">Select your role</option>
              <?php foreach ($roles as $r): ?>
                <?php if (strtolower($r['role']) === 'admin') continue; /* hide admin */ ?>
                <option value="<?= htmlspecialchars($r['roleId'], ENT_QUOTES) ?>"><?= htmlspecialchars($r['role'], ENT_QUOTES) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="grid-two">
          <div>
            <label for="password">Password</label>
            <input type="password" name="password" id="password" placeholder="Create a password" required>
          </div>
          <div>
            <label for="confirm_password">Confirm Password</label>
            <input type="password" name="confirm_password" id="confirm_password" placeholder="Re-type password" required>
          </div>
        </div>

        <div style="margin-top:1rem;">
          <input type="submit" name="signup" value="Sign Up" class="btn">
          <p class="small inline-link">Already part of Afrikins? <a href="signin.php">Sign in</a></p>
        </div>
      </form>

      <div style="margin-top:2rem; display:flex; gap:2rem; flex-wrap:wrap;">
        <div style="flex:1; min-width:200px;">
          <h3>Why Afrikins?</h3>
          <p class="small">We center African perspectives—the stories you won’t find in mainstream archives. From oral histories to contemporary social change, our documentaries connect communities across the continent.</p>
        </div>
        <div style="flex:1; min-width:200px;">
          <h3>What you get</h3>
          <ul class="small">
            <li>Early and easy access to film cuts</li>
            <li>Invitation to collaborative research</li>
            <li>Credits on projects you contribute to</li>
            <li>Unlimited access to do </li>
          </ul>
        </div>
      </div>
    </div>

    <!-- sidebar -->
    <div class="card sidebar">
      <h2>About Afrikins Documentaries</h2>
      <p>Afrikins is a passionate team of documentary makers, archivists, and storytellers dedicated to capturing the richness of African cultures, histories, and contemporary lives. We seek to empower local voices, expose untold stories, and build a living archive through film.</p>
      <p class="small">Whether you are a researcher, cinematographer, editor, or community partner, your contribution shapes narratives that resonate across generations.</p>
      <div class="role-note">Roles include: Producer, Director, Cinematographer, Editor, Researcher</div>
      <p class="small">Join us in preserving stories that matter. Your perspective can redefine how Africa is seen and remembered.</p>
    </div>
  </div>

  <div class="footer">
    &copy; <?= date('Y') ?> Afrikins Documentary Collective. All rights reserved.
  </div>

</body>
</html>
