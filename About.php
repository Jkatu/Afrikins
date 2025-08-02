<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>About Us</title>
</head>
<body>
  <h1>About Us</h1>
  <nav>
    <a href="layout.html">Home</a> |
  </nav>
<p>Shina is a genetic geneology application that helps trace users ancestry and draws their family trees </p>
<p>We guarantee privacy of your data</p>

<p>Try us today an reconnect with long lost relatives. </p>
<style>
    body{
        background-image: url(images/Back.jpeg);
        height: 100vh;
        background-size: cover;
        background-position: center;
    }
  </style>
      <h2>To contact us please write a message below</h2>
        
<form action="" method="post">
    <input type="text" placeholder="Enter your full name" required/><br>
    <input type="email" placeholder="Enter your email address" required /><br>
    <input type="tel" placeholder="Enter your phone number" required /><br>
    <input type="password" placeholder="Enter your password" required /><br>
    <input type="color" placeholder="Choose your favorite color" /><br>
    <input type="date" placeholder="Select a date" /><br>
    <input type="datetime-local" placeholder="Select date and time" /><br>
    <input type="number" placeholder="Enter your age" required /><br>
    <input type="range" min="1" max="100" value="50" /><br>
    <input type="file" /><br>
    <textarea placeholder="Enter your message" rows="4" cols="50" required></textarea><br>
    <input type="checkbox" name="subscribe" value="yes" /> Subscribe to our newsletter<br>
    <input type="radio" name="gender" value="male" /> Male
    <input type="radio" name="gender" value="female" /> Female<br>
    <select name="" id="">
        <option value="" disabled selected>Select an option</option>
        <option value="option1">Option 1</option>
        <option value="option2">Option 2</option>
        <option value="option3">Option 3</option>
        <option value="option4">Option 4</option>
        <option value="option5">Option 5</option>
    </select><br>
    <input type="submit" value="Submit" />
    <input type="reset" value="Reset" />
</form>
