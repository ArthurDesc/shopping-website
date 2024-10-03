<?php include './includes/session.php'; ?>


    <?php include './includes/_header.php'; ?>

    

    <?php include './includes/_footer.php'; ?>

    <!-- Scripts -->
    <script src="<?php echo BASE_URL; ?>assets/js/script.js" defer></script>
    <script>
  document.addEventListener('click', function(event) {
    const sidebar = document.getElementById('sidebar');
    const menuToggle = document.getElementById('menu-toggle');

    // Vérifie si le clic est à l'extérieur de la barre latérale et du bouton de menu
    if (!sidebar.contains(event.target) && !menuToggle.contains(event.target)) {
      sidebar.classList.add('-translate-x-full');
    }
  });

  document.getElementById('menu-toggle').addEventListener('click', function(event) {
    event.stopPropagation(); // Empêche le clic de se propager au document
    const sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('-translate-x-full');
  });
</script>
</body>
</html>