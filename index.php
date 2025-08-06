<?php
// File to handle displaying and submitting Vibe code projects

$projectsFile = 'projects.json';
$projects = [];
$message = '';
$error = '';

// Load existing projects
if (file_exists($projectsFile)) {
    $projectsJson = file_get_contents($projectsFile);
    $projects = json_decode($projectsJson, true) ?: [];
}

// Handle new project submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $code = trim($_POST['code'] ?? '');

    if ($name === '' || $description === '' || $code === '') {
        $error = 'All fields are required.';
    } else {
        // Sanitize inputs
        $project = [
            'name' => htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
            'description' => htmlspecialchars($description, ENT_QUOTES, 'UTF-8'),
            'code' => htmlspecialchars($code, ENT_QUOTES, 'UTF-8'),
            'submitted_at' => date('Y-m-d H:i:s')
        ];
        $projects[] = $project;

        // Save to file
        $saved = file_put_contents($projectsFile, json_encode($projects, JSON_PRETTY_PRINT));

        if ($saved === false) {
            $error = 'Failed to save project. Please try again later.';
        } else {
            $message = 'Project submitted successfully!';
            // reload updated projects
            $projects = json_decode(file_get_contents($projectsFile), true);
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Vibe Code Sharing Community</title>
<link rel="stylesheet" href="style.css" />
</head>
<body>
<header>
  <h1>Vibe Code Sharing Community</h1>
  <p>Share your Vibe code projects with the community!</p>
</header>

<main class="container">

  <?php if ($error): ?>
    <div class="error"><?php echo $error; ?></div>
  <?php endif; ?>

  <?php if ($message): ?>
    <div class="success"><?php echo $message; ?></div>
  <?php endif; ?>

  <section>
    <h2>Submit a new project</h2>
    <form method="POST" action="">
      <input type="text" name="name" placeholder="Project Name" required />
      <input type="text" name="description" placeholder="Short Description" required />
      <textarea name="code" placeholder="Your Vibe code here..." rows="6" required></textarea>
      <button type="submit">Submit Project</button>
    </form>
  </section>

  <section>
    <h2>Shared Projects</h2>
    <div class="project-list">
      <?php foreach ($projects as $project): ?>
        <div class="project-card">
          <h3><?php echo $project['name']; ?></h3>
          <p><em><?php echo $project['description']; ?></em></p>
          <pre><?php echo $project['code']; ?></pre>
          <small>Submitted at: <?php echo $project['submitted_at']; ?></small>
        </div>
      <?php endforeach; ?>
      <?php if (empty($projects)): ?>
        <p>No projects shared yet. Be the first!</p>
      <?php endif; ?>
    </div>
  </section>

</main>

</body>
</html>