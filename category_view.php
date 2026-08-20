
<?php
session_start();
include 'db_connect.php'; 
include 'header.php'; 

if (!isset($_GET['category']) || empty($_GET['category'])) {
    header('Location: product.php'); 
    exit;
}

$current_category = htmlspecialchars($_GET['category']);
$products_per_page = 3; 
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $products_per_page;

$products = [];
$total_products = 0;

try {
    $stmt_count = $db->prepare("SELECT COUNT(id) FROM products WHERE category = ?");
    $stmt_count->execute([$current_category]);
    $total_products = $stmt_count->fetchColumn();

    $stmt = $db->prepare("SELECT id, name, short_description, price, image_url FROM products WHERE category = ? ORDER BY id ASC LIMIT ? OFFSET ?"); 
    $stmt->bindParam(1, $current_category);
    $stmt->bindParam(2, $products_per_page, PDO::PARAM_INT);
    $stmt->bindParam(3, $offset, PDO::PARAM_INT);
    $stmt->execute(); 
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo '<div class="container mt-5"><div class="alert alert-danger">Database Error: ' . $e->getMessage() . '</div></div>';
}

$total_pages = ceil($total_products / $products_per_page);
?>

<div class="container-fluid page-header py-6 wow fadeIn" data-wow-delay="0.1s">
    <div class="container text-center pt-5 pb-3">
        <h1 class="display-4 text-white animated slideInDown mb-3"><?php echo $current_category; ?></h1>
    </div>
</div>

<div class="container-xxl bg-light py-6 my-6 pb-0">
    <div class="container">
        <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 500px;">
            <p class="text-primary text-uppercase mb-2"> Products</p>
            <h1 class="display-6 mb-4">Explore Our <?php echo $current_category; ?> Collection</h1>
        </div>
        
        <div class="row g-4 justify-content-center">
        
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                <div class="product-item d-flex flex-column bg-white rounded overflow-hidden h-100 shadow-sm">
                    
                    <div class="position-relative">
                        <img class="img-fluid" src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="height: 200px; width: 100%; object-fit: cover;">
                        </div>
                    
                    <div class="text-center p-4">
                        <h4 class="mb-3 text-dark"><?php echo htmlspecialchars($product['name']); ?></h4>
                        <p class="text-muted"><?php echo htmlspecialchars($product['short_description']); ?></p>
                        <h5 class="text-primary mb-3">SAR<?php echo number_format($product['price'], 2); ?></h5>
                        
                        <div class="d-flex justify-content-center">
                            <a href="product_details.php?id=<?php echo $product['id']; ?>" class="btn btn-primary py-2 px-3 mx-1" title="View Details">
                                <i class="fa fa-eye me-2"></i> View
                            </a>
                            <a href="add_to_cart.php?id=<?php echo $product['id']; ?>" class="btn btn-outline-primary py-2 px-3 mx-1" title="Add to Cart">
                            <i class="fa fa-shopping-cart me-2"></i> Cart
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center">
                <p class="text-danger">⚠️ No products found in the *<?php echo $current_category; ?>* category.</p>
            </div>
        <?php endif; ?>
        
        </div>
        
        <?php if ($total_pages > 1): ?>
        <div class="row mt-5 wow fadeInUp" data-wow-delay="0.1s">
            <div class="col-12">
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?php echo ($current_page <= 1) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="category_view.php?category=<?php echo urlencode($current_category); ?>&page=<?php echo $current_page - 1; ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                        
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php echo ($i == $current_page) ? 'active' : ''; ?>">
                            <a class="page-link" href="category_view.php?category=<?php echo urlencode($current_category); ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                        <?php endfor; ?>
                        
                        <li class="page-item <?php echo ($current_page >= $total_pages) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="category_view.php?category=<?php echo urlencode($current_category); ?>&page=<?php echo $current_page + 1; ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>

<?php 
include 'footer.php'; 
?>