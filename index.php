<?php
session_start();
include "db.php";

// Fetch categories
$catSql = "SELECT * FROM categories LIMIT 8";
$catResult = $conn->query($catSql);

// Fetch featured jobs
$jobSql = "SELECT jobs.id, jobs.title, jobs.description, jobs.location, jobs.salary, employers.company_name 
           FROM jobs 
           LEFT JOIN employers ON jobs.employer_id = employers.id 
           ORDER BY jobs.created_at DESC LIMIT 6";
$jobResult = $conn->query($jobSql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rozee.pk Clone - Job Portal</title>
    <style>
        body { margin:0; font-family: Arial, sans-serif; background:#f5f6f9; color:#333; }
        header { background:#007bff; color:white; padding:15px 40px; display:flex; justify-content:space-between; align-items:center; }
        header h1 { margin:0; font-size:24px; }
        nav a { color:white; margin-left:20px; text-decoration:none; font-weight:bold; }
        nav a:hover { text-decoration:underline; }
        .hero { background:#0056b3; padding:60px 20px; text-align:center; color:white; }
        .hero h2 { font-size:36px; margin-bottom:20px; }
        .search-bar { max-width:600px; margin:0 auto; display:flex; }
        .search-bar input { flex:1; padding:15px; border:none; border-radius:6px 0 0 6px; }
        .search-bar button { padding:15px 25px; border:none; background:#28a745; color:white; font-weight:bold; border-radius:0 6px 6px 0; cursor:pointer; }
        .section { max-width:1200px; margin:40px auto; padding:0 20px; }
        .section h2 { margin-bottom:20px; color:#007bff; }
        .categories { display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:20px; }
        .category { background:white; padding:20px; border-radius:10px; box-shadow:0 4px 10px rgba(0,0,0,0.08); text-align:center; transition:transform 0.2s; }
        .category:hover { transform:translateY(-5px); }
        .jobs { display:grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap:20px; }
        .job { background:white; padding:20px; border-radius:10px; box-shadow:0 3px 8px rgba(0,0,0,0.1); transition:0.2s; }
        .job:hover { transform:scale(1.02); }
        .job h3 { margin-top:0; color:#0056b3; }
        .job p { margin:5px 0; }
        .apply-btn { display:inline-block; margin-top:10px; padding:8px 15px; background:#28a745; color:white; border-radius:5px; text-decoration:none; }
        .apply-btn:hover { background:#218838; }
        footer { text-align:center; padding:20px; margin-top:50px; background:#007bff; color:white; }
    </style>
</head>
<body>

<header>
    <h1>Rozee.pk Clone</h1>
    <nav>
        <a href="index.php">Home</a>
        <a href="post_job.php">Post a Job</a>
        <a href="search.php">Search Jobs</a>
        <a href="profile.php">My Profile</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<div class="hero">
    <h2>Find Your Dream Job Today</h2>
    <form class="search-bar" action="search.php" method="get">
        <input type="text" name="q" placeholder="Search jobs by title, skill, or company..." required>
        <button type="submit">Search</button>
    </form>
</div>

<div class="section">
    <h2>Job Categories</h2>
    <div class="categories">
        <?php if ($catResult && $catResult->num_rows > 0): ?>
            <?php while ($cat = $catResult->fetch_assoc()): ?>
                <div class="category">
                    <h3><?= htmlspecialchars($cat['name']) ?></h3>
                    <a href="search.php?category=<?= $cat['id'] ?>">Browse Jobs</a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No categories found. Please add some categories.</p>
        <?php endif; ?>
    </div>
</div>

<div class="section">
    <h2>Featured Jobs</h2>
    <div class="jobs">
        <?php if ($jobResult && $jobResult->num_rows > 0): ?>
            <?php while ($job = $jobResult->fetch_assoc()): ?>
                <div class="job">
                    <h3><?= htmlspecialchars($job['title']) ?></h3>
                    <p><strong>Company:</strong> <?= htmlspecialchars($job['company_name']) ?></p>
                    <p><strong>Location:</strong> <?= htmlspecialchars($job['location']) ?></p>
                    <p><strong>Salary:</strong> <?= htmlspecialchars($job['salary']) ?></p>
                    <p><?= substr(htmlspecialchars($job['description']), 0, 120) ?>...</p>
                    <a class="apply-btn" href="apply.php?job_id=<?= $job['id'] ?>">Apply Now</a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No jobs available at the moment. Please check back later!</p>
        <?php endif; ?>
    </div>
</div>

<footer>
    <p>&copy; <?= date("Y") ?> Rozee.pk Clone | Built for Learning</p>
</footer>

</body>
</html>
