<h>Something New</h2>
    <nav class="navbar">
    <div class="nav-container">
        <a href="index.php" class="nav-logo">Website</a>
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="about.php">About Us</a></li>
            <li><a href="products.php">Products</a></li>
            <?php if (!isset($_SESSION['username'])): ?>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Register</a></li>
            <?php else: ?>
                <li class="dropdown">
                    <a href="#" class="dropbtn">Account</a>
                    <div class="dropdown-content">
                        <a href="dashboard.php">Dashboard</a>
                        <a href="logout.php">Logout</a>
                    </div>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>