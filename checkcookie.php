<script>
async function checkLoginStatus() {
    try {
        // Panggil me.php di folder yang sama
        const response = await fetch('me.php', {
            method: 'GET',
            credentials: 'include' 
        });

        if (response.ok) {
            console.log("Cookie Valid");
            return true;
        } else {
            console.log("Cookie Invalid");
            window.location.href = "login.php";
            return false;
        }
    } catch (e) {
        console.error("Auth error", e);
        return false;
    }
}
</script>