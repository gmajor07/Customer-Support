<?php
require 'header.php';

require 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $message = trim($_POST['message']);

    if (empty($title) || empty($message)) {
        $error = "Both fields are required.";
    }

    $stmt = $conn->prepare("INSERT INTO templates (title, message) VALUES (?, ?)");
    $stmt->bind_param("ss", $title, $message);

    if ($stmt->execute()) {
        $success  = "Template saved successfully!";
    } else {
        $error = "Error: Unable to save the template.";
    }

    $stmt->close();
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add new template </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link href="cardstyle.css" rel="stylesheet">

</head>
<body>
   

<div class="container">
            <h1 class="text-center mb-4">Add Message Template</h1>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
            <label for="title" class="form-label" >Template Title:</label>
            <input type="text" name="title" class="form-control" id="title" placeholder="Enter your title" required>
            </div>
            <div class="mb-3">
                <label for="message" class="form-label" >Message:</label><br>

                <textarea id="message" name="message"  class="form-control" rows="4" cols="50" placeholder="Enter your message" required></textarea><br><br>

            </div>
           
            <button type="submit" class="btn btn-custom w-100">Save Template</button>
        </form>
      
    </div>

</body>
</html>

<?php
require 'footer.php';
?> 
