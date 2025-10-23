<?php
include("../config/dbconfig.php");
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        echo "<script>alert('Both fields are required.'); window.history.back();</script>";
        exit();
    }

    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];

            // ✅ CRITICAL FIX: Check if we have NEW session data from "Get to Know You"
            $hasNewSessionData = !empty($_SESSION['zodiac_sign']) || !empty($_SESSION['name']) || !empty($_SESSION['birthdate']);

            if ($hasNewSessionData) {
                // ✅ USER HAS NEW DATA: Update database with session data
                $name = $_SESSION['name'] ?? $user['name'];
                $birthdate = $_SESSION['birthdate'] ?? $user['birthdate'];
                $gender = $_SESSION['gender'] ?? $user['gender'];
                $zodiac_sign = $_SESSION['zodiac_sign'] ?? $user['zodiac_sign'];
                $undertone = $_SESSION['undertone'] ?? $user['undertone'];
                $season = $_SESSION['season'] ?? $user['season'];

                $update = $conn->prepare("UPDATE users SET name=?, birthdate=?, gender=?, zodiac_sign=?, undertone=?, season=? WHERE id=?");
                $update->bind_param("ssssssi", $name, $birthdate, $gender, $zodiac_sign, $undertone, $season, $user['id']);
                $update->execute();

                // ✅ Keep the NEW session data (don't overwrite with old DB data)
                // Session already has the new data, so we don't need to change it

            } else {
                // ✅ USER HAS NO NEW DATA: Restore from database
                if (!empty($user['zodiac_sign'])) {
                    $_SESSION['zodiac_sign'] = $user['zodiac_sign'];
                    setcookie('zodiac_sign', $user['zodiac_sign'], time() + (86400 * 30), "/");
                }
                
                if (!empty($user['undertone'])) {
                    $_SESSION['undertone'] = $user['undertone'];
                    setcookie('undertone', $user['undertone'], time() + (86400 * 30), "/");
                }
                
                if (!empty($user['season'])) {
                    $_SESSION['season'] = $user['season'];
                }

                // Restore basic profile data
                $_SESSION['name'] = $user['name'] ?? '';
                $_SESSION['birthdate'] = $user['birthdate'] ?? '';
                $_SESSION['gender'] = $user['gender'] ?? '';
            }

            // ✅ Sync color data if exists
            echo "
            <script>
                const colorData = localStorage.getItem('colorAnalysisData');
                if (colorData) {
                    fetch('../user/save_color_data.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(JSON.parse(colorData))
                    }).then(() => {
                        localStorage.removeItem('colorAnalysisData');
                        window.location.href = '../dashboard/index.php';
                    });
                } else {
                    window.location.href = '../dashboard/index.php';
                }
            </script>";
            exit();

        } else {
            echo "<script>alert('Invalid password.'); window.history.back();</script>";
            exit();
        }
    } else {
        echo "<script>alert('No account found with that email.'); window.history.back();</script>";
        exit();
    }
}
?>