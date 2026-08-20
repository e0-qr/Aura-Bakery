<?php

session_start();
define('ADMIN_PASSWORD', 'Dr1234'); 
$login_message = '';
$current_action = isset($_GET['action']) ? $_GET['action'] : 'list';
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$msg = "";

include 'db_connect.php'; 


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password_submit'])) {
    $entered_password = trim($_POST['admin_password']);
    if ($entered_password === ADMIN_PASSWORD) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: admin.php');
        exit;
    } else {
        $login_message = '<div class="alert alert-danger text-center">❌ Incorrect password. Please try again.</div>';
    }
}

if (isset($_GET['logout'])) {
    unset($_SESSION['admin_logged_in']);
    header('Location: admin.php');
    exit;
}


if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true):
    include 'header.php';
?>

<div class="container-fluid page-header py-6 wow fadeIn" data-wow-delay="0.1s">
    <div class="container text-center pt-5 pb-3">
        <h1 class="display-4 text-white animated slideInDown mb-3">Admin Login</h1>
    </div>
</div>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-5">
            <div class="card p-4 shadow-lg">
                <h4 class="card-title text-center mb-4">🔐 Enter Admin Password</h4>
                
                <?php echo $login_message; ?>

                <div class="alert alert-info text-center small">
                    Note: Admin Password is <code>Dr1234</code>
                </div>

                <form method="POST" action="admin.php">
                    <div class="mb-3">
                        <input type="password" name="admin_password" class="form-control" placeholder="Password" required>
                    </div>
                    <button type="submit" name="password_submit" class="btn btn-primary w-100">Login</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php 
    include 'footer.php'; 
    exit; 
endif; 


if ($current_action == 'delete' && $product_id > 0) {
    try {
        $stmt = $db->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $msg = '<div class="alert alert-success text-center">Product deleted successfully.</div>';
    } catch (PDOException $e) {
        $msg = '<div class="alert alert-danger text-center">Error: Could not delete product.</div>';
    }
    
    header('Location: admin.php');
    exit;
}



if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_product'])) {
    $name = trim($_POST['name']);
    $price = (float)$_POST['price'];
    $category = trim($_POST['category']);
    $short_description = trim($_POST['short_description']);
    $description = trim($_POST['description']);
    $ingredients = trim($_POST['ingredients']);
    $image_url = trim($_POST['image_url']);
    $p_id = (int)$_POST['product_id']; 

    if (empty($name) || empty($price) || empty($category) || empty($short_description) || empty($description) || empty($image_url)) {
        $msg = '<div class="alert alert-danger">Error: Please fill in all required fields.</div>';
    } else {
        if ($p_id > 0) {
            
            $stmt = $db->prepare("UPDATE products SET name = ?, price = ?, category = ?, short_description = ?, description = ?, ingredients = ?, image_url = ? WHERE id = ?");
            if ($stmt->execute([$name, $price, $category, $short_description, $description, $ingredients, $image_url, $p_id])) {
                $msg = '<div class="alert alert-success">Product ' . htmlspecialchars($name) . ' updated successfully!</div>';
                $current_action = 'list';
            } else {
                $msg = '<div class="alert alert-danger">Database Error: Could not update product.</div>';
            }
        } else {
            
            $stmt = $db->prepare("INSERT INTO products (name, description, short_description, ingredients, price, category, image_url) VALUES (?, ?, ?, ?, ?, ?, ?)");
            if ($stmt->execute([$name, $description, $short_description, $ingredients, $price, $category, $image_url])) {
                $msg = '<div class="alert alert-success">✅ Product ' . htmlspecialchars($name) . ' added successfully!</div>';
                $current_action = 'list';
            } else {
                $msg = '<div class="alert alert-danger">Database Error: Could not add product.</div>';
            }
        }
    }
}



include 'header.php';
?>

<div class="container-fluid page-header py-6 wow fadeIn" data-wow-delay="0.1s">
    <div class="container text-center pt-5 pb-3">
        <h1 class="display-4 text-white animated slideInDown mb-3">Admin Management</h1>
        <p class="text-white">Full Product CRUD (Create, Read, Update, Delete)</p>
        <a href="admin.php?logout=1" class="btn btn-danger btn-sm mt-2">
            <i class="fa fa-sign-out-alt"></i> Logout
        </a>
    </div>
</div>

<div class="container py-5">
    <?php echo $msg;  ?>

    <?php if ($current_action == 'list'): 
        
        $stmt = $db->query("SELECT * FROM products ORDER BY id DESC");
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
        <div class="row mb-4">
            <div class="col-12 text-end">
                <a href="admin.php?action=add" class="btn btn-success">
                    <i class="fa fa-plus-circle me-1"></i> Add New Product
                </a>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-primary">
                                <tr>
                                    <th>ID</th> 
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Price (SAR)</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $p): ?>
                                <tr>
                                    <td><?php echo $p['id']; ?></td>
                                    <td><img src="<?php echo htmlspecialchars($p['image_url']); ?>" style="width: 50px;"></td>
                                    <td><?php echo htmlspecialchars($p['name']); ?></td>
                                    <td><?php echo htmlspecialchars($p['category']); ?></td>
                                    <td>SAR<?php echo $p['price']; ?></td>
                                    <td>
                                        <a href="admin.php?action=edit&id=<?php echo $p['id']; ?>" class="btn btn-warning btn-sm me-2">
                                            <i class="fa fa-edit"></i> Edit
                                        </a>
                                        <a href="admin.php?action=delete&id=<?php echo $p['id']; ?>" 
                                        class="btn btn-danger btn-sm" 
                                        onclick="return confirm('Are you sure you want to delete this product permanently?');">
                                            <i class="fa fa-trash"></i> Delete
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
    <?php elseif ($current_action == 'add' || $current_action == 'edit'): 
        
        
        $form_title = ($current_action == 'add') ? 'Add New Product' : 'Edit Product';
        $product = ['id' => 0, 'name' => '', 'price' => '', 'category' => '', 'short_description' => '', 'description' => '', 'ingredients' => '', 'image_url' => ''];
        
        if ($current_action == 'edit' && $product_id > 0) {
            $stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
            $stmt->execute([$product_id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$product) { 
                echo '<div class="alert alert-danger">Product not found.</div>';
                $current_action = 'list'; 
                return; 
            }
        }
    ?>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h3 class="mb-4 text-center"><?php echo $form_title; ?></h3>
            
            <form method="POST" action="admin.php" class="card p-4 shadow-sm">
                <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['id']); ?>">
                <input type="hidden" name="action" value="<?php echo $current_action; ?>">

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Product Name</label>
                        <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="price" class="form-label">Price (SAR)</label>
                        <input type="number" step="0.01" id="price" name="price" class="form-control" value="<?php echo htmlspecialchars($product['price']); ?>" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6"> 
                        <label for="category" class="form-label">Category</label>
                        <select id="category" name="category" class="form-select" required>
                            <option value="" disabled <?php echo empty($product['category']) ? 'selected' : ''; ?>>Select Category</option>
                            <?php 
                            $categories = ['Cakes', 'Bread'];
                            foreach ($categories as $cat):
                                $selected = ($cat === $product['category']) ? 'selected' : '';
                            ?>
                                <option value="<?php echo $cat; ?>" <?php echo $selected; ?>><?php echo $cat; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="image_url" class="form-label">Image URL (e.g., img/P8.jpeg)</label>
                        <input type="text" id="image_url" name="image_url" class="form-control" value="<?php echo htmlspecialchars($product['image_url']); ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="short_description" class="form-label">Short Description (For Cards/Category View)</label>
                    <input type="text" id="short_description" name="short_description" class="form-control" value="<?php echo htmlspecialchars($product['short_description']); ?>" maxlength="150" required>
                    <small class="form-text text-muted">Max 150 characters for product card display.</small>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Long Description (For Product Details Page)</label>
                    <textarea id="description" name="description" class="form-control" rows="3" required><?php echo htmlspecialchars($product['description']); ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="ingredients" class="form-label">Ingredients</label>
                    <textarea id="ingredients" name="ingredients" class="form-control" rows="2"><?php echo htmlspecialchars($product['ingredients']); ?></textarea>
                    <small class="form-text text-muted">Detailed list of ingredients.</small>
                </div>

                <button type="submit" name="save_product" class="btn btn-primary w-100 mt-3">
                    <i class="fa fa-save me-2"></i> Save Product
                </button>
                <a href="admin.php" class="btn btn-secondary w-100 mt-2">Cancel and Back to List</a>
            </form>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
