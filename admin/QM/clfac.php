<?php

session_start();  // Start the session at the beginning of the file
include '../../db_connect.php';

// Assuming the admin's ID is stored in the session as 'id'
if (!isset($_SESSION['id'])) {
    die("Error: admin not logged in.");
}

// Fetch the list of questionnaires from the database
$query = "SELECT * FROM questionnaires";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Questionnaire Management</title>
    <link rel="stylesheet" href="clfac.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <style>
        .list-group-item {
            text-align: left; /* Align questions to the left */
        }
        .edit-mode {
            border: 1px solid #ccc;
        }
        .category-title {
    color: white; /* Always apply white color */
    
}

/* Pull-tab button styling */
.menu-button {
    position: fixed;
    top: 50%;
    left: 0;
    transform: translateY(-50%); /* Lock vertical position at center */
    background-color: blue;
    color: white;
    padding: 10px 20px;
    font-weight: bold;
    cursor: pointer;
    z-index: 1050;
    border-radius: 0px 10px 10px 0px; /* Optional: rounded corners on the right */
    writing-mode: vertical-rl; /* Makes the text vertical */
    text-orientation: upright; /* Keeps text orientation upright */
    font-size: 20px;
    height: 150px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: left 0.28s ease-in-out; /* Only apply horizontal transition */
}

/* Offcanvas adjustments */
.offcanvas-start {
    width: 400px;
    transition: transform 0.3s ease-in-out;
}

/* When the drawer is open, move the button horizontally */
.drawer-open .menu-button {
    left: 450px; /* Move button horizontally without affecting vertical alignment */
}


    .category-container {
        display: flex;
        overflow-x: auto;
        white-space: nowrap;
        margin-top: 20px; /* Add spacing between the header and categories */
    }

    .category-box {
        flex: 0 0 300px;
        margin-right: 20px;
        background-color: #fff; /* Ensure consistent background color */
        border: 1px solid #ddd; /* Add border for better visibility */
        border-radius: 5px;
        padding: 10px; /* Padding for content inside */
    }

    .category-title {
        margin-bottom: 10px; /* Space below the title */
        text-align: left; /* Align title to the left */
    }

    .list-group-item {
        text-align: left;
    }

    .d-flex.justify-content-between.align-items-center {
        margin-bottom: 10px; /* Space between header and buttons */
    }

    /* Ensure buttons are aligned properly */
    .btn {
        margin-right: 5px;
    }


    </style>
</head>

<body>
  <nav class="navbar navbar-expand-lg" style="background: linear-gradient(rgba(9, 165, 255, 0.80), rgba(0, 225, 255, 0.75));">
    <div class="container-fluid">        
      <div class="row">
            <div class="col-12">
                <button class="btn" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasScrolling" aria-controls="offcanvasScrolling">
                  <img src="../../photo/images.jpg" alt="Menu" class="img-fluid"><span class="rct">Questionnaire Management</span>
                </button>
            </div>
        </div>
    </div>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNavDropdown">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="#" id="n2" >Home</a>
          </li>
          <li class="nav-item dropdown">
            <a class="fa-solid fa-bars" id="hamburger-icon" role="button" data-bs-toggle="dropdown" aria-expanded="false"></a>    
            <ul class="dropdown-menu dropdown-menu-lg-end">
            <li><a class="dropdown-item" href="../../logout_admin.php">Log Out</a></li>
              </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Menu "Pull-Tab" Button -->
<div class="menu-button" data-bs-toggle="offcanvas" data-bs-target="#adminOffcanvas" aria-controls="adminOffcanvas">
  <span>Menu</span>
</div>

<!-- Offcanvas Drawer for Admin -->
<div class="offcanvas offcanvas-start" data-bs-scroll="true" data-bs-backdrop="false" tabindex="-1" id="adminOffcanvas" aria-labelledby="adminOffcanvasLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="adminOffcanvasLabel">RCT Faculty Evaluation System</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <div class="but">
            <ul class="dropdown" id="it">
                <li><a type="button" class="btn" id="cl" href="../dashboard/admin.php">Dashboard</a></li>
                <li><a class="btn" id="cl" href="../classbscs/clfac.php" role="button">Class Management</a></li>
                <li><a class="btn" id="cl" href="../faculty/clfac.php" role="button">Faculty Management</a></li>
                <li><a href="../QM/clfac.php" type="button" class="btn" id="cl">Questionnaire Management</a></li>
                <li><a href="../reports/clfac.php" type="button" class="btn" id="cl">Reports</a></li>
            </ul>
        </div>
    </div>
</div>

  <!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="successModalLabel">Success</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p id="successModalMessage">Question deleted successfully!</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
      </div>
    </div>
  </div>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmationModalLabel">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="confirmationModalMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="confirmDeleteButton" class="btn btn-danger">Confirm</button>
            </div>
        </div>
    </div>
</div>



<!-- Add Question Modal -->
<div class="modal fade" id="createQuestionnaireModal" tabindex="-1" aria-labelledby="createQuestionnaireLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createQuestionnaireLabel">Create New Questionnaire</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="questionnaireForm" action="questionnaire.php" method="post">
                    <div class="mb-3">
                        <label for="category" class="form-label">Category</label>
                        <input type="text" class="form-control" id="category" name="category" required>
                    </div>
                    <div class="mb-3">
                        <label for="question" class="form-label">Question</label>
                        <textarea class="form-control" id="question" name="question_text" required></textarea>
                    </div>

                    <div id="successMessage" class="alert alert-success" style="display:none;"></div> <!-- Success Message -->
                    <button type="submit" class="btn btn-primary">Add Question</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Pending Edits Modal -->
<div class="modal fade" id="pendingEditsModal" tabindex="-1" aria-labelledby="pendingEditsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pendingEditsModalLabel">Pending Questionnaire Edits</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="pendingEditsBody">
                <!-- List of pending edits will be inserted here -->
            </div>
        </div>
    </div>
</div>


<!-- Display Added Questionnaires -->
<div class="container mt-5">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center">
        <h2 style="color: white">Questionnaire List</h2>
        
        <div>
            <!-- Notifications for pending edits -->
            <button class="btn btn-info" id="pendingEditsButton" data-bs-toggle="modal" data-bs-target="#pendingEditsModal">
                Pending Edits (<span id="pendingEditsCount">0</span>)
            </button>

            <!-- Create Questionnaire Button -->
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createQuestionnaireModal">
                Create Questionnaire
            </button>

            <!-- Edit Questionnaire Button -->
            <button class="btn btn-warning" id="toggleEditMode">
                Edit Questionnaire
            </button>

            <!-- Delete Icon Button -->
            <button class="btn btn-danger" id="toggleDeleteMode">
                <i class="fas fa-trash-alt"></i> Delete
            </button>
        </div>
    </div>

<!-- Category and Questionnaire Section -->
<div class="category-container">
    <?php
    if (mysqli_num_rows($result) > 0) {
        $categories = [];

        // Group questions by category
        while ($row = mysqli_fetch_assoc($result)) {
            $categories[$row['category']][] = $row;
        }

        // Loop through categories and display each one with its questions
        foreach ($categories as $category => $questions) {
            echo "<div class='category-box' data-category-id='{$category}'>"; // Added data-category-id to the div

            // Category title section
            echo "<div class='d-flex justify-content-between align-items-center'>";
            echo "<h3 class='category-title' id='categoryTitle_{$category}' style='color: black;'>{$category}</h3>";

            // Add Edit Category button
            echo "<button class='btn btn-warning btn-sm edit-category-btn' data-category-id='{$category}' style='display:none;'>Edit Category</button>";

            echo "</div>";

            // List of questions for this category
            echo "<ul class='list-group mb-4'>";
            foreach ($questions as $row) {
                echo "
                <li class='list-group-item d-flex justify-content-between align-items-center' data-id='{$row['id']}'>
                    <span class='question-text'>{$row['question']}</span>
                    <div>
                        <button class='btn btn-success btn-sm edit-question-btn' style='display:none;'>Edit Question</button>
                        <form action='questionnaire.php' method='POST' class='delete-question-form' style='display:none;'>
                            <input type='hidden' name='delete_id' value='{$row['id']}'>
                            <button type='submit' class='btn btn-danger delete-question-btn btn-sm'>Delete</button>
                        </form>
                    </div>
                </li>";
            }
            echo "</ul>";
            echo "</div>"; // End category box
        }
    } else {
        echo "<li class='list-group-item'>No questionnaires available.</li>";
    }
    ?>
</div>


</div>
</div>
</div>


  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
// Offcanvas Drawer Toggle
const menuButton = document.querySelector('.menu-button');
const offcanvasElement = document.getElementById('adminOffcanvas');

// Add 'drawer-open' class to the body when the drawer opens
offcanvasElement.addEventListener('show.bs.offcanvas', function () {
    document.body.classList.add('drawer-open');
});

// Remove 'drawer-open' class from the body when the drawer closes
offcanvasElement.addEventListener('hide.bs.offcanvas', function () {
    document.body.classList.remove('drawer-open');
});

// Toggle Edit Mode for both categories and questions
const toggleEditModeButton = document.getElementById('toggleEditMode');
const editQuestionButtons = document.querySelectorAll('.edit-question-btn');
const editCategoryButtons = document.querySelectorAll('.edit-category-btn');
let isEditMode = false;

// Toggle the edit mode to show/hide the buttons
toggleEditModeButton.addEventListener('click', function () {
    isEditMode = !isEditMode;
    editQuestionButtons.forEach(button => {
        button.style.display = isEditMode ? 'inline-block' : 'none';
    });
    editCategoryButtons.forEach(button => {
        button.style.display = isEditMode ? 'inline-block' : 'none';
    });
});

// Handle Category Edit functionality
editCategoryButtons.forEach(button => {
    button.addEventListener('click', function () {
        const categoryBox = this.closest('.category-box');
        const categoryTitleElement = categoryBox.querySelector('.category-title');
        const originalCategory = categoryTitleElement.textContent.trim();

        // Create input field to edit the category name
        const inputField = document.createElement('input');
        inputField.type = 'text';
        inputField.value = originalCategory;
        inputField.classList.add('form-control', 'edit-mode');

        // Replace the category title with the input field
        categoryTitleElement.replaceWith(inputField);

        // Change the button text to "Save"
        this.textContent = 'Save';
        this.classList.remove('btn-warning');
        this.classList.add('btn-primary');

        // Save the updated category name when "Save" is clicked
        this.addEventListener('click', function saveCategory() {
            const updatedCategory = inputField.value.trim();

            // AJAX call to update the category in the database
            const categoryId = categoryBox.getAttribute('data-category-id');
            fetch('questionnaire.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `update_category_id=${categoryId}&updated_category_name=${updatedCategory}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const newCategoryTitle = document.createElement('h3');
                    newCategoryTitle.classList.add('category-title');
                    newCategoryTitle.textContent = updatedCategory;

                    // Ensure the color remains white after updating
                    newCategoryTitle.style.color = 'white';
                    inputField.replaceWith(newCategoryTitle);

                    // Change the button back to "Edit Category"
                    this.textContent = 'Edit Category';
                    this.classList.remove('btn-primary');
                    this.classList.add('btn-warning');

                    // Remove the event listener after save
                    this.removeEventListener('click', saveCategory);
                } else {
                    alert('Error updating category.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the category.');
            });
        });
    });
});

// Handle Question Edit functionality
editQuestionButtons.forEach(button => {
    button.addEventListener('click', function () {
        const listItem = this.closest('li');
        const questionTextElement = listItem.querySelector('.question-text');
        const originalQuestion = questionTextElement.textContent.trim();

        // Create input field to edit the question text
        const inputField = document.createElement('input');
        inputField.type = 'text';
        inputField.value = originalQuestion;
        inputField.classList.add('form-control', 'edit-mode');

        // Replace the question text with the input field
        questionTextElement.replaceWith(inputField);

        // Change the button text to "Save"
        this.textContent = 'Save';
        this.classList.remove('btn-success');
        this.classList.add('btn-primary');

        // Save the updated question when "Save" is clicked
        this.addEventListener('click', function saveQuestion() {
            const updatedQuestion = inputField.value.trim();

            // AJAX call to update the question in the database
            const questionId = listItem.getAttribute('data-id');
            fetch('questionnaire.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `update_id=${questionId}&updated_text=${updatedQuestion}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const newQuestionText = document.createElement('span');
                    newQuestionText.classList.add('question-text');
                    newQuestionText.textContent = updatedQuestion;

                    inputField.replaceWith(newQuestionText);
                    this.textContent = 'Edit Question';
                    this.classList.remove('btn-primary');
                    this.classList.add('btn-success');

                    // Remove the event listener after save
                    this.removeEventListener('click', saveQuestion);
                } else {
                    alert('Error updating question.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the question.');
            });
        });
    });
});

// Toggle Delete Mode for questions only
const toggleDeleteModeButton = document.getElementById('toggleDeleteMode');
let isDeleteMode = false;

toggleDeleteModeButton.addEventListener('click', function () {
    isDeleteMode = !isDeleteMode;

    // Select delete buttons within the question elements
    const deleteQuestionButtons = document.querySelectorAll('.delete-question-btn'); // For questions

    // Show or hide delete buttons for questions
    deleteQuestionButtons.forEach(button => {
        button.closest('form').style.display = isDeleteMode ? 'inline' : 'none';
    });

    // Update the Delete mode button's text and styling
    toggleDeleteModeButton.textContent = isDeleteMode ? 'Exit Delete Mode' : 'Delete';
    toggleDeleteModeButton.classList.toggle('btn-danger', isDeleteMode);
    toggleDeleteModeButton.classList.toggle('btn-secondary', !isDeleteMode);
});

// Delete Question functionality
function deleteQuestion(questionId) {
    // Show the confirmation modal
    const confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'));
    document.getElementById('confirmationModalMessage').textContent = "Are you sure you want to delete this question?";
    confirmationModal.show();

    // Handle confirmation button click
    document.getElementById('confirmDeleteButton').addEventListener('click', function confirmDeletion() {
        fetch('questionnaire.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `delete_id=${questionId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove question from the UI
                const questionElement = document.querySelector(`.list-group-item[data-id="${questionId}"]`);
                const categoryBox = questionElement.closest('.category-box'); // Get the parent category box

                if (questionElement) {
                    questionElement.remove();
                }

                // Check if the category is empty
                const remainingQuestions = categoryBox.querySelectorAll('.list-group-item');
                if (remainingQuestions.length === 0) {
                    categoryBox.remove();
                }

                // Show success modal
                const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                document.getElementById('successModalMessage').textContent = data.message;
                successModal.show();

                // Automatically hide the success modal after 3 seconds
                setTimeout(() => {
                    successModal.hide();
                }, 3000);
            } else {
                alert(data.message || 'An error occurred while deleting the question.');
            }
        })
        .catch(error => console.error('Error:', error));

        // Hide the confirmation modal and remove the event listener
        confirmationModal.hide();
        this.removeEventListener('click', confirmDeletion);
    });
}

// Attach event listeners dynamically for delete buttons
document.querySelectorAll('.delete-question-btn').forEach(button => {
    button.addEventListener('click', function (event) {
        event.preventDefault(); // Prevent form submission
        const questionId = this.closest('form').querySelector('input[name="delete_id"]').value;
        deleteQuestion(questionId);
    });
});


document.querySelectorAll('form[action="questionnaire.php"]').forEach(form => {
    form.addEventListener('submit', function (event) {
        event.preventDefault(); // Prevent the default form submission

        const formData = new FormData(this);
        const submitButton = this.querySelector('button[type="submit"]'); // Identify the action button clicked
        const formAction = submitButton ? submitButton.textContent.trim() : ''; // Check the action type (Edit, Delete)

        // Skip "Add Question" and let the specific block handle it
        if (formAction === 'Add Question') {
            return;
        }

        fetch('questionnaire.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Handle Edit or Delete success messages
                if (formAction === 'Edit Question') {
                    alert('Question updated successfully!');
                } else if (formAction === 'Delete') {
                    alert('Question deleted successfully!');
                }

                // Optionally, reload or update the UI dynamically
            } else {
                alert(data.message || 'An error occurred.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred.');
        });
    });
});


document.getElementById("questionnaireForm").addEventListener("submit", function (event) {
    event.preventDefault(); // Prevent default form submission

    const formData = new FormData(this);

    fetch("questionnaire.php", {
        method: "POST",
        body: formData,
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                const categoryName = formData.get("category").trim();
                const questionText = formData.get("question_text").trim();

                // Remove the "No questionnaires available" note if present
                const noQuestionnairesNote = document.querySelector(".category-container .list-group-item");
                if (noQuestionnairesNote && noQuestionnairesNote.textContent === "No questionnaires available.") {
                    noQuestionnairesNote.remove();
                }

                // Find or create the category box dynamically
                let categoryBox = document.querySelector(`.category-box[data-category-id="${categoryName}"]`);
                if (!categoryBox) {
                    const newCategoryBox = document.createElement("div");
                    newCategoryBox.classList.add("category-box");
                    newCategoryBox.setAttribute("data-category-id", categoryName);
                    newCategoryBox.innerHTML = `
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="category-title" style="color: black;">${categoryName}</h3>
                        </div>
                        <ul class="list-group mb-4"></ul>
                    `;
                    document.querySelector(".category-container").appendChild(newCategoryBox);
                    categoryBox = newCategoryBox;
                }

                // Add the question to the category box
                const questionList = categoryBox.querySelector(".list-group");
                const newQuestion = document.createElement("li");
                newQuestion.classList.add("list-group-item", "d-flex", "justify-content-between", "align-items-center");
                newQuestion.setAttribute("data-id", data.new_question_id); // Use ID from server
                newQuestion.innerHTML = `
                    <span class="question-text">${questionText}</span>
                    <div>
                        <button class="btn btn-success btn-sm edit-question-btn" style="display:none;">Edit Question</button>
                        <form action="questionnaire.php" method="POST" class="delete-question-form" style="display:none;">
                            <input type="hidden" name="delete_id" value="${data.new_question_id}">
                            <button type="submit" class="btn btn-danger delete-question-btn btn-sm">Delete</button>
                        </form>
                    </div>
                `;
                questionList.appendChild(newQuestion);

                // Clear the question input field
                this.querySelector("#question").value = "";

                // Show a success message inside the modal
                const successMessage = document.getElementById("successMessage");
                successMessage.textContent = "Question added successfully!";
                successMessage.style.display = "block";

                // Hide the success message after 3 seconds
                setTimeout(() => {
                    successMessage.style.display = "none";
                }, 3000);
            } else {
                alert(data.message || "An error occurred while adding the question.");
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            alert("An error occurred while adding the question.");
        });
});

</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Start polling for notifications after the page loads
    pollPendingEdits();

    function pollPendingEdits() {
        // Check for pending edits every 5 seconds
        setInterval(function () {
            fetch('fetch_pending_edits.php')
                .then(response => response.json())
                .then(data => {
                    const pendingEditsCount = data.pending_count;
                    document.getElementById('pendingEditsCount').textContent = pendingEditsCount;

                    // If there are new edits, alert the admin or highlight the button
                    if (pendingEditsCount > 0) {
                        document.getElementById('pendingEditsButton').classList.add('btn-danger');
                    } else {
                        document.getElementById('pendingEditsButton').classList.remove('btn-danger');
                    }
                })
                .catch(error => console.error('Error fetching pending edits:', error));
        }, 5000); // Adjust the interval as needed (5000ms = 5 seconds)
    }
});


//Display Pending
document.getElementById('pendingEditsButton').addEventListener('click', function () {
    // Fetch pending edits
    fetch('fetch_pending_edits_list.php')
        .then(response => response.json())
        .then(data => {
            let editsHTML = '';
            data.edits.forEach(edit => {
                editsHTML += `
                    <div class="mb-4 p-3 border rounded">
                        <h5>Category: ${edit.category}</h5>
                        <p>Original Question: ${edit.original_question}</p>
                        <p>Proposed Edit: ${edit.updated_question}</p>
                        <div class="d-flex justify-content-between">
                            <button class="btn btn-success" onclick="approveEdit(${edit.edit_id})">Approve</button>
                            <button class="btn btn-danger" onclick="rejectEdit(${edit.edit_id})">Reject</button>
                        </div>
                    </div>
                `;
            });
            document.getElementById('pendingEditsBody').innerHTML = editsHTML;
        })
        .catch(error => console.error('Error fetching pending edits list:', error));
});

//Approve amd Reject
function approveEdit(editId) {
    updateEditStatus(editId, 'approved');
}

function rejectEdit(editId) {
    updateEditStatus(editId, 'rejected');
}

function updateEditStatus(editId, status) {
    fetch('update_edit_status.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `edit_id=${editId}&status=${status}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Edit ' + status + ' successfully.');
            location.reload(); // Reload to reflect changes
        } else {
            alert('Error updating edit status.');
        }
    })
    .catch(error => console.error('Error updating edit status:', error));
}


</script>
  

</body>
</html>
