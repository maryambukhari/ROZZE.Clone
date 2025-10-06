<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'seeker') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch seeker details
$sql = "SELECT * FROM job_seekers WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$seeker = $result->fetch_assoc();

// Update Profile
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];

    $update = "UPDATE job_seekers SET full_name=?, email=? WHERE id=?";
    $stmt = $conn->prepare($update);
    $stmt->bind_param("ssi", $full_name, $email, $user_id);
    $stmt->execute();

    $_SESSION['message'] = "Profile updated successfully!";
    header("Location: profile.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Profile</title>
    <style>
        body { font-family: Arial, sans-serif; background:#f5f6f9; }
        .container { max-width:700px; margin:50px auto; background:white; padding:30px; border-radius:10px; box-shadow:0 4px 10px rgba(0,0,0,0.1); }
        h2 { color:#007bff; }
        label { display:block; margin:10px 0 5px; }
        input { width:100%; padding:10px; margin-bottom:15px; border:1px solid #ccc; border-radius:6px; }
        button { padding:10px 20px; background:#28a745; color:white; border:none; border-radius:6px; cursor:pointer; }
        button:hover { background:#218838; }
        .apps { margin-top:30px; }
        .job { background:#f9f9f9; padding:15px; border-radius:8px; margin-bottom:10px; }
    </style>
</head>
<body>
<div class="container">
    <h2>Welcome, <?= htmlspecialchars($seeker['full_name']) ?></h2>
    <p><strong>Email:</strong> <?= htmlspecialchars($seeker['email']) ?></p>
    <p><strong>Role:</strong> seeker</p>

    <h3>Update Profile</h3>
    <form method="post">
        <label>Full Name</label>
        <input type="text" name="full_name" value="<?= htmlspecialchars($seeker['full_name']) ?>" required>
        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($seeker['email']) ?>" required>
        <button type="submit">Update Profile</button>
    </form>

    <div class="apps">
        <h3>My Applications</h3>
        <?php
        $appSql = "SELECT applications.*, jobs.title FROM applications 
                   LEFT JOIN jobs ON applications.job_id = jobs.id 
                   WHERE seeker_id=?";
        $stmt = $conn->prepare($appSql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $apps = $stmt->get_result();

        if ($apps->num_rows > 0) {
            while ($app = $apps->fetch_assoc()) {
                echo "<div class='job'><strong>{$app['title']}</strong> - Status: {$app['status']}</div>";
            }
        } else {
            echo "<p>No applications yet.</p>";
        }
        ?>
    </div>
</div>
</body>
</html>
