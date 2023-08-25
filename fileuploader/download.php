<?php
include 'config.php';

if (isset($_GET['file_id'])) {
    $file_id = $_GET['file_id'];

    $sql = "SELECT file_path, file_name, file_type, file_size FROM files WHERE file_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $file_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $file_path = $row['file_path'];
        $file_name = $row['file_name'];
        $file_type = $row['file_type'];
        $file_size = $row['file_size'];

        if (file_exists($file_path)) {
            header("Content-type: $file_type");
            header("Content-length: $file_size");
            header("Content-Disposition: attachment; filename=$file_name");

            // Use readfile to output the file
            readfile($file_path);
            exit;
        } else {
            echo "File not found on the server.";
        }
    } else {
        echo "File not found in the database.";
    }

    $stmt->close();
}

$conn->close();
?>