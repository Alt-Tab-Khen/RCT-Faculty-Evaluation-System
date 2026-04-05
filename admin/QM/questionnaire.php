<?php
include '../../db_connect.php';

$response = ['success' => false]; // Initialize the response array

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle adding a new question
    if (isset($_POST['question_text'])) {
        $question_text = mysqli_real_escape_string($conn, $_POST['question_text']);
        $category = mysqli_real_escape_string($conn, $_POST['category']);
        $status = 'active';

        // Insert the new question
        $query = "INSERT INTO questionnaires (question, category, status) VALUES ('$question_text', '$category', '$status')";

        if (mysqli_query($conn, $query)) {
            $response['success'] = true;
            $response['message'] = 'Question added successfully';
        } else {
            $response['message'] = 'Error adding question';
        }
    }

    // Handle updating a question
    if (isset($_POST['update_id']) && isset($_POST['updated_text'])) {
        $edit_id = mysqli_real_escape_string($conn, $_POST['update_id']);
        $edit_text = mysqli_real_escape_string($conn, $_POST['updated_text']);

        // Update the question
        $update_query = "UPDATE questionnaires SET question = '$edit_text' WHERE id = $edit_id";
        
        if (mysqli_query($conn, $update_query)) {
            $response['success'] = true;
            $response['message'] = 'Question updated successfully';
        } else {
            $response['message'] = 'Error updating question';
        }
    }

    // Handle updating a category
    if (isset($_POST['update_category_id']) && isset($_POST['updated_category_name'])) {
        $category_id = mysqli_real_escape_string($conn, $_POST['update_category_id']);
        $category_name = mysqli_real_escape_string($conn, $_POST['updated_category_name']);

        // Update the category
        $update_category_query = "UPDATE questionnaires SET category = '$category_name' WHERE category = '$category_id'"; 

        if (mysqli_query($conn, $update_category_query)) {
            $response['success'] = true;
            $response['message'] = 'Category updated successfully';
        } else {
            $response['message'] = 'Error updating category';
        }
    }

    // Handle deleting a question
    if (isset($_POST['delete_id'])) {
        $delete_id = mysqli_real_escape_string($conn, $_POST['delete_id']);

        // Delete the question
        $delete_query = "DELETE FROM questionnaires WHERE id = $delete_id";

        if (mysqli_query($conn, $delete_query)) {
            $response['success'] = true;
            $response['message'] = 'Question deleted successfully';
        } else {
            $response['message'] = 'Error deleting question';
        }
    }

    // Handle deleting a category
    if (isset($_POST['delete_category_id'])) {
        $delete_category_id = mysqli_real_escape_string($conn, $_POST['delete_category_id']);

        // Delete all questions under the category
        $delete_questions_query = "DELETE FROM questionnaires WHERE category = '$delete_category_id'";

        if (mysqli_query($conn, $delete_questions_query)) {
            $response['success'] = true;
            $response['message'] = 'Category and all its questions deleted successfully';
        } else {
            $response['message'] = 'Error deleting category';
        }
    }
}

echo json_encode($response); // Return the response as JSON
?>
