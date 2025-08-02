<!DOCTYPE html>
<html>
<head>
  <title>Register</title>
</head>
<body>
  <h2>Register</h2>
  <form action="processes.php" method="POST">
    <label for="username">Username:</label><br>
    <input type="text" id="username" name="username" required><br><br>

    <label for="email">Email:</label><br>
    <input type="email" id="email" name="email" required><br><br>

    <label for="password">Password:</label><br>
    <input type="password" id="password" name="password" required><br><br>

    <label for="confirm">Confirm Password:</label><br>
    <input type="password" id="confirm" name="confirm" required><br><br>

    <input type="submit" value="Register">
  </form>
  <style>
    body{
        background-image: url(images/Back.jpeg);
        height: 100vh;
        background-size: cover;
        background-position: center;
    }
  </style>
</body>
</html>
