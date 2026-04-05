<?php
include '../../db_connect.php';
session_start();

if (!isset($_SESSION['prof_id'])) {
    die("Error: Professor not logged in.");
}

$prof_id = $_SESSION['prof_id'];

// Fetch the questionnaires from the database
$query = "SELECT * FROM questionnaires ORDER BY category, `order` ASC";
$result = mysqli_query($conn, $query);

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsive Design</title>
    <link rel="stylesheet" href="clfac.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<style>
  .category-title {
    color: white; /* Make the category title white */
    font-size: 1.5rem; /* Optional: adjust the size if needed */
    font-weight: bold; /* Optional: make it bold */
    margin-bottom: 10px; /* Add some space below the category title */
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

</style>
<body>
  <nav class="navbar navbar-expand-lg" style="background: linear-gradient(rgba(9, 165, 255, 0.80), rgba(0, 225, 255, 0.75));">
    <div class="container-fluid">        
      <div class="row">
            <div class="col-12">
                <button class="btn" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasScrolling" aria-controls="offcanvasScrolling">
                  <img src="../../photo/images.jpg" alt="Menu" class="img-fluid"><span class="rct">Question Session</span>
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
            <li><a class="dropdown-item" href="logout_instructor.php">Log Out</a></li>
              </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>

    <!-- Display Questionnaires -->
    <div class="container mt-5">
      <h2 class="text-white">Questionnaires</h2>
      <?php
      if (mysqli_num_rows($result) > 0) {
          $currentCategory = '';

          while ($row = mysqli_fetch_assoc($result)) {
              if ($currentCategory != $row['category']) {
                  if ($currentCategory != '') {
                      echo '</ul>';
                  }
                  $currentCategory = $row['category'];
                  echo "<h3 class='category-title'>$currentCategory</h3>";
                  echo "<ul class='list-group mb-4'>";
              }
              echo "
              <li class='list-group-item d-flex justify-content-between align-items-center'>
                  <span class='question-text'>{$row['question']}</span>
                  <div>
                      <button class='btn btn-primary btn-sm edit-question-btn' data-id='{$row['id']}' data-question='{$row['question']}'>Edit</button>
                  </div>
              </li>";
          }
          echo "</ul>";
      } else {
          echo "<p class='text-white'>No questionnaires available.</p>";
      }
      ?>
  </div>

<!-- Edit Modal -->
<div class="modal fade" id="editQuestionModal" tabindex="-1" aria-labelledby="editQuestionModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editQuestionModalLabel">Edit Question</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Success Message (Hidden initially) -->
        <div id="successMessage" class="alert alert-success" style="display:none;">
          Edit submitted for approval.
        </div>
        
        <form id="editForm" method="POST">
          <input type="hidden" name="question_id" id="editQuestionId">
          <div class="mb-3">
            <label for="editQuestionText" class="form-label">Question</label>
            <textarea class="form-control" id="editQuestionText" name="updated_question" rows="3" required></textarea>
          </div>
          <button type="submit" class="btn btn-primary">Submit for Approval</button>
        </form>
      </div>
    </div>
  </div>
</div>


<!-- Menu "Pull-Tab" Button -->
<div class="menu-button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasScrolling" aria-controls="offcanvasScrolling">
  <span>Menu</span>
</div>

<!-- Offcanvas Drawer -->
<div class="offcanvas offcanvas-start" data-bs-scroll="true" data-bs-backdrop="false" tabindex="-1" id="offcanvasScrolling" aria-labelledby="offcanvasScrollingLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasScrollingLabel">RCT Faculty Evaluation System</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <div class="but">
            <ul class="dropdown" id="it">
                <li><a href="../dashboard-tt/admin.php" type="button" class="btn" id="cl">Dashboard</a></li>
                <li><a href="../QS/clfac.php" type="button" class="btn" id="cl">Question Session</a></li>
                <li><a href="../comment/clfac.php" type="button" class="btn" id="cl">Comments</a></li>
                <li><a href="../Perfres/clfac.php" type="button" class="btn" id="cl">Performance Result</a></li>
            </ul>
        </div>
    </div>
</div>
    

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    <script>
const menuButton = document.querySelector('.menu-button');
const offcanvasElement = document.getElementById('offcanvasScrolling');


// Add 'drawer-open' class to the body when the drawer opens
offcanvasElement.addEventListener('show.bs.offcanvas', function () {
    document.body.classList.add('drawer-open');
});

// Remove 'drawer-open' class from the body when the drawer closes
offcanvasElement.addEventListener('hide.bs.offcanvas', function () {
    document.body.classList.remove('drawer-open');
});

</script>
   
   <script>
      const hamburgerIcon = document.getElementById('hamburger-icon');

      hamburgerIcon.addEventListener('click', function () {
        this.classList.toggle('active');
      });
    </script>

<script>
// Open the edit modal and fill with question data
document.querySelectorAll('.edit-question-btn').forEach(button => {
    button.addEventListener('click', function() {
        const questionId = this.getAttribute('data-id');
        const questionText = this.getAttribute('data-question');

        document.getElementById('editQuestionId').value = questionId;
        document.getElementById('editQuestionText').value = questionText;

        const editModal = new bootstrap.Modal(document.getElementById('editQuestionModal'));
        editModal.show();
    });
});

// Handle form submission for edits
document.getElementById('editForm').addEventListener('submit', function (e) {
  e.preventDefault();

  const formData = new FormData(this);

  fetch('submit_edit.php', {
    method: 'POST',
    body: formData
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        // Show the success message inside the modal
        const successMessage = document.getElementById('successMessage');
        successMessage.style.display = 'block';
        successMessage.textContent = data.message; // Use the success message from the response

        // Optionally hide the form after submission
        setTimeout(() => {
          successMessage.style.display = 'none'; // Hide the message after some time
          const editModal = bootstrap.Modal.getInstance(document.getElementById('editQuestionModal'));
          editModal.hide(); // Close the modal
          location.reload(); // Reload the page after hiding modal (if needed)
        }, 3000); // Adjust the time as needed
      } else {
        alert(data.message); // If there's an error, alert the message
      }
    })
    .catch(error => {
      console.error('Error:', error);
    });
});


  </script>
</body>
</html>
