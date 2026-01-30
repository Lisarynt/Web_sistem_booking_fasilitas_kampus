    <footer class="mt-5 pt-3 pb-3 text-center text-muted border-top">
        <p>© 2026 UniReserve - 23552011432_Lisa Ayu Aryanti_23CNSB</p>
    </footer>

    <script src="bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <script>
        // Pastikan path ini benar (jika dashboard.php ada di root dan logout di folder flow)
        const LOGOUT_URL = 'flow/logout.php'; 

        const btnLogout = document.getElementById('logout'); // Cek apakah di sidebar sudah ada id="logout"
        if (btnLogout) {
            btnLogout.addEventListener('click', async (e) => {
                e.preventDefault();
                if (confirm('Yakin ingin keluar?')) {
                    try {
                        const response = await fetch(LOGOUT_URL);
                        const result = await response.json();
                        
                        if (result.success) {
                            window.location.replace('landing_page.php'); 
                        } else {
                            alert('Gagal logout: ' + result.message);
                        }
                    } catch (err) {
                        window.location.replace('landing_page.php');
                    }
                }
            });
        }
    </script>
</body>
</html>