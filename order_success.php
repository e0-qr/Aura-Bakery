
<?php


session_start();
include 'db_connect.php';
include 'header.php';


if (!isset($_GET['order_id']) || !is_numeric($_GET['order_id'])) {
    
    header('Location: index.php');
    exit;
}

$order_id = (int)$_GET['order_id'];
$order_details = [];
$order_items = [];
$error_message = '';

try {
    
    $stmt = $db->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->execute([$order_id]);
    $order_details = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order_details) {
        $error_message = "❌ Order ID #{$order_id} not found.";
    } else {
        
        $stmt_items = $db->prepare("
            SELECT oi.*, p.name 
            FROM order_items oi
            JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = ?
        ");
        $stmt_items->execute([$order_id]);
        $order_items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);
    }

} catch (PDOException $e) {
    $error_message = "❌ Database error: " . $e->getMessage();
}

?>

<div class="container-fluid py-6" style="min-height: 500px;">
    <div class="container">
        <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 700px;">
            <p class="text-primary text-uppercase mb-2"> Order Confirmation </p>
            <h1 class="display-6 mb-4">Thank You for Your Order!</h1>
        </div>
        
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger text-center"><?php echo $error_message; ?></div>
            <div class="text-center mt-3">
                <a href="product.php" class="btn btn-primary">Continue Shopping</a>
            </div>
        <?php elseif (!empty($order_details)): ?>
            
            <div class="row g-5 wow fadeInUp" data-wow-delay="0.3s">
                
                <div class="col-lg-6">
                    <div class="card shadow-sm p-4 h-100">
                        <h4 class="mb-3 text-primary">Order #<?php echo $order_details['id']; ?> Summary</h4>
                        <p><strong>Date:</strong> <?php echo date('Y-m-d H:i', strtotime($order_details['order_date'])); ?></p>
                        <p><strong>Grand Total:</strong> SAR<span class="text-danger fs-5"><?php echo number_format($order_details['grand_total'], 2); ?></span></p>
                        <p><strong>Payment:</strong> <?php echo strtoupper($order_details['payment_method']); ?></p>

                        <h5 class="mt-4 mb-2">Shipping Information</h5>
                        <ul class="list-unstyled">
                            <li><strong>Name:</strong> <?php echo htmlspecialchars($order_details['first_name'] . ' ' . $order_details['last_name']); ?></li>
                            <li><strong>Email:</strong> <?php echo htmlspecialchars($order_details['email']); ?></li>
                            <li><strong>Phone:</strong> <?php echo htmlspecialchars($order_details['phone']); ?></li>
                            <li><strong>Address:</strong> <?php echo htmlspecialchars($order_details['address']); ?></li>
                        </ul>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card shadow-sm p-4 h-100">
                        <h4 class="mb-3 text-primary">Items Ordered</h4>
                        <ul class="list-group">
                            <?php foreach($order_items as $item): ?>
                            <li class="list-group-item d-flex justify-content-between">
                                <div>
                                    <h6 class="my-0"><?php echo htmlspecialchars($item['name']); ?></h6>
                                    <small class="text-muted"><?php echo $item['quantity']; ?> x SAR<?php echo number_format($item['price'], 2); ?></small>
                                </div>
<span class="fw-bold">SAR<?php echo number_format($item['quantity'] * $item['price'], 2); ?></span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                
                <div class="col-12 text-center mt-5">
                    <a href="product.php" class="btn btn-primary btn-lg rounded-pill px-5">Continue Shopping</a>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php 
include 'footer.php'; 
?>