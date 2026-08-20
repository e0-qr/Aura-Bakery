
<?php 

session_start();
include 'db_connect.php'; 
include 'header.php'; 

$categories = [];
try {
    
    $stmt = $db->prepare("SELECT DISTINCT category FROM products"); 
    $stmt->execute(); 
    $categories_list = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $category_data = [];
    foreach ($categories_list as $category) {
        
        $stmt_img = $db->prepare("SELECT image_url FROM products WHERE category = ? LIMIT 1");
        $stmt_img->execute([$category]);
        
        $image_url = $stmt_img->fetchColumn() ?: 'ima/default_category.jpg'; 
        
        $category_data[] = [
            'name' => $category,
            'image' => $image_url
        ];
    }

} catch (PDOException $e) {
    echo '<div class="container mt-5"><div class="alert alert-danger">Database Error: ' . $e->getMessage() . '</div></div>';
    $categories_list = [];
}

?>

<div class="container-fluid page-header py-6 wow fadeIn" data-wow-delay="0.1s">
    <div class="container text-center pt-5 pb-3">
        <h1 class="display-4 text-white animated slideInDown mb-3">Product Categories</h1>
    </div>
</div>

<div class="container-xxl bg-light py-6 my-6 pb-0">
    <div class="container">
        <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 500px;">
            <p class="text-primary text-uppercase mb-2">Select Your Treat</p>
            <h1 class="display-6 mb-4">Browse Our Delicious Categories</h1>
        </div>
        
        <div class="row g-4 justify-content-center">
        
        <?php if (!empty($category_data)): ?>
            <?php foreach ($category_data as $cat): ?>
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                <a href="category_view.php?category=<?php echo urlencode($cat['name']); ?>" class="text-decoration-none">
                    <div class="product-item d-flex flex-column bg-white rounded overflow-hidden h-100 shadow-sm">
                        <div class="position-relative">
                            <img class="img-fluid" src="<?php echo htmlspecialchars($cat['image']); ?>" alt="<?php echo htmlspecialchars($cat['name']); ?>" style="height: 200px; width: 100%; object-fit: cover;">
                        </div>
                        <div class="text-center p-4">
                            <h4 class="mb-3 text-dark"><?php echo htmlspecialchars($cat['name']); ?></h4>
                            <p class="text-muted">Click to view all <?php echo htmlspecialchars($cat['name']); ?> products.</p>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center">
                <p class="text-danger">⚠️ No product categories found in the database.</p>
            </div>
        <?php endif; ?>
        
        </div>
    </div>
</div>

<?php 
include 'footer.php'; 
?>