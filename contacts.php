<?php
    require 'config/dbConnect.php';
    require 'includes/header.php';
    require 'includes/nav.php';
?>
<div class="row">
    <div class="content">
        <h2>Contact Afrikins</h2>
        
<form action="proc/processes.php" method="post">
    <input type="text" name="allnames" placeholder="Enter your full name" required autofocus/><br>
    <input type="email" name="email" placeholder="Enter your email address" required /><br>
    <input type="tel" name="phone" placeholder="Enter your phone number" required /><br>
    <select name="subject" required>
        <option value="" disabled selected>Select Subject</option>
        <option>Film Collaboration</option>
        <option>Research Partnership</option>
        <option>Press & Media</option>
        <option>General Inquiry</option>
    </select><br>
    <textarea name="message" rows="5" cols="30" placeholder="Tell us more about your inquiry..." required></textarea><br>
    <input type="submit" name="send_message" value="Send Message" />
    <input type="reset" value="Clear" />
</form>

<p>
    At Afrikins, we value connection and collaboration. Whether you are a filmmaker, researcher, cultural 
    advocate, or simply passionate about Africa’s untold stories, we’d love to hear from you. Use the form above 
    to reach out with project ideas, partnership opportunities, or general questions.
</p>

<p>
    We believe in the power of community. Your input, stories, and feedback help us create documentaries that 
    authentically reflect African voices and experiences. No matter where you are in the world, your perspective 
    matters to us.
</p>

<h2>Our Commitment</h2>
<p>
    Every message we receive is important. Our team will review your inquiry and get back to you promptly. 
    Together, we can preserve heritage, challenge stereotypes, and inspire through film.
</p>

    </div>
    <div class="sidebar">
        <h2>Why Contact Us?</h2>
        <p>
            We’re always open to new ideas, collaborations, and conversations about Africa’s rich history and 
            vibrant present. Whether you want to contribute to a project or simply learn more about what we do, 
            we’re here to connect.
        </p>
        <p>
            Afrikins is a hub for storytellers, researchers, and communities. Your message might be the start of a 
            powerful documentary that changes how the world sees Africa.
        </p>
    </div>
</div>
<?php
    require 'includes/footer.php';
?>
