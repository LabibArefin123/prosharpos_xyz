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

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>ProsharPOS – Smart POS Software for Bangladesh</title>

    <!-- Primary Meta Tags -->
    <meta name="description" content="ProsharPOS is a smart Point of Sale (POS) software for Bangladesh. Manage sales, inventory, customers, suppliers, and reports easily with ProsharPOS.">
    <meta name="keywords" content="prosharpos, pos software bangladesh, retail pos system, shop management software, inventory management bangladesh, sales billing software, pos system">
    <meta name="author" content="ProsharPOS Team">
    <meta name="robots" content="index, follow">

    <!-- Canonical URL -->
    <!-- <link rel="canonical" href="https://www.prosharpos.xyz/" /> -->

    <!-- Open Graph (Facebook, LinkedIn) -->
    <meta property="og:title" content="ProsharPOS – Smart POS Software for Bangladesh">
    <meta property="og:description" content="Simplify sales, inventory, and business management with ProsharPOS – a modern POS solution built for Bangladeshi businesses.">
    <!-- <meta property="og:image" content="https://www.prosharpos.xyz/images/seo-banner.jpg"> -->
    <!-- <meta property="og:url" content="https://www.prosharpos.xyz/"> -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="ProsharPOS">
    <meta property="og:updated_time" content="2025-07-31T12:00:00+00:00">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="ProsharPOS – POS Software for Bangladesh">
    <meta name="twitter:description" content="Fast billing, real-time inventory, and powerful reports with ProsharPOS.">
    <!-- <meta name="twitter:image" content="https://www.prosharpos.xyz/images/seo-banner.jpg"> -->

    <!-- JSON-LD Structured Data (SoftwareApplication Schema) -->
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "SoftwareApplication",
            "name": "ProsharPOS",
            "url": "https://www.prosharpos.xyz/",
            "image": "https://www.prosharpos.xyz/images/seo-banner.jpg",
            "description": "ProsharPOS is a smart web-based Point of Sale (POS) software for managing sales, inventory, customers, and business reports in Bangladesh.",
            "applicationCategory": "BusinessApplication",
            "operatingSystem": "Web",
            "author": {
                "@type": "Organization",
                "name": "ProsharPOS Team"
            }
        }
    </script>

    <!-- Favicon -->
    <!-- <link rel="icon" type="image/png" src="images/icon.JPG"> -->
    <link rel="icon" type="image/png" href="images/icon.JPG">

    <!-- Bootstrap & Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />

    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="./style.css">

    <!-- SEO Extras -->
    <link rel="sitemap" type="application/xml" title="Sitemap" href="https://www.prosharpos.xyz/sitemap.xml">
    <link rel="alternate" type="application/rss+xml" title="RSS" href="https://www.prosharpos.xyz/feed.xml">
    <link rel="robots" href="/robots.txt">
</head>

<body>
    <div class="container-fluid">
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
                    <a href="mailto:mdlabibarefin@gmail.com"
                        class="text-decoration-none text-secondary d-flex align-items-center gap-1">
                        <i class="bi bi-envelope"></i> <span class="d-none d-sm-inline">mdlabibarefin@gmail.com</span>
                    </a>
                    <a href="tel:+8801776197999"
                        class="text-decoration-none text-secondary d-flex align-items-center gap-1">
                        <i class="bi bi-telephone"></i> <span class="d-none d-sm-inline">+8801776197999</span>
                    </a>
                    <a href="https://labib.work" target="_blank"
                        class="text-decoration-none text-secondary d-flex align-items-center gap-1">
                        <i class="bi bi-globe2"></i> <span class="d-none d-sm-inline">labib.work</span>
                    </a>
                </div>
                <a href="mailto:mdlabibarefin@gmail.com"
                    class="btn btn-primary btn-sm rounded-circle d-flex align-items-center justify-content-center"
                    style="width: 36px; height: 36px;">
                    <i class="bi bi-envelope-fill"></i>
                </a>
            </div>

            <!-- Navbar -->
            <nav class="navbar navbar-expand-md navbar-light bg-white border-bottom shadow-sm mb-4 px-2">
                <div class="container-fluid px-0">
                    <a class="navbar-brand d-flex align-items-center gap-2" href="index.php">
                        <img src="images/image.png" alt="Logo" class="d-none d-md-block" style="width: 200px; height: 6  0px;">
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

        <!-- Banner -->
        <section class="proshar-hero">
            <style>
                .proshar-hero {
                    padding: 100px 0;
                    background: linear-gradient(120deg, #f6fff9, #f2edff);
                }

                .hero-badge {
                    color: #3ad29f;
                    font-weight: 600;
                    font-size: 14px;
                    display: inline-block;
                    margin-bottom: 15px;
                }

                .hero-title {
                    font-size: 52px;
                    font-weight: 800;
                    color: #1f2933;
                    line-height: 1.2;
                    margin-bottom: 20px;
                }

                .hero-text {
                    font-size: 17px;
                    color: #6b7280;
                    max-width: 500px;
                    margin-bottom: 30px;
                }

                .hero-btn {
                    background-color: #6c7cff;
                    color: #fff;
                    padding: 14px 28px;
                    border-radius: 8px;
                    font-weight: 600;
                    text-decoration: none;
                }

                .hero-btn:hover {
                    background-color: #5a6af0;
                    color: #fff;
                }

                .hero-image {
                    max-width: 100%;
                    height: auto;
                }
            </style>
            <div class="container">
                <div class="row align-items-center">

                    <!-- Left Content -->
                    <div class="col-lg-6 col-md-12">
                        <span class="hero-badge">Bangladesh POS Software</span>

                        <h1 class="hero-title">
                            Easy-to-use<br>
                            Point of Sale
                        </h1>

                        <p class="hero-text">
                            ProsharPOS helps businesses in Bangladesh manage sales,
                            inventory, customers, and reports easily.
                            Fast, secure, and works on all devices.
                        </p>
                    </div>

                    <!-- Right Illustration -->
                    <div class="col-lg-6 col-md-12 text-center">
                        <img src="images/hero/hero.png" alt="ProsharPOS Illustration" class="hero-image">
                    </div>

                </div>
            </div>

        </section>

        <br>

        <!-- About Section -->
        <section id="about" class="bg-white py-5 px-3 px-md-4">
            <div class="text-center mb-5">
                <h1 class="text-center fw-bold display-6">
                    About
                    <span class="text-proshar-blue">Proshar</span><span class="text-proshar-green">POS</span>
                </h1>
                <p class="fs-5 text-primary fst-italic">
                    Smart Point of Sale Software for Bangladesh
                </p>
            </div>

            <div class="text-secondary fs-6 lh-lg mx-auto" style="max-width: 1140px; padding: 0 1rem; text-align: justify;">
                <p>
                    <span class="fw-bold proshar-hover">ProsharPOS</span> is a modern, web-based Point of Sale (POS) solution designed to simplify
                    <strong>sales, inventory, and business management</strong> for shops and businesses across Bangladesh.
                    It helps retailers manage daily operations efficiently with accuracy and confidence.
                </p>

                <p>
                    From fast billing and stock control to customer and supplier management,
                    <span class="fw-bold proshar-hover">ProsharPOS</span> provides a complete business solution in one place.
                    Its intuitive interface ensures ease of use for both owners and staff.
                </p>

                <h3 class="fw-semibold text-primary my-4">Sell Faster. Manage Smarter.</h3>

                <p>ProsharPOS offers:</p>
                <ul class="list-unstyled fs-6 mb-4" style="max-width: 420px;">
                    <li>✅ <strong>Fast & Accurate Sales Billing</strong></li>
                    <li>✅ <strong>Real-Time Inventory Tracking</strong></li>
                    <li>✅ <strong>Profit, Sales & Expense Reports</strong></li>
                    <li>✅ <strong>Customer & Supplier Management</strong></li>
                    <li>✅ <strong>User Roles & Secure Access Control</strong></li>
                </ul>

                <p>
                    Take full control of your shop and <strong>grow your business efficiently</strong> —
                    <span class="fw-bold proshar-hover">ProsharPOS</span> makes daily operations simple and reliable.
                    <br><br>
                    <em class="fw-semibold fs-5 text-dark">
                        Sell. Track. Grow. With <span class="proshar-hover">ProsharPOS</span>.
                    </em>
                </p>

                <p>
                    Whether you run a grocery store, pharmacy, electronics shop, or retail outlet,
                    <span class="proshar-hover">ProsharPOS</span> adapts to your business needs with powerful features,
                    clear insights, and smooth performance.
                </p>

                <p class="fw-semibold text-dark mt-4">
                    Built for Bangladeshi businesses,
                    <span class="proshar-hover">ProsharPOS</span> helps reduce manual work,
                    minimize errors, and improve overall profitability through a smart digital POS system.
                </p>
            </div>
        </section>

        <style>
            .text-proshar-blue {
                color: #16a5b1ff;
            }

            .text-proshar-green {
                color: #00B894;
            }

            .proshar-hover {
                color: #212529;
                transition: color 0.3s ease;
                cursor: default;
            }

            .proshar-hover:hover {
                color: #2C7BE5;
            }
        </style>

        <br>

        <!-- Features Section -->
        <section id="feature" class="bg-white py-5 px-3 px-md-5">
            <div class="container">
                <h2 class="text-center fw-bold mb-5 display-6 text-dark">
                    ProsharPOS Core Features
                </h2>

                <div class="row g-4 justify-content-center">

                    <!-- Feature 1 -->
                    <div class="col-6 col-md-6 col-lg-4">
                        <div class="bg-info bg-opacity-10 shadow-sm p-4 rounded-4 h-100 text-center">
                            <div class="mb-3">
                                <i class="fas fa-cash-register text-info fs-1"></i>
                            </div>
                            <h3 class="h5 fw-semibold mb-3 text-dark">Fast Billing</h3>
                            <p class="text-secondary small">
                                Complete sales in seconds with a fast, smooth, and accurate billing system designed for busy shops.
                            </p>
                        </div>
                    </div>

                    <!-- Feature 2 -->
                    <div class="col-6 col-md-6 col-lg-4">
                        <div class="bg-primary bg-opacity-10 shadow-sm p-4 rounded-4 h-100 text-center">
                            <div class="mb-3">
                                <i class="fas fa-boxes text-primary fs-1"></i>
                            </div>
                            <h3 class="h5 fw-semibold mb-3 text-dark">Inventory Management</h3>
                            <p class="text-secondary small">
                                Track stock levels in real time, prevent shortages, and manage products with full control.
                            </p>
                        </div>
                    </div>

                    <!-- Feature 3 -->
                    <div class="col-6 col-md-6 col-lg-4">
                        <div class="bg-success bg-opacity-10 shadow-sm p-4 rounded-4 h-100 text-center">
                            <div class="mb-3">
                                <i class="fas fa-users text-success fs-1"></i>
                            </div>
                            <h3 class="h5 fw-semibold mb-3 text-dark">Customer & Supplier Management</h3>
                            <p class="text-secondary small">
                                Manage customer history, due balances, and supplier records in one organized system.
                            </p>
                        </div>
                    </div>

                    <!-- Feature 4 -->
                    <div class="col-6 col-md-6 col-lg-4">
                        <div class="bg-danger bg-opacity-10 shadow-sm p-4 rounded-4 h-100 text-center">
                            <div class="mb-3">
                                <i class="fas fa-chart-line text-danger fs-1"></i>
                            </div>
                            <h3 class="h5 fw-semibold mb-3 text-dark">Sales & Profit Reports</h3>
                            <p class="text-secondary small">
                                View daily, monthly, and yearly reports with clear insights into sales, profit, and expenses.
                            </p>
                        </div>
                    </div>

                    <!-- Feature 5 -->
                    <div class="col-6 col-md-6 col-lg-4">
                        <div class="bg-warning bg-opacity-10 shadow-sm p-4 rounded-4 h-100 text-center">
                            <div class="mb-3">
                                <i class="fas fa-user-shield text-warning fs-1"></i>
                            </div>
                            <h3 class="h5 fw-semibold mb-3 text-dark">User Roles & Security</h3>
                            <p class="text-secondary small">
                                Control access with secure user roles to protect sensitive business data.
                            </p>
                        </div>
                    </div>

                    <!-- Feature 6 -->
                    <div class="col-6 col-md-6 col-lg-4">
                        <div class="shadow-sm p-4 rounded-4 h-100 text-center" style="background-color: rgba(102, 16, 242, 0.1);">
                            <div class="mb-3">
                                <i class="fas fa-headset fs-1" style="color: #6610f2;"></i>
                            </div>
                            <h3 class="h5 fw-semibold mb-3 text-dark">Reliable Support</h3>
                            <p class="text-secondary small">
                                Get timely support and guidance to keep your business running smoothly without interruptions.
                            </p>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <br>

        <!-- Software Insights Section -->
        <section id="hardware" class="bg-white py-5 px-3 px-md-5">
            <h2 class="text-center fw-bold mb-2 display-6 text-dark">ProsharPOS Modules</h2>
            <p class="text-center text-muted mb-5 fs-6">
                Explore the powerful modules that run your daily business operations.
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
                                Get a real-time overview of sales, stock levels, invoices, challans, payments, and financial summaries from one centralized dashboard.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Invoice Management -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-lg h-100 overflow-hidden rounded-4">
                        <div class="card-img-hover bg-white d-flex align-items-center justify-content-center p-3" style="height: 240px;">
                            <img src="images/software/invoice.PNG" alt="Invoice Management"
                                class="img-fluid" role="button"
                                data-bs-toggle="modal" data-bs-target="#imageModal"
                                onclick="setModalImage(this.src)"
                                style="object-fit: contain; max-height: 100%; max-width: 100%;">
                        </div>
                        <div class="card-body text-center px-4 py-3">
                            <h5 class="card-title fw-semibold text-dark">Invoice Management</h5>
                            <p class="card-text text-secondary text-justify">
                                Create, manage, and track sales invoices efficiently with customer details, payment status, and invoice history.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Challan Management -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-lg h-100 overflow-hidden rounded-4">
                        <div class="card-img-hover bg-white d-flex align-items-center justify-content-center p-3" style="height: 240px;">
                            <img src="images/software/challan.PNG" alt="Challan Management"
                                class="img-fluid" role="button"
                                data-bs-toggle="modal" data-bs-target="#imageModal"
                                onclick="setModalImage(this.src)"
                                style="object-fit: contain; max-height: 100%; max-width: 100%;">
                        </div>
                        <div class="card-body text-center px-4 py-3">
                            <h5 class="card-title fw-semibold text-dark">Challan Management</h5>
                            <p class="card-text text-secondary text-justify">
                                Generate delivery challans, track product movement, and maintain accurate records between stock and invoices.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Product & Stock -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-lg h-100 overflow-hidden rounded-4">
                        <div class="card-img-hover bg-white d-flex align-items-center justify-content-center p-3" style="height: 240px;">
                            <img src="images/software/products.PNG" alt="Product & Stock Management"
                                class="img-fluid" role="button"
                                data-bs-toggle="modal" data-bs-target="#imageModal"
                                onclick="setModalImage(this.src)"
                                style="object-fit: contain; max-height: 100%; max-width: 100%;">
                        </div>
                        <div class="card-body text-center px-4 py-3">
                            <h5 class="card-title fw-semibold text-dark">Product & Stock Management</h5>
                            <p class="card-text text-secondary text-justify">
                                Manage products, categories, brands, units, warranties, storage locations, and monitor stock availability in real time.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Financial Management -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-lg h-100 overflow-hidden rounded-4">
                        <div class="card-img-hover bg-white d-flex align-items-center justify-content-center p-3" style="height: 240px;">
                            <img src="images/software/finance.PNG" alt="Financial Management"
                                class="img-fluid" role="button"
                                data-bs-toggle="modal" data-bs-target="#imageModal"
                                onclick="setModalImage(this.src)"
                                style="object-fit: contain; max-height: 100%; max-width: 100%;">
                        </div>
                        <div class="card-body text-center px-4 py-3">
                            <h5 class="card-title fw-semibold text-dark">Financial Management</h5>
                            <p class="card-text text-secondary text-justify">
                                Track bank balances, deposits, withdrawals, petty cash, and payments with complete financial transparency.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- People & Settings -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-lg h-100 overflow-hidden rounded-4">
                        <div class="card-img-hover bg-white d-flex align-items-center justify-content-center p-3" style="height: 240px;">
                            <img src="images/software/settings.PNG" alt="People & Settings"
                                class="img-fluid" role="button"
                                data-bs-toggle="modal" data-bs-target="#imageModal"
                                onclick="setModalImage(this.src)"
                                style="object-fit: contain; max-height: 100%; max-width: 100%;">
                        </div>
                        <div class="card-body text-center px-4 py-3">
                            <h5 class="card-title fw-semibold text-dark">People & System Settings</h5>
                            <p class="card-text text-secondary text-justify">
                                Manage customers, suppliers, users, roles, permissions, and company profiles with secure access control.
                            </p>
                        </div>
                    </div>
                </div>

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
                <input type="hidden" name="form_type" value="customer">
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

        <footer class="text-center text-muted small py-1 border-top">
            &copy;
            <a href="#"
                class="text-decoration-none text-primary fw-semibold"
                rel="noopener noreferrer">
                ProsharPOS.
            </a> All rights reserved
            | Design and Developed by
            <a href="https://labib.work" target="_blank" rel="noopener noreferrer"
                class="text-decoration-none text-primary fw-semibold d-inline-block">
                <span class="onstage-text">
                    Labib Arefin
                </span>
            </a>
        </footer>

    </div>

    <!-- Bootstrap JS Bundle (Popper + Bootstrap JS) -->

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