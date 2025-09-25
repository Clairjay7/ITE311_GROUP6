<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>St. Peter Hospital Inc. - Integrated Hospital Management System</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Playfair+Display:wght@400;500;600;700;800;900&display=swap');
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8fffe;
            font-weight: 400;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Navigation */
        .navbar {
            background: linear-gradient(135deg, #2e7d32, #4caf50);
            padding: 1rem 0;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav-logo {
            display: flex;
            align-items: center;
            color: white;
            font-size: 1.6rem;
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            letter-spacing: -0.01em;
        }

        .nav-logo i {
            margin-right: 10px;
            font-size: 2rem;
        }

        .nav-menu {
            display: flex;
            list-style: none;
            align-items: center;
        }

        .nav-menu li {
            margin-left: 2rem;
        }

        .nav-menu a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .nav-menu a:hover {
            color: #c8e6c9;
        }

        .login-btn {
            background: rgba(255,255,255,0.2);
            padding: 0.5rem 1rem;
            border-radius: 25px;
            border: 2px solid rgba(255,255,255,0.3);
            transition: all 0.3s ease;
        }

        .login-btn:hover {
            background: rgba(255,255,255,0.3);
            border-color: rgba(255,255,255,0.5);
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, #e8f5e8, #c8e6c9);
            padding: 120px 0 80px;
            display: flex;
            align-items: center;
            min-height: 100vh;
        }

        .hero-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
        }

        .hero-content h1 {
            font-family: 'Playfair Display', serif;
            font-size: 4rem;
            color: #2e7d32;
            margin-bottom: 1rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            text-shadow: 0 2px 4px rgba(46, 125, 50, 0.1);
        }

        .hero-content h2 {
            font-family: 'Inter', sans-serif;
            font-size: 1.9rem;
            color: #388e3c;
            margin-bottom: 1rem;
            font-weight: 600;
            letter-spacing: -0.01em;
        }

        .hero-content p {
            font-size: 1.2rem;
            color: #555;
            margin-bottom: 2rem;
            line-height: 1.8;
        }

        .hero-buttons {
            display: flex;
            gap: 1rem;
        }

        .btn {
            padding: 1rem 2rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 1rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #4caf50, #66bb6a);
            color: white;
            box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(76, 175, 80, 0.4);
        }

        .btn-secondary {
            background: transparent;
            color: #4caf50;
            border: 2px solid #4caf50;
        }

        .btn-secondary:hover {
            background: #4caf50;
            color: white;
        }

        .hero-image {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .hero-image .hospital-img {
            width: 100%;
            max-width: 500px;
            height: auto;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            transition: transform 0.3s ease;
        }

        .hero-image .hospital-img:hover {
            transform: scale(1.05);
        }

        /* About Section */
        .about {
            padding: 80px 0;
            background: white;
        }

        .about h2 {
            font-family: 'Playfair Display', serif;
            text-align: center;
            font-size: 3rem;
            color: #2e7d32;
            margin-bottom: 3rem;
            font-weight: 700;
            letter-spacing: -0.02em;
        }

        .about-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 4rem;
            align-items: center;
        }

        .about-text h3 {
            font-family: 'Playfair Display', serif;
            color: #388e3c;
            font-size: 1.8rem;
            margin: 2rem 0 1rem;
            font-weight: 600;
            letter-spacing: -0.01em;
        }

        .about-text p {
            font-family: 'Inter', sans-serif;
            font-size: 1.1rem;
            line-height: 1.8;
            color: #555;
            margin-bottom: 1rem;
            font-weight: 400;
        }

        .about-stats {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .stat {
            text-align: center;
            padding: 2rem;
            background: linear-gradient(135deg, #e8f5e8, #c8e6c9);
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .stat i {
            font-size: 3rem;
            color: #4caf50;
            margin-bottom: 1rem;
        }

        .stat h3 {
            font-size: 2rem;
            color: #2e7d32;
            margin-bottom: 0.5rem;
        }

        .stat p {
            color: #555;
            font-weight: 500;
        }


        /* Footer */
        .footer {
            background: linear-gradient(135deg, #2e7d32, #1b5e20);
            color: white;
            padding: 3rem 0 1rem;
            text-align: center;
        }

        .footer-content h3 {
            font-family: 'Playfair Display', serif;
            color: #c8e6c9;
            margin-bottom: 1rem;
            font-size: 1.6rem;
            font-weight: 600;
            letter-spacing: -0.01em;
        }

        .footer-content p {
            line-height: 1.6;
            color: #e8f5e8;
            margin-bottom: 2rem;
        }

        .footer-bottom {
            border-top: 1px solid #4caf50;
            padding-top: 1rem;
            color: #c8e6c9;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .hero-content {
                grid-template-columns: 1fr;
                text-align: center;
            }
            
            .hero-content h1 {
                font-size: 2.5rem;
            }
            
            .about-grid {
                grid-template-columns: 1fr;
            }
            
            .nav-menu {
                display: none;
            }
            
            .hero-image .hospital-img {
                max-width: 300px;
            }
            
        }

        /* Smooth Scrolling */
        html {
            scroll-behavior: smooth;
        }

        /* Animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hero-content > * {
            animation: fadeInUp 0.8s ease-out;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <i class="fas fa-hospital"></i>
                <span>St. Peter Hospital Inc.</span>
            </div>
            <ul class="nav-menu">
                <li><a href="#home">Home</a></li>
                <li><a href="#about">About</a></li>
                <li><a href="<?= base_url('login') ?>" class="login-btn">Login</a></li>
            </ul>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero">
        <div class="hero-content">
            <div>
                <h1>St. Peter Hospital Inc.</h1>
                <h2>Your Health, Our Mission</h2>
                <p>Serving General Santos City with trusted care, modern facilities, and a commitment to improving lives every day.</p>
                <div class="hero-buttons">
                    <a href="#about" class="btn btn-primary">Learn More</a>
                    <a href="<?= base_url('login') ?>" class="btn btn-secondary">Login</a>
                </div>
            </div>
            <div class="hero-image">
                <img src="https://rmmcmain.com/assets/site/images/web-background/caroucel-1.webp?fbclid=IwY2xjawMgn5JleHRuA2FlbQIxMABicmlkETFud0dUdUVBS2VRam5kWVJaAR6BJIUH-Bt3L9MwHEnOb6tIfO-9xdLEeSa5DCSbJXWg0XuT7Jzf0QdA4H9rdQ_aem_oVR6X4Xzqe4JFI_5IpJWhg" alt="St. Peter Hospital" class="hospital-img">
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="about">
        <div class="container">
            <h2>About St. Peter Hospital Inc.</h2>
            <div class="about-grid">
                <div class="about-text">
                    <p>St. Peter Hospital Inc. is more than just a healthcare facility — it is a family of doctors, nurses, and staff united with one purpose: to provide compassionate, reliable, and excellent care for every patient. With our main hospital and partner clinics in General Santos City, we serve the community with dedication, professionalism, and a genuine heart to heal.</p>
                    
                    <h3>Our Mission</h3>
                    <p>Our mission is to stand as a pillar of trust and hope in healthcare — where every patient is treated with dignity, every family is supported with compassion, and every life is cared for with expertise and integrity.</p>
                </div>
                <div class="about-stats">
                    <div class="stat">
                        <i class="fas fa-users"></i>
                        <h3>500+</h3>
                        <p>Patients Daily</p>
                    </div>
                    <div class="stat">
                        <i class="fas fa-clinic-medical"></i>
                        <h3>Multiple</h3>
                        <p>Affiliate Clinics</p>
                    </div>
                    <div class="stat">
                        <i class="fas fa-user-md"></i>
                        <h3>Expert</h3>
                        <p>Medical Staff</p>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <h3><i class="fas fa-hospital"></i> St. Peter Hospital Inc.</h3>
                <p>Providing quality healthcare services with advanced technology and compassionate care.</p>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 St. Peter Hospital Inc. All rights reserved. | Integrated Hospital Management System</p>
            </div>
        </div>
    </footer>

    <script>
        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 100) {
                navbar.style.background = 'linear-gradient(135deg, #1b5e20, #2e7d32)';
                navbar.style.boxShadow = '0 4px 20px rgba(0,0,0,0.2)';
            } else {
                navbar.style.background = 'linear-gradient(135deg, #2e7d32, #4caf50)';
                navbar.style.boxShadow = '0 2px 10px rgba(0,0,0,0.1)';
            }
        });

        // Add loading animation
        window.addEventListener('load', function() {
            document.body.style.opacity = '1';
        });

        // Initialize page
        document.body.style.opacity = '0';
        document.body.style.transition = 'opacity 0.5s ease';
    </script>
</body>
</html>
