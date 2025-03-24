<?php
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'] ?? null;
    $email = $_POST['email'] ?? null;
    $phone = $_POST['phone'] ?? null;
    $membership = $_POST['membership'] ?? null;
    $payment_method = $_POST['payment_method'] ?? null;

    // Credit Card Details
    $card_name = $_POST['card_name'] ?? null;
    $card_number = $_POST['card_number'] ?? null;
    $expiry_date = $_POST['expiry_date'] ?? null;
    $cvv = $_POST['cvv'] ?? null;

    // Mobile Money Details
    $mobile_money_number = $_POST['mobile_money_number'] ?? null;
    $network_provider = $_POST['network_provider'] ?? null;

    // Membership Plan Pricing
    $membership_prices = [
        "1-month" => 100,
        "3-months" => 300,
        "6-months" => 600,
        "12-months" => 1200
    ];
    
    $amount = $membership_prices[$membership] ?? null;

    if (!$name || !$email || !$phone || !$membership || !$payment_method || !$amount) {
        die("❌ Missing required fields.");
    }

    $conn->begin_transaction();

    try {
        // Insert member details
        $sql1 = "INSERT INTO members (name, email, phone, membership_plan, payment_method) VALUES (?, ?, ?, ?, ?)";
        $stmt1 = $conn->prepare($sql1);
        $stmt1->bind_param("sssss", $name, $email, $phone, $membership, $payment_method);
        $stmt1->execute();
        $member_id = $stmt1->insert_id;
        $stmt1->close();

        // Payment Processing
        $sql2 = "";
        $stmt2 = null;

        if ($payment_method === "credit-card") {
            if (!$card_name || !$card_number || !$expiry_date || !$cvv) {
                throw new Exception("❌ Missing credit card details!");
            }

            $sql2 = "INSERT INTO payments (member_id, card_name, card_number, expiry_date, cvv, amount, status) 
                     VALUES (?, ?, ?, ?, ?, ?, 'Pending')";
            $stmt2 = $conn->prepare($sql2);
            $stmt2->bind_param("issssi", $member_id, $card_name, $card_number, $expiry_date, $cvv, $amount);
        } elseif ($payment_method === "mobile-money") {
            if (!$mobile_money_number || !$network_provider) {
                throw new Exception("❌ Missing mobile money details!");
            }

            $sql2 = "INSERT INTO payments (member_id, mobile_money_number, network_provider, amount, status) 
                     VALUES (?, ?, ?, ?, 'Pending')";
            $stmt2 = $conn->prepare($sql2);
            $stmt2->bind_param("issi", $member_id, $mobile_money_number, $network_provider, $amount);
        }

        if ($stmt2) {
            $stmt2->execute();
            $stmt2->close();
        }

        $conn->commit();
        echo '<div style="text-align: center; font-size: 1.2em; color: green;">✅ Signup and Payment Successful!</div>';
        } catch (Exception $e) {
            $conn->rollback();
            echo '<div style="text-align: center; font-size: 1.2em; color: red;">❌ Error: ' . $e->getMessage() . '</div>';
        }
        

    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Powerhouse Gym </title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: url("assets/img/gallery/h1_hero.png");
            color: white;
            margin: 0;
        }

/* Keyframes for Rotating Border */
@keyframes borderRotate {
    0% {
        background: linear-gradient(0deg, red, rgb(254, 254, 255), green, purple);
    }
    25% {
        background: linear-gradient(90deg, red, rgb(249, 249, 255), green, purple);
    }
    50% {
        background: linear-gradient(180deg, red, rgb(249, 249, 255), green, purple);
    }
    75% {
        background: linear-gradient(270deg, red, rgb(246, 246, 252), green, purple);
    }
    100% {
        background: linear-gradient(360deg, red, rgb(240, 240, 245), green, purple);
    }
}

/* Keyframes for Glow Effect */
@keyframes glowEffect {
    0% { box-shadow: 0 0 5px rgb(255, 254, 254); }
    50% { box-shadow: 0 0 20px rgb(249, 249, 252); }
    100% { box-shadow: 0 0 30px green; }
}

/* Signup Form Styling */
.signup-container {
    background: #2c2c2c;
    padding: 20px;
    border-radius: 10px;
    width: 580px;
    margin: 50px auto;
    margin-top: 250px;
    color: white;
    border: 5px solid transparent;
    position: relative;
    overflow: hidden;
    animation: borderRotate 5s linear infinite, glowEffect 2s infinite alternate ease-in-out;
}

/* Re-added Animated Border */
.signup-container::before {
    content: "";
    position: absolute;
    top: -5px;
    left: -5px;
    right: -5px;
    bottom: -5px;
    border-radius: 10px;
    z-index: -1;
    background: linear-gradient(0deg, red, blue, green, purple);
    animation: borderRotate 3s linear infinite;
}

.signup-container:hover {
    animation: glowEffect 2s infinite alternate;
}

h2 {
    text-align: center;
    color:  #ff9900;
}

/* Form Input Styling */
.form-group {
    margin-bottom: 15px;
}

label {
    display: block;
    margin-bottom: 5px;
}

input, select {
    width: 100%;
    padding: 10px;
    border-radius: 5px;
    border: none;
    background: #444;
    color: white;
}

/* Buttons */
.signup-btn, .signup-btn1 {
    width: 100%;
    padding: 10px;
    font-size: 16px;
    border-radius: 5px;
    cursor: pointer;
    margin-top: 5px;
    border: none;
}

.signup-btn {
    background: #ff9900;
    color: black;
}

.signup-btn:hover {
    background: #ff7700;
}

.signup-btn1 {
    background: #df0f00;
    color: white;
}

.signup-btn1:hover {
    background: #c201b2;
}

/* Popup Styling */
.popup-container {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.3);
            z-index: 1000;
            width: 300px;
            text-align: center;
            border-radius: 8px;
        }

        .popup-content {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .popup-content {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

.input-group {
    margin: 10px 0;
}

.pay-btn, .close-btn {
    margin-top: 10px;
    padding: 10px;
    width: 100%;
    border: none;
    cursor: pointer;
}

.pay-btn {
    background-color: #28a745;
    color: white;
}

.close-btn {
    background-color: #dc3545;
    color: white;
}
 
    </style>
</head>
<body>
<div class="signup-container">
        <h2>Gym Membership Signup</h2>
       <!-- Main Form -->
  <form id="signupForm" action="signupform.php" method="POST">
    <div class="form-group">
        <label for="name">Full Name</label>
        <input type="text" id="name" name="name" required>
    </div>
    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>
    </div>
    <div class="form-group">
    <label for="phone">Phone Number</label>
    <input type="tel" id="phone" name="phone" maxlength="15" pattern="\+?[0-9]{7,12}"  required>
  </div>

    <div class="form-group">
        <label for="membership">Membership Plan</label>
        <select id="membership" name="membership" required>
            <option value="1-month">1 Month - Gh100</option>
            <option value="3-months">3 Months - Gh300</option>
            <option value="6-months">6 Months - Gh600</option>
            <option value="12-months">12 Months - Gh1200</option>
        </select>
    </div>
    <div class="form-group">
    <label for="payment">Select Payment Method:</label>
    <select id="payment" name="payment_method">
        <option value="">-- Choose Payment Method --</option>
        <option value="credit-card">Credit Card</option>
        <option value="mobile-money">Mobile Money</option>
    </select>

    <!-- Credit Card Fields (Hidden Initially) -->
    <div id="credit-card-fields" style="display: none;">
        <h2>Credit Card Details</h2>
        <input type="text" name="card_name" placeholder="Cardholder Name" maxlength="30">
        <input type="text" name="card_number" placeholder="Card Number" maxlength="16">
        <input type="text" name="expiry_date" placeholder="MM/YY" maxlength="5">
        <input type="text" name="cvv" placeholder="CVV" maxlength="3">
    </div>

    <!-- Mobile Money Fields (Hidden Initially) -->
    <div id="mobile-money-fields" style="display: none;">
        <h2>Mobile Money Details</h2>
        <input type="text" name="mobile_money_number" placeholder="Mobile Money Number" maxlength="12">
        <select name="network_provider">
            <option value="MTN">MTN</option>
            <option value="Vodafone">Vodafone</option>
            <option value="AirtelTigo">AirtelTigo</option>
        </select>
    </div>

    <button type="submit" class="signup-btn">Sign Up</button>
    <button type="button" class="signup-btn1" onclick="window.location.href='index.html';">
    Back To Website
</button>

</form>

<script>
document.getElementById("payment").addEventListener("change", function () {
    let paymentMethod = this.value;
    document.getElementById("credit-card-fields").style.display = "none";
    document.getElementById("mobile-money-fields").style.display = "none";

    if (paymentMethod === "credit-card") {
        document.getElementById("credit-card-fields").style.display = "block";
    } else if (paymentMethod === "mobile-money") {
        document.getElementById("mobile-money-fields").style.display = "block";
    }
});
</script>

</body>
</html>

