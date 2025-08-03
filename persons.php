<?php
require 'config/dbConnect.php'; // you can remove if unused; below we open SQLite directly
require 'includes/header.php';
require 'includes/nav.php';
require 'includes/fnc.php';
checkUserLoggedIn();

// open SQLite
$dbPath = 'E:/classdb.db'; // adjust if needed
$db = new SQLite3($dbPath);
$db->busyTimeout(5000);
$db->exec('PRAGMA journal_mode = WAL;');
$db->exec('PRAGMA foreign_keys = ON;');

?>
<div class="row">
    <div class="content">
        <h2>Welcome to users page</h2>

        <table>
            <caption>All Users</caption>
            <thead>
                <tr>
                    <th>SN</th>
                    <th>Fullname</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Gender</th>
                    <th>Role</th>
                    <th>Date Updated</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
<?php
$query = <<<SQL
SELECT u.userId, u.fullname, u.email, u.phone, g.gender, r.role, u.userUpdated
FROM users u
LEFT JOIN roles r ON u.roleId = r.roleId
LEFT JOIN gender g ON u.genderId = g.genderId
WHERE u.status = 0
ORDER BY u.fullname ASC
SQL;

$result = $db->query($query);
$sn = 0;
$hasAny = false;
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $hasAny = true;
    $sn++;
    $uid = htmlspecialchars($row['userId']);
    $fullname = htmlspecialchars($row['fullname']);
    $email = htmlspecialchars($row['email']);
    $phone = htmlspecialchars($row['phone']);
    $gender = htmlspecialchars($row['gender'] ?? '—');
    $role = htmlspecialchars($row['role'] ?? '—');
    $updated = htmlspecialchars($row['userUpdated']);
    ?>
    <tr>
        <td><?php echo $sn; ?></td>
        <td><?php echo $fullname; ?></td>
        <td><?php echo $email; ?></td>
        <td><?php echo $phone; ?></td>
        <td><?php echo $gender; ?></td>
        <td><?php echo $role; ?></td>
        <td><?php echo $updated; ?></td>
        <td>
            [ <a href="edit_user.php?id=<?php echo $uid; ?>">Edit</a> ] |
            [ <a href="proc/processes.php?delete_user=<?php echo $uid; ?>" onclick="return confirm('Are you sure you want to delete <?php echo addslashes($fullname); ?>?');">Del</a> ]
        </td>
    </tr>
    <?php
}
if (!$hasAny) {
    echo '<tr><td colspan="8">No users found.</td></tr>';
}
?>
            </tbody>
        </table>

        <h2>Learn More About Our Team and Mission</h2>
        <p>sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?</p>
    </div>
    <div class="sidebar">
        <h2>Side Bar</h2>
        <p>We are a team of dedicated professionals committed to delivering high-quality services and products.</p>
        <p>This is the about page. It contains information about the website, its purpose, and the team behind it. 
        You can find details on our mission, vision, and values here. We aim to provide a comprehensive overview of 
        our services and how we can help you achieve your goals.</p>
    </div>
</div>
<?php
require 'includes/footer.php';
?>
