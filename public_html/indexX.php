<?php
require 'config.php'; // contains $pdo connection and session_start()

// Generate CSRF token if not exists
if (empty($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}

// Helper to clean inputs
function clean($input)
{
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Variables
$errors = [];
$success = false;
$name = $company_name = $email = $phone = $address = $area = $city = $post_code = $country = $note = '';

date_default_timezone_set('Asia/Dhaka');

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF check
    if (!hash_equals($_SESSION['token'], $_POST['csrf_token'] ?? '')) {
        $errors['form'] = 'Invalid CSRF token. Please refresh the page.';
    }

    // Clean form data
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

    // Validation
    if (!$name) $errors['name'] = 'Name is required.';
    if (!$company_name) $errors['company_name'] = 'Company name is required.';
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Valid email is required.';
    if (!$phone) $errors['phone'] = 'Phone number is required.';
    if (!$address) $errors['address'] = 'Address is required.';
    if (!$area) $errors['area'] = 'Area is required.';
    if (!$city) $errors['city'] = 'City is required.';
    if (!$post_code) $errors['post_code'] = 'Post code is required.';
    if (!$country) $errors['country'] = 'Country is required.';

    // Save to database
    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO customers 
            (software, source, name, company_name, email, phone, address, area, city, post_code, country, note, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $res = $stmt->execute([
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

        if ($res) {
            $success = true;
            // Clear variables
            $name = $company_name = $email = $phone = $address = $area = $city = $post_code = $country = $note = '';
        } else {
            $errors['form'] = 'Something went wrong while saving. Please try again.';
        }
    }
}

// Optional: load slides and country list
$slides = $slides ?? [
    ['image' => 'images/slider/slide1.jpg', 'title' => 'Welcome to BidTrack', 'subtitle' => 'Streamline Tender Management with Confidence'],
    ['image' => 'images/slider/slide2.jpg', 'title' => 'Manage Tender', 'subtitle' => 'Simplify the Entire Tender Process'],
    ['image' => 'images/slider/slide3.jpg', 'title' => 'Live Dashboard', 'subtitle' => 'Get Real-Time Insights & Track Every Activity'],
];

$countries = [];
if (file_exists('countries.json')) {
    $countries = json_decode(file_get_contents('countries.json'), true) ?? [];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>BidTrack</title>

    <!-- Font Awesome CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="./style.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="images/logo.png">
</head>

<body>
    <div class="container">
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
                        <img src="images/logo.png" alt="Logo" class="d-none d-md-block" style="width: 32px; height: 32px;">
                        <span class="text-primary fw-bold fs-4">BidTrack</span>
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
            <div id="sliderCarousel" class="carousel slide rounded-4 shadow-lg" data-bs-ride="carousel" data-bs-interval="2000">
                <div class="carousel-inner">
                    <?php foreach ($slides as $idx => $slide): ?>
                        <div class="carousel-item <?php if ($idx === 0) echo 'active'; ?>"
                            style="background-image: url('<?= htmlspecialchars($slide['image']) ?>');">
                            <div class="carousel-gradient"></div>
                            <div class="carousel-caption text-white text-center px-3">
                                <div class="banner-caption-bg">
                                    <h1 class="display-4 fw-bold mb-2"><?= htmlspecialchars($slide['title']) ?></h1>
                                    <p class="lead mb-0"><?= htmlspecialchars($slide['subtitle']) ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Controls -->
                <button class="carousel-control-prev" type="button" data-bs-target="#sliderCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#sliderCarousel" data-bs-slide="next">
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
            <div class="text-center mb-4">
                <h2 class="fw-bold display-6 text-dark mb-2">
                    About TOTALOFFTEC
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

        <!-- Features Section -->
        <section id="feature" class="bg-white py-4 px-3 px-md-5">
            <h2 class="text-center fw-bold mb-5 display-6 text-dark">
                BidTrack Core Features
            </h2>

            <div class="row g-4">
                <!-- Feature 1 -->
                <div class="col-md-4">
                    <div class="feature-card bg-info bg-opacity-10 shadow-sm">
                        <div class="mb-3">
                            <i class="fas fa-tachometer-alt text-info fs-1"></i>
                        </div>
                        <h3 class="h5 fw-semibold mb-3 text-dark">
                            Speedy Process
                        </h3>
                        <p class="text-secondary small text-justify">
                            Our intelligent algorithms allow you to complete bidding evaluations in just minutes with
                            utmost accuracy.
                        </p>
                    </div>
                </div>

                <!-- Feature 2 -->
                <div class="col-md-4">
                    <div class="feature-card bg-info bg-opacity-10 shadow-sm">
                        <div class="mb-3">
                            <i class="fas fa-shield-alt text-info fs-1"></i>
                        </div>
                        <h3 class="h5 fw-semibold mb-3 text-dark">
                            Secure
                        </h3>
                        <p class="text-secondary small text-justify">
                            All your tender documents are encrypted with military-grade protection, ensuring privacy and
                            security.
                        </p>
                    </div>
                </div>

                <!-- Feature 3 -->
                <div class="col-md-4">
                    <div class="feature-card bg-indigo bg-opacity-10 shadow-sm">
                        <div class="mb-3">
                            <i class="fas fa-users text-primary fs-1"></i>
                        </div>
                        <h3 class="h5 fw-semibold mb-3 text-dark">
                            Team Collaboration
                        </h3>
                        <p class="text-secondary small text-justify">
                            Enable real-time collaboration with your evaluation team — from anywhere, anytime.
                        </p>
                    </div>
                </div>

                <!-- Feature 4 -->
                <div class="col-md-4">
                    <div class="feature-card bg-success bg-opacity-10 shadow-sm ">
                        <div class="mb-3">
                            <i class="fas fa-chart-line text-success fs-1"></i>
                        </div>
                        <h3 class="h5 fw-semibold mb-3 text-dark">
                            Real-time Analytics
                        </h3>
                        <p class="text-secondary small text-justify">
                            Monitor evaluation metrics live with detailed dashboards and customizable reports.
                        </p>
                    </div>
                </div>

                <!-- Feature 5 -->
                <div class="col-md-4">
                    <div class="feature-card bg-warning bg-opacity-10 shadow-sm">
                        <div class="mb-3">
                            <i class="fas fa-cogs text-warning fs-1"></i>
                        </div>
                        <h3 class="h5 fw-semibold mb-3 text-dark">
                            Customization
                        </h3>
                        <p class="text-secondary small text-justify">
                            Tailor the evaluation templates and workflow to perfectly fit your organization's needs.
                        </p>
                    </div>
                </div>

                <!-- Feature 6 -->
                <div class="col-md-4">
                    <div class="feature-card bg-warning bg-opacity-10 shadow-sm">
                        <div class="mb-3">
                            <i class="fas fa-headset text-warning fs-1"></i>
                        </div>
                        <h3 class="h5 fw-semibold mb-3 text-dark">
                            27/7 Support
                        </h3>
                        <p class="text-secondary small text-justify">
                            Our support team is always available to assist you at every stage of your tender evaluation
                            journey.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <section id="hardware" class="bg-white py-5 px-3 px-md-5">
            <h2 class="text-center fw-bold mb-5 display-6 text-dark">
                Software Main Features
            </h2>

            <div class="row g-5">
                <!-- Software Cards -->
                <!-- Repeatable Card Component -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-lg h-100 overflow-hidden rounded-4">
                        <div class="bg-white d-flex align-items-center justify-content-center p-3" style="height: 240px;">
                            <img src="images/software/dashboard.PNG" alt="Dashboard"
                                class="img-fluid" role="button" data-bs-toggle="modal" data-bs-target="#imageModal"
                                onclick="setModalImage(this.src)" style="object-fit: contain; max-height: 100%; max-width: 100%;">
                        </div>
                        <div class="card-body text-center px-4 py-3">
                            <h5 class="card-title fw-semibold text-dark">Dashboard</h5>
                            <p class="card-text text-secondary" style="text-align: justify;">
                                Get a real-time overview of all tenders—track submissions, status updates, deadlines, and performance insights from one centralized dashboard.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Repeat the same for other cards, changing title/image/text -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-lg h-100 overflow-hidden rounded-4">
                        <div class="bg-white d-flex align-items-center justify-content-center p-3" style="height: 240px;">
                            <img src="images/software/tender_list.PNG" alt="Tender Listings"
                                class="img-fluid" role="button" data-bs-toggle="modal" data-bs-target="#imageModal"
                                onclick="setModalImage(this.src)" style="object-fit: contain; max-height: 100%; max-width: 100%;">
                        </div>
                        <div class="card-body text-center px-4 py-3">
                            <h5 class="card-title fw-semibold text-dark">Tender Listings</h5>
                            <p class="card-text text-secondary" style="text-align: justify;">
                                Browse and manage all available tenders with advanced filters—view categories, publishing entities, deadlines, and requirements with ease.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Tender Participation -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-lg h-100 overflow-hidden rounded-4">
                        <div class="bg-white d-flex align-items-center justify-content-center p-3" style="height: 240px;">
                            <img src="images/software/tender_participated.PNG" alt="Tender Participation"
                                class="img-fluid" role="button" data-bs-toggle="modal" data-bs-target="#imageModal"
                                onclick="setModalImage(this.src)" style="object-fit: contain; max-height: 100%; max-width: 100%;">
                        </div>
                        <div class="card-body text-center px-4 py-3">
                            <h5 class="card-title fw-semibold text-dark">Tender Participation</h5>
                            <p class="card-text text-secondary" style="text-align: justify;">
                                Submit tenders directly through the system—upload documents, track status, get alerts for changes, and ensure compliance with digital logs.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Awarded Tenders -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-lg h-100 overflow-hidden rounded-4">
                        <div class="bg-white d-flex align-items-center justify-content-center p-3" style="height: 240px;">
                            <img src="images/software/tender_awarded.PNG" alt="Awarded Tenders"
                                class="img-fluid" role="button" data-bs-toggle="modal" data-bs-target="#imageModal"
                                onclick="setModalImage(this.src)" style="object-fit: contain; max-height: 100%; max-width: 100%;">
                        </div>
                        <div class="card-body text-center px-4 py-3">
                            <h5 class="card-title fw-semibold text-dark">Awarded Tenders</h5>
                            <p class="card-text text-secondary" style="text-align: justify;">
                                View and manage awarded tenders—track delivery timelines, manage agreements, and keep records of contract execution securely in one place.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bootstrap Modal for Image Zoom -->
            <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-xl">
                    <div class="modal-content bg-dark">
                        <div class="modal-header border-0">
                            <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-0">
                            <img id="modalImage" class="w-100 rounded-3" alt="Zoomed Image" />
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
                        <input type="text" name="name" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($name) ?>" maxlength="100" placeholder="Enter Full Name" required>
                        <?php if (isset($errors['name'])): ?><div class="invalid-feedback"><?= $errors['name'] ?></div><?php endif; ?>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Company Name <span class="text-danger">*</span></label>
                        <input type="text" name="company_name" class="form-control <?= isset($errors['company_name']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($company_name) ?>" maxlength="100" placeholder="Enter Company Name" required>
                        <?php if (isset($errors['company_name'])): ?><div class="invalid-feedback"><?= $errors['company_name'] ?></div><?php endif; ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($email) ?>" maxlength="100" placeholder="Enter Email" required>
                        <?php if (isset($errors['email'])): ?><div class="invalid-feedback"><?= $errors['email'] ?></div><?php endif; ?>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Phone <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control <?= isset($errors['phone']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($phone) ?>" maxlength="20" placeholder="Enter Phone" required>
                        <?php if (isset($errors['phone'])): ?><div class="invalid-feedback"><?= $errors['phone'] ?></div><?php endif; ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Address <span class="text-danger">*</span></label>
                        <input type="text" name="address" class="form-control <?= isset($errors['address']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($address) ?>" maxlength="255" placeholder="Enter Address" required>
                        <?php if (isset($errors['address'])): ?><div class="invalid-feedback"><?= $errors['address'] ?></div><?php endif; ?>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Area <span class="text-danger">*</span></label>
                        <input type="text" name="area" class="form-control <?= isset($errors['area']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($area) ?>" maxlength="100" placeholder="Ex: Uttara" required>
                        <?php if (isset($errors['area'])): ?><div class="invalid-feedback"><?= $errors['area'] ?></div><?php endif; ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">City <span class="text-danger">*</span></label>
                        <input type="text" name="city" class="form-control <?= isset($errors['city']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($city) ?>" maxlength="100" placeholder="Ex: Dhaka" required>
                        <?php if (isset($errors['city'])): ?><div class="invalid-feedback"><?= $errors['city'] ?></div><?php endif; ?>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Post Code <span class="text-danger">*</span></label>
                        <input type="text" name="post_code" class="form-control <?= isset($errors['post_code']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($post_code) ?>" maxlength="20" placeholder="Ex: 1201" required>
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
                        <textarea name="note" class="form-control" rows="3" maxlength="500"><?= htmlspecialchars($note) ?></textarea>
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill shadow-sm">Try for Demo</button>
                </div>
            </form>
        </section>

        <br>

        <footer class="text-center text-muted small py-3 border-top">
            &copy; <?= date('Y') ?> All Rights Reserved — BidTrack | Developed by
            <a href="https://totalofftec.com" target="_blank" class="text-decoration-none text-primary fw-semibold" rel="noopener noreferrer">
                TOTALOFFTEC
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