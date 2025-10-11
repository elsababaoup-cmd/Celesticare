<?php
session_start();
include(__DIR__ . "/../includes/navbar.php");

// Optional: pre-fill form if user already entered details
$name = $_SESSION['name'] ?? '';
$birthdate = $_SESSION['birthdate'] ?? '';
$gender = $_SESSION['gender'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Get to Know You - CelestiCare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow-y: scroll;
            scrollbar-width: none;
            -ms-overflow-style: none;
            background: linear-gradient(135deg, #d3cce3 0%, #e9e4f0 100%);
            font-family: 'Poppins', sans-serif;
        }
        body::-webkit-scrollbar { display: none; }
        .setup-container { min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .setup-box {
            background-color: #2e2e2e;
            color: #ffffff;
            padding: 40px;
            border-radius: 20px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        }
        .setup-box h2 { font-size: 20px; text-align: center; margin-bottom: 8px; font-weight: 600; }
        .setup-box p { text-align: center; font-size: 0.9rem; color: #cfcfcf; margin-bottom: 25px; }
        .form-control { background-color: #4b4b4b; border: none; color: #fff; border-radius: 30px; padding: 12px 20px; }
        .form-control::placeholder { color: #cfcfcf; }
        select.form-control { appearance: none; }
        .btn-continue { background-color: #6b5b95; color: #fff; border: none; border-radius: 30px; padding: 10px 20px; width: 100%; transition: 0.3s; }
        .btn-continue:hover { background-color: #8c77c5; }
        .brand-title { font-weight: 700; font-size: 24px; letter-spacing: 1px; text-align: center; margin-bottom: 4px; }
    </style>
</head>
<body>

<div class="container setup-container">
    <div class="setup-box">
        <div class="brand-title">CELESTICARE</div>
        <h2>Time to get to know you</h2>
        <p>Provide the following details below</p>

        <form action="../zodiac/zodiac_result.php" method="POST">
            <div class="mb-3">
                <input type="text" name="name" class="form-control" placeholder="Name" value="<?= htmlspecialchars($name) ?>" required>
            </div>

            <div class="mb-3">
                <input type="date" name="birthdate" class="form-control" value="<?= htmlspecialchars($birthdate) ?>" required>
            </div>

            <div class="mb-4">
                <select name="gender" class="form-control" required>
                    <option value="" disabled <?= $gender=='' ? 'selected' : '' ?>>Masculine, Feminine</option>
                    <option value="Masculine" <?= $gender=='Masculine' ? 'selected' : '' ?>>Masculine</option>
                    <option value="Feminine" <?= $gender=='Feminine' ? 'selected' : '' ?>>Feminine</option>
                </select>
            </div>

            <button type="submit" class="btn btn-continue">Continue</button>
        </form>
    </div>
</div>

</body>
</html>
