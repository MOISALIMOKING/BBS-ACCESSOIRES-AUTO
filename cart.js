// تحميل السلة من LocalStorage أو بداية بسلة فارغة
let cart = JSON.parse(localStorage.getItem('cart')) || [];

// تحديث عدد المنتجات (المجموع الكلي للكميات)
function updateCartCount() {
  const totalCount = cart.reduce((sum, item) => sum + item.quantity, 0);
  document.getElementById('cart-count').textContent = totalCount;
}

// حفظ السلة
function saveCart() {
  localStorage.setItem('cart', JSON.stringify(cart));
}

// إضافة منتج للسلة (منع التكرار وزيادة الكمية)
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
  showCart();
}

// عرض السلة
function renderCartItems() {
  const cartItems = document.getElementById('cart-items');
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

      // نعرض اسم المنتج + الكمية + السعر الكلي
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

  document.getElementById('total').textContent = total;
}

// حذف منتج كامل من السلة
function removeItem(index) {
  cart.splice(index, 1);
  saveCart();
  renderCartItems();
  updateCartCount();
}
