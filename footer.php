    <footer class="mt-5 pt-3 pb-3 text-center text-muted border-top">
        <p>© 2026 UniReserve - @Copyright by 23552011432_Lisa Ayu Aryanti_23CNSB_UASWEB1</p>
    </footer>

    <script src="bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <script>
        // Logika Logout (Frontend)
        const btnLogout = document.getElementById('logout');
        if (btnLogout) {
            btnLogout.addEventListener('click', async (e) => {
                e.preventDefault();
                if (confirm('Yakin ingin keluar?')) {
                    try {
                        // Logout akan menghapus token di database (Backend)
                        const response = await fetch('flow/logout.php');
                        const result = await response.json();
                        
                        if (result.success) {
                            window.location.replace('landing_page.php'); 
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