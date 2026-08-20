<?php

session_start();
include 'db_connect.php';
include 'header.php'; 

$category_data = [];
try {
    $stmt = $db->prepare("SELECT DISTINCT category FROM products"); 
    $stmt->execute(); 
    $categories_list = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($categories_list as $category) {
        $stmt_img = $db->prepare("SELECT image_url FROM products WHERE category = ? LIMIT 1");
        $stmt_img->execute([$category]);
        $image_url = $stmt_img->fetchColumn() ?: 'img/default.jpg'; 
        
        $category_data[] = [
            'name' => $category,
            'image' => $image_url
        ];
    }
} catch (PDOException $e) {
}
?>

<?php if (isset($_GET['order_success']) && $_GET['order_success'] == 1): ?>
    <div class="container wow fadeInDown" data-wow-delay="0.1s" style="margin-top: 100px; position: relative; z-index: 9999;">
        <div class="alert alert-success alert-dismissible fade show text-center shadow-lg border-0" role="alert" style="background-color: #d1e7dd; color: #0f5132;">
            <i class="fa fa-check-circle fa-2x align-middle me-2"></i> 
            <span class="fs-5 align-middle fw-bold">Your order has been successfully received. Thank ypu for shopping with us.</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
<?php endif; ?>

<div class="container-fluid p-0 pb-5 wow fadeIn" data-wow-delay="0.1s">
    <div class="owl-carousel header-carousel position-relative">
        <div class="owl-carousel-item position-relative">
            <img class="img-fluid" src="img/carousel-1.jpg" alt="">
            <div class="owl-carousel-inner">
                <div class="container">
                    <div class="row justify-content-start">
                        <div class="col-lg-8">
                            <p class="text-primary text-uppercase fw-bold mb-2"> The Best Bakery</p>
                            <h1 class="display-1 text-light mb-4 animated slideInDown">We Bake With Passion</h1>
                            <p class="text-light fs-5 mb-4 pb-3">Welcome to our bakery, where every item is freshly crafted with care.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="owl-carousel-item position-relative">
            <img class="img-fluid" src="img/carousel-2.jpg" alt="">
            <div class="owl-carousel-inner">
                <div class="container">
                    <div class="row justify-content-start">
                        <div class="col-lg-8">
                            <p class="text-primary text-uppercase fw-bold mb-2">The Best Bakery</p>
                            <h1 class="display-1 text-light mb-4 animated slideInDown">Fresh Ingredients</h1>
                            <p class="text-light fs-5 mb-4 pb-3">Discover our daily-baked pastries made with quality ingredients.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-xxl py-6">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-3 col-md-6 wow fadeIn" data-wow-delay="0.1s">
                <div class="fact-item bg-light rounded text-center h-100 p-5">
                    <i class="fa fa-certificate fa-4x text-primary mb-4"></i>
                    <p class="mb-2">Years Experience</p>
                    <h1 class="display-5 mb-0" data-toggle="counter-up">50</h1>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 wow fadeIn" data-wow-delay="0.3s">
                <div class="fact-item bg-light rounded text-center h-100 p-5">
                    <i class="fa fa-users fa-4x text-primary mb-4"></i>
                    <p class="mb-2">Skilled Professionals</p>
                    <h1 class="display-5 mb-0" data-toggle="counter-up">175</h1>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 wow fadeIn" data-wow-delay="0.5s">
<div class="fact-item bg-light rounded text-center h-100 p-5">
                    <i class="fa fa-bread-slice fa-4x text-primary mb-4"></i>
                    <p class="mb-2">Total Products</p>
                    <h1 class="display-5 mb-0" data-toggle="counter-up">135</h1>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 wow fadeIn" data-wow-delay="0.7s">
                <div class="fact-item bg-light rounded text-center h-100 p-5">
                    <i class="fa fa-cart-plus fa-4x text-primary mb-4"></i>
                    <p class="mb-2">Order Everyday</p>
                    <h1 class="display-5 mb-0" data-toggle="counter-up">9357</h1>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-xxl bg-light my-6 py-6 pt-0">
    <div class="container">
        <div class="bg-primary text-light rounded-bottom p-5 my-6 mt-0 wow fadeInUp" data-wow-delay="0.1s">
            <div class="row g-4 align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 text-light mb-0">The Best Bakery In Your City</h1>
                </div>
                <div class="col-lg-6 text-lg-end">
                    <div class="d-inline-flex align-items-center text-start">
                        <i class="fa fa-phone-alt fa-4x flex-shrink-0"></i>
                        <div class="ms-4">
                            <p class="fs-5 fw-bold mb-0">Call Us</p>
                            <p class="fs-1 fw-bold mb-0">+012 345 6789</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 500px;">
            <p class="text-primary text-uppercase mb-2"> Select Your Treat</p>
            <h1 class="display-6 mb-4">Explore Our Categories</h1>
        </div>

        <div class="row g-4 justify-content-center">
             
            <?php if (!empty($category_data)): ?>
                <?php foreach ($category_data as $cat): ?>
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                    <a href="category_view.php?category=<?php echo urlencode($cat['name']); ?>" class="text-decoration-none">
                        <div class="product-item d-flex flex-column bg-white rounded overflow-hidden h-100 shadow-sm">
                             
                            <div class="position-relative">
                                <img class="img-fluid" src="<?php echo htmlspecialchars($cat['image']); ?>" alt="<?php echo htmlspecialchars($cat['name']); ?>" style="height: 250px; width: 100%; object-fit: cover;">
                            </div>
                             
                            <div class="text-center p-4">
                                <h4 class="mb-3 text-dark"><?php echo htmlspecialchars($cat['name']); ?></h4>
                                <p class="text-muted">Click to view all <?php echo htmlspecialchars($cat['name']); ?> products.</p>
                                <button class="btn btn-primary px-4 rounded-pill">View Products</button>
                            </div>
                        </div>
                    </a>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <p class="text-danger">⚠️ No categories found. Please run setup_db.php first.</p>
                </div>
            <?php endif; ?>
             
        </div>
    </div>
</div>
<div class="container-xxl py-6">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s">
                <div class="row img-twice position-relative h-100">
                    <div class="col-6">
                        <img class="img-fluid rounded" src="img/about-1.jpg" alt="">
                    </div>
<div class="col-6 align-self-end">
                        <img class="img-fluid rounded" src="img/about-2.jpg" alt="">
                    </div>
                </div>
            </div>
            <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.5s">
                <div class="h-100">
                    <p class="text-primary text-uppercase mb-2">About Us</p>
                    <h1 class="display-6 mb-4">We Bake Every Product With Care</h1>
                    <p>Our bakery is dedicated to delivering fresh, high-quality goods made daily with passion. We focus on offering delicious items that bring warmth and comfort to every customer.</p>
                    <div class="row g-2 mb-4">
                        <div class="col-sm-6"><i class="fa fa-check text-primary me-2"></i>Quality Products</div>
                        <div class="col-sm-6"><i class="fa fa-check text-primary me-2"></i>Custom Products</div>
                        <div class="col-sm-6"><i class="fa fa-check text-primary me-2"></i>Online Order</div>
                        <div class="col-sm-6"><i class="fa fa-check text-primary me-2"></i>Home Delivery</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
