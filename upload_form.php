<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure File Upload</title>
</head>
<body>
    <h2>Secure File Upload</h2>
    
    <form action="upload.php" method="post" enctype="multipart/form-data">
        <label for="file">Select File:</label>
        <input type="file" name="file" id="file" accept=".jpg, .png, .pdf, .docx" required>
        <br>
        <input type="submit" value="Upload">
    </form>
</body>
</html>
