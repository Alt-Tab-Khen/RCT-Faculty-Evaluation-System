<?php
include '../../db_connect.php';

session_start();
if (isset($_SESSION['student_id'])) {
    echo "Student ID: " . $_SESSION['student_id'];
} else {
    echo "Student ID not set or session expired.";
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluation Form</title>
    <link rel="stylesheet" href="clfac.css?v=<?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body>
  <nav class="navbar navbar-expand-lg" style="background: linear-gradient(rgba(9, 165, 255, 0.80), rgba(0, 225, 255, 0.75));">
    <div class="container-fluid">        
      <div class="row">
            <div class="col-12">
                <button class="btn" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasScrolling" aria-controls="offcanvasScrolling">
                    <img src="../../photo/images.jpg" alt="Menu" class="img-fluid">
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
                <li><a class="dropdown-item" href="#">Log Out</a></li>
              </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>
    
<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="successModalLabel">Evaluation Submitted</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Your evaluation has been successfully submitted!
            </div>
        </div>
    </div>
</div>


  <form action="submit_evaluation.php" method="POST" id="evaluation-form">
    <!-- Hidden field for prof_id -->
    <input type="hidden" name="prof_id" value="<?php echo $_GET['prof_id']; ?>" />

    <div class="container mt-5">
        <h3 class="mb-4" style="color: white">Evaluation Questions</h3>

        <!-- Question list generated dynamically -->
        <div id="question-list"></div>

        <!-- Feedback Text Area -->
        <div class="form-group mt-4">
            <label for="feedback" style="color: white;">Additional Comments</label>
            <textarea name="feedback" id="feedback" rows="4" class="form-control" placeholder="Write your feedback here..."></textarea>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary mt-4">Submit Evaluation</button>
    </div>
</form>


  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>

  <script>
document.addEventListener('DOMContentLoaded', function () {
    fetchQuestions();

    function fetchQuestions() {
        fetch('fetch_questions.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                const questionList = document.getElementById('question-list');
                let questionsHTML = '';

                let currentCategory = '';

                if (data.length > 0) {
                    data.forEach((question, index) => {
                        if (question.category !== currentCategory) {
                            if (currentCategory !== '') {
                                // Close previous category div
                                questionsHTML += '</div>';
                            }

                            questionsHTML += `
                                <div class="category-container mb-4 p-3 rounded" style="background-color: white;">
                                    <h5 class="question-category">${question.category}</h5>
                            `;
                            currentCategory = question.category;
                        }

                        questionsHTML += `
                            <div class="question-container mb-3">
                                <p class="question-text">${question.question}</p>
                                <input type="hidden" name="categories[${question.id}]" value="${question.category}">
                                <input type="hidden" name="ratings[${question.id}]" id="rating-${question.id}" value="0">
                                <div class="star-rating" data-question-id="${question.id}">
                                    <i class="fa fa-star" data-rating="1"></i>
                                    <i class="fa fa-star" data-rating="2"></i>
                                    <i class="fa fa-star" data-rating="3"></i>
                                    <i class="fa fa-star" data-rating="4"></i>
                                    <i class="fa fa-star" data-rating="5"></i>
                                </div>
                            </div>
                        `;

                        if (index === data.length - 1) {
                            // Close the final category div
                            questionsHTML += '</div>';
                        }
                    });
                } else {
                    questionsHTML += '<p>No active questions available for evaluation.</p>';
                }

                questionList.innerHTML = questionsHTML;

                // Attach event listeners after rendering stars
                const stars = document.querySelectorAll('.star-rating i');
                stars.forEach(star => {
                    star.addEventListener('click', handleStarClick);
                });
            })
            .catch(error => console.error('Error fetching questions:', error));
    }

    // Handle star click
    function handleStarClick(event) {
        const clickedStar = event.target;
        const rating = parseInt(clickedStar.getAttribute('data-rating'));
        const questionId = clickedStar.closest('.star-rating').getAttribute('data-question-id');

        // Update hidden input with selected rating
        document.getElementById('rating-' + questionId).value = rating;

        // Highlight selected stars
        const allStars = clickedStar.closest('.star-rating').querySelectorAll('i');
        allStars.forEach((star, index) => {
            if (index < rating) {
                star.classList.add('selected');
            } else {
                star.classList.remove('selected');
            }
        });
    }

    // Handle form submission with modal confirmation
    document.getElementById('evaluation-form').addEventListener('submit', function (event) {
        event.preventDefault(); // Prevent default form submission

        const formData = new FormData(this);

        fetch('submit_evaluation.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success modal
                    const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                    successModal.show();

                    // Redirect after a short delay
                    setTimeout(() => {
                        window.location.href = data.redirect_url;
                    }, 3000); // Adjust delay as needed
                } else {
                    alert(data.message || 'Failed to submit evaluation. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error submitting evaluation:', error);
                alert('An error occurred. Please try again later.');
            });
    });
});
</script>


</body>
</html>
