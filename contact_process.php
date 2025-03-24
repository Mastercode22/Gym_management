<?php
require 'config.php'; // Ensure this file connects to your database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form input values
    $name = htmlspecialchars(trim($_POST['name']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $subject = htmlspecialchars(trim($_POST['subject']));
    $message = htmlspecialchars(trim($_POST['message']));

    // Validate input (ensure fields are not empty)
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        echo "<script>alert('⚠️ Please fill in all fields.'); window.history.back();</script>";
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('⚠️ Invalid email address.'); window.history.back();</script>";
        exit;
    }

    // Insert the message into the database
    $sql = "INSERT INTO messages (name, email, subject, message, created_at) VALUES (?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $name, $email, $subject, $message);

    if ($stmt->execute()) {
        echo "<script>alert('✅ Your message has been sent successfully! Our team will get back to you soon.'); window.location.href = 'contact.html';</script>";
    } else {
        echo "<script>alert('❌ Error submitting your message. Try again later.'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "<script>alert('❌ Invalid request.'); window.history.back();</script>";
}
?>
