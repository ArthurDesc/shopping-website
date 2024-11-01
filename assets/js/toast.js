function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `fixed right-4 top-[70px] p-4 rounded-md text-white ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    } shadow-lg transition-opacity duration-500 ease-in-out opacity-0`;
    
    toast.textContent = message;
    toast.style.zIndex = '1000';
    document.body.appendChild(toast);

    requestAnimationFrame(() => {
        toast.style.opacity = '1';
    });

    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 500);
    }, 3000);
}

function showWishlistToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `fixed right-4 top-[70px] p-4 rounded-md text-white ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    } shadow-lg transition-opacity duration-500 ease-in-out opacity-0 cursor-pointer hover:bg-opacity-90`;
    
    toast.innerHTML = `
        <a href="/shopping-website/pages/wishlist.php" class="flex items-center text-white">
            ${message}
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
            </svg>
        </a>
    `;

    toast.style.zIndex = '1000';
    document.body.appendChild(toast);

    requestAnimationFrame(() => {
        toast.style.opacity = '1';
    });

    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 500);
    }, 3000);
}

// Exposer les fonctions globalement
window.showToast = showToast;
window.showWishlistToast = showWishlistToast;
