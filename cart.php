
<?php
session_start();
include 'db_connect.php'; 
include 'header.php'; 

$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$products = [];
$grand_total = 0.00;


if (!empty($cart_items)) {
    $ids = implode(',', array_keys($cart_items));
    
    try {
        $stmt = $db->prepare("SELECT id, name, price, image_url FROM products WHERE id IN ($ids)");
        $stmt->execute();
        
        while ($product = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $product_id = $product['id'];
            if (isset($cart_items[$product_id])) {
                $quantity = $cart_items[$product_id];
                $subtotal = $product['price'] * $quantity;
                $grand_total += $subtotal;
                
                $products[$product_id] = [
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'quantity' => $quantity,
                    'subtotal' => $subtotal
                ];
            }
        }
    } catch (PDOException $e) {
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_quantity'])) {
    $product_id = (int)$_POST['product_id'];
    $new_quantity = (int)$_POST['quantity'];
    
    if ($new_quantity > 0 && array_key_exists($product_id, $_SESSION['cart'])) {
        $_SESSION['cart'][$product_id] = $new_quantity;
    } elseif ($new_quantity <= 0 && array_key_exists($product_id, $_SESSION['cart'])) {
        unset($_SESSION['cart'][$product_id]); 
    }
    header('Location: cart.php');
    exit;
}

if (isset($_GET['remove_id']) && is_numeric($_GET['remove_id'])) {
    $remove_id = (int)$_GET['remove_id'];
    if (array_key_exists($remove_id, $_SESSION['cart'])) {
        unset($_SESSION['cart'][$remove_id]);
    }
    header('Location: cart.php');
    exit;
}
?>

<div class="container-fluid page-header py-6 wow fadeIn" data-wow-delay="0.1s">
    <div class="container text-center pt-5 pb-3">
        <h1 class="display-4 text-white animated slideInDown mb-3">Your Shopping Cart</h1>
    </div>
</div>

<div class="container-xxl bg-light py-6 my-6 pb-0">
    <div class="container">
        <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 500px;">
            <p class="text-primary text-uppercase mb-2"> Your Order</p>
            <h1 class="display-6 mb-4">Your Items</h1>
        </div>

        <div class="row wow fadeInUp" data-wow-delay="0.3s">
            <div class="col-12">
                
                <?php if (empty($products)): ?>
                    <div class="alert alert-info text-center">
                        <i class="fa fa-shopping-basket me-2"></i> Your cart is empty.
                        <br><a href="product.php" class="alert-link">Continue Shopping</a>
                    </div>
                <?php else: ?>
                
                <div class="table-responsive">
                    <table class="table table-bordered align-middle text-center bg-white shadow-sm">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $id => $item): ?>
                            <tr>
                                <td class="text-start"><?php echo htmlspecialchars($item['name']); ?></td>
                                <td>SAR<?php echo number_format($item['price'], 2); ?></td>
                                
                                <td>
                                    <form action="cart.php" method="POST" class="d-flex justify-content-center">
                                        <input type="hidden" name="product_id" value="<?php echo $id; ?>">
                                        <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="0" class="form-control w-50 me-2" required>
                                        <button type="submit" name="update_quantity" class="btn btn-sm btn-outline-secondary" title="Update Quantity">
                                            <i class="fa fa-sync-alt"></i>
                                        </button>
                                    </form>
                                </td>
                                
                                <td>SAR<?php echo number_format($item['subtotal'], 2); ?></td>
                                
                                <td>
                                    <a href="cart.php?remove_id=<?php echo $id; ?>" class="btn btn-sm btn-danger" title="Remove Item">
                                        <i class="fa fa-trash"></i> Remove
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Grand Total:</th>
                                <th>SAR<?php echo number_format($grand_total, 2); ?></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                <div class="d-flex justify-content-end mt-4">
                    <a href="product.php" class="btn btn-outline-secondary me-2">Continue Shopping</a>
                <a href="checkout.php" class="btn btn-primary">Proceed to Checkout</a>
                </div>

                <?php endif; ?>
                
            </div>
        </div>
    </div>
</div>

<?php 
include 'footer.php'; 
?>
