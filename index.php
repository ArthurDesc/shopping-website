<?php include './includes/session.php'; ?>


    <?php include './includes/_header.php'; ?>

    

    <?php include './includes/_footer.php'; ?>

    <!-- Scripts -->
    <script src="<?php echo BASE_URL; ?>assets/js/script.js" defer></script>
    <script>
  document.getElementById('menu-toggle').addEventListener('click', function() {
    const sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('-translate-x-full');
  });
</script>
</body>
</html>