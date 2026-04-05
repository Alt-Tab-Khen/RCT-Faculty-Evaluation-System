<?php
include '../db_connect.php';

if (isset($_GET['prof_id'])) {
    $prof_id = $_GET['prof_id'];

    $query = "SELECT prof_id, first_name, last_name, username, email, faculty FROM instructor WHERE prof_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $prof_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(['error' => 'Instructor not found']);
    }
}
?>
