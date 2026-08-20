
<?php

$message = "";

try {
    // 1. إعداد الاتصال بقاعدة البيانات
    $db = new PDO("sqlite:bakery.db");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $message .= "✅ Database connection established.<br>";

    // 2. إسقاط الجداول القديمة
    $db->exec("DROP TABLE IF EXISTS order_items");
    $db->exec("DROP TABLE IF EXISTS orders");
    $db->exec("DROP TABLE IF EXISTS users"); // إضافة جدول المستخدمين للإسقاط
    $db->exec("DROP TABLE IF EXISTS inquiries");
    $db->exec("DROP TABLE IF EXISTS products");
    $message .= "✅ Existing tables dropped.<br>";
    
    
    // 3. إنشاء جدول المستخدمين (Users)
    $db->exec("CREATE TABLE users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        first_name TEXT NOT NULL,
        last_name TEXT NOT NULL,
        email TEXT NOT NULL UNIQUE,
        password TEXT NOT NULL
    )");

    // 4. إنشاء جدول المنتجات (Products) - كما كان
    $db->exec("CREATE TABLE products (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        description TEXT NOT NULL,
        short_description TEXT NOT NULL, 
        ingredients TEXT,
        price REAL NOT NULL,
        category TEXT NOT NULL,
        image_url TEXT
    )");

    // 5. إنشاء جدول الاستفسارات (Inquiries) - كما كان
    $db->exec("CREATE TABLE inquiries (id INTEGER PRIMARY KEY AUTOINCREMENT, visitor_name TEXT NOT NULL, visitor_email TEXT, visitor_subject TEXT, visitor_message TEXT NOT NULL, submission_date DATETIME DEFAULT CURRENT_TIMESTAMP)");

    // 6. إنشاء جدول الطلبات (Orders) - تم إضافة user_id
    $db->exec("CREATE TABLE orders (
        id INTEGER PRIMARY KEY AUTOINCREMENT, 
        user_id INTEGER, -- تم إضافة user_id لربط الطلب بمستخدم مسجل (قد يكون NULL لغير المسجلين)
        first_name TEXT NOT NULL, 
        last_name TEXT NOT NULL, 
        address TEXT NOT NULL, 
        phone TEXT NOT NULL, 
        email TEXT NOT NULL, 
        grand_total REAL NOT NULL, 
        payment_method TEXT NOT NULL, 
        order_status TEXT DEFAULT 'Pending', -- إضافة حالة الطلب
        order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
        
        -- تعريف المفتاح الخارجي
        FOREIGN KEY (user_id) REFERENCES users(id)
    )");

    // 7. إنشاء جدول تفاصيل الطلب (Order Items) - كما كان
    $db->exec("CREATE TABLE order_items (
        id INTEGER PRIMARY KEY AUTOINCREMENT, 
        order_id INTEGER NOT NULL, 
        product_id INTEGER NOT NULL, 
        quantity INTEGER NOT NULL, 
        price REAL NOT NULL, 
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE, 
        FOREIGN KEY (product_id) REFERENCES products(id)
    )");
    $message .= "✅ All tables created successfully (including Users and user_id in Orders).<br>";
    
    
    // 8. إدخال مستخدم تجريبي (Admin)
    // كلمة المرور الافتراضية 'admin123'
    $admin_password_hashed = password_hash('admin123', PASSWORD_DEFAULT);
    $db->exec("INSERT INTO users (first_name, last_name, email, password) VALUES ('Admin', 'User', 'admin@example.com', '{$admin_password_hashed}')");
    $message .= "✅ Sample Admin User created (admin@example.com / admin123).<br>";

    // 9. إدخال بيانات المنتجات التجريبية - كما كانت
    $sql_insert_all = "
        INSERT INTO products (name, description, short_description, ingredients, price, category, image_url) VALUES 
        -- C A K E S --------------------------------------------------------------------------------------------------------------------------------
        
        -- 1. Deluxe Chocolate Cake
        ('Deluxe Chocolate Cake', 
        'A decadent chocolate experience crafted from rich, dark cocoa and layered with smooth, velvety chocolate ganache. This cake offers deep flavor notes with a perfectly moist texture, making every bite a luxurious treat. Ideal for celebrations or indulging moments of pure chocolate bliss.', 
        'Rich, moist dark chocolate cake with silky ganache.',
        'Dark cocoa powder, all-purpose flour, fine granulated sugar, fresh eggs, unsalted butter, whole milk, vanilla extract, baking powder, baking soda, a pinch of salt, premium chocolate ganache made from melted dark chocolate and heavy cream, plus a touch of espresso powder to enhance the chocolate flavor.', 
        22.00, 'Cakes', 'img/P1.jpeg'),

        -- 2. Classic Vanilla Cake
        ('Classic Vanilla Cake', 
        'A timeless classic made with pure vanilla extract and a delicately soft sponge. Light, fluffy, and beautifully balanced with smooth buttercream frosting, this cake is an excellent match for elegant events and simple everyday enjoyment.', 
        'Soft vanilla sponge with creamy buttercream.',
        'All-purpose flour, granulated sugar, fresh eggs, unsalted butter, whole milk, pure vanilla extract, baking powder, a pinch of salt, and smooth vanilla buttercream made with butter, powdered sugar, milk, and natural vanilla.', 
        18.00, 'Cakes', 'img/P2.jpeg'),
        
        -- 3. Velvet Red Velvet Cake
        ('Velvet Red Velvet Cake', 
        'A luxurious red velvet creation featuring moist, tender layers infused with a subtle cocoa touch. Finished with silky cream cheese frosting, this cake delivers a perfect harmony of sweetness and tang—visually stunning and irresistibly flavorful.', 
        'Moist red velvet layers with cream cheese frosting.',
        'All-purpose flour, cocoa powder, granulated sugar, fresh eggs, vegetable oil, buttermilk, red food coloring, white vinegar, baking soda, a touch of salt, and silky cream cheese frosting made with cream cheese, butter, powdered sugar, and vanilla.', 
        24.00, 'Cakes', 'img/P3.jpeg'),

        -- 4. Carrot Cinnamon Cake
        ('Carrot Cinnamon Cake', 
        'A hearty and aromatic cake made with freshly grated carrots and a blend of warm spices such as cinnamon and nutmeg. Its natural sweetness and dense moist texture pair beautifully with a bright cream cheese frosting, making it both comforting and elegant.', 
        'Spiced carrot cake topped with cream cheese frosting.',
        'Freshly grated carrots, all-purpose flour, brown sugar, granulated sugar, fresh eggs, vegetable oil, cinnamon, nutmeg, a hint of ginger, crushed walnuts or pecans (optional), baking soda, baking powder, a pinch of salt, and a rich cream cheese frosting made with cream cheese, butter, powdered sugar, and vanilla.', 
        20.00, 'Cakes', 'img/P4.jpeg'),

        -- 5. Date & Caramel Cake
        ('Date & Caramel Cake', 
        'Inspired by rich Middle Eastern flavors, this cake combines soft mashed dates with caramel undertones for a deeply moist and aromatic dessert. Finished with a smooth caramel glaze, it offers layers of natural sweetness in every slice.', 
        'Moist date cake with caramel glaze.',
        'Soft Medjool dates soaked and mashed, all-purpose flour, brown sugar, fresh eggs, unsalted butter, vanilla extract, baking powder, a pinch of salt, homemade caramel sauce made with butter, brown sugar, and cream, plus a light caramel glaze for finishing.', 
        23.00, 'Cakes', 'img/P5.jpeg'),

        -- 6. Refreshing Lemon Cake
        ('Refreshing Lemon Cake', 
        'A bright and zesty cake bursting with fresh lemon flavor. Its tender crumb and light structure are complemented by a tangy lemon glaze that adds shine and an irresistible citrus punch—perfect for warm days and refreshing gatherings.', 
        'Lemon-flavored sponge with tangy lemon glaze.',
        'All-purpose flour, granulated sugar, fresh eggs, unsalted butter, fresh lemon juice, lemon zest, baking powder, baking soda, a pinch of salt, and a bright lemon glaze made with powdered sugar, lemon juice, and extra zest for added aroma.', 
        17.00, 'Cakes', 'img/P6.jpg'),

        -- 7. Russian Honey Cake (Medovik)
        ('Russian Honey Cake (Medovik)', 
        'A traditional Eastern European dessert known for its delicate, caramelized honey layers. Each thin layer is baked to achieve a lightly crisp texture that softens beautifully once stacked with the tangy sour-cream filling. The result is a sophisticated combination of sweetness, creaminess, and a melt-in-the-mouth finish.', 
        'Layered honey cake with creamy sour-cream filling.',
        'Natural honey, all-purpose flour, granulated sugar, unsalted butter, fresh eggs, baking soda activated with gentle heat, a pinch of salt, sour-cream filling made from thick sour cream, powdered sugar, vanilla extract, and finely crushed biscuit crumbs for optional exterior coating.', 
        26.00, 'Cakes', 'img/P7.jpeg'),
        
        -- B R E A D --------------------------------------------------------------------------------------------------------------------------------

        -- 8. Artisan Sourdough Bread
        ('Artisan Sourdough Bread', 
        'A naturally fermented sourdough loaf crafted through a long, slow fermentation process that enhances flavor and texture. Its golden, blistered crust surrounds a chewy, airy center with a mild tang. Perfect for sandwiches, toasting, soups, and gourmet spreads.', 
        'Rustic sourdough with a crisp crust and tangy flavor.',
        'Naturally fermented sourdough starter (flour + water), bread flour, whole wheat flour, filtered water, sea salt, and extended fermentation cultures that develop deep tangy notes and an open crumb structure.', 
        6.00, 'Bread', 'img/B1.jpg'),

        -- 9. Whole Wheat Bread
        ('Whole Wheat Bread', 
        'A wholesome, nutrient-dense loaf made from pure whole wheat flour, offering a warm nutty aroma and a satisfying texture. Its soft crumb and natural flavor make it ideal for everyday meals while providing excellent nutritional value.', 
        'Soft, hearty whole-grain loaf rich in fiber.',
        'Whole wheat flour, warm filtered water, brown sugar or honey, active dry yeast, sea salt, vegetable oil, natural wheat bran, and optional oat flakes for extra fiber and texture.', 
        5.00, 'Bread', 'img/B2.jpg'),

        -- 10. Brioche Bread
        ('Brioche Bread', 
        'A luxurious French brioche enriched with butter and eggs to create an ultra-soft, slightly sweet crumb. Its golden exterior and elegant flavor profile make it perfect for breakfast pastries, gourmet sliders, or premium sandwiches.', 
        'Rich, buttery brioche with a delicate, fluffy texture.',
        'Bread flour, fresh eggs, unsalted butter, whole milk, sugar, sea salt, active dry yeast, a touch of vanilla or orange zest (optional), and an egg wash for a glossy golden finish.', 
        7.00, 'Bread', 'img/B3.jpg'),

        -- 11. French Baguette
        ('French Baguette', 
        'A traditional French baguette baked to perfection with a crisp, crackling crust and tender, chewy interior. Simple ingredients paired with artisan techniques give this bread its iconic aroma and unmistakable French character.', 
        'Classic French baguette with a crunchy crust.',
        'Bread flour, filtered water, sea salt, active dry yeast, optional malt powder, and long fermentation for enhanced aroma and crust development.', 
        4.00, 'Bread', 'img/B4.jpg'),

        -- 12. Focaccia Bread
        ('Focaccia Bread', 
        'A rich, aromatic Italian flatbread with a pillowy soft interior and signature olive-oil depth. Topped with herbs, sea salt, and optional vegetables, it delivers savory flavor and a delightful texture in every bite.', 
        'Soft Italian focaccia topped with herbs and olive oil.',
        'All-purpose flour, warm water, extra virgin olive oil, sea salt, fresh rosemary, active dry yeast, and optional toppings like cherry tomatoes, olives, caramelized onions, plus an olive-oil drizzle before baking.', 
        7.00, 'Bread', 'img/B5.jpg'),

        -- 13. Honey Oat Bread
        ('Honey Oat Bread', 
        'A nurturing whole-grain loaf sweetened lightly with natural honey. Its tender crumb and mild sweetness make it a favorite for breakfast, sandwiches, and healthier daily options.', 
        'Soft, slightly sweet oat bread made with natural honey.',
        'Whole wheat flour, rolled oats, warm water, natural honey, active dry yeast, sea salt, vegetable oil or melted butter, and additional oat flakes on top for texture and rustic appearance.', 
        5.50, 'Bread', 'img/B6.jpg'),

        -- 14. Pain au Lait (French Milk Bread)
        ('Pain au Lait (French Milk Bread)', 
        'A light, tender French milk bread known for its subtle sweetness and buttery aroma. With its golden crust and soft, pillowy interior, Pain au Lait is perfect for breakfast, children’s snacks, or elegant mini sandwiches.', 
        'Soft, slightly sweet French milk bread.',
        'Bread flour, whole milk, sugar, unsalted butter, fresh eggs, sea salt, active dry yeast, optional vanilla, and a glossy egg wash for its characteristic soft golden crust.', 
        5.50, 'Bread', 'img/B7.jpg')
    ";
    
    $db->exec($sql_insert_all);
    $message .= "✅ Total 14 products (Cakes and Bread) inserted successfully.<br>";


} catch (PDOException $e) {
    die('<div style="background-color:#f8d7da; color:#721c24; border:1px solid #f5c6cb; padding:15px; margin:20px;"><h3>Database Setup Error:</h3><p><strong>SQLSTATE:</strong> ' . (isset($e->errorInfo[0]) ? $e->errorInfo[0] : 'N/A') . ' | ' . $e->getMessage() . '</p></div>');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Setup Complete</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-success text-white">
                <h2>Database Setup Complete</h2>
            </div>
            <div class="card-body">
                <p class="lead">SQLite Database (bakery.db) and ALL data created successfully!</p>
                <div class="alert alert-info">
                    <?php echo $message; ?>
                    <p>Total 14 products (7 Cakes, 7 Bread) are ready.</p>
                    <p class="fw-bold text-success">Admin User Login: <span class="text-dark">admin@example.com / admin123</span></p>
                </div>
                <a href="product.php" class="btn btn-primary">Go to Products Page</a>
            </div>
        </div>
    </div>
</body>
</html>