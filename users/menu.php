<?php
// Start session and check if user is logged in
include 'auth_session.php';

// Include the database connection file
include '../users/db.php';

// Initialize error handling
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '../errors.log');

// Initialize search query
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';

// Fetch product categories dynamically if possible
$categories = ['hot', 'cold', 'alternative_drink', 'light_meal']; // Modify or fetch dynamically if necessary

// Ensure the search term is safe
$searchQuery = htmlspecialchars($searchQuery, ENT_QUOTES, 'UTF-8');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CaféBristo Menu</title>
    <link rel="stylesheet" href="../assets/css/includes.css">
    <link rel="stylesheet" href="../assets/css/styles2.css">
</head>

<body>
    <header>
        <nav class="navbar">
            <div class="container">
                <a href="index.php" class="brand-logo"><img src="../assets/images/rlogo.png" alt="CafeBristo"></a>
                <div class="navbar-links">
                    <ul class="nav-links">
                        <li><a href="profile.php">Edit Profile</a></li>
                        <li><a href="menu.php">Place Order</a></li>
                        <li><a href="booking.php">Book Table</a></li>
                    </ul>
                    <div class="auth-links">
                        <a href="../users/logout.php" class="login-link"><i class="fas fa-sign-in-alt"></i> Logout</a>
                        <div class="cart">
                            <a href="cart.php"><i class="fas fa-shopping-cart"></i></a>
                        </div>
                    </div>
                </div>

                <a href="#" class="sidenav-trigger menu" onclick="toggleNav()">
                    <i class="fas fa-bars"></i>
                </a>
                <a href="#" class="sidenav-trigger close" onclick="toggleNav()">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </nav>
        <ul class="sidenav" id="mobile-nav">
            <li><a href="profile.php">Edit Profile</a></li>
            <li><a href="menu.php">Place Order</a></li>
            <li><a href="booking.php">Book Table</a></li>
            <li><a href="../users/logout.php">Logout</a></li>
            <li><a href="cart.php"><i class="fas fa-shopping-cart"></i></a></li>
        </ul>
    </header>

    <main>
        <section class="menu-page">
            <h3>CaféBristo MENU</h3>
            <form method="get" action="menu.php" class="search-form">
                <label for="search">Search Menu:</label>
                <input type="search" id="search" name="search" placeholder="Search for items..." value="<?php echo $searchQuery; ?>">
                <button type="submit" class="btn">Search</button>
            </form>
            <?php
            foreach ($categories as $category) {
                echo "<h1 id='categories'>" . htmlspecialchars(ucfirst($category), ENT_QUOTES, 'UTF-8') . "</h1>";
                echo "<div class='menu-cards'>";

                // Prepare SQL query with search filter
                $sql = "SELECT * FROM product WHERE categories = ? AND (title LIKE ? OR description LIKE ?)";
                if ($stmt = $conn->prepare($sql)) {
                    $searchTerm = "%$searchQuery%";
                    $stmt->bind_param('sss', $category, $searchTerm, $searchTerm);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $id = htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8');
                            $title = htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8');
                            $description = htmlspecialchars($row['description'], ENT_QUOTES, 'UTF-8');
                            $price = htmlspecialchars($row['price'], ENT_QUOTES, 'UTF-8');
                            $image = htmlspecialchars($row['image'], ENT_QUOTES, 'UTF-8');

                            // Determine the image source
                            if (preg_match('/^(http:\/\/|https:\/\/)/', $image)) {
                                $imageSrc = $image;
                            } elseif (preg_match('/^uploads\//', $image)) {
                                $imageSrc = htmlspecialchars("./" . $image, ENT_QUOTES, 'UTF-8');
                            } else {
                                $imageSrc = htmlspecialchars($image, ENT_QUOTES, 'UTF-8');
                            }

                            echo "<div class='card'>
                                    <img src='$imageSrc' alt='$title'>
                                    <div class='card-info'>
                                        <h2>$title</h2>
                                        <p>$description</p>
                                        <span class='price'>Rs.$price</span>
                                        <a href='cart.php?action=add&id=$id' class='add-to-cart'>Add to Cart</a>
                                    </div>
                                  </div>";
                        }
                    } else {
                        echo "<p>No products found in this category.</p>";
                    }

                    $stmt->close();
                } else {
                    echo "<!-- SQL Preparation Error: " . $conn->error . " -->";
                }

                echo "</div>";
            }

            // Close the database connection
            $conn->close();
            ?>
        </section>
    </main>

    <?php include '../includes/footer.php'; ?>
    <script src="../assets/js/dev.js"></script>
    <script src="../assets/js/script.js"></script>
</body>

</html>
