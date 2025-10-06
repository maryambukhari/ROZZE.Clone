<?php
include "db.php";
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'employer'){
    echo "<script>alert('Only employers can post jobs!'); window.location='index.php';</script>";
    exit;
}

if(isset($_POST['post'])){
    $title = $_POST['title'];
    $description = $_POST['description'];
    $requirements = $_POST['requirements'];
    $salary = $_POST['salary'];
    $location = $_POST['location'];
    $job_type = $_POST['job_type'];

    // get employer's company
    $check = $conn->query("SELECT * FROM companies WHERE user_id=".$_SESSION['user_id']);
    if($check->num_rows == 0){
        echo "<script>alert('Please create your company profile first!'); window.location='profile.php';</script>";
        exit;
    }
    $company = $check->fetch_assoc();
    $company_id = $company['id'];

    $stmt = $conn->prepare("INSERT INTO jobs (company_id,title,description,requirements,salary,location,job_type) VALUES (?,?,?,?,?,?,?)");
    $stmt->bind_param("issssss",$company_id,$title,$description,$requirements,$salary,$location,$job_type);
    if($stmt->execute()){
        echo "<script>alert('Job posted successfully!'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Error posting job');</script>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Post Job</title>
<style>
body{font-family:Arial;background:#f0f2f5;padding:40px;}
form{background:#fff;padding:20px;border-radius:10px;max-width:500px;margin:auto;box-shadow:0 4px 8px rgba(0,0,0,0.1);}
input,textarea,select{width:100%;padding:10px;margin:10px 0;border:1px solid #ddd;border-radius:5px;}
button{background:#0069d9;color:#fff;padding:10px;width:100%;border:none;border-radius:5px;cursor:pointer;}
button:hover{background:#004fa1;}
</style>
</head>
<body>
<h2>Post a Job</h2>
<form method="post">
  <input type="text" name="title" placeholder="Job Title" required>
  <textarea name="description" placeholder="Job Description" required></textarea>
  <textarea name="requirements" placeholder="Requirements"></textarea>
  <input type="text" name="salary" placeholder="Salary">
  <input type="text" name="location" placeholder="Location">
  <select name="job_type">
    <option>Full-Time</option>
    <option>Part-Time</option>
    <option>Remote</option>
    <option>Internship</option>
  </select>
  <button type="submit" name="post">Post Job</button>
</form>
</body>
</html>
