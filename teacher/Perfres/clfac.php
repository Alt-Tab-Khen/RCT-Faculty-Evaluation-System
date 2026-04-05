<?php
include '../../db_connect.php';

session_start();
if (!isset($_SESSION['prof_id'])) {
    die("Error: Professor not logged in.");
}

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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
        .overall-rating {
            text-align: center;
            font-size: 2.5rem;
            margin: 20px 0;
            color: white;
        }
        .category-container {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 30px;
        }
        .category-card {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            width: 200px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .category-title {
            font-size: 1.5rem;
            margin-bottom: 10px;
        }
        .category-rating {
            font-size: 2rem;
            color: #17a2b8;
        }
        .performance-graph-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 30px;
            background-color: rgba(255, 255, 255, 0.9); /* White with transparency */
            border-radius: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Add some shadow for effect */
            margin-top: 50px; /* Space from top */
        }

        .performance-graph-title {
            text-align: center;
            font-size: 1.8rem;
            margin-bottom: 20px;
            color: #333; /* Darker color for contrast */
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

</head>

<body>
  <nav class="navbar navbar-expand-lg" style="background: linear-gradient(rgba(9, 165, 255, 0.80), rgba(0, 225, 255, 0.75));">
    <div class="container-fluid">        
      <div class="row">
            <div class="col-12">
                <button class="btn" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasScrolling" aria-controls="offcanvasScrolling">
                  <img src="../../photo/images.jpg" alt="Menu" class="img-fluid"><span class="rct">Performance Result</span>
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
    <!-- Overall Rating -->
    <div class="overall-rating">
        Overall Rating: <span id="overall-rating">00</span>
    </div>

    <!-- Category Ratings -->
    <div class="category-container">
        <!-- This will be populated dynamically -->
    </div>

    <!-- Performance History Graph -->
    <div class="performance-graph-container">
        <h3 class="performance-graph-title">Performance History</h3>
        <canvas id="performanceHistoryChart"></canvas>
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
document.addEventListener('DOMContentLoaded', function() {
    const profId = '<?= $_SESSION['prof_id']; ?>';  // Pass the professor's ID from the session

    fetchRatings(profId);

    function fetchRatings(profId) {
        fetch(`fetch_ratings.php?prof_id=${profId}`)
            .then(response => response.json())
            .then(data => {
                // Update the overall rating
                document.getElementById('overall-rating').textContent = data.overall_rating || "No Ratings Yet";

                // Update category ratings dynamically
                const categoryContainer = document.querySelector('.category-container');
                categoryContainer.innerHTML = '';  // Clear existing categories

                if (data.categories.length > 0) {
                    data.categories.forEach(category => {
                        // Create a card for each category
                        const categoryCard = document.createElement('div');
                        categoryCard.classList.add('category-card');
                        
                        const categoryTitle = document.createElement('div');
                        categoryTitle.classList.add('category-title');
                        categoryTitle.textContent = category.name;

                        const categoryRating = document.createElement('div');
                        categoryRating.classList.add('category-rating');
                        categoryRating.textContent = category.rating;

                        categoryCard.appendChild(categoryTitle);
                        categoryCard.appendChild(categoryRating);
                        categoryContainer.appendChild(categoryCard);
                    });
                } else {
                    categoryContainer.textContent = "No Categories Available";
                }
            })
            .catch(error => {
                console.error('Error fetching ratings:', error);
            });
    }
});


    </script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const profId = '<?= $_SESSION['prof_id']; ?>';  // Pass the professor's ID from the session
        const ctx = document.getElementById('performanceHistoryChart').getContext('2d');
        let chart;

        // Fetch the performance data
        function fetchPerformanceData(profId) {
            fetch(`fetch_performance_data.php?prof_id=${profId}`)
                .then(response => response.json())
                .then(data => {
                    const labels = data.map(item => item.evaluation_date);
                    const values = data.map(item => item.avg_rating);

                    // Create the line chart if it doesn't exist yet
                    if (!chart) {
                        chart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: 'Average Rating',
                                    data: values,
                                    borderColor: 'rgba(75, 192, 192, 1)',
                                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                    fill: true,
                                    tension: 0.1
                                }]
                            },
                            options: {
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        suggestedMax: 5 // Assuming the rating scale is from 1 to 5
                                    }
                                }
                            }
                        });
                    } else {
                        // Update the chart with new data
                        chart.data.labels = labels;
                        chart.data.datasets[0].data = values;
                        chart.update();
                    }
                })
                .catch(error => console.error('Error fetching performance data:', error));
        }

        // Fetch the data on page load
        fetchPerformanceData(profId);

        // Fetch data every 10 seconds for real-time updates
        setInterval(function() {
            fetchPerformanceData(profId);
        }, 10000); // Adjust the interval as needed (10 seconds in this case)
    });
</script>






</body>
</html>
