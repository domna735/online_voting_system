<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Poll</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Create Poll</h1>
        <?php include('nav.php'); display_nav(3); ?>
    </header>
    <main>
        <form action="create_poll_process.php" method="POST">
            <label for="question">Poll Question:</label><br>
            <textarea id="question" name="question" required></textarea><br><br>
            <label for="content">Content:</label><br>
            <textarea id="content" name="content" required></textarea><br><br>
            <label for="options">Options (one per line):</label><br>
            <textarea id="options" name="options" required></textarea><br><br>
            <button type="submit">Create Poll</button>
        </form>
    </main>
    <?php include('footer.php'); ?>
</body>
</html>



