<?php
require 'vendor/autoload.php'; // Include the Composer autoloader

// Load environment variables from the .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Retrieve the Stripe secret key from the .env file
$stripeSecretKey = $_ENV['STRIPE_SECRET_KEY']; // Ensure this matches the key in your .env file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    \Stripe\Stripe::setApiKey($stripeSecretKey); // Use the key from the .env file

    try {
        // Create a customer using the Stripe API
        $customer = \Stripe\Customer::create([
            'name' => $_POST['name'],
            'email' => $_POST['email'],
            'address' => [
                'line1' => $_POST['address_line1'],
                'city' => $_POST['city'],
                'state' => $_POST['state'],
                'postal_code' => $_POST['postal_code'],
                'country' => 'US'
            ],
            'phone' => $_POST['phone'],
        ]);
        echo "Customer created successfully. Customer ID: {$customer->id}";
    } catch (\Stripe\Exception\ApiErrorException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
<!-- HTML Form -->
<form method="POST" action="">
    <input type="text" name="name" placeholder="Name" required />
    <input type="email" name="email" placeholder="Email" required />
    <input type="text" name="address_line1" placeholder="Address" required />
    <input type="text" name="city" placeholder="City" required />
    <input type="text" name="state" placeholder="State" required />
    <input type="text" name="postal_code" placeholder="Postal Code" required />
    <input type="text" name="phone" placeholder="Phone" required />
    <button type="submit">Create Customer</button>
</form>
