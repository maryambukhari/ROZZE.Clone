<?php
session_start();
include "db.php";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'seeker') {
    echo "<h2 style='color:red;text-align:center;'>⚠️ You must be logged in as a job seeker to apply!</h2>";
    exit();
}

if (!isset($_GET['job_id']) || empty($_GET['job_id'])) {
    echo "<h2 style='color:red;text-align:center;'>❌ Invalid Job ID</h2>";
    exit();
}

$job_id = intval($_GET['job_id']);
$user_id = intval($_SESSION['user_id']); 

// ✅ Step 1: Check if seeker profile exists
$stmt = $conn->prepare("SELECT id FROM job_seekers WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($seeker_id);

if (!$stmt->fetch()) {
    $stmt->close();

    // ❌ No profile found → create one automatically
    // ⚡ Change "name" to "full_name" if that's in your `users` table
    $user_stmt = $conn->prepare("SELECT name, email FROM users WHERE id=?");
    $user_stmt->bind_param("i", $user_id);
    $user_stmt->execute();
    $user_stmt->bind_result($name, $email);
    $user_stmt->fetch();
    $user_stmt->close();

    // ✅ Adjust columns according to your `job_seekers` table
    $insert_stmt = $conn->prepare("INSERT INTO job_seekers (user_id, full_name, email) VALUES (?, ?, ?)");
    $insert_stmt->bind_param("iss", $user_id, $name, $email);
    $insert_stmt->execute();
    $seeker_id = $insert_stmt->insert_id;
    $insert_stmt->close();
} else {
    $stmt->close();
}

// ✅ Step 2: Check if already applied
$stmt = $conn->prepare("SELECT id FROM applications WHERE job_id=? AND seeker_id=?");
$stmt->bind_param("ii", $job_id, $seeker_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo "<h2 style='color:orange;text-align:center;'>⚠️ You have already applied for this job.</h2>";
    exit();
}
$stmt->close();

// ✅ Step 3: Insert new application
$stmt = $conn->prepare("INSERT INTO applications (job_id, seeker_id, status, applied_at) VALUES (?, ?, 'Pending', NOW())");
$stmt->bind_param("ii", $job_id, $seeker_id);

if ($stmt->execute()) {
    echo "<h2 style='color:green;text-align:center;'>✅ Application submitted successfully!</h2>";
    echo "<p style='text-align:center;'><a href='profile.php'>Go to My Profile</a></p>";
} else {
    echo "<h2 style='color:red;text-align:center;'>❌ Database Error: " . $conn->error . "</h2>";
}
$stmt->close();
?>
