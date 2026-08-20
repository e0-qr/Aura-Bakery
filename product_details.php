
<?php 

session_start();
include 'db_connect.php'; 
include 'header.php'; 


if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: product.php'); 
    exit;
}

$product_id = (int)$_GET['id'];
$product = null;

try {

    $stmt = $db->prepare("SELECT id, name, description, short_description, ingredients, price, category, image_url FROM products WHERE id = ?"); 
    $stmt->execute([$product_id]); 
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo '<div class="container mt-5"><div class="alert alert-danger">Database Error: ' . $e->getMessage() . '</div></div>';
}

if (!$product): ?>

    <div class="container mt-5">
        <div class="alert alert-warning text-center">
            <h3>⚠️ Product Not Found</h3>
            <p>The requested product ID (<?php echo $product_id; ?>) does not exist.</p>
        </div>
    </div>

<?php else: ?>

<div class="container-fluid page-header py-6 wow fadeIn" data-wow-delay="0.1s">
    <div class="container text-center pt-5 pb-3">
        <h1 class="display-4 text-white animated slideInDown mb-3"><?php echo htmlspecialchars($product['name']); ?></h1>
    </div>
</div>

<div class="container-xxl py-6 my-6 pb-0">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-5 wow fadeInUp" data-wow-delay="0.1s">
                <div class="position-relative overflow-hidden rounded mb-4 shadow-lg">
                    <img class="img-fluid w-100" src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="height: 400px; object-fit: cover;">
                </div>
                
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h3 class="text-primary mb-0">SAR<?php echo number_format($product['price'], 2); ?></h3>
                    <span class="badge bg-secondary fs-6"><?php echo htmlspecialchars($product['category']); ?></span>
                </div>
                
                <div class="mb-4">
                    <a href="add_to_cart.php?id=<?php echo $product_id; ?>" class="btn btn-primary py-2 w-100" title="Add to Cart">
                        <i class="fa fa-shopping-cart me-2"></i> Add to Cart
                    </a>
                </div>
                
                <p class="mb-4 text-muted fs-5"><?php echo htmlspecialchars($product['short_description']); ?></p>
            </div>
            
            <div class="col-lg-7 wow fadeInUp" data-wow-delay="0.5s">
                <h2 class="mb-4">Full Details</h2>
                
                <div class="mb-4 p-4 rounded bg-white shadow-sm">
                    <h4 class="text-dark mb-3"><i class="fa fa-info-circle text-primary me-2"></i> Product Description</h4>
                    <p class="text-justify"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                </div>

                <div class="mb-4 p-4 rounded bg-white shadow-sm">
                    <h4 class="text-dark mb-3"><i class="fa fa-list-alt text-primary me-2"></i> Ingredients</h4>
                    <p class="text-justify"><?php echo nl2br(htmlspecialchars($product['ingredients'])); ?></p>
                </div>
                
                <a href="category_view.php?category=<?php echo urlencode($product['category']); ?>" class="btn btn-primary py-2 px-4">Back to <?php echo htmlspecialchars($product['category']); ?></a>
            </div>
        </div>
    </div>
</div>

<?php endif; ?>

<?php 
include 'footer.php'; 
?>