/**
 * Favorite System JavaScript Component
 * Untuk menangani toggle favorit di seluruh aplikasi
 */

class FavoriteManager {
  constructor() {
    this.baseUrl = window.location.origin;
    this.init();
  }

  init() {
    // Bind events when DOM is loaded
    document.addEventListener("DOMContentLoaded", () => {
      this.bindEvents();
    });
  }

  bindEvents() {
    // Handle favorite button clicks
    document.addEventListener("click", (e) => {
      if (e.target.closest(".heart-btn")) {
        e.preventDefault();
        const button = e.target.closest(".heart-btn");
        const productId = button.dataset.productId;
        if (productId) {
          this.toggleFavorite(productId, button);
        }
      }
    });
  }

  async toggleFavorite(productId, buttonElement = null) {
    try {
      const response = await fetch(`${this.baseUrl}/pages/profil/toggle_favorit.php`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          product_id: parseInt(productId),
        }),
      });

      const data = await response.json();

      if (data.success) {
        this.updateFavoriteUI(productId, data.action, buttonElement);
        this.showNotification(data.message, data.action === "added" ? "success" : "info");

        // Trigger custom event for other components to listen
        document.dispatchEvent(
          new CustomEvent("favoriteChanged", {
            detail: { productId, action: data.action },
          })
        );
      } else {
        this.showNotification(data.message || "Terjadi kesalahan", "error");
      }
    } catch (error) {
      console.error("Error toggling favorite:", error);
      this.showNotification("Terjadi kesalahan sistem", "error");
    }
  }

  updateFavoriteUI(productId, action, buttonElement = null) {
    // Find all heart buttons for this product
    const selectors = [`button[onclick="toggleFavorite(${productId})"]`, `button[data-product-id="${productId}"]`, `.heart-btn[data-product-id="${productId}"]`];

    let buttons = [];
    selectors.forEach((selector) => {
      buttons = buttons.concat(Array.from(document.querySelectorAll(selector)));
    });

    // If specific button provided, prioritize it
    if (buttonElement) {
      buttons.unshift(buttonElement);
    }

    buttons.forEach((button) => {
      const heartIcon = button.querySelector("i") || button;

      if (action === "added") {
        // Added to favorites - make it red
        button.classList.remove("not-favorited", "text-gray-300");
        button.classList.add("favorited", "text-red-500");
        heartIcon.classList.remove("text-gray-300");
        heartIcon.classList.add("text-red-500");
      } else {
        // Removed from favorites - make it gray
        button.classList.remove("favorited", "text-red-500");
        button.classList.add("not-favorited", "text-gray-300");
        heartIcon.classList.remove("text-red-500");
        heartIcon.classList.add("text-gray-300");
      }

      // Add animation
      button.style.transform = "scale(1.2)";
      setTimeout(() => {
        button.style.transform = "scale(1)";
      }, 200);
    });

    // Special handling for favorite page - remove card if unfavorited
    if (action === "removed" && window.location.pathname.includes("favorit")) {
      const productCard = document.querySelector(`[data-product-id="${productId}"]`)?.closest(".product-card");
      if (productCard) {
        this.removeProductCard(productCard);
      }
    }
  }

  removeProductCard(productCard) {
    productCard.style.transition = "all 0.3s ease";
    productCard.style.transform = "scale(0.8)";
    productCard.style.opacity = "0";

    setTimeout(() => {
      productCard.remove();

      // Update product count if exists
      const countElement = document.querySelector(".favorite-count");
      if (countElement) {
        const currentCount = parseInt(countElement.textContent.match(/\d+/)?.[0] || 0);
        const newCount = Math.max(0, currentCount - 1);
        countElement.textContent = countElement.textContent.replace(/\d+/, newCount);
      }

      // Check if no products left and show empty state
      const remainingProducts = document.querySelectorAll(".product-card");
      if (remainingProducts.length === 0) {
        this.showEmptyState();
      }
    }, 300);
  }

  showEmptyState() {
    const container = document.querySelector(".grid") || document.querySelector('[class*="grid"]');
    if (container) {
      container.innerHTML = `
                <div class="col-span-full text-center py-16">
                    <div class="mb-6">
                        <i class="fas fa-heart text-6xl text-gray-300 mb-4"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-600 mb-3">Belum ada produk favorit</h3>
                    <p class="text-gray-500 mb-6">Yuk, tambahkan produk kesukaan Anda ke favorit untuk memudahkan pencarian!</p>
                    <a href="../produk/" class="inline-flex items-center px-6 py-3 bg-orange-500 text-white font-medium rounded-lg hover:bg-orange-600 transition-colors">
                        <i class="fas fa-shopping-bag mr-2"></i>
                        Jelajahi Produk
                    </a>
                </div>
            `;
    }
  }

  showNotification(message, type = "success") {
    // Create notification if doesn't exist
    let popup = document.getElementById("popupNotification");
    if (!popup) {
      popup = document.createElement("div");
      popup.id = "popupNotification";
      popup.className = "popup-notification";
      document.body.appendChild(popup);
    }

    // Set notification content and style
    popup.textContent = message;

    const colors = {
      success: "#4CAF50",
      error: "#f44336",
      info: "#2196F3",
      warning: "#ff9800",
    };

    popup.style.backgroundColor = colors[type] || colors.success;
    popup.classList.add("show");

    // Hide notification after 3 seconds
    setTimeout(() => {
      popup.classList.remove("show");
    }, 3000);
  }

  // Utility method to get favorite status
  async getFavoriteStatus(productId) {
    try {
      const response = await fetch(`${this.baseUrl}/pages/profil/get_favorite_status.php?product_id=${productId}`);
      const data = await response.json();
      return data.is_favorited || false;
    } catch (error) {
      console.error("Error getting favorite status:", error);
      return false;
    }
  }

  // Method to initialize favorite buttons with correct state
  async initializeFavoriteButtons() {
    const heartButtons = document.querySelectorAll(".heart-btn[data-product-id]");

    for (const button of heartButtons) {
      const productId = button.dataset.productId;
      const isFavorited = await this.getFavoriteStatus(productId);

      const heartIcon = button.querySelector("i") || button;

      if (isFavorited) {
        button.classList.add("favorited", "text-red-500");
        button.classList.remove("not-favorited", "text-gray-300");
        heartIcon.classList.add("text-red-500");
        heartIcon.classList.remove("text-gray-300");
      } else {
        button.classList.add("not-favorited", "text-gray-300");
        button.classList.remove("favorited", "text-red-500");
        heartIcon.classList.add("text-gray-300");
        heartIcon.classList.remove("text-red-500");
      }
    }
  }
}

// Global function for backward compatibility
function toggleFavorite(productId) {
  if (window.favoriteManager) {
    window.favoriteManager.toggleFavorite(productId);
  }
}

// Initialize favorite manager
window.favoriteManager = new FavoriteManager();

// CSS styles for notifications (inject if not exists)
if (!document.querySelector("#favorite-notification-styles")) {
  const style = document.createElement("style");
  style.id = "favorite-notification-styles";
  style.textContent = `
        .popup-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            border-radius: 8px;
            color: white;
            font-size: 14px;
            font-weight: 500;
            z-index: 1000;
            opacity: 0;
            transform: translateY(-20px);
            transition: opacity 0.3s, transform 0.3s;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .popup-notification.show {
            opacity: 1;
            transform: translateY(0);
        }

        .heart-btn {
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .heart-btn:hover {
            transform: scale(1.1);
        }

        .heart-btn.favorited {
            color: #ef4444 !important;
        }

        .heart-btn.not-favorited {
            color: #d1d5db !important;
        }

        .product-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .product-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
    `;
  document.head.appendChild(style);
}
