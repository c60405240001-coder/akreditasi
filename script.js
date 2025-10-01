// Menampilkan Sidebar
const openSidebar = document.getElementById('open-sidebar');
const closeSidebar = document.getElementById('close-sidebar');
const sidebar = document.getElementById('sidebar');
const mainContent = document.getElementById('main-content');

// Fungsi untuk membuka sidebar
openSidebar.addEventListener('click', () => {
    sidebar.style.left = '0'; // Menampilkan sidebar
    mainContent.style.marginLeft = '250px'; // Memberikan margin untuk konten utama
});

// Fungsi untuk menutup sidebar
closeSidebar.addEventListener('click', () => {
    sidebar.style.left = '-250px'; // Menyembunyikan sidebar
    mainContent.style.marginLeft = '0'; // Mengembalikan margin konten utama
});
