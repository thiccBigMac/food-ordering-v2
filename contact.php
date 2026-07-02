<?php include "auth/session.php" ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact — HamroKhaja</title>
    <link rel="stylesheet" href="/food-ordering/styles/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        html, body { overflow-y: auto !important; height: auto !important; }
        .feedback-container {
            margin: 40px auto 100px;
            width: 60%;
            max-width: 600px;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        .feedback-container h2 {
            font-size: 22px;
            margin-bottom: 20px;
            color: #2E4E50;
            text-align: center;
        }
        .feedback-container select,
        .feedback-container textarea {
            width: 100%;
            padding: 12px;
            font-size: 15px;
            border: 1.5px solid #ddd;
            border-radius: 6px;
            margin-bottom: 15px;
            outline: none;
            font-family: inherit;
        }
        .feedback-container select:focus,
        .feedback-container textarea:focus {
            border-color: #2E4E50;
        }
        .feedback-container label {
            font-weight: 600;
            color: #555;
            margin-bottom: 6px;
            display: block;
        }
        .feedback-btn {
            background-color: #2E4E50;
            color: #fff;
            border: none;
            padding: 12px 24px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            transition: background-color 0.3s ease;
        }
        .feedback-btn:hover { background-color: #1f3638; }
        .alert { padding: 12px; border-radius: 6px; margin-bottom: 15px; text-align: center; }
        .alert-success { background: #d1fae5; color: #065f46; }
    </style>
</head>
<body>
<?php include 'includes/header.php'; ?>

<div class="feedback-container">
    <h2>💬 Send Feedback</h2>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success"> Feedback sent successfully!</div>
    <?php endif; ?>

    <?php
    require_once('db/connection.php');
    $restaurants = $conn->query("SELECT id, username FROM restaurants ORDER BY username ASC");
    ?>

    <form action="auth/submit_feedback.php" method="POST">
        <label for="restaurant">Select Restaurant</label>
        <select name="restaurant_id" id="restaurant" required>
            <option value="">-- Choose a restaurant --</option>
            <?php while ($r = $restaurants->fetch_assoc()): ?>
                <option value="<?php echo $r['id']; ?>">
                    <?php echo htmlspecialchars($r['username']); ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="feedback_message">Your Feedback</label>
        <textarea name="feedback_message" id="feedback_message" rows="5" placeholder="Write your feedback here..." required></textarea>

        <button type="submit" class="feedback-btn">Send Feedback</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>