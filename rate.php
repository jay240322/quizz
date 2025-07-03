<?php
// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $host = "localhost";
  $user = "root";
  $password = "";
  $dbname = "quiz";

  $conn = new mysqli($host, $user, $password, $dbname);

  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  $rating = intval($_POST['rating']);
  $feedback = $conn->real_escape_string($_POST['feedback']);

  $sql = "INSERT INTO feedbacks (rating, feedback) VALUES ($rating, '$feedback')";

  if ($conn->query($sql) === TRUE) {
    echo "<script>alert('Thanks for your feedback!'); window.location.href='thank.html';</script>";
  } else {
    echo "<script>alert('Error: " . $conn->error . "');</script>";
  }

  $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Rate Your Quiz</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <style>
    #home-icon {
      position: fixed;
      top: 10px;
      left: 20px;
      padding: 12px 20px;
      background-color: transparent;
      color: white;
      font-size: 24px;
      font-weight: bold;
      border-radius: 10px;
      cursor: pointer;
      z-index: 15;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: box-shadow 0.3s ease, color 0.3s ease;
      text-decoration: none;
    }

    body:not(.dark) #home-icon i {
      color: black;
    }

    body.dark #home-icon i {
      color: white;
    }

    body:not(.dark) #home-icon:hover i {
      color: #333;
    }

    body.dark #home-icon:hover i {
      color: #ddd;
    }

    :root {
      --dark-bg: #004d4d;
      --dark-container: #006666;
      --dark-text: #ffffff;
      --accent: #008080;
      --star-light: #66b2b2;
      --light-bg: #f0fdfd;
      --light-container: #ccf2f2;
      --light-text: #004d4d;
    }
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background-color: var(--dark-bg);
      color: var(--dark-text);
      transition: 0.4s;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .light-theme {
      background-color: var(--light-bg);
      color: var(--light-text);
    }
    .rate-container {
      background-color: var(--dark-container);
      padding: 40px;
      border-radius: 12px;
      text-align: center;
      width: 95%;
      max-width: 700px;
      box-shadow: 0 0 15px var(--accent);
      transition: 0.4s;
    }
    .light-theme .rate-container {
      background-color: var(--light-container);
    }
    h2 {
      color: var(--accent);
    }
    p {
      margin-bottom: 20px;
    }
    .stars {
      font-size: 40px;
      margin-bottom: 20px;
    }
    .star {
      color: #80cbc4;
      cursor: pointer;
      margin: 0 5px;
      transition: color 0.3s;
      user-select: none;
    }
    .selected {
      color: var(--accent);
    }
    textarea {
      display: none;
      width: 100%;
      height: 80px;
      padding: 10px;
      font-size: 16px;
      border-radius: 8px;
      margin-top: 10px;
      border: 2px solid #004d4d;
      background-color: var(--dark-bg);
      color: white;
      resize: vertical;
    }
    .light-theme textarea {
      background-color: white;
      color: black;
      border-color: var(--light-text);
    }
    button {
      display: none;
      margin-top: 15px;
      background-color: var(--accent);
      border: none;
      padding: 10px 20px;
      font-size: 18px;
      color: white;
      border-radius: 8px;
      cursor: pointer;
    }
    .toggle-container {
      position: fixed;
      top: 10px;
      right: 10px;
      z-index: 20;
    }
    .switch {
      position: relative;
      display: inline-block;
      width: 50px;
      height: 26px;
    }
    .switch input {
      opacity: 0;
      width: 0;
      height: 0;
    }
    .slider {
      position: absolute;
      cursor: pointer;
      background-color: black;
      border-radius: 34px;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      transition: 0.4s;
    }
    .slider:before {
      position: absolute;
      content: "";
      height: 18px;
      width: 18px;
      left: 4px;
      bottom: 4px;
      background-color: white;
      border-radius: 50%;
      transition: 0.4s;
    }
    input:checked + .slider {
      background-color: var(--accent);
    }
    input:checked + .slider:before {
      transform: translateX(24px);
    }
  </style>
</head>
<body>
  <a id="home-icon" href="dashboard.html" title="Go to Dashboard">
    <i class="fa fa-home" aria-hidden="true"></i>
  </a>
  <div class="toggle-container">
    <label class="switch">
      <input type="checkbox" id="themeToggle" />
      <span class="slider"></span>
    </label>
  </div>

  <div class="rate-container">
    <h2>Rate Your Quiz</h2>
    <p>How was your quiz experience? Select stars and give feedback to help us improve!</p>
    <div class="stars" id="stars">
      <span class="star" data-value="1">&#9733;</span>
      <span class="star" data-value="2">&#9733;</span>
      <span class="star" data-value="3">&#9733;</span>
      <span class="star" data-value="4">&#9733;</span>
      <span class="star" data-value="5">&#9733;</span>
    </div>
    <form method="POST" action="">
      <textarea name="feedback" id="feedback" placeholder="Write your feedback..."></textarea>
      <input type="hidden" name="rating" id="rating" required>
      <button type="submit" id="submitBtn">Submit</button>
    </form>
  </div>

  <script>
    const stars = document.querySelectorAll('.star');
    const feedback = document.getElementById('feedback');
    const submitBtn = document.getElementById('submitBtn');
    const ratingInput = document.getElementById('rating');
    let selectedRating = 0;

    stars.forEach(star => {
      star.addEventListener('click', () => {
        selectedRating = star.dataset.value;
        ratingInput.value = selectedRating;
        stars.forEach(s => s.classList.remove('selected'));
        for (let i = 0; i < selectedRating; i++) stars[i].classList.add('selected');
        feedback.style.display = 'block';
        submitBtn.style.display = 'inline-block';
        feedback.focus();
      });
    });

    document.getElementById('themeToggle').addEventListener('change', e => {
      document.body.classList.toggle('light-theme', e.target.checked);
    });
  </script>
</body>
</html>
