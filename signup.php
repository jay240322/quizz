<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "user_system";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$msg = "";
$msgClass = "";
$showLoginLink = true;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if ($password !== $confirm) {
        $msg = "Passwords do not match!";
        $msgClass = "error";
    } else {
        $checkUser = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $checkUser->bind_param("s", $username);
        $checkUser->execute();
        $result = $checkUser->get_result();

        if ($result->num_rows > 0) {
            $msg = "Username already exists!";
            $msgClass = "error";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, email, phone, password) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $username, $email, $phone, $hashedPassword);
            if ($stmt->execute()) {
                // Instead of a green box, set message to plain text with styled link
                $msg = 'Registration successful. <a href="login.php" class="inline-link">Login here</a>.';
                $msgClass = "plain";  // We'll style .message.plain to look like bottom-text
                $showLoginLink = false; // Hide the bottom "Already have an account" line
            } else {
                $msg = "Error during registration.";
                $msgClass = "error";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Sign Up Form</title>
  <style>
  * { box-sizing: border-box; }

  body {
    margin: 0;
    padding: 0;
    font-family: 'Segoe UI', sans-serif;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100vh;
    transition: background 0.4s, color 0.4s;
    position: relative;
    overflow: hidden;
  }

  /* Background video styling */
  #bg-video {
    position: fixed;
    top: 0; left: 0;
    width: 100vw;
    height: 100vh;
    object-fit: cover;
    z-index: -1;
    display: none;
    transition: opacity 0.8s ease;
    opacity: 0;
  }

  body.light-mode #bg-video {
    display: block;
    opacity: 1;
  }

  /* Dark mode background */
  body.dark-mode {
    background-color: #000;
    color: #fff;
  }

  /* Light mode background is transparent to show video */
  body.light-mode {
    background-color: transparent;
    color: #000;
  }

  .container {
    width: 380px;
    padding: 30px 25px;
    border-radius: 15px;
    box-shadow: 0 0 15px rgba(255, 255, 255, 0.1);
    border: 1px solid #333;
    transition: background 0.4s, box-shadow 0.4s;
    z-index: 1;
    /* default background for dark mode */
    background-color: rgba(255, 255, 255, 0.9);
  }

  /* Dark mode container background */
  body.dark-mode .container {
    background-color: rgba(17, 17, 17, 0.9);
    border-color: #444;
    box-shadow: 0 0 15px rgba(0,0,0,0.7);
  }

  /* Light mode container fully transparent */
  body.light-mode .container {
    background-color: transparent;
    border-color: transparent;
    box-shadow: none;
  }

  h2 {
    text-align: center;
    margin-bottom: 25px;
    font-size: 28px;
  }

  input[type="text"],
  input[type="email"],
  input[type="password"],
  input[type="tel"] {
    width: 100%;
    padding: 12px;
    margin: 12px 0;
    background-color: #000;
    border: 2px solid #444;
    border-radius: 8px;
    color: #fff;
    font-size: 16px;
    box-shadow: 0 0 8px rgba(255,255,255,0.4);
    transition: 0.3s ease;
  }

  body.light-mode input {
    background-color: rgba(255 255 255 / 0.6);
    color: #000;
    border: 1px solid #ccc;
    box-shadow: 0 0 6px rgba(0,0,0,0.1);
    backdrop-filter: blur(6px);
  }

  input:focus {
    outline: none;
    box-shadow: 0 0 12px #fff;
    border-color: #fff;
  }

  body.light-mode input:focus {
    box-shadow: 0 0 10px #000;
    border-color: #000;
  }

  button {
    width: 100%;
    padding: 12px;
    margin-top: 20px;
    background-color: #000;
    border: 2px solid #fff;
    border-radius: 10px;
    color: #fff;
    font-size: 16px;
    cursor: pointer;
    transition: all 0.3s ease-in-out;
  }

  button:hover {
    background-color: #fff;
    color: #000;
    border-color: #000;
  }

  body.light-mode button {
    background-color: rgba(255 255 255 / 0.7);
    color: #000;
    border-color: #000;
    box-shadow: 0 0 10px #000;
    backdrop-filter: blur(6px);
  }

  body.light-mode button:hover {
    background-color: #000;
    color: #fff;
    border-color: #fff;
  }

  .bottom-text {
    text-align: center;
    margin-top: 20px;
    font-size: 14px;
  }

  .bottom-text a {
    color: #4dabf7;
    text-decoration: none;
    font-weight: bold;
  }

  .bottom-text a:hover {
    text-decoration: underline;
  }

  .message {
    text-align: center;
    margin: 15px 0 0 0;
    padding: 12px 15px;
    border-radius: 10px;
    font-size: 15px;
    font-weight: 600;
    box-shadow: 0 0 8px rgba(0,0,0,0.1);
    transition: background-color 0.3s ease, box-shadow 0.3s ease;
    max-width: 350px;
    margin-left: auto;
    margin-right: auto;
  }

  .message.error {
    color: #e74c3c;
    background-color: #63171b33;
    box-shadow: 0 0 15px #e74c3caa;
  }

  .message.success {
    color: #2ecc71;
    background-color: #14532d33;
    box-shadow: 0 0 15px #2ecc71aa;
  }

  .message.plain {
    background: none;
    box-shadow: none;
    font-weight: normal;
    font-size: 14px;
    color: inherit;
    padding: 0;
    margin-top: 12px;
  }

  .message.plain a.inline-link {
    color: #4dabf7;
    text-decoration: none;
    font-weight: bold;
  }

  .message.plain a.inline-link:hover {
    text-decoration: underline;
  }

  .toggle-switch {
    position: absolute;
    top: 20px;
    right: 20px;
    z-index: 2;
  }

  .switch {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 28px;
  }

  .switch input {
    opacity: 0;
    width: 0;
    height: 0;
  }

  .slider {
    position: absolute;
    cursor: pointer;
    top: 0; left: 0; right: 0; bottom: 0;
    background-color: #444;
    transition: .4s;
    border-radius: 34px;
  }

  .slider:before {
    position: absolute;
    content: "";
    height: 20px;
    width: 20px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
  }

  input:checked + .slider {
    background-color: #2196F3;
  }

  input:checked + .slider:before {
    transform: translateX(22px);


</style>


</head>
<body class="dark-mode">
<video id="bg-video" autoplay muted loop>
  <source src="quiz background/login.mp4" type="video/mp4">
  Your browser does not support the video tag.
</video>

  <div class="toggle-switch">
    <label class="switch">
      <input type="checkbox" id="modeToggle">
      <span class="slider"></span>
    </label>
  </div>

  <div class="container">
    <h2>Sign Up</h2>

    <form method="POST" action="">
      <input type="text" name="username" placeholder="Username" required>
      <input type="email" name="email" placeholder="Email" required>
      <input type="tel" name="phone" placeholder="Phone Number" required maxlength="10" pattern="\d{10}" title="Phone number must be 10 digits" oninput="this.value=this.value.replace(/\D/g,'')">
      <input type="password" name="password" placeholder="Password" minlength="8" required>
      <input type="password" name="confirm_password" placeholder="Confirm Password" required>
      <button type="submit">CREATE ACCOUNT</button>
    </form>

    <!-- Show message below the form -->
    <?php if ($msg): ?>
      <div class="message <?= $msgClass ?>"><?= $msg ?></div>
    <?php endif; ?>

    <!-- Show this only if registration not successful -->
    <?php if ($showLoginLink): ?>
      <div class="bottom-text">
        Already have an account? <a href="login.php">Login</a>
      </div>
    <?php endif; ?>

  </div>

  <script>
    const toggle = document.getElementById("modeToggle");
    toggle.addEventListener("change", () => {
      document.body.classList.toggle("light-mode", toggle.checked);
      document.body.classList.toggle("dark-mode", !toggle.checked);
    });
  </script>
</body>
</html>
