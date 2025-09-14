// 🛒 تحميل السلة من LocalStorage أو بداية بسلة فارغة
let cart = JSON.parse(localStorage.getItem('cart')) || [];

// 💾 حفظ السلة
function saveCart() {
  localStorage.setItem('cart', JSON.stringify(cart));
}

// 🔄 تحديث عدد المنتجات (المجموع الكلي للكميات)
function updateCartCount() {
  const totalCount = cart.reduce((sum, item) => sum + item.quantity, 0);
  const cartCount = document.getElementById('cart-count');
  if (cartCount) {
    cartCount.textContent = totalCount;
  }
}

// ➕ إضافة منتج للسلة (منع التكرار وزيادة الكمية)
function addToCart(name, price) {
  const existingItem = cart.find(item => item.name === name);
  if (existingItem) {
    existingItem.quantity += 1; // إذا موجود نزيد الكمية
  } else {
    cart.push({ name, price, quantity: 1 }); // إذا جديد ندخلو
  }
  saveCart();
  updateCartCount();
  renderCartItems();
  showAddedMessage(); // ✅ رسالة صغيرة
}

// 📋 عرض محتوى السلة
function renderCartItems() {
  const cartItems = document.getElementById('cart-items');
  if (!cartItems) return; // نتأكد العنصر موجود

  cartItems.innerHTML = '';
  let total = 0;

  if (cart.length === 0) {
    cartItems.innerHTML = '<li>السلة فارغة.</li>';
  } else {
    cart.forEach((item, index) => {
      const li = document.createElement('li');
      li.style.display = 'flex';
      li.style.justifyContent = 'space-between';
      li.style.alignItems = 'center';
      li.style.marginBottom = '8px';

      li.innerHTML = `
        <span>${item.name} × ${item.quantity}</span>
        <span>${item.price * item.quantity} دج</span>
      `;

      // زر حذف
      const btn = document.createElement('button');
      btn.textContent = '❌';
      btn.onclick = () => removeItem(index);
      li.appendChild(btn);

      cartItems.appendChild(li);
      total += item.price * item.quantity;
    });
  }

  const totalEl = document.getElementById('total');
  if (totalEl) totalEl.textContent = total;
}

// ❌ حذف منتج من السلة
function removeItem(index) {
  cart.splice(index, 1);
  saveCart();
  renderCartItems();
  updateCartCount();
}

// 👁️ عرض/إخفاء السلة (Popup)
function toggleCart() {
  const popup = document.getElementById('cart-popup');
  if (popup) {
    popup.style.display = popup.style.display === 'block' ? 'none' : 'block';
  }
}

// ✅ رسالة صغيرة عند الإضافة
function showAddedMessage() {
  let msg = document.createElement('div');
  msg.textContent = "✔ تم إضافة المنتج للسلة";
  msg.style.position = "fixed";
  msg.style.bottom = "20px";
  msg.style.right = "20px";
  msg.style.background = "#4CAF50";
  msg.style.color = "#fff";
  msg.style.padding = "10px 15px";
  msg.style.borderRadius = "8px";
  msg.style.boxShadow = "0 0 10px rgba(0,0,0,0.3)";
  msg.style.zIndex = "2000";
  document.body.appendChild(msg);

  setTimeout(() => {
    msg.remove();
  }, 3000);
}

// 🔄 تحديث عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', () => {
  updateCartCount();
  renderCartItems();
});
