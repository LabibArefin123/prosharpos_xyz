<?php
require 'config.php'; // Contains $pdo and session_start()

// Generate CSRF token
if (empty($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}

// Init
$errors = [];
$success = false;

$name = $company_name = $email = $phone = $address = $area = $city = $post_code = $country = $note = $software = $source = '';
date_default_timezone_set('Asia/Dhaka');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['token'], $_POST['csrf_token'] ?? '')) {
        $errors['form'] = 'Invalid CSRF token.';
    }

    // Clean form fields
    $software     = clean($_POST['software'] ?? '');
    $source       = clean($_POST['source'] ?? '');
    $name         = clean($_POST['name'] ?? '');
    $company_name = clean($_POST['company_name'] ?? '');
    $email        = clean($_POST['email'] ?? '');
    $phone        = clean($_POST['phone'] ?? '');
    $address      = clean($_POST['address'] ?? '');
    $area         = clean($_POST['area'] ?? '');
    $city         = clean($_POST['city'] ?? '');
    $post_code    = clean($_POST['post_code'] ?? '');
    $country      = clean($_POST['country'] ?? '');
    $note         = clean($_POST['note'] ?? '');
    $now          = date('Y-m-d H:i:s');

    // Validate required fields
    if (!$name) $errors['name'] = 'Name is required.';
    if (!$company_name) $errors['company_name'] = 'Company Name is required.';
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'A valid Email is required.';
    if (!$phone) $errors['phone'] = 'Phone is required.';
    if (!$address) $errors['address'] = 'Address is required.';
    if (!$area) $errors['area'] = 'Area is required.';
    if (!$city) $errors['city'] = 'City is required.';
    if (!$post_code) $errors['post_code'] = 'Post Code is required.';
    if (!$country) $errors['country'] = 'Country is required.';

    // Insert into DB if no errors
    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO customers (
            software, source, name, company_name, email, phone,
            address, area, city, post_code, country, note,
            created_at, updated_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $success = $stmt->execute([
            $software,
            $source,
            $name,
            $company_name,
            $email,
            $phone,
            $address,
            $area,
            $city,
            $post_code,
            $country,
            $note,
            $now,
            $now
        ]);

        if ($success) {
            $name = $company_name = $email = $phone = $address = $area = $city = $post_code = $country = $note = $software = $source = '';
        } else {
            $errors['form'] = 'Failed to save data. Please try again.';
        }
    }
}

// Load countries from JSON
$countries = file_exists('countries.json') ? json_decode(file_get_contents('countries.json'), true) : [];

// Default slides (optional)
$slides = [
    [
        'image' => 'images/slider/slide1.jpg',
        'title' => 'Welcome to BidTrack',
        'subtitle' => 'Streamline Tender Management with Confidence'
    ],
    [
        'image' => 'images/slider/slide2.jpg',
        'title' => 'Manage Tender',
        'subtitle' => 'Simplify the Entire Tender Process'
    ],
    [
        'image' => 'images/slider/slide3.jpg',
        'title' => 'Live Dashboard',
        'subtitle' => 'Get Real-Time Insights & Track Every Activity'
    ]
];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>BidTrack – Your Tender Management Partner</title>

    <!-- Primary Meta Tags -->
    <meta name="description" content="BidTrack – Smart tender tracking software designed for efficient tender participation and project management. Boost your success with BidTrack.">
    <meta name="keywords" content="bidtrack, tender tracking, tender management, e-tender software, tender dashboard, bidding software, tender software bangladesh">
    <meta name="author" content="BidTrack Team">
    <meta name="robots" content="index, follow">

    <!-- Canonical URL -->
    <link rel="canonical" href="https://www.bidtrack.xyz/" />

    <!-- Open Graph (Facebook, LinkedIn) -->
    <meta property="og:title" content="BidTrack – Smart Tender Management Software">
    <meta property="og:description" content="Simplify and manage your tender participation workflow with BidTrack.">
    <meta property="og:image" content="https://www.bidtrack.xyz/images/seo-banner.jpg">
    <meta property="og:url" content="https://www.bidtrack.xyz/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="BidTrack">
    <meta property="og:updated_time" content="2025-07-31T12:00:00+00:00">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="BidTrack – Your Tender Management Partner">
    <meta name="twitter:description" content="Track tenders, manage participation, and win bids with BidTrack.">
    <meta name="twitter:image" content="https://www.bidtrack.xyz/images/seo-banner.jpg">

    <!-- JSON-LD Structured Data (SoftwareApplication Schema) -->
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "SoftwareApplication",
            "name": "BidTrack",
            "url": "https://www.bidtrack.xyz/",
            "image": "https://www.bidtrack.xyz/images/seo-banner.jpg",
            "description": "BidTrack – Smart tender tracking software designed for efficient tender participation and project management.",
            "applicationCategory": "BusinessApplication",
            "operatingSystem": "Web",
            "author": {
                "@type": "Organization",
                "name": "BidTrack Team"
            }
        }
    </script>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="https://www.bidtrack.xyz/images/bidtrack_logo.png">

    <!-- Bootstrap & Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />

    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="./style.css">
    <link rel="sitemap" type="application/xml" title="Sitemap" href="https://bidtrack.xyz/sitemap.xml">
    <link rel="alternate" type="application/rss+xml" title="RSS" href="https://bidtrack.xyz/feed.xml">
    <link rel="robots" href="/robots.txt">
</head>

<body>
    <div class="container-fluid">
        <section id="header" class="sticky-top bg-white shadow-sm">
            <!-- Top Bar -->
            <div
                class="top-bar d-flex justify-content-between align-items-center bg-opacity-25 border-bottom border-secondary py-2 px-3 mb-1 small text-secondary">
                <div class="d-flex gap-3">
                    <a href="mailto:bidtrack@totalofftec.com"
                        class="text-decoration-none text-secondary d-flex align-items-center gap-1">
                        <i class="bi bi-envelope"></i> <span class="d-none d-sm-inline">bidtrack@totalofftec.com</span>
                    </a>
                    <a href="tel:+8809643111222"
                        class="text-decoration-none text-secondary d-flex align-items-center gap-1">
                        <i class="bi bi-telephone"></i> <span class="d-none d-sm-inline">+8809643111222</span>
                    </a>
                    <a href="https://totalofftec.com" target="_blank"
                        class="text-decoration-none text-secondary d-flex align-items-center gap-1">
                        <i class="bi bi-globe2"></i> <span class="d-none d-sm-inline">totalofftec.com</span>
                    </a>
                    <a href="https://maps.app.goo.gl/JGxPSh5HZnbuUK6H7" target="_blank"
                        class="text-decoration-none text-secondary d-flex align-items-center gap-1">
                        <i class="bi bi-geo-alt"></i> <span class="d-none d-sm-inline">Location</span>
                    </a>
                </div>
                <a href="mailto:timetrack@totalofftec.com"
                    class="btn btn-primary btn-sm rounded-circle d-flex align-items-center justify-content-center"
                    style="width: 36px; height: 36px;">
                    <i class="bi bi-envelope-fill"></i>
                </a>
            </div>

            <!-- Navbar -->
            <nav class="navbar navbar-expand-md navbar-light bg-white border-bottom shadow-sm mb-4 px-2">
                <div class="container-fluid px-0">
                    <a class="navbar-brand d-flex align-items-center gap-2" href="#">
                        <img src="images/logo.png" alt="Logo" class="d-none d-md-block" style="width: 200px; height: 6  0px;">
                        <!-- <span class="text-primary fw-bold fs-4">BidTrack</span> -->
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar"
                        aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="mainNavbar">
                        <ul class="navbar-nav ms-auto mb-2 mb-md-0 fw-semibold">
                            <li class="nav-item"><a class="nav-link" href="#">Home</a></li>
                            <li class="nav-item separator d-flex align-items-center px-1">|</li>
                            <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
                            <li class="nav-item separator d-flex align-items-center px-1">|</li>
                            <li class="nav-item"><a class="nav-link" href="#feature">Product Feature</a></li>
                            <li class="nav-item separator d-flex align-items-center px-1">|</li>
                            <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
                        </ul>
                    </div>
                </div>
            </nav>
        </section>

        <!-- Banner -->
        <section id="banner">
            <div id="sliderCarousel" class="carousel slide rounded-4 shadow-lg" data-bs-ride="carousel"
                data-bs-interval="8000">
                <div class="carousel-inner">

                    <?php foreach ($slides as $idx => $slide): ?>
                        <div class="carousel-item <?php if ($idx === 0) echo 'active'; ?>"
                            style="background-image: url('<?= htmlspecialchars($slide['image']) ?>');">
                            <div class="carousel-gradient"></div>
                            <div class="carousel-caption caption-overlay text-white text-center px-3">
                                <h2 class="display-4 fw-bold"><?= htmlspecialchars($slide['title']) ?></h2>
                                <p class="lead"><?= htmlspecialchars($slide['subtitle']) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Controls -->
                <button class="carousel-control-prev" type="button" data-bs-target="#sliderCarousel"
                    data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#sliderCarousel"
                    data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>

                <!-- Indicators -->
                <div class="carousel-indicators mt-3">
                    <?php foreach ($slides as $idx => $_): ?>
                        <button type="button" data-bs-target="#sliderCarousel" data-bs-slide-to="<?= $idx ?>"
                            <?php if ($idx === 0) echo 'class="active" aria-current="true"'; ?>
                            aria-label="Slide <?= $idx + 1 ?>"></button>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <br>

        <!-- About Section -->
        <section id="about" class="bg-white py-5 px-3 px-md-4">
            <div class="text-center mb-5">
                <h1 class="text-center fw-bold display-6">
                    About
                    <span class="text-bidtrack-blue">Bid</span><span class="text-bidtrack-green">Track</span>
                </h1>
                <p class="fs-5 text-primary fst-italic">Your Centralized Platform for Smarter Tender Management</p>
            </div>

            <div class="text-secondary fs-6 lh-lg mx-auto" style="max-width: 1140px; padding: 0 1rem; text-align: justify;">
                <p>
                    <span class="fw-bold bidtrack-hover">BidTrack</span> is a powerful, web-based solution built for professionals navigating complex procurement landscapes. By seamlessly unifying bid tracking, <strong>document management, team collaboration, and evaluation</strong>, it empowers smarter decisions and drives greater success for Bidders.

                    With smart analytics, deadline tracking, and a seamless user experience, <span class="fw-bold bidtrack-hover">BidTrack</span> transforms bidding into your ultimate strategic advantage.
                </p>

                <h3 class="fw-semibold text-primary my-4">Bid Smarter, Move Faster.</h3>

                <p>BidTrack offers:</p>
                <ul class="list-unstyled fs-6 mb-4" style="max-width: 350px;">
                    <li>✅ <strong>Real-Time Collaboration</strong></li>
                    <li>✅ <strong>Smart Ranking Analytics</strong></li>
                    <li>✅ <strong>Automated Reporting</strong></li>
                    <li>✅ <strong>Full Visibility &amp; Control</strong></li>
                </ul>

                <p>
                    Take control of your bids and <strong>improve your win rates</strong> — <span class="fw-bold bidtrack-hover">BidTrack</span> makes it simple.
                    <br><br>
                    <em class="fw-semibold fs-5 text-dark">Track. Manage. Win. With <span class="bidtrack-hover">BidTrack</span>.</em>
                </p>

                <p>
                    From offer submission and financial documentation to cross-departmental collaboration, <span class="bidtrack-hover">BidTrack</span> provides complete visibility, operational control, and actionable insights at every stage. Its dynamic features empower organizations to stay competitive and agile in fast-paced bidding environments.
                </p>

                <p class="fw-semibold text-dark mt-4">
                    Trusted by procurement-driven enterprises, <span class="bidtrack-hover">BidTrack</span> enables smarter bid strategies, reduces operational risks, and enhances win rates through a seamless and intelligent digital experience.
                </p>
            </div>
        </section>

        <style>
            .text-bidtrack-blue {
                color: #16a5b1ff;
            }

            .text-bidtrack-green {
                color: #00B894;
            }

            .bidtrack-hover {
                color: #212529;
                /* normal text color */
                transition: color 0.3s ease;
                cursor: default;
            }

            .bidtrack-hover:hover {
                color: #2C7BE5;
                /* About's blue color on hover */
            }
        </style>

        <br>

        <!-- Features Section -->
        <section id="feature" class="bg-white py-5 px-3 px-md-5">
            <div class="container">
                <h2 class="text-center fw-bold mb-5 display-6 text-dark">
                    BidTrack Core Features
                </h2>

                <div class="row g-4 justify-content-center">

                    <!-- Feature 1 -->
                    <div class="col-6 col-md-6 col-lg-4">
                        <div class="bg-info bg-opacity-10 shadow-sm p-4 rounded-4 h-100 text-center">
                            <div class="mb-3">
                                <i class="fas fa-tachometer-alt text-info fs-1"></i>
                            </div>
                            <h3 class="h5 fw-semibold mb-3 text-dark">Speedy Process</h3>
                            <p class="text-secondary small">
                                Our intelligent algorithms allow you to complete bidding evaluations in just minutes with utmost accuracy.
                            </p>
                        </div>
                    </div>

                    <!-- Feature 2 -->
                    <div class="col-6 col-md-6 col-lg-4">
                        <div class="bg-primary bg-opacity-10 shadow-sm p-4 rounded-4 h-100 text-center">
                            <div class="mb-3">
                                <i class="fas fa-shield-alt text-primary fs-1"></i>
                            </div>
                            <h3 class="h5 fw-semibold mb-3 text-dark">Secure</h3>
                            <p class="text-secondary small">
                                All your tender documents are encrypted with military-grade protection, ensuring privacy and security.
                            </p>
                        </div>
                    </div>

                    <!-- Feature 3 -->
                    <div class="col-6 col-md-6 col-lg-4">
                        <div class="bg-success bg-opacity-10 shadow-sm p-4 rounded-4 h-100 text-center">
                            <div class="mb-3">
                                <i class="fas fa-users text-success fs-1"></i>
                            </div>
                            <h3 class="h5 fw-semibold mb-3 text-dark">Team Collaboration</h3>
                            <p class="text-secondary small">
                                Enable real-time collaboration with your evaluation team — from anywhere, anytime.
                            </p>
                        </div>
                    </div>

                    <!-- Feature 4 -->
                    <div class="col-6 col-md-6 col-lg-4">
                        <div class="bg-danger bg-opacity-10 shadow-sm p-4 rounded-4 h-100 text-center">
                            <div class="mb-3">
                                <i class="fas fa-chart-line text-danger fs-1"></i>
                            </div>
                            <h3 class="h5 fw-semibold mb-3 text-dark">Real-time Analytics</h3>
                            <p class="text-secondary small">
                                Monitor evaluation metrics live with detailed dashboards and customizable reports.
                            </p>
                        </div>
                    </div>

                    <!-- Feature 5 -->
                    <div class="col-6 col-md-6 col-lg-4">
                        <div class="bg-warning bg-opacity-10 shadow-sm p-4 rounded-4 h-100 text-center">
                            <div class="mb-3">
                                <i class="fas fa-cogs text-warning fs-1"></i>
                            </div>
                            <h3 class="h5 fw-semibold mb-3 text-dark">Customization</h3>
                            <p class="text-secondary small">
                                Tailor the evaluation templates and workflow to perfectly fit your organization's needs.
                            </p>
                        </div>
                    </div>

                    <!-- Feature 6 -->
                    <div class="col-6 col-md-6 col-lg-4">
                        <div class="shadow-sm p-4 rounded-4 h-100 text-center" style="background-color: rgba(102, 16, 242, 0.1);">
                            <div class="mb-3">
                                <i class="fas fa-headset fs-1" style="color: #6610f2;"></i>
                            </div>
                            <h3 class="h5 fw-semibold mb-3 text-dark">24/7 Support</h3>
                            <p class="text-secondary small">
                                Our support team is always available to assist you at every stage of your tender evaluation journey.
                            </p>
                        </div>
                    </div>

                </div>
            </div>
        </section>


        <br>

        <!-- Hardware / More Insights Section -->
        <section id="hardware" class="bg-white py-5 px-3 px-md-5">
            <h2 class="text-center fw-bold mb-2 display-6 text-dark">More Insights</h2>
            <p class="text-center text-muted mb-5 fs-6">
                Explore the modules that drive your bidding success.
            </p>

            <div class="row g-5">
                <!-- Dashboard -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-lg h-100 overflow-hidden rounded-4">
                        <div class="card-img-hover bg-white d-flex align-items-center justify-content-center p-3" style="height: 240px;">
                            <img src="images/software/dashboard.PNG" alt="Dashboard"
                                class="img-fluid" role="button"
                                data-bs-toggle="modal" data-bs-target="#imageModal"
                                onclick="setModalImage(this.src)"
                                style="object-fit: contain; max-height: 100%; max-width: 100%;">
                        </div>
                        <div class="card-body text-center px-4 py-3">
                            <h5 class="card-title fw-semibold text-dark">Dashboard</h5>
                            <p class="card-text text-secondary text-justify">
                                Get a real-time overview of all tenders—track submissions, status updates, deadlines, and performance insights from one centralized dashboard.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Tender Listings -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-lg h-100 overflow-hidden rounded-4">
                        <div class="card-img-hover bg-white d-flex align-items-center justify-content-center p-3" style="height: 240px;">
                            <img src="images/software/tender_list.PNG" alt="Tender Listings"
                                class="img-fluid" role="button"
                                data-bs-toggle="modal" data-bs-target="#imageModal"
                                onclick="setModalImage(this.src)"
                                style="object-fit: contain; max-height: 100%; max-width: 100%;">
                        </div>
                        <div class="card-body text-center px-4 py-3">
                            <h5 class="card-title fw-semibold text-dark">Tender Listings</h5>
                            <p class="card-text text-secondary text-justify">
                                Browse and manage all available tenders with advanced filters—view categories, publishing entities, deadlines, and requirements with ease.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Tender Participation -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-lg h-100 overflow-hidden rounded-4">
                        <div class="card-img-hover bg-white d-flex align-items-center justify-content-center p-3" style="height: 240px;">
                            <img src="images/software/tender_participated.PNG" alt="Tender Participation"
                                class="img-fluid" role="button"
                                data-bs-toggle="modal" data-bs-target="#imageModal"
                                onclick="setModalImage(this.src)"
                                style="object-fit: contain; max-height: 100%; max-width: 100%;">
                        </div>
                        <div class="card-body text-center px-4 py-3">
                            <h5 class="card-title fw-semibold text-dark">Tender Participation</h5>
                            <p class="card-text text-secondary text-justify">
                                Submit tenders directly through the system—upload documents, track status, get alerts for changes, and ensure compliance with digital logs.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Awarded Tenders -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-lg h-100 overflow-hidden rounded-4">
                        <div class="card-img-hover bg-white d-flex align-items-center justify-content-center p-3" style="height: 240px;">
                            <img src="images/software/tender_awarded.PNG" alt="Awarded Tenders"
                                class="img-fluid" role="button"
                                data-bs-toggle="modal" data-bs-target="#imageModal"
                                onclick="setModalImage(this.src)"
                                style="object-fit: contain; max-height: 100%; max-width: 100%;">
                        </div>
                        <div class="card-body text-center px-4 py-3">
                            <h5 class="card-title fw-semibold text-dark">Awarded Tenders</h5>
                            <p class="card-text text-secondary text-justify">
                                View and manage awarded tenders—track delivery timelines, manage agreements, and keep records of contract execution securely in one place.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bootstrap Modal for Zoomed Image -->
            <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-fullscreen"> <!-- fullscreen for all devices -->
                    <div class="modal-content border-0" style="background-color: transparent; box-shadow: none;">

                        <div class="modal-header border-0 p-2">
                            <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-0 d-flex justify-content-center align-items-center">
                            <img id="modalImage"
                                class="w-100 h-100"
                                alt="Zoomed Image"
                                style="object-fit: contain; touch-action: pinch-zoom; cursor: zoom-out;"
                                onclick="document.querySelector('#imageModal .btn-close').click()">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Script to Set Modal Image -->
            <script>
                function setModalImage(src) {
                    document.getElementById('modalImage').src = src;
                }
            </script>

            <!-- Optional Styling -->
            <style>
                .card-img-hover img {
                    transition: transform 0.4s ease;
                }

                .card-img-hover:hover img {
                    transform: scale(1.05);
                }

                /* Remove border-radius and spacing on small and large devices */
                #modalImage {
                    border-radius: 0 !important;
                }

                .modal-body {
                    padding: 0 !important;
                }

                /* Optional: Remove scrollbar flash from fullscreen modal */
                .modal-content {
                    overflow: hidden;
                }
            </style>

        </section>
        <br>

        <section id="about" class="bg-white py-5 px-3 px-md-4">
            <style>
                @font-face {
                    font-family: 'OnStage';
                    src: url('fonts/OnStage_Regular.ttf') format('truetype');
                    font-weight: normal;
                    font-style: normal;
                }

                .onstage-text {
                    font-family: 'OnStage', sans-serif;
                    color: #ff9900;
                }

                .off-highlight {
                    color: #B2BEB5;
                }
            </style>

            <div class="text-center mb-4">
                <h2 class="text-center fw-bold display-6 text-dark">
                    About
                    <span class="onstage-text">
                        total<span class="off-highlight">offtec</span>
                    </span>
                </h2>

                <h3 class="fw-semibold fs-3 fs-md-2 text-dark">
                    Secure IT Services & Integrated Solutions
                </h3>
                <p class="mt-2 text-muted fs-6">
                    Leading Technology Solutions and System Integration Company
                </p>
            </div>

            <div class="text-justify text-secondary fs-6 lh-lg">
                <p>
                    <a href="https://totalofftec.com" target="_blank" rel="noopener noreferrer"
                        class="fw-semibold text-dark text-decoration-none" style="transition: color 0.3s;">
                        TOTALOFFTEC
                    </a>,
                    established in 2008, is committed to meeting each client's unique needs with fully integrated IT solutions.
                    Backed by industry experience and a skilled team, we aim to be your trusted ICT partner.
                    We are an ISO 9001:2015-certified company based in Dhaka, Bangladesh.
                </p>
                <p>
                    <a href="https://totalofftec.com" target="_blank" rel="noopener noreferrer"
                        class="fw-semibold text-dark text-decoration-none" style="transition: color 0.3s;">
                        TOTALOFFTEC
                    </a>
                    delivers innovative office technology, premium tailor-made software, and automation systems
                    to enhance workplace productivity and comfort. Our customised solutions empower businesses
                    to optimise operations and foster a more efficient, creative environment.
                </p>
            </div>
        </section>
        <br>

        <section id="contact">
            <h2>Get in Touch</h2>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    ✅ Contact form submitted successfully!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($errors['form'])): ?>
                <div class="alert alert-danger"><?= $errors['form'] ?></div>
            <?php endif; ?>

            <!-- <form action="" method="POST" novalidate> -->
            <form method="POST" action="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['token'] ?>">

                <input type="hidden" name="software" value="Bidtrack">
                <input type="hidden" name="source" value="Website">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($name) ?>" maxlength="100" placeholder="Write your full name" required>
                        <?php if (isset($errors['name'])): ?><div class="invalid-feedback"><?= $errors['name'] ?></div><?php endif; ?>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Company Name <span class="text-danger">*</span></label>
                        <input type="text" name="company_name" class="form-control <?= isset($errors['company_name']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($company_name) ?>" maxlength="100" placeholder="Write your company name" required>
                        <?php if (isset($errors['company_name'])): ?><div class="invalid-feedback"><?= $errors['company_name'] ?></div><?php endif; ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($email) ?>" maxlength="100" placeholder="Write your email" required>
                        <?php if (isset($errors['email'])): ?><div class="invalid-feedback"><?= $errors['email'] ?></div><?php endif; ?>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Phone <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control <?= isset($errors['phone']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($phone) ?>" maxlength="20" placeholder="Write your phone number" required>
                        <?php if (isset($errors['phone'])): ?><div class="invalid-feedback"><?= $errors['phone'] ?></div><?php endif; ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Address <span class="text-danger">*</span></label>
                        <input type="text" name="address" class="form-control <?= isset($errors['address']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($address) ?>" maxlength="255" placeholder="Write your address" required>
                        <?php if (isset($errors['address'])): ?><div class="invalid-feedback"><?= $errors['address'] ?></div><?php endif; ?>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Area <span class="text-danger">*</span></label>
                        <input type="text" name="area" class="form-control <?= isset($errors['area']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($area) ?>" maxlength="100" placeholder="Write your area, Ex: Uttara" required>
                        <?php if (isset($errors['area'])): ?><div class="invalid-feedback"><?= $errors['area'] ?></div><?php endif; ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">City <span class="text-danger">*</span></label>
                        <input type="text" name="city" class="form-control <?= isset($errors['city']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($city) ?>" maxlength="100" placeholder="Write your city, Ex: Dhaka" required>
                        <?php if (isset($errors['city'])): ?><div class="invalid-feedback"><?= $errors['city'] ?></div><?php endif; ?>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Post Code <span class="text-danger">*</span></label>
                        <input type="text" name="post_code" class="form-control <?= isset($errors['post_code']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($post_code) ?>" maxlength="20" placeholder="Write your post code, Ex: 1201" required>
                        <?php if (isset($errors['post_code'])): ?><div class="invalid-feedback"><?= $errors['post_code'] ?></div><?php endif; ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Country <span class="text-danger">*</span></label>
                        <select name="country" class="form-select <?= isset($errors['country']) ? 'is-invalid' : '' ?>" required>
                            <option value="">Select Country</option>
                            <?php foreach ($countries as $c): ?>
                                <option value="<?= htmlspecialchars($c) ?>" <?= $c === $country ? 'selected' : '' ?>><?= htmlspecialchars($c) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['country'])): ?><div class="invalid-feedback"><?= $errors['country'] ?></div><?php endif; ?>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Note</label>
                        <textarea name="note" class="form-control" rows="3" maxlength="500" placeholder="Write briefly about your requirement"><?= htmlspecialchars($note) ?></textarea>
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill shadow-sm">Try for Demo</button>
                </div>
            </form>
        </section>

        <br>

        <footer class="text-center text-muted small py-3 border-top">
            &copy;
            <a href="https://bidtrack.xyz" target="_blank"
                class="text-decoration-none text-primary fw-semibold"
                rel="noopener noreferrer">
                BidTrack
            </a>
            | Developed by
            <a href="https://totalofftec.com" target="_blank" rel="noopener noreferrer"
                class="text-decoration-none text-primary fw-semibold d-inline-block">
                <span class="onstage-text">
                    total<span class="off-highlight">offtec</span>
                </span>
            </a>
        </footer>

    </div>

    <!-- Bootstrap JS Bundle (Popper + Bootstrap JS) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

<?php if (!empty($success)): ?>
    <script>
        // Open Laravel login page in a new tab with pre-filled login values
        // window.open("http://192.168.2.47:8000/login?autologin=no&u=admin&p=AAaa00@@", "_blank");
        window.open("https://bidtrack.kazionline.com/login?autologin=no&u=admin&p=AAaa00@@", "_blank");
    </script>
<?php endif; ?>

</html>