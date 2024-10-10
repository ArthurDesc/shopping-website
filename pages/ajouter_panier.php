<?php 

require_once 'includes/_db.php';

if(!isset($_SESSION)){

    session_start();
}

if(!isset($_SESSION["panier"])){

    $_SESSION['panier'] = array();

}

 if(isset($_GET['id_produit'])){

    $panier = $_GET['id_produit'];

    $produit = mysqli_query($conn, "SELECT * FROM produits WERE id_produit = $panier");
    if(empty(mysqli_fetch_assoc($produit))){

        die("ce n'existe pas")

    }

    if(isset($_SESSION['panier'][$panier])){

        $_SESSION['panier'][$id]++;
    }else{
        $_SESSION['panier'][$id]= 1;
        echo "le produit a bien été mis dans le panier !";
    }

    header("location:produit.php");

 }




?>


