<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FlexFusion - Your Ultimate Fitness Destination</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #ff4d4d;
            --secondary-color: #2c3e50;
            --accent-color: #3498db;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            overflow-x: hidden;
        }

        /* Navbar Styles */
        .navbar {
            background-color: rgba(0, 0, 0, 0.9);
            padding: 1rem 2rem;
            transition: all 0.3s ease;
        }

        .navbar-brand {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary-color) !important;
        }

        .nav-link {
            color: white !important;
            font-weight: 500;
            margin: 0 10px;
            transition: color 0.3s ease;
        }

        .nav-link:hover {
            color: var(--primary-color) !important;
        }

        /* Hero Section */
        .hero {
            height: 100vh;
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)),
                        url('https://images.unsplash.com/photo-1534438327276-14e5300c3a48?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80');
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            color: white;
        }

        .hero-content {
            max-width: 800px;
            margin: 0 auto;
            text-align: center;
        }

        .hero h1 {
            font-size: 4rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }

        .hero p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
        }

        /* Features Section */
        .features {
            padding: 100px 0;
            background-color: #f8f9fa;
        }

        .feature-card {
            padding: 30px;
            border-radius: 10px;
            background: white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-10px);
        }

        .feature-icon {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 20px;
        }

        /* Programs Section */
        .programs {
            padding: 100px 0;
            background-color: var(--secondary-color);
            color: white;
        }

        .program-card {
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 30px;
            transition: all 0.3s ease;
        }

        .program-card:hover {
            background: rgba(255,255,255,0.2);
            transform: scale(1.02);
        }

        /* Testimonials Section */
        .testimonials {
            padding: 100px 0;
            background-color: #f8f9fa;
        }

        .testimonial-card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin: 20px;
        }

        /* CTA Section */
        .cta {
            padding: 100px 0;
            background: linear-gradient(45deg, var(--primary-color), var(--accent-color));
            color: white;
        }

        /* Footer */
        footer {
            background-color: var(--secondary-color);
            color: white;
            padding: 50px 0 20px;
        }

        .social-links a {
            color: white;
            font-size: 1.5rem;
            margin: 0 10px;
            transition: color 0.3s ease;
        }

        .social-links a:hover {
            color: var(--primary-color);
        }

        /* Custom Buttons */
        .btn-custom {
            padding: 12px 30px;
            border-radius: 30px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }

        .btn-primary-custom {
            background-color: var(--primary-color);
            border: none;
            color: white;
        }

        .btn-primary-custom:hover {
            background-color: #ff3333;
            transform: translateY(-2px);
        }

        .btn-outline-custom {
            border: 2px solid white;
            color: white;
        }

        .btn-outline-custom:hover {
            background-color: white;
            color: var(--primary-color);
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">FlexFusion</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#programs">Programs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#testimonials">Testimonials</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="auth/login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-primary-custom ms-2" href="auth/register.php">Join Now</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="container">
            <div class="hero-content" data-aos="fade-up">
                <h1>Transform Your Body, Transform Your Life</h1>
                <p>Join FlexFusion and experience the ultimate fitness journey with personalized training, expert guidance, and state-of-the-art facilities.</p>
                <a href="auth/register.php" class="btn btn-primary-custom btn-lg me-3">Get Started</a>
                <a href="#programs" class="btn btn-outline-custom btn-lg">Our Programs</a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="container">
            <h2 class="text-center mb-5" data-aos="fade-up">Why Choose FlexFusion?</h2>
            <div class="row">
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-card text-center">
                        <i class="fas fa-dumbbell feature-icon"></i>
                        <h3>Expert Trainers</h3>
                        <p>Our certified trainers provide personalized guidance to help you achieve your fitness goals.</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-card text-center">
                        <i class="fas fa-heartbeat feature-icon"></i>
                        <h3>Customized Programs</h3>
                        <p>Get personalized workout and diet plans tailored to your specific needs and goals.</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="feature-card text-center">
                        <i class="fas fa-chart-line feature-icon"></i>
                        <h3>Progress Tracking</h3>
                        <p>Monitor your progress with our advanced tracking system and stay motivated.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Programs Section -->
    <section class="programs" id="programs">
        <div class="container">
            <h2 class="text-center mb-5" data-aos="fade-up">Our Programs</h2>
            <div class="row">
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="program-card">
                        <h3>Personal Training</h3>
                        <p>One-on-one sessions with expert trainers for maximum results.</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="program-card">
                        <h3>Group Classes</h3>
                        <p>High-energy group workouts for motivation and fun.</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="program-card">
                        <h3>Nutrition Planning</h3>
                        <p>Customized diet plans to complement your fitness journey.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials" id="testimonials">
        <div class="container">
            <h2 class="text-center mb-5" data-aos="fade-up">Success Stories</h2>
            <div class="row">
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="testimonial-card">
                        <p>"FlexFusion transformed my life. The trainers are amazing and the results are incredible!"</p>
                        <h5>- Sarah Johnson</h5>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="testimonial-card">
                        <p>"The personalized attention and professional guidance helped me achieve my fitness goals."</p>
                        <h5>- Mike Thompson</h5>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="testimonial-card">
                        <p>"Best gym I've ever been to. The community is supportive and the facilities are top-notch."</p>
                        <h5>- Emily Davis</h5>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta">
        <div class="container text-center" data-aos="fade-up">
            <h2>Ready to Start Your Fitness Journey?</h2>
            <p class="mb-4">Join FlexFusion today and take the first step towards a healthier lifestyle.</p>
            <a href="auth/register.php" class="btn btn-outline-custom btn-lg">Join Now</a>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h4>FlexFusion</h4>
                    <p>Your ultimate fitness destination for transformation and wellness.</p>
                </div>
                <div class="col-md-4">
                    <h4>Quick Links</h4>
                    <ul class="list-unstyled">
                        <li><a href="#home" class="text-white">Home</a></li>
                        <li><a href="#features" class="text-white">Features</a></li>
                        <li><a href="#programs" class="text-white">Programs</a></li>
                        <li><a href="#testimonials" class="text-white">Testimonials</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h4>Connect With Us</h4>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>
            <hr class="mt-4">
            <div class="text-center">
                <p>&copy; 2024 FlexFusion. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AOS Animation Library -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Initialize AOS
        AOS.init({
            duration: 1000,
            once: true
        });

        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                document.querySelector('.navbar').style.padding = '0.5rem 2rem';
                document.querySelector('.navbar').style.backgroundColor = 'rgba(0, 0, 0, 0.95)';
            } else {
                document.querySelector('.navbar').style.padding = '1rem 2rem';
                document.querySelector('.navbar').style.backgroundColor = 'rgba(0, 0, 0, 0.9)';
            }
        });
    </script>
</body>
</html>
