
<?php 
include 'db_connect.php'; 

$message_status = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (!empty($_POST['visitor_name']) && !empty($_POST['visitor_message'])) {
        try {
            
            $name = $_POST['visitor_name'];
            $email = $_POST['visitor_email'];
            $subject = $_POST['visitor_subject'];
            $message = $_POST['visitor_message'];

            $stmt = $db->prepare("INSERT INTO inquiries (visitor_name, visitor_email, visitor_subject, visitor_message) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $subject, $message]);

            
            $message_status = '<div class="alert alert-success text-center">✅ Your Inquiry has been received and will be dealt with seriously, thank you.</div>';
        } catch (PDOException $e) {
            $message_status = '<div class="alert alert-danger text-center">❌ Error saving data: ' . $e->getMessage() . '</div>';
        }
    } else {
        $message_status = '<div class="alert alert-warning text-center">Please fill in all required fields (Name and Message).</div>';
    }
}

include 'header.php'; 
?>

    <div class="container-fluid page-header py-6 wow fadeIn" data-wow-delay="0.1s">
        <div class="container text-center pt-5 pb-3">
            <h1 class="display-4 text-white animated slideInDown mb-3">Contact Us</h1>
            <nav aria-label="breadcrumb animated slideInDown">
                <ol class="breadcrumb justify-content-center mb-0">
                    <li class="breadcrumb-item"><a class="text-white" href="index.php">Home</a></li>
                    <li class="breadcrumb-item text-primary active" aria-current="page">Contact</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="container-xxl py-6">
        <div class="container">
            <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 500px;">
                <p class="text-primary text-uppercase mb-2">Contact Us</p>
                <h1 class="display-6 mb-4">If You Have Inquiry, Please Contact Us</h1>
            </div>
            <div class="row g-0 justify-content-center">
                <div class="col-lg-8 wow fadeInUp" data-wow-delay="0.1s">
                    <?php echo $message_status; ?>

                    <form method="POST">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="name" name="visitor_name" placeholder="Your Name" required>
                                    <label for="name">Your Name</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="email" class="form-control" id="email" name="visitor_email" placeholder="Your Email">
                                    <label for="email">Your Email</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="subject" name="visitor_subject" placeholder="Subject">
                                    <label for="subject">Subject</label>
                                </div>
                            </div>
                            <div class="col-12">
<div class="form-floating">
                                    <textarea class="form-control" placeholder="Leave a message here" id="message" name="visitor_message" style="height: 200px" required></textarea>
                                    <label for="message">Message</label>
                                </div>
                            </div>
                            <div class="col-12 text-center">
                                <button class="btn btn-primary rounded-pill py-3 px-5" type="submit">Send Message</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="container-xxl py-6 px-0 wow fadeInUp" data-wow-delay="0.1s">
        <iframe class="w-100 mb-n2" style="height: 450px;"
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3001156.4288297426!2d-78.01371936852176!3d42.72876761954724!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x4ccc4bf0f123a5a9%3A0xddcfc6c1de189567!2sNew%20York%2C%20USA!5e0!3m2!1sen!2sbd!4v1603794290143!5m2!1sen!2sbd"
            frameborder="0" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>
    </div>
    <?php include 'footer.php'; ?>
