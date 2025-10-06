<?php
session_start();
include "db.php";

// Get filters
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$category = isset($_GET['category']) ? intval($_GET['category']) : 0;
$location = isset($_GET['location']) ? trim($_GET['location']) : '';
$salary = isset($_GET['salary']) ? intval($_GET['salary']) : 0;

// Build query
$sql = "SELECT jobs.id, jobs.title, jobs.description, jobs.location, jobs.salary, employers.company_name, categories.name AS category_name 
        FROM jobs 
        LEFT JOIN employers ON jobs.employer_id = employers.id
        LEFT JOIN categories ON jobs.category_id = categories.id
        WHERE 1 ";

if ($q !== '') {
    $q = $conn->real_escape_string($q);
    $sql .= " AND (jobs.title LIKE '%$q%' OR jobs.description LIKE '%$q%' OR employers.company_name LIKE '%$q%') ";
}
if ($category > 0) {
    $sql .= " AND jobs.category_id = $category ";
}
if ($location !== '') {
    $location = $conn->real_escape_string($location);
    $sql .= " AND jobs.location LIKE '%$location%' ";
}
if ($salary > 0) {
    $sql .= " AND jobs.salary >= $salary ";
}

$sql .= " ORDER BY jobs.created_at DESC";

$result = $conn->query($sql);

// Fetch categories for filter dropdown
$catResult = $conn->query("SELECT * FROM categories");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Jobs - Rozee.pk Clone</title>
    <style>
        body { margin:0; font-family: Arial, sans-serif; background:#f8f9fb; color:#333; }
        header { background:#007bff; padding:20px; color:white; text-align:center; }
        header h1 { margin:0; }
        .container { max-width:1200px; margin:30px auto; padding:0 20px; }
        .search-filters { background:white; padding:20px; border-radius:10px; box-shadow:0 4px 8px rgba(0,0,0,0.08); margin-bottom:30px; }
        .search-filters form { display:grid; grid-template-columns: repeat(auto-fit, minmax(200px,1fr)); gap:15px; }
        .search-filters input, .search-filters select, .search-filters button {
            padding:12px; border:1px solid #ccc; border-radius:6px; font-size:14px;
        }
        .search-filters button { background:#28a745; color:white; border:none; cursor:pointer; font-weight:bold; }
        .search-filters button:hover { background:#218838; }
        .jobs { display:grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap:20px; }
        .job { background:white; padding:20px; border-radius:10px; box-shadow:0 3px 8px rgba(0,0,0,0.1); transition:0.2s; }
        .job:hover { transform:scale(1.02); }
        .job h3 { margin-top:0; color:#0056b3; }
        .apply-btn { display:inline-block; margin-top:10px; padding:8px 15px; background:#28a745; color:white; border-radius:5px; text-decoration:none; }
        .apply-btn:hover { background:#218838; }
        footer { text-align:center; margin-top:40px; padding:20px; background:#007bff; color:white; }
    </style>
</head>
<body>

<header>
    <h1>Search Jobs</h1>
</header>

<div class="container">
    <div class="search-filters">
        <form method="get" action="search.php">
            <input type="text" name="q" placeholder="Search by keyword..." value="<?= htmlspecialchars($q) ?>">
            
            <select name="category">
                <option value="">All Categories</option>
                <?php while ($cat = $catResult->fetch_assoc()): ?>
                    <option value="<?= $cat['id'] ?>" <?= $category == $cat['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['name']) ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <input type="text" name="location" placeholder="Location" value="<?= htmlspecialchars($location) ?>">

            <select name="salary">
                <option value="0">Any Salary</option>
                <option value="20000" <?= $salary == 20000 ? 'selected' : '' ?>>20,000+</option>
                <option value="50000" <?= $salary == 50000 ? 'selected' : '' ?>>50,000+</option>
                <option value="100000" <?= $salary == 100000 ? 'selected' : '' ?>>100,000+</option>
            </select>

            <button type="submit">Search</button>
        </form>
    </div>

    <div class="jobs">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($job = $result->fetch_assoc()): ?>
                <div class="job">
                    <h3><?= htmlspecialchars($job['title']) ?></h3>
                    <p><strong>Company:</strong> <?= htmlspecialchars($job['company_name']) ?></p>
                    <p><strong>Category:</strong> <?= htmlspecialchars($job['category_name']) ?></p>
                    <p><strong>Location:</strong> <?= htmlspecialchars($job['location']) ?></p>
                    <p><strong>Salary:</strong> <?= htmlspecialchars($job['salary']) ?></p>
                    <p><?= substr(htmlspecialchars($job['description']), 0, 120) ?>...</p>
                    <a class="apply-btn" href="apply.php?job_id=<?= $job['id'] ?>">Apply Now</a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No jobs found matching your criteria.</p>
        <?php endif; ?>
    </div>
</div>

<footer>
    <p>&copy; <?= date("Y") ?> Rozee.pk Clone | Advanced Search</p>
</footer>

</body>
</html>
