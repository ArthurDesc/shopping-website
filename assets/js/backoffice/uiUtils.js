function showToast(message, type = "success") {
    // Créer l'élément toast
    const toast = document.createElement("div");
    toast.className = `fixed bottom-5 right-5 p-4 rounded-md text-white ${
      type === "success" ? "bg-green-500" : "bg-red-500"
    } shadow-lg transition-opacity duration-500 ease-in-out`;
    toast.style.zIndex = "1000";
    toast.innerHTML = message; // Changé de textContent à innerHTML
  
    // Ajouter le toast au body
    document.body.appendChild(toast);
  
    // Faire apparaître le toast
    setTimeout(() => {
      toast.style.opacity = "1";
    }, 10);
  
    // Faire disparaître le toast après 3 secondes
    setTimeout(() => {
      toast.style.opacity = "0";
      setTimeout(() => {
        document.body.removeChild(toast);
      }, 500);
    }, 3000);
  }