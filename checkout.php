
<?php

session_start();
include 'db_connect.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    
    
    if (empty($_SESSION['cart'])) {
        header('Location: cart.php?error=no_items');
        exit;
    }

    $firstname      = trim($_POST['firstname']);
    $lastname       = trim($_POST['lastname']);
    $address        = trim($_POST['address']);
    $phone          = trim($_POST['phone']);
    $email          = trim($_POST['email']);
    $payment_method = $_POST['payment_method']; 

    
    $cart_items = $_SESSION['cart'];
    $grand_total = 0.00;
    $product_prices = []; 
    $ids = implode(',', array_keys($cart_items));

    if (!empty($ids)) {
        try {
            $placeholders = rtrim(str_repeat('?,', count($cart_items)), ',');
            $stmt = $db->prepare("SELECT id, price FROM products WHERE id IN ($placeholders)");
            $stmt->execute(array_keys($cart_items));
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $product_prices[$row['id']] = $row['price'];
                $grand_total += $row['price'] * $cart_items[$row['id']];
            }
        } catch (PDOException $e) {
            header('Location: checkout.php?error=price_fetch_failed');
            exit;
        }
    }
    
    
    try {
        $db->beginTransaction(); 
        
        $sql_order = "INSERT INTO orders (user_id, first_name, last_name, address, phone, email, grand_total, payment_method) 
                    VALUES (:user_id, :first_name, :last_name, :address, :phone, :email, :grand_total, :payment_method)";
        
        $stmt_order = $db->prepare($sql_order);
        
        $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : NULL; 
        
        $stmt_order->execute([
            ':first_name'    => $firstname,
            ':last_name'     => $lastname,
            ':address'       => $address,
            ':phone'         => $phone,
            ':email'         => $email,
            ':grand_total'   => $grand_total,
            ':payment_method'=> $payment_method
        ]);
        
        $order_id = $db->lastInsertId();
        
        $sql_item = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (:order_id, :product_id, :quantity, :price)";
        $stmt_item = $db->prepare($sql_item);

        foreach ($cart_items as $product_id => $quantity) {
            $price_at_sale = $product_prices[$product_id] ?? 0.00; 
            
            if ($price_at_sale > 0) {
                $stmt_item->execute([
                    ':order_id'    => $order_id,
                    ':product_id'  => $product_id,
                    ':quantity'    => $quantity,
                    ':price'       => $price_at_sale
                ]);
            }
        }
        
        $db->commit(); 
        
        unset($_SESSION['cart']); 
        

        header('Location: order_success.php?order_id=' . $order_id);
        exit;

    } catch (Exception $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        
        die("An error occurred during order processing. Please try again. Error: " . $e->getMessage());
    }
}



include 'header.php';

if (empty($_SESSION['cart'])) {
    ?>
    <div class="container py-6" style="min-height: 500px;">
        <div class="alert alert-warning text-center mt-5">
            🛒 Your cart is empty. Please add items before proceeding to checkout.
        </div>
        <div class="text-center">
            <a href="product.php" class="btn btn-primary mt-3">Go to Products</a>
        </div>
    </div>
    <?php 
    include 'footer.php';
    exit;
}


$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$products_in_cart = [];
$grand_total = 0.00;
$ids = implode(',', array_keys($cart_items));

if (!empty($ids)) {
    try {
        $placeholders = rtrim(str_repeat('?,', count($cart_items)), ',');
        $stmt = $db->prepare("SELECT id, name, price FROM products WHERE id IN ($placeholders)");
        $stmt->execute(array_keys($cart_items));
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $quantity = $cart_items[$row['id']];
            $subtotal = $row['price'] * $quantity;
            $grand_total += $subtotal;
            
            $products_in_cart[$row['id']] = [
                'name' => $row['name'],
                'price' => $row['price'],
                'quantity' => $quantity,
                'subtotal' => $subtotal
            ];
        }
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Error calculating totals.</div>";
    }
}

?>

<div class="container-fluid py-6">
    <div class="container">
        <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 500px;">
            <p class="text-primary text-uppercase mb-2"> Checkout </p>
            <h1 class="display-6 mb-4">Complete Your Order</h1>
        </div>

        <form method="POST" action="checkout.php" class="wow fadeInUp" data-wow-delay="0.5s">
            <div class="row g-5">
                <div class="col-lg-7">
                    <h5 class="mb-4">Billing Details</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="firstname" name="firstname" placeholder="First Name" required>
                                <label for="firstname">First Name</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="lastname" name="lastname" placeholder="Last Name" required>
                                <label for="lastname">Last Name</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="address" name="address" placeholder="Address" required>
                                <label for="address">Address</label>
                            </div>
                        </div>
<div class="col-md-6">
                            <div class="form-floating">
                                <input type="tel" class="form-control" id="phone" name="phone" placeholder="Phone Number" required>
                                <label for="phone">Phone Number</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="email" class="form-control" id="email" name="email" placeholder="Email Address" required>
                                <label for="email">Email Address</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="bg-light p-4 rounded-3 mb-4">
                        <h5 class="mb-3">Order Summary</h5>
                        <ul class="list-group mb-3">
                            <?php foreach($products_in_cart as $item): ?>
                            <li class="list-group-item d-flex justify-content-between lh-sm">
                                <div>
                                    <h6 class="my-0"><?php echo htmlspecialchars($item['name']); ?></h6>
                                    <small class="text-muted">Quantity: <?php echo $item['quantity']; ?></small>
                                </div>
                                <span class="text-muted">SAR<?php echo number_format($item['subtotal'], 2); ?></span>
                            </li>
                            <?php endforeach; ?>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Shipping (Flat Rate)</span>
                                <strong>SAR0.00</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between bg-dark text-white">
                                <span>Grand Total (SAR)</span>
                                <strong>SAR<?php echo number_format($grand_total, 2); ?></strong>
                            </li>
                        </ul>
                    </div>

                    <h5 class="mb-4">Payment Method</h5>
                    <div class="d-block my-3">
                        <div class="form-check">
                            <input id="credit" name="payment_method" type="radio" class="form-check-input" value="credit" checked required>
                            <label class="form-check-label" for="credit">Credit Card / Online Payment</label>
                        </div>
                        <div class="form-check">
                            <input id="cod" name="payment_method" type="radio" class="form-check-input" value="cod" required>
                            <label class="form-check-label" for="cod">Cash on Delivery (COD)</label>
                        </div>
                    </div>

                    <div id="card-details" class="row g-3 mb-4 p-3 border rounded">
                        <div class="col-12">
                            <div class="form-floating">
                                <input type="text" class="form-control card-input" id="cardname" placeholder="Name on Card" required>
                                <label for="cardname">Name on Card</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating">
                                <input type="text" class="form-control card-input" id="cardnumber" placeholder="Credit Card Number" required>
                                <label for="cardnumber">Credit Card Number</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
<input type="text" class="form-control card-input" id="expiration" placeholder="Expiration" required>
                                <label for="expiration">Expiration (MM/YY)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control card-input" id="cvv" placeholder="CVV" required>
                                <label for="cvv">CVV</label>
                            </div>
                        </div>
                    </div>
                    <button type="submit" name="place_order" class="btn btn-primary w-100 py-3">Place Order</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php 
include 'footer.php'; 
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const creditRadio = document.getElementById('credit');
    const codRadio = document.getElementById('cod');
    const cardDetails = document.getElementById('card-details');
    const cardInputs = cardDetails.querySelectorAll('.card-input');

    function togglePaymentFields() {
        if (codRadio.checked) {
            cardDetails.style.display = 'none';
            cardInputs.forEach(input => input.removeAttribute('required'));
        } else {
            cardDetails.style.display = 'flex';
            cardInputs.forEach(input => input.setAttribute('required', ''));
        }
    }

    creditRadio.addEventListener('change', togglePaymentFields);
    codRadio.addEventListener('change', togglePaymentFields);

    togglePaymentFields();
});
</script>
