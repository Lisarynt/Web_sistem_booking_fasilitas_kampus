<!DOCTYPE html>
<html>
<head>
    <title>Redirecting...</title>
</head>
<body>
    <script>
    async function checkLoginStatus() {
        try {
            const response = await fetch('me.php', {
                method: 'GET',
                credentials: 'include'
            });
            return response.ok;
        } catch (e) {
            return false;
        }
    }

    checkLoginStatus().then(isLoggedIn => {
        if (isLoggedIn) {
            window.location.href = "dashboard.php";
        } else {
            window.location.href = "login.php";
        }
    });
    </script>
</body>
</html>