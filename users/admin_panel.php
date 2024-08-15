<?php
include 'auth_session.php';
include 'db.php';
// Fetch categories for dropdown
$categories = $conn->query("SELECT DISTINCT categories FROM product");
// Fetch all products for listing
$product = $conn->query("SELECT * FROM product");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - CafeBristo</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/adminIndex.css">
</head>

<body>
    <nav class="navbar">
        <div class="container">
            <a href="index.php" class="brand-logo"><img src="../assets/images/rlogo.png" alt="CafeBristo"></a>
            <div class="navbar-links">
                <ul class="nav-links">
                    <li><a href="admin_panel.php">Menu</a></li>
                    <li><a href="userContact.php">Contact User</a></li>
                    <li><a href="table.php">Book Table</a></li>
                </ul>
                <div class="auth-links">
                    <a href="logout.php" class="login-link"><i class="fas fa-sign-in-alt"></i> Log Out</a>
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
        <li><a href="#updateProduct">Menu</a></li>
        <li><a href="userContact.php">Contact User</a></li>
        <li><a href="table.php">Book Table</a></li>
        <li><a href="logout.php">Log Out</a></li>
    </ul>
    <main class="adminpage">
        <h1>Admin Panel - Manage Product</h1>
        <div class="function">
            <a href="#updateProduct"><button class="btn">Add Product</button></a>
            <a href="list"><button class="btn">Edit Product</button></a>
            <a href="newcategories"><button class="btn">Add Categories</button></a>
        </div>

        <!-- Add New Product Form -->
        <form method="post" id="updateProduct" action="admin_panel.php" enctype="multipart/form-data">
            <h2>Add New Product</h2>
            <?php

            // Handle Add New Product
            if (isset($_POST['add'])) {
                $title = $conn->real_escape_string($_POST['title']);
                $description = $conn->real_escape_string($_POST['description']);
                $price = $conn->real_escape_string($_POST['price']);
                $category = $conn->real_escape_string($_POST['categories']);
                $image = $_FILES['image']['name'];
                $image_url = $conn->real_escape_string($_POST['image_url']);

                // Determine image path
                if ($image) {
                    $target_dir = "uploads/";
                    // Create a sanitized filename
                    $image_ext = pathinfo($image, PATHINFO_EXTENSION);
                    $sanitized_title = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($title));
                    $target_file = $target_dir . $sanitized_title . '.' . $image_ext;

                    // Move the uploaded file to the target directory
                    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                        $image_path = $target_file;
                    } else {
                        echo "Error uploading the file.";
                        $image_path = '';
                    }
                } else {
                    $image_path = $image_url;
                }

                $sql = "INSERT INTO product (title, description, price, categories, image) VALUES ('$title', '$description', '$price', '$category', '$image_path')";
                if ($conn->query($sql)) {
                    echo "Product added successfully!";
                } else {
                    echo "Error: " . $conn->error;
                }
            }
            ?>
            <?php if (isset($message)) echo "<p>$message</p>"; ?>
            <label>Title:</label>
            <input type="text" name="title" required>
            <label>Description:</label>
            <textarea name="description" required></textarea>
            <label>Price:</label>
            <input type="number" step="0.01" name="price" required>
            <label>Categories:</label>
            <select name="categories" required>
                <?php
                if ($categories->num_rows > 0) {
                    while ($row = $categories->fetch_assoc()) {
                        echo "<option value='{$row['categories']}'>{$row['categories']}</option>";
                    }
                }
                ?>
            </select>
            <label>Image (File Upload):</label>
            <input type="file" name="image" accept="image/*">
            <label>Or Image URL:</label>
            <input type="url" name="image_url" placeholder="http://example.com/image.jpg">
            <br>
            <input type="submit" name="add" value="Add Product">
        </form>

        <!-- Product List -->
        <h2 id="list">Product List</h2>
        <?php
        // Handle Delete Product
        if (isset($_GET['delete'])) {
            $id = $conn->real_escape_string($_GET['delete']);
            $sql = "DELETE FROM product WHERE id=$id";
            if ($conn->query($sql)) {
                echo "Product deleted successfully!";
            } else {
                echo "Error: " . $conn->error;
            }
        }
        ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Image</th>
                    <th>Categories</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($product->num_rows > 0) {
                    while ($row = $product->fetch_assoc()) {
                        $imageSrc = $row['image'];
                        if (preg_match('/^(http:\/\/|https:\/\/)/', $imageSrc)) {
                            $imageSrc = $row['image'];
                        } elseif (preg_match('/^uploads\//', $imageSrc)) {
                            $imageSrc = htmlspecialchars("./" . $imageSrc, ENT_QUOTES, 'UTF-8');
                        }

                        echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['title']}</td>
                            <td>{$row['description']}</td>
                            <td>{$row['price']}</td>
                            <td><img src='$imageSrc' alt='Product Image' style='max-width:100px;'></td>
                            <td>{$row['categories']}</td>
                            <td>
                                <a href='admin_panel.php?edit={$row['id']}'>Edit</a> | 
                                <a href='admin_panel.php?delete={$row['id']}' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                            </td>
                          </tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>No product found</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- Edit Product Form -->
        <?php
        if (isset($_GET['edit'])) {
            $id = $conn->real_escape_string($_GET['edit']);
            $sql = "SELECT * FROM product WHERE id=$id";
            $result = $conn->query($sql);
            if ($row = $result->fetch_assoc()) {
        ?>
                <form method="post" action="admin_panel.php" enctype="multipart/form-data">
                    <h2>Edit Product</h2>
                    <?php

                    // Handle Edit Product
                    if (isset($_POST['update'])) {
                        $id = $conn->real_escape_string($_POST['id']);
                        $title = $conn->real_escape_string($_POST['title']);
                        $description = $conn->real_escape_string($_POST['description']);
                        $price = $conn->real_escape_string($_POST['price']);
                        $category = $conn->real_escape_string($_POST['categories']);
                        $image = $_FILES['image']['name'];
                        $image_url = $conn->real_escape_string($_POST['image_url']);

                        // Determine image path
                        if ($image) {
                            $target_dir = "uploads/";
                            // Create a sanitized filename
                            $image_ext = pathinfo($image, PATHINFO_EXTENSION);
                            $sanitized_title = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($title));
                            $target_file = $target_dir . $sanitized_title . '.' . $image_ext;

                            // Move the uploaded file to the target directory
                            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                                $image_path = $target_file;
                            } else {
                                echo "Error uploading the file.";
                                $image_path = '';
                            }
                        } else {
                            $image_path = $image_url;
                        }

                        $sql = "UPDATE product SET title='$title', description='$description', price='$price', categories='$category', image='$image_path' WHERE id=$id";
                        if ($conn->query($sql)) {
                            echo "Product updated successfully!";
                        } else {
                            echo "Error: " . $conn->error;
                        }
                    }
                    ?>
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                    <label>Title:</label>
                    <input type="text" name="title" value="<?php echo htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8'); ?>" required>
                    <label>Description:</label>
                    <textarea name="description" required><?php echo htmlspecialchars($row['description'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                    <label>Price:</label>
                    <input type="number" step="0.01" name="price" value="<?php echo htmlspecialchars($row['price'], ENT_QUOTES, 'UTF-8'); ?>" required>
                    <label>Image (File Upload):</label>
                    <input type="file" name="image" accept="image/*">
                    <label>Or Image URL:</label>
                    <input type="url" name="image_url" value="<?php echo htmlspecialchars($row['image'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="http://example.com/image.jpg">
                    <img src="<?php echo htmlspecialchars($row['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="Product Image" style="max-width:100px;">
                    <label>Categories:</label>
                    <select name="categories" required>
                        <?php
                        $categories->data_seek(0); // Reset the categories result set
                        while ($cat = $categories->fetch_assoc()) {
                            $selected = ($cat['categories'] == $row['categories']) ? 'selected' : '';
                            echo "<option value='{$cat['categories']}' $selected>{$cat['categories']}</option>";
                        }
                        ?>
                    </select>
                    <input type="submit" name="update" value="Update Product">
                </form>
        <?php
            }
        }
        ?>
    </main>
    <script src="../assets/js/script.js"></script>
    <script src="../assets/js/dev.js"></script>
</body>

</html>