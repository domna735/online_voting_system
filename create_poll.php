<?php
session_start();
// Generate a CSRF token for the poll form if not already set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Poll</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .option-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 10px;
        }
        .option-wrapper textarea {
            margin-right: 10px;
        }
    </style>
    <script>
        function additionalOptions() {
            const optionsContainer = document.getElementById('options-container');
            // Limit maximum number of options to 10
            const currentOptions = optionsContainer.getElementsByClassName('option-wrapper').length;
            if(currentOptions >= 10) {
                alert("Maximum of 10 options allowed.");
                return;
            }
            const optionWrapper = document.createElement('div');
            optionWrapper.className = 'option-wrapper';

            const newOption = document.createElement('textarea');
            newOption.name = 'options[]';
            newOption.required = true;
            newOption.maxLength = 100; // Restrict maximum length for each option

            const deleteButton = document.createElement('button');
            deleteButton.type = 'button';
            deleteButton.innerText = 'Delete';
            deleteButton.onclick = function() {
                optionsContainer.removeChild(optionWrapper);
            };

            optionWrapper.appendChild(newOption);
            optionWrapper.appendChild(deleteButton);
            optionsContainer.appendChild(optionWrapper);
        }
    </script>
</head>
<body>
    <header>
        <h1>Create Poll</h1>
        <?php include('nav.php'); display_nav(3); ?>
    </header>
    <main>
        <form action="create_poll_process.php" method="POST">
            <!-- Include CSRF token for protection -->
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            
            <label for="question">Poll Question:</label><br>
            <textarea id="question" name="question" required maxlength="250"></textarea><br><br>
            
            <label for="content">Content:</label><br>
            <textarea id="content" name="content" required maxlength="500"></textarea><br><br>
            
            <label for="options">Options:</label><br>
            <div id="options-container">
                <div class="option-wrapper">
                    <textarea name="options[]" required maxlength="100"></textarea>
                </div>
                <br>
                <div class="option-wrapper">
                    <textarea name="options[]" required maxlength="100"></textarea>
                </div>
                <br>
            </div>
            <button type="button" onclick="additionalOptions()">Add Option</button><br><br>
            <button type="submit">Create Poll</button>
        </form>
    </main>
    <?php include('footer.php'); ?>
</body>
</html>