import './bootstrap';

// resources/js/app.js

document.addEventListener('DOMContentLoaded', function() {
    const productList = document.getElementById('productList');
    const searchInput = document.getElementById('searchInput');
    const conditionFilter = document.getElementById('conditionFilter');
    const sortFilter = document.getElementById('sortFilter'); // Elemen baru
    const categoryFilters = document.querySelectorAll('.category-filter');
    const productCountSpan = document.getElementById('productCount');
    let productItems = Array.from(productList.querySelectorAll('.product-item')); // Jadikan Array agar bisa di-sort

    // FUNGSI UTAMA UNTUK MENYARING DAN MENYORTIR PRODUK
    function applyFiltersAndSort() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        const selectedCondition = conditionFilter.value;
        const selectedSort = sortFilter.value; // Ambil nilai sortir
        let activeCategory = 'semua';

        // Tentukan kategori aktif (logic dari tombol yang aktif)
        const activeCatButton = document.querySelector('.category-filter.main-gradient');
        if (activeCatButton && activeCatButton.dataset.category) {
            activeCategory = activeCatButton.dataset.category;
        }

        let filteredProducts = productItems.filter(item => {
            const itemName = item.querySelector('h3').textContent.toLowerCase();
            const itemCondition = item.dataset.condition;
            const itemCategory = item.dataset.category;

            // Filter Pencarian (Search)
            const matchesSearch = itemName.includes(searchTerm);

            // Filter Kondisi (Condition)
            const matchesCondition = selectedCondition === 'all' || itemCondition === selectedCondition;

            // Filter Kategori (Category)
            const matchesCategory = activeCategory === 'semua' || itemCategory === activeCategory;

            return matchesSearch && matchesCondition && matchesCategory;
        });

        // 1. Sortir Produk
        if (selectedSort === 'tahun ini') {
            // Perlu menggunakan data-price (saat ini harga dummy hanya ada di PHP, perlu dipindahkan ke data-attribute)
            // Karena data-price di sini hanya dummy, kita lewati sorting harga di frontend.
            // Jika harga benar-benar diperlukan, pastikan data-price ada di HTML.
            // Contoh implementasi sorting (berdasarkan ID/index dummy):
            filteredProducts.sort((a, b) => parseInt(a.dataset.price) - parseInt(b.dataset.price));
        } else if (selectedSort === 'bulan ini') {
            filteredProducts.sort((a, b) => parseInt(b.dataset.price) - parseInt(a.dataset.price));
        } else if (selectedSort === 'terbaru') {
            // Asumsi data-time ada dan berisi timestamp (untuk data dummy di sini, kita biarkan urutan bawaan HTML)
            // filteredProducts.sort((a, b) => b.dataset.time - a.dataset.time); 
        }

        // 2. Tampilkan/Sembunyikan dan Render ulang
        productList.innerHTML = ''; // Kosongkan list
        filteredProducts.forEach(item => {
            productList.appendChild(item);
            item.style.display = 'block';
        });
        
        // Perbarui jumlah produk yang ditampilkan
        productCountSpan.textContent = filteredProducts.length;

        // Sembunyikan produk yang tidak difilter
        productItems.forEach(item => {
            if (!filteredProducts.includes(item)) {
                item.style.display = 'none';
            }
        });
    }

    // Event Listeners
    searchInput.addEventListener('input', applyFiltersAndSort);
    conditionFilter.addEventListener('change', applyFiltersAndSort);
    sortFilter.addEventListener('change', applyFiltersAndSort); // Listener baru

    categoryFilters.forEach(button => {
        button.addEventListener('click', function() {
            // Logika untuk menandai tombol kategori yang aktif
            categoryFilters.forEach(btn => {
                btn.classList.remove('main-gradient', 'text-white', 'shadow-md');
                btn.classList.add('bg-white', 'text-gray-900', 'hover:bg-gray-50');
            });

            this.classList.remove('bg-white', 'text-gray-900', 'hover:bg-gray-50');
            this.classList.add('main-gradient', 'text-white', 'shadow-md');

            applyFiltersAndSort();
        });
    });

    // Inisialisasi: Terapkan filter awal
    applyFiltersAndSort();
});