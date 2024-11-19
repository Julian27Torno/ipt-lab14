<?php
require 'vendor/autoload.php'; 


$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();


$stripe = new \Stripe\StripeClient($_ENV['STRIPE_SECRET_KEY']);


$products = $stripe->products->all();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
       
        $lineItems = [];

        if (!empty($_POST['product_ids'])) {
            foreach ($_POST['product_ids'] as $productId) {
              
                $prices = $stripe->prices->all(['product' => $productId]);
                foreach ($prices->data as $price) {
                    if ($price->type === 'one_time') {
                        $lineItems[] = [
                            'price' => $price->id,
                            'quantity' => 1,
                        ];
                    }
                }
            }
        }

      
        $paymentLink = $stripe->paymentLinks->create([
            'line_items' => $lineItems,
        ]);

        
        $paymentUrl = $paymentLink->url;

        echo "<p>Payment Link successfully created!</p>";
        echo "<a href='{$paymentUrl}' target='_blank'>Pay Now</a>";
        exit;
    } catch (\Stripe\Exception\ApiErrorException $e) {
        echo "Error: " . $e->getMessage();
    }
}


$payments = $stripe->paymentIntents->all(['limit' => 10]); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Link Builder</title>
</head>
<body>
    <h1>Payment Link Builder</h1>
    <form method="POST" action="">
        <label>Select Products:</label><br>
        <?php foreach ($products->data as $product): ?>
            <input type="checkbox" name="product_ids[]" value="<?= htmlspecialchars($product->id) ?>">
            <?= htmlspecialchars($product->name) ?><br>
        <?php endforeach; ?>
        <br>
        <button type="submit">Generate Payment Link</button>
    </form>

    <h2>Successful Payments</h2>
    <?php if (!empty($payments->data)): ?>
        <table border="1" cellpadding="10" cellspacing="0">
            <thead>
                <tr>
                    <th>Payment ID</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Payment Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($payments->data as $payment): ?>
                    <tr>
                        <td><?= htmlspecialchars($payment->id) ?></td>
                        <td><?= htmlspecialchars($payment->amount / 100) ?> <?= htmlspecialchars($payment->currency) ?></td>
                        <td><?= htmlspecialchars($payment->status) ?></td>
                        <td><?= date('Y-m-d H:i:s', $payment->created) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No successful payments found.</p>
    <?php endif; ?>
</body>
</html>
