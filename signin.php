<?php
    require 'config/dbConnect.php';
    require 'includes/header.php';
    require 'includes/nav.php';
?>

<div class="row">
    <div class="content">
        <h2>Terms and Conditions</h2>
        <p>
            Welcome to Afrikins Documentary Collective. By accessing or using our platform, you agree to comply with and 
            be bound by these Terms and Conditions. These terms are designed to protect both our community of storytellers 
            and the integrity of the stories we share.
        </p>

        <p>
            All content, including documentaries, photographs, and articles, remains the intellectual property of Afrikins 
            and its collaborators unless otherwise stated. You may not reproduce, distribute, or use our materials without 
            prior written consent.
        </p>

        <p>
            Users are expected to respect cultural sensitivities, historical accuracy, and community guidelines when engaging 
            with our platform. We reserve the right to remove content or suspend accounts that violate these principles.
        </p>

        <h2>Collaborations and Contributions</h2>
        <p>
            Afrikins welcomes contributions from filmmakers, researchers, and cultural advocates. By submitting material, you 
            confirm that you own the rights to it or have obtained the necessary permissions. Contributions may be edited for 
            clarity, accuracy, and style while preserving the authenticity of the story.
        </p>

        <h2>Our Commitment</h2>
        <p>
            We are committed to truthful storytelling that amplifies African voices, preserves heritage, and challenges stereotypes. 
            Every project we take on is handled with professionalism, cultural respect, and a dedication to excellence.
            We are commited 
        </p>
    </div>

    <div class="sidebar">
        <h2>Sign In</h2>
        <form action="proc/processes.php" method="post">
            <input type="text" name="username" placeholder="Enter your username" autofocus required/><br>
            <input type="password" name="passphrase" placeholder="Enter your password" required /><br><br>
            <input type="submit" name="signin" value="Sign In" />
            <a href="signup.php">Don't have an account? Sign Up</a>
        </form>

        <h2>About Afrikins</h2>
        <p>
            Afrikins is a collective of filmmakers, researchers, and storytellers dedicated to capturing Africa’s diverse 
            narratives. From rural traditions to urban innovation, we document stories that matter.
        </p>
        <p>
            Our mission is to preserve heritage, challenge stereotypes, and inspire through authentic African storytelling. 
            We also want to show
            Join us in shaping ho1w Africa is seen and remembered for generations to come.


        </p>
    </div>
</div>

<?php
    require 'includes/footer.php';
?>
