<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Adoption Center - Home</title>
    <link rel="stylesheet" href="performance.css">
    <link rel="stylesheet" href="../css/home.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
</head>

<body>
    <!-- Navigation Header -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <svg class="brand-icon" viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <ellipse cx="60" cy="70" rx="18" ry="22" fill="url(#nav-gradient)" />
                    <ellipse cx="45" cy="45" rx="12" ry="15" fill="url(#nav-gradient)" />
                    <ellipse cx="75" cy="45" rx="12" ry="15" fill="url(#nav-gradient)" />
                    <ellipse cx="35" cy="55" rx="10" ry="13" fill="url(#nav-gradient)" />
                    <ellipse cx="85" cy="55" rx="10" ry="13" fill="url(#nav-gradient)" />
                    <defs>
                        <linearGradient id="nav-gradient" x1="0" y1="0" x2="120" y2="120">
                            <stop offset="0%" stop-color="#FF1B6B" />
                            <stop offset="100%" stop-color="#45CAFF" />
                        </linearGradient>
                    </defs>
                </svg>
                <span class="brand-name">Pet Adoption Center</span>
            </div>

            <ul class="nav-menu">
                <li class="nav-item"><a href="#home" class="nav-link active">Home</a></li>
                <li class="nav-item"><a href="#about" class="nav-link">About Us</a></li>
                <li class="nav-item dropdown">
                    <a href="#categories" class="nav-link">Categories ▾</a>
                    <ul class="dropdown-menu">
                        <li><a href="#cats">🐱 Cats</a></li>
                        <li><a href="#dogs">🐕 Dogs</a></li>
                        <li><a href="#rabbits">🐇 Rabbits</a></li>
                        <li><a href="#tortoise">🐢 Tortoises</a></li>
                        <li><a href="pet_food.php" class="nav-link">Pet Food</a></li>
                        <li><a href="pet_toys.php" class="nav-link">Pet Toys</a></li>
                        <li><a href="pet_homes.php" class="nav-link">Pet Homes</a></li>
                        <li><a href="pet_healthcare.php" class="nav-link">Pet Healthcare</a></li>
                        
    <div class="category-icon">💊</div>
    <h3 class="category-name">Pet Healthcare</h3>
    <p class="category-description">Vitamins, supplements, and first aid for your pets.</p>
    <button class="category-btn" onclick="window.location.href='pet_healthcare.php'">Visit Shop</button>
</div>




                    </ul>
                </li>
                <li class="nav-item"><a href="#shop" class="nav-link">Shop</a></li>
                
                <!-- Admin Only Link -->
                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                    <li class="nav-item"><a href="add_pet.php" class="nav-link" style="color: #FF1B6B; font-weight: bold;">+ Add Pet</a></li>
                <?php endif; ?>
                
            </ul>

            <div class="nav-actions">
                <button class="theme-toggle" id="themeToggle" aria-label="Toggle theme">
                    <span class="theme-icon">🌙</span>
                </button>
                <?php if (isset($_SESSION['user_name'])): ?>
                    <button class="btn-secondary" onclick="showProfile()">My Profile</button>
                <?php endif; ?>
                <a href="logout.php" class="btn-logout">Logout</a>
            </div>

            <!-- Mobile Menu Toggle -->
            <button class="mobile-menu-toggle" id="mobileMenuToggle">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>

    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <?php if (isset($_SESSION['user_name'])): ?>
                <h1 class="hero-title">Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
                <p class="hero-subtitle">Ready to find your new best friend?</p>
            <?php else: ?>
            <h1 class="hero-title">Find Your Perfect Companion</h1>
            <p class="hero-subtitle">Thousands of loving pets are waiting for their forever home</p>
            <?php endif; ?>
            <div class="hero-search">
                <input type="text" class="search-input" placeholder="Search for pets by name, breed, or type...">
                <button class="search-btn">Search</button>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="categories" id="categories">
        <div class="container">
            <h2 class="section-title">Browse by Category</h2>
            <p class="section-subtitle">Choose your favorite type of pet</p>

            <div class="category-grid">
                <!-- Cats -->
                <div class="category-card" data-category="cats">
                    <div class="category-icon">🐱</div>
                    <h3 class="category-name">Cats</h3>
                    <p class="category-description">Find your perfect feline friend</p>
                    <div class="category-count">230+ available</div>
                    <button class="category-btn" onclick="window.location.href='cats.php'">Browse Cats</button>
                </div>

                <!-- Dogs -->
                <div class="category-card" data-category="dogs">
                    <div class="category-icon">🐕</div>
                    <h3 class="category-name">Dogs</h3>
                    <p class="category-description">Loyal companions waiting for you</p>
                    <div class="category-count">340+ available</div>
                    
                    <button class="category-btn" onclick="window.location.href='dogs.php'">Browse Dogs</button>
                </div>

                <!-- Rabbits -->
                <div class="category-card" data-category="rabbits">
                    <div class="category-icon">🐇</div>
                    <h3 class="category-name">Rabbits</h3>
                    <p class="category-description">Adorable bunnies ready to hop home</p>
                    <div class="category-count">85+ available</div>
                    
                    <button class="category-btn" onclick="window.location.href='rabbits.php'">Browse Rabbits</button>
                </div>

                <!-- Tortoises -->
                <div class="category-card" data-category="tortoise">
                    <div class="category-icon">🐢</div>
                    <h3 class="category-name">Tortoises</h3>
                    <p class="category-description">Slow and steady companions</p>
                    <div class="category-count">45+ available</div>
                    
                    <button class="category-btn" onclick="window.location.href='tortoises.php'">Browse Tortoises</button>
                </div>
            </div>
        </div>
    </section>

    <!-- About Us Section -->
    <section class="about" id="about">
        <div class="container">
            <div class="about-content">
                <div class="about-text">
                    <h2 class="section-title">About Us</h2>
                    <p class="about-description">
                        We are dedicated to connecting loving families with pets in need of homes. Our mission is to
                        provide
                        a safe, caring environment for all animals while they wait for their forever families.
                    </p>
                    <p class="about-description">
                        With over 10 years of experience in pet adoption, we've successfully placed thousands of pets in
                        loving homes. Every pet in our care receives proper veterinary attention, nutrition, and lots of
                        love.
                    </p>
                    <div class="about-stats">
                        <div class="stat-item">
                            <div class="stat-number">12,000+</div>
                            <div class="stat-label">Pets Adopted</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">5,000+</div>
                            <div class="stat-label">Happy Families</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">700+</div>
                            <div class="stat-label">Pets Available</div>
                        </div>
                    </div>
                </div>
                <div class="about-image">
                    <div class="image-placeholder">🐾</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Shop Section -->
    <section class="shop" id="shop">
        <div class="container">
            <h2 class="section-title">Pet Shop</h2>
            <p class="section-subtitle">Everything your pet needs</p>

            <div class="shop-grid">
                <div class="shop-card">
                    <div class="shop-icon">🍖</div>
                    <h3 class="shop-item-name">Pet Food</h3>
                    <p class="shop-description">Premium quality food for all pets</p>
                </div>

                <div class="shop-card">
                    <div class="shop-icon">🎾</div>
                    <h3 class="shop-item-name">Toys</h3>
                    <p class="shop-description">Fun toys to keep your pet entertained</p>
                </div>

                <div class="shop-card">
                    <div class="shop-icon">🏠</div>
                    <h3 class="shop-item-name">Pet Homes</h3>
                    <p class="shop-description">Comfortable homes and beds</p>
                </div>

                <div class="shop-card">
                    <div class="shop-icon">💊</div>
                    <h3 class="shop-item-name">Healthcare</h3>
                    <p class="shop-description">Vitamins and medical supplies</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h4 class="footer-title">Pet Adoption Center</h4>
                    <p class="footer-text">Connecting pets with loving families since 2015</p>
                </div>
                <div class="footer-section">
                    <h4 class="footer-title">Quick Links</h4>
                    <ul class="footer-links">
                        <li><a href="#home">Home</a></li>
                        <li><a href="#about">About Us</a></li>
                        <li><a href="#categories">Categories</a></li>
                        <li><a href="#shop">Shop</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4 class="footer-title">Contact</h4>
                    <p class="footer-text">Email: info@petadoption.com</p>
                    <p class="footer-text">Phone: (555) 123-4567</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 Pet Adoption Center. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        function showProfile() {
            const userName = "<?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name'], ENT_QUOTES, 'UTF-8') : 'N/A'; ?>";
            const userEmail = "<?php echo isset($_SESSION['user_email']) ? htmlspecialchars($_SESSION['user_email'], ENT_QUOTES, 'UTF-8') : 'N/A'; ?>";
            alert(`--- Your Profile ---\n\n👤 Name: ${userName}\n📧 Email: ${userEmail}`);
        }

        // The dashboard.js script might contain other logic, so we keep it.
    </script>
    <script src="dashboard.js"></script>
</body>

</html>