<?php
require 'config.php'; // Contains $pdo and session_start()

// Generate CSRF token
if (empty($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}

// Init
$errors = [];
$success = false;

$name = $company_name = $email = $phone = $address = $area = $city = $post_code = $country = $note = $plan = $software = $source = '';
date_default_timezone_set('Asia/Dhaka');

// A simple clean function example (you may have your own)
function clean($data)
{
    return htmlspecialchars(trim($data), ENT_QUOTES);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['token'], $_POST['csrf_token'] ?? '')) {
        $errors['form'] = 'Invalid CSRF token.';
    } else {
        $form_type = $_POST['form_type'] ?? '';

        // Common fields cleaning
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
        $now          = date('Y-m-d H:i:s');

        // Validate common required fields
        if (!$name) $errors['name'] = 'Name is required.';
        if (!$company_name) $errors['company_name'] = 'Company Name is required.';
        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'A valid Email is required.';
        if (!$phone) $errors['phone'] = 'Phone is required.';
        if (!$address) $errors['address'] = 'Address is required.';
        if (!$area) $errors['area'] = 'Area is required.';
        if (!$city) $errors['city'] = 'City is required.';
        if (!$post_code) $errors['post_code'] = 'Post Code is required.';
        if (!$country) $errors['country'] = 'Country is required.';

        if ($form_type === 'customer') {
            // Contact form with note
            $note = clean($_POST['note'] ?? '');

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
                    // Clear fields
                    $name = $company_name = $email = $phone = $address = $area = $city = $post_code = $country = $note = $software = $source = '';
                } else {
                    $errors['form'] = 'Failed to save contact data. Please try again.';
                }
            }
        } elseif ($form_type === 'plan') {
            // Plan purchase form
            $plan = clean($_POST['price_plan'] ?? '');

            if (!$plan) {
                $errors['plan'] = 'Please select a price plan.';
            }

            if (empty($errors)) {
                $stmt = $pdo->prepare("INSERT INTO plans (
                    software, source, name, company_name, email, phone,
                    address, area, city, post_code, country, plan,
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
                    $plan,
                    $now,
                    $now
                ]);

                if ($success) {
                    // Clear fields
                    $name = $company_name = $email = $phone = $address = $area = $city = $post_code = $country = $plan = $software = $source = '';
                } else {
                    $errors['form'] = 'Failed to save subscription data. Please try again.';
                }
            }
        } else {
            $errors['form'] = 'Invalid form submission.';
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
    <title>BidTrack – Plans & Pricing</title>
    <meta name="description" content="Choose the right BidTrack tender management plan for your needs. Cancel anytime, with unlimited support and free guided setup.">
    <meta name="keywords" content="bidtrack plans, pricing, tender management software, tender tracking">
    <meta name="author" content="BidTrack Team">
    <meta name="robots" content="index, follow">

    <!-- <link rel="canonical" href="https://www.bidtrack.xyz/plan.php" /> -->
    <link rel="icon" type="image/png" href="https://www.bidtrack.xyz/images/icon.png">

    <!-- Bootstrap & Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />

    <style>
        body {
            background: #f9fafb;
            min-height: 100vh;
            padding: 1rem 1rem;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }

        /* ‣ Sticky Header */
        #header.sticky-top {
            top: 0;
            z-index: 1030;
        }

        .plan-card {
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            padding: 20px;
            background: #fff;
            box-shadow: 0px 3px 8px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s;
        }

        .plan-card:hover {
            transform: translateY(-5px);
        }

        .price-old {
            text-decoration: line-through;
            color: gray;
        }

        .price-new {
            font-size: 2rem;
            font-weight: bold;
        }

        .plan-btn {
            background-color: #198754;
            border: none;
            padding: 10px 0;
            font-size: 1rem;
            width: 100%;
        }

        .plan-features li {
            margin-bottom: 8px;
        }

        .plan-features i {
            color: green;
        }
    </style>
</head>

<body>
    <section id="header" class="sticky-top bg-white shadow-sm">

        <div class="top-bar d-flex justify-content-center align-items-center gap-3 bg-warning text-dark py-2 px-3">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-tag-fill fs-5"></i> <!-- Icon before SALE -->
                <strong>SALE</strong> — Buy now and save 30% off today
            </div>
            <a href="plan.php" class="btn btn-dark btn-sm rounded-pill px-3">
                See plans & pricing
            </a>
        </div>
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
                <a class="navbar-brand d-flex align-items-center gap-2" href="index.php">
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
                        <li class="nav-item"><a class="nav-link" href="#feature">Product Features</a></li>
                        <li class="nav-item separator d-flex align-items-center px-1">|</li>
                        <li class="nav-item"><a class="nav-link" href="#plan">Prices & Plans</a></li>
                        <li class="nav-item separator d-flex align-items-center px-1">|</li>
                        <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </section>

    <section class="py-5 bg-light" id="plan">
        <style>
            /* Section padding adjustment */
            #plan {
                padding-top: 3rem;
                padding-bottom: 3rem;
            }

            /* Increase side space inside container */
            #plan .container {
                max-width: 1100px;
                /* was default ~1140px on lg */
            }

            /* Make plan cards more roomy horizontally */
            .plan-card {
                border: 1px solid #e0e0e0;
                border-radius: 10px;
                padding: 25px;
                background: #fff;
                box-shadow: 0px 3px 8px rgba(0, 0, 0, 0.05);
                transition: transform 0.2s;
                height: 100%;
            }

            .plan-card:hover {
                transform: translateY(-5px);
            }

            .price-old {
                text-decoration: line-through;
                color: gray;
            }

            .price-new {
                font-size: 2rem;
                font-weight: bold;
            }

            .plan-btn {
                background-color: #198754;
                border: none;
                padding: 10px 0;
                font-size: 1rem;
                width: 100%;
            }

            .plan-features li {
                margin-bottom: 8px;
            }

            .plan-features i {
                color: #7d4dfe;
            }

            /* Mobile adjustments for section + cards */
            @media (max-width: 767px) {
                #plan {
                    padding-top: 1.5rem;
                    padding-bottom: 1.5rem;
                }

                .plan-card {
                    padding: 10px !important;
                }

                .price-new {
                    font-size: 1.5rem;
                }
            }
        </style>

        <div class="container text-center mb-5">
            <h1 class="fw-bold">Find a plan that's right for you</h1>
            <p class="text-muted">
                ⭐ Cancel anytime &nbsp; | &nbsp; 📞 Unlimited support &nbsp; | &nbsp; 🛠 Free guided setup
            </p>
        </div>

        <div class="container">
            <div class="row g-4">

                <!-- Plan 1 -->
                <div class="col-lg-3 col-md-6 col-sm-12">
                    <div class="plan-card text-center h-100">
                        <h4 class="fw-bold">Standard</h4>
                        <div class="mb-2"><span class="price-old">BDT 30,000</span></div>
                        <div class="price-new">BDT 10,000<span class="fs-6"> /yr</span></div>
                        <p class="text-success small">Add setup cost BDT 20,000 (one time)</p>
                        <a href="#"
                            class="btn btn-success plan-btn"
                            style="background-color:#7d4dfe; border-color:#7d4dfe; color:white;"
                            data-bs-toggle="modal"
                            data-bs-target="#contactModal"
                            data-plan="Standard">Select Plan</a>
                        <ul class="list-unstyled text-start mt-3 plan-features">
                            <li><i class="bi bi-check-circle-fill"></i> 10<span class="text-danger">**</span> GB Storage</li>
                            <li><i class="bi bi-check-circle-fill"></i> 100<span class="text-danger">**</span> Tenders</li>
                            <li><i class="bi bi-check-circle-fill"></i> Instant Activation</li>
                            <li><i class="bi bi-check-circle-fill"></i> Any Time Upgradation</li>
                            <li><i class="bi bi-check-circle-fill"></i> 24/7 Support</li>
                        </ul>
                    </div>
                </div>

                <!-- Plan 2 -->
                <div class="col-lg-3 col-md-6 col-sm-12">
                    <div class="plan-card text-center h-100">
                        <h4 class="fw-bold">Professional</h4>
                        <div class="mb-2"><span class="price-old">BDT 35,000</span></div>
                        <div class="price-new">BDT 15,000<span class="fs-6"> /yr</span></div>
                        <p class="text-success small">Add setup cost BDT 20,000 (one time)</p>
                        <a href="#"
                            class="btn btn-success plan-btn"
                            style="background-color:#7d4dfe; border-color:#7d4dfe; color:white;"
                            data-bs-toggle="modal"
                            data-bs-target="#contactModal"
                            data-plan="Professional">Select Plan</a>
                        <ul class="list-unstyled text-start mt-3 plan-features">
                            <li><i class="bi bi-check-circle-fill"></i> 25<span class="text-danger">**</span> GB Storage</li>
                            <li><i class="bi bi-check-circle-fill"></i> 250<span class="text-danger">**</span> Tenders</li>
                            <li><i class="bi bi-check-circle-fill"></i> Instant Activation</li>
                            <li><i class="bi bi-check-circle-fill"></i> Any Time Upgradation</li>
                            <li><i class="bi bi-check-circle-fill"></i> 24/7 Support</li>
                        </ul>
                    </div>
                </div>

                <!-- Plan 3 -->
                <div class="col-lg-3 col-md-6 col-sm-12">
                    <div class="plan-card text-center h-100">
                        <h4 class="fw-bold">Premium</h4>
                        <div class="mb-2"><span class="price-old">BDT 45,000</span></div>
                        <div class="price-new">BDT 25,000<span class="fs-6"> /yr</span></div>
                        <p class="text-success small">Add setup cost BDT 20,000 (one time)</p>
                        <a href="#"
                            class="btn btn-success plan-btn"
                            style="background-color:#7d4dfe; border-color:#7d4dfe; color:white;"
                            data-bs-toggle="modal"
                            data-bs-target="#contactModal"
                            data-plan="Premium">Select Plan</a>
                        <ul class="list-unstyled text-start mt-3 plan-features">
                            <li><i class="bi bi-check-circle-fill"></i> 100<span class="text-danger">**</span> GB Storage</li>
                            <li><i class="bi bi-check-circle-fill"></i> 500<span class="text-danger">**</span> Tenders</li>
                            <li><i class="bi bi-check-circle-fill"></i> Instant Activation</li>
                            <li><i class="bi bi-check-circle-fill"></i> Any Time Upgradation</li>
                            <li><i class="bi bi-check-circle-fill"></i> 24/7 Support</li>
                        </ul>
                    </div>
                </div>

                <!-- Plan 4 -->
                <div class="col-lg-3 col-md-6 col-sm-12">
                    <div class="plan-card text-center h-100">
                        <h4 class="fw-bold">Premium Plus</h4>
                        <div class="mb-2"><span class="price-old">BDT 70,000</span></div>
                        <div class="price-new">BDT 50,000<span class="fs-6"> /yr</span></div>
                        <p class="text-success small">Add setup cost BDT 20,000 (one time)</p>
                        <a href="#"
                            class="btn plan-btn"
                            style="background-color:#7d4dfe; border-color:#7d4dfe; color:white;"
                            data-bs-toggle="modal"
                            data-bs-target="#contactModal"
                            data-plan="Premium Plus">Select Plan</a>
                        <ul class="list-unstyled text-start mt-3 plan-features">
                            <li><i class="bi bi-check-circle-fill"></i> 500<span class="text-danger">**</span> GB Storage</li>
                            <li><i class="bi bi-check-circle-fill"></i> 1000<span class="text-danger">**</span> Tenders</li>
                            <li><i class="bi bi-check-circle-fill"></i> Instant Activation</li>
                            <li><i class="bi bi-check-circle-fill"></i> Any Time Upgradation</li>
                            <li><i class="bi bi-check-circle-fill"></i> 24/7 Support</li>
                        </ul>
                    </div>
                </div>
                <h4 style="text-align: center;"><span class="text-danger">**</span>Which one comes first<span class="text-danger">**</span></h4>

            </div>

        </div>
    </section>

    <div class="modal fade" id="contactModal" tabindex="-1" aria-labelledby="contactModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content p-4">

                <!-- Modal Header -->
                <div class="modal-header border-0 pb-0 d-flex align-items-center justify-content-between position-relative">
                    <h2 class="modal-title fw-bold text-primary flex-grow-1 text-center m-0" id="contactModalLabel">
                        Buy Your Plan
                    </h2>
                    <span class="custom-close" data-close>&times;</span>
                </div>

                <style>
                    /* Close button styles */
                    .custom-close {
                        cursor: pointer;
                        font-size: 1.5rem;
                        font-weight: bold;
                        color: #333;
                        padding: 0 8px;
                        line-height: 1;
                        transition: color 0.2s ease;
                    }

                    .custom-close:hover {
                        color: #ff9900;
                    }

                    /* Modal dialog width and side padding */
                    .modal-dialog.modal-xl {
                        max-width: 90%;
                        /* slightly narrower for more side space */
                    }

                    /* Responsive mobile styles */
                    @media (max-width: 575.98px) {
                        .modal-dialog {
                            max-width: 95vw;
                            margin: 1.5rem auto;
                            padding: 0 1rem;
                            /* side padding */
                        }

                        .modal-content {
                            padding: 1.5rem 1.5rem !important;
                            /* increased padding */
                        }

                        /* Stack modal header flex items */
                        .modal-header {
                            flex-direction: row;
                        }

                        .modal-header h2 {
                            font-size: 1.5rem;
                        }

                        .custom-close {
                            position: absolute;
                            right: 1rem;
                            top: 1rem;
                            padding: 0;
                        }

                        /* Make form controls full width and bigger */
                        .modal-body form .form-control,
                        .modal-body form .form-select {
                            width: 100% !important;
                            padding: 0.75rem 1rem !important;
                            font-size: 1.1rem;
                        }

                        /* Fix form row gutters */
                        .modal-body form .row {
                            margin-left: -0.5rem;
                            margin-right: -0.5rem;
                        }

                        .modal-body form .row>[class*="col-"] {
                            padding-left: 0.5rem;
                            padding-right: 0.5rem;
                        }

                        /* Larger submit button for easy tapping */
                        .modal-body form button[type="submit"] {
                            padding: 1rem 2.5rem;
                            font-size: 1.2rem;
                        }
                    }

                    /* Adjust input sizes on small tablets */
                    @media (max-width: 767.98px) {

                        .form-control-lg,
                        .form-select-lg {
                            font-size: 1rem;
                            padding: 0.5rem 0.75rem;
                        }
                    }
                </style>

                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        const modal = document.getElementById("contactModal");
                        const closeBtn = modal.querySelector(".custom-close");

                        closeBtn.addEventListener("click", function() {
                            // Hide modal
                            modal.style.display = "none";
                            modal.classList.remove("show");
                            document.body.classList.remove("modal-open");

                            // Remove Bootstrap's backdrop
                            const backdrop = document.querySelector('.modal-backdrop');
                            if (backdrop) {
                                backdrop.remove();
                            }

                            // Allow page scrolling again
                            document.body.style.overflow = "";
                        });
                    });
                </script>

                <!-- Modal Body -->
                <div class="modal-body pt-2">
                    <form method="POST" action="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['token'] ?>">
                        <input type="hidden" name="software" value="Bidtrack">
                        <input type="hidden" name="source" value="Website">
                        <input type="hidden" name="form_type" value="plan">

                        <div class="row g-4">
                            <!-- Full Name -->
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-bold">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control form-control-lg <?= isset($errors['name']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($name) ?>" maxlength="100" placeholder="Write your full name" required>
                                <?php if (isset($errors['name'])): ?><div class="invalid-feedback"><?= $errors['name'] ?></div><?php endif; ?>
                            </div>

                            <!-- Company Name -->
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-bold">Company Name <span class="text-danger">*</span></label>
                                <input type="text" name="company_name" class="form-control form-control-lg <?= isset($errors['company_name']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($company_name) ?>" maxlength="100" placeholder="Write your company name" required>
                                <?php if (isset($errors['company_name'])): ?><div class="invalid-feedback"><?= $errors['company_name'] ?></div><?php endif; ?>
                            </div>

                            <!-- Email -->
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control form-control-lg <?= isset($errors['email']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($email) ?>" maxlength="100" placeholder="Write your email" required>
                                <?php if (isset($errors['email'])): ?><div class="invalid-feedback"><?= $errors['email'] ?></div><?php endif; ?>
                            </div>

                            <!-- Phone -->
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-bold">Phone <span class="text-danger">*</span></label>
                                <input type="text" name="phone" class="form-control form-control-lg <?= isset($errors['phone']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($phone) ?>" maxlength="20" placeholder="Write your phone number" required>
                                <?php if (isset($errors['phone'])): ?><div class="invalid-feedback"><?= $errors['phone'] ?></div><?php endif; ?>
                            </div>

                            <!-- Address -->
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-bold">Address <span class="text-danger">*</span></label>
                                <input type="text" name="address" class="form-control form-control-lg <?= isset($errors['address']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($address) ?>" maxlength="255" placeholder="Write your address" required>
                                <?php if (isset($errors['address'])): ?><div class="invalid-feedback"><?= $errors['address'] ?></div><?php endif; ?>
                            </div>

                            <!-- Area -->
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-bold">Area <span class="text-danger">*</span></label>
                                <input type="text" name="area" class="form-control form-control-lg <?= isset($errors['area']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($area) ?>" maxlength="100" placeholder="Write your area, Ex: Uttara" required>
                                <?php if (isset($errors['area'])): ?><div class="invalid-feedback"><?= $errors['area'] ?></div><?php endif; ?>
                            </div>

                            <!-- City -->
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-bold">City <span class="text-danger">*</span></label>
                                <input type="text" name="city" class="form-control form-control-lg <?= isset($errors['city']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($city) ?>" maxlength="100" placeholder="Write your city, Ex: Dhaka" required>
                                <?php if (isset($errors['city'])): ?><div class="invalid-feedback"><?= $errors['city'] ?></div><?php endif; ?>
                            </div>

                            <!-- Post Code -->
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-bold">Post Code <span class="text-danger">*</span></label>
                                <input type="text" name="post_code" class="form-control form-control-lg <?= isset($errors['post_code']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($post_code) ?>" maxlength="20" placeholder="Write your post code, Ex: 1201" required>
                                <?php if (isset($errors['post_code'])): ?><div class="invalid-feedback"><?= $errors['post_code'] ?></div><?php endif; ?>
                            </div>

                            <!-- Country -->
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-bold">Country <span class="text-danger">*</span></label>
                                <select name="country" class="form-select form-select-lg" required>
                                    <option value="">Select Country</option>
                                    <?php foreach ($countries as $c): ?>
                                        <option value="<?= htmlspecialchars($c) ?>" <?= $c === $country ? 'selected' : '' ?>><?= htmlspecialchars($c) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Price Plan -->
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-bold">Price Plan <span class="text-danger">*</span></label>
                                <select name="price_plan" id="pricePlanSelect" class="form-select form-select-lg" required>
                                    <option value="">Select a Plan</option>
                                    <option value="Standard">Standard - BDT 10,000 /yr + 20000 BDT setup cost</option>
                                    <option value="Professional">Professional - BDT 15,000 /yr + 20000 BDT setup cost</option>
                                    <option value="Premium">Premium - BDT 25,000 /yr + 20000 BDT setup cost</option>
                                    <option value="Premium Plus">Premium Plus - BDT 50,000 /yr + 20000 BDT setup cost</option>
                                </select>
                                <small id="totalCostDisplay" class="form-text text-muted mt-2"></small>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="text-center mt-4">
                            <button type="submit" class="btn px-5 py-3 rounded-pill shadow-sm" style="background-color:#7d4dfe; border-color:#7d4dfe; color:white;">
                                Submit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    </div>

    <footer class="text-center text-muted small py-3 border-top">
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
        &copy;
        <a href="https://bidtrack.xyz" target="_blank"
            class="text-decoration-none text-primary fw-semibold"
            rel="noopener noreferrer">
            BidTrack.
        </a> All rights reserved
        | Design and Developed by
        <a href="https://totalofftec.com" target="_blank" rel="noopener noreferrer"
            class="text-decoration-none text-primary fw-semibold d-inline-block">
            <span class="onstage-text">
                total<span class="off-highlight">offtec</span>
            </span>
        </a>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const contactModalEl = document.getElementById("contactModal");
            if (!contactModalEl) return;

            const modalContent = contactModalEl.querySelector(".modal-content");
            const planSelect = document.getElementById("pricePlanSelect");
            const totalCostDisplay = document.getElementById("totalCostDisplay");

            const setupCost = 20000; // fixed setup cost BDT 20,000

            // Plan prices mapping
            const planPrices = {
                "Standard": 10000,
                "Professional": 15000,
                "Premium": 25000,
                "Premium Plus": 50000
            };

            function formatTotalCost(amount) {
                return amount.toLocaleString('en-US');
            }

            // Update total cost display with blue strong text and exact output format
            function updateTotalCost() {
                if (!totalCostDisplay) return;
                const selectedPlan = planSelect.value;
                if (selectedPlan && planPrices[selectedPlan] !== undefined) {
                    const total = planPrices[selectedPlan] + setupCost;
                    totalCostDisplay.innerHTML = `<strong style="color: #0d6efd;">[Total cost = ${formatTotalCost(total)} Tk]</strong>`;
                } else {
                    totalCostDisplay.textContent = "-- Tk";
                }
            }

            // Show modal function
            function showModal() {
                contactModalEl.classList.add("show");
                contactModalEl.style.display = "block";
                document.body.classList.add("modal-open");
                updateTotalCost();
            }

            // Hide modal function
            function hideModal() {
                contactModalEl.classList.remove("show");
                contactModalEl.style.display = "none";
                document.body.classList.remove("modal-open");
                if (totalCostDisplay) totalCostDisplay.textContent = "-- Tk";
            }

            // Close modal on clicking outside modal-content of contactModal only
            contactModalEl.addEventListener("click", function(e) {
                if (!modalContent.contains(e.target)) {
                    hideModal();
                }
            });

            // Open modal when plan button clicked
            const planButtons = document.querySelectorAll(".plan-btn");
            planButtons.forEach(btn => {
                btn.addEventListener("click", function(e) {
                    e.preventDefault();
                    const selectedPlan = this.dataset.plan || this.getAttribute("data-plan") || "";
                    if (planSelect) {
                        planSelect.value = selectedPlan;
                        updateTotalCost();
                    }
                    showModal();

                    setTimeout(() => {
                        const firstInput = contactModalEl.querySelector('input[name="name"], select, textarea');
                        if (firstInput) firstInput.focus();
                    }, 120);
                });
            });

            // Update total cost on price plan select change
            if (planSelect) {
                planSelect.addEventListener("change", updateTotalCost);
            }

            // Close on ESC key
            document.addEventListener("keydown", function(e) {
                if (e.key === "Escape") {
                    hideModal();
                }
            });
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const navbarLinks = document.querySelectorAll('.navbar-nav .nav-link');

            navbarLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    const href = this.getAttribute('href');

                    // If href starts with '#' but NOT '#plan', redirect to index.php + hash
                    if (href.startsWith('#') && href !== '#plan') {
                        e.preventDefault();
                        window.location.href = 'index.php' + href;
                    }
                    // Else if href is '#' (home), redirect to index.php root
                    else if (href === '#') {
                        e.preventDefault();
                        window.location.href = 'index.php';
                    }
                    // Otherwise (#plan), default behavior (scroll on same page)
                });
            });
        });
    </script>


</body>

</html>