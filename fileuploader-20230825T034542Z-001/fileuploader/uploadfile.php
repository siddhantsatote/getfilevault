<?php
include 'config.php';

if (isset($_FILES['uploaded_file'])) {
    $file = $_FILES['uploaded_file'];

    if ($file['error'] === UPLOAD_ERR_OK) {
        $fileName = basename($file['name']);
        $fileType = $file['type'];
        $fileSize = $file['size'];
        $filePath = 'uploads/' . $fileName;

        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            // Insert file details into the database
            $stmt = $conn->prepare("INSERT INTO files (file_name, file_type, file_size, file_path) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssis", $fileName, $fileType, $fileSize, $filePath);

            if ($stmt->execute()) {
                echo "File uploaded successfully.";
            } else {
                echo "Error during execution: " . $stmt->error;
            }

            $stmt->close();
        } else {
            echo "Error moving uploaded file.";
        }
    } else {
        echo "Error during file upload: " . $file['error'];
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>File Uploader and Downloader</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <h2>File Uploader</h2>
    <form action="uploadfile.php" method="post" enctype="multipart/form-data">
        <label for="uploaded_file">Select a file to upload:</label>
        <input type="file" name="uploaded_file" id="uploaded_file">
        <input type="submit" value="Upload">
    </form>

    <h2>File Downloader</h2>
    <form action="download.php" method="get">
        <label for="file_id">Select a file to download:</label>
        <select name="file_id">
            <?php
            include 'config.php';

            // Retrieve file IDs and names from the database
            $sql = "SELECT file_id, file_name FROM files";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $file_id = $row['file_id'];
                    $file_name = $row['file_name'];
                    echo "<option value='$file_id'>$file_name</option>";
                }
            } else {
                echo "<option value=''>No files available</option>";
            }

            $conn->close();
            ?>
        </select>
        <input type="submit" value="Download">
    </form>
</body>
</html>