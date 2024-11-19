<?php
require 'vendor/autoload.php'; 


$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();


$stripeSecretKey = $_ENV['STRIPE_SECRET_KEY']; 

$message = ""; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    \Stripe\Stripe::setApiKey($stripeSecretKey); 

    try {
        
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
        $message = "<div class='success'>Customer created successfully. Customer ID: {$customer->id}</div>";
    } catch (\Stripe\Exception\ApiErrorException $e) {
        $message = "<div class='error'>Error: " . $e->getMessage() . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Customer</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .form-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            box-sizing: border-box;
        }
        .form-container h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #333333;
        }
        .form-container .message {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 5px;
            font-size: 14px;
        }
        .form-container .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .form-container .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .form-container input {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #cccccc;
            border-radius: 5px;
            font-size: 16px;
        }
        .form-container button {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: #ffffff;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .form-container button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Create Customer</h1>
        <?php if (!empty($message)) echo "<div class='message'>{$message}</div>"; ?>
        <form method="POST" action="">
            <input type="text" name="name" placeholder="Full Name" required />
            <input type="email" name="email" placeholder="Email Address" required />
            <input type="text" name="address_line1" placeholder="Address Line 1" required />
            <input type="text" name="city" placeholder="City" required />
            <input type="text" name="state" placeholder="State" required />
            <input type="text" name="postal_code" placeholder="Postal Code" required />
            <input type="text" name="phone" placeholder="Phone Number" required />
            <button type="submit">Create Customer</button>
        </form>
    </div>
</body>
</html>
