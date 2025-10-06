<?php
session_start();
include "db.php";

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$job_id = intval($_GET['id']);
$sql = "SELECT jobs.*, employers.company_name 
        FROM jobs 
        LEFT JOIN employers ON jobs.employer_id = employers.id 
        WHERE jobs.id = $job_id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    echo "<h2>Job not found!</h2>";
    exit();
}

$job = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($job['title']) ?> - Job Details</title>
    <style>
        body { font-family: Arial; background:#f5f6f9; padding:20px; }
        .job-box { background:white; padding:20px; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,0.1); max-width:700px; margin:auto; }
        h1 { color:#007bff; }
        .apply-btn { display:inline-block; margin-top:20px; padding:10px 20px; background:#28a745; color:white; text-decoration:none; border-radius:5px; }
        .apply-btn:hover { background:#218838; }
    </style>
</head>
<body>

<div class="job-box">
    <h1><?= htmlspecialchars($job['title']) ?></h1>
    <p><strong>Company:</strong> <?= htmlspecialchars($job['company_name']) ?></p>
    <p><strong>Location:</strong> <?= htmlspecialchars($job['location']) ?></p>
    <p><strong>Salary:</strong> <?= htmlspecialchars($job['salary']) ?></p>
    <p><strong>Description:</strong><br> <?= nl2br(htmlspecialchars($job['description'])) ?></p>

    <a class="apply-btn" href="apply.php?job_id=<?= $job['id'] ?>">Apply Now</a>
</div>

</body>
</html>
