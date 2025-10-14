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

            // ✅ Sync unsaved session data (from Get To Know You or color analysis)
            $name = $_SESSION['name'] ?? $user['name'];
            $birthdate = $_SESSION['birthdate'] ?? $user['birthdate'];
            $gender = $_SESSION['gender'] ?? $user['gender'];
            $zodiac_sign = $_SESSION['zodiac_sign'] ?? $user['zodiac_sign'];
            $undertone = $_SESSION['undertone'] ?? $user['undertone'];
            $season = $_SESSION['season'] ?? $user['season'];

            // ✅ Update the DB with any missing info
            $update = $conn->prepare("UPDATE users SET name=?, birthdate=?, gender=?, zodiac_sign=?, undertone=?, season=? WHERE id=?");
            $update->bind_param("ssssssi", $name, $birthdate, $gender, $zodiac_sign, $undertone, $season, $user['id']);
            $update->execute();

            // ✅ Fetch updated user record
            $refresh = $conn->prepare("SELECT * FROM users WHERE id = ?");
            $refresh->bind_param("i", $user['id']);
            $refresh->execute();
            $freshUser = $refresh->get_result()->fetch_assoc();

            // ✅ Update session again with latest DB values
            $_SESSION['name'] = $freshUser['name'] ?? '';
            $_SESSION['birthdate'] = $freshUser['birthdate'] ?? '';
            $_SESSION['gender'] = $freshUser['gender'] ?? '';
            $_SESSION['zodiac_sign'] = $freshUser['zodiac_sign'] ?? '';
            $_SESSION['undertone'] = $freshUser['undertone'] ?? '';
            $_SESSION['season'] = $freshUser['season'] ?? '';

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
