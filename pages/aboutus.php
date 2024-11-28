<!DOCTYPE html>
<html>
  <head>
    <title>About Us</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="robots" content="index,follow">
  <meta name="generator" content="GrapesJS Studio">
  <link href="../../public/css/tailwind.css" rel="stylesheet">
  </head>
  <body class="bg-gray-50 text-gray-800 font-sans">
  <?php include __DIR__ .'../../components/navbar.php';?>  
    <!-- Hero Section -->
    <section class="bg-bgColor text-white py-16">
        <div class="text-center">
            <h1 class="text-4xl md:text-6xl font-bold text-primaryTextColor">About Us</h1>
        </div>
    </section>

    <!-- Welcome Section -->
    <section class="px-6 md:px-16 py-12 bg-bgColor">
        <h2 class="text-3xl md:text-4xl font-bold text-center mb-6 text-primaryTextColor">Welcome to <span class="text-teal-500">EcommerceJA</span></h2>
        <p class="text-lg md:text-xl text-center max-w-3xl mx-auto text-primaryTextColor">
            Your one-stop destination for the latest trends in fashion! Whether you're looking for chic outfits to slay your day or casual wear to keep it cool, we've got you covered.
        </p>
    </section>

    <!-- Mission Section -->
    <section class="px-6 md:px-16 py-12 bg-bgColor">
        <div class="max-w-4xl mx-auto">
            <h2 class="text-2xl md:text-3xl font-bold text-teal-500 mb-6">Our Mission</h2>
            <p class="text-lg md:text-xl text-gray-700 text-primaryTextColor">
                We’re all about empowering young adults to express their unique style with clothes that combine comfort, quality, and trendsetting designs. Our mission is to make fashion accessible and fun, so you can look and feel your best every day.
            </p>
        </div>
    </section>

    <!-- Collections Section -->
    <section class="px-6 md:px-16 py-12 bg-bgColor">
        <div class="max-w-4xl mx-auto">
            <h2 class="text-2xl md:text-3xl font-bold text-teal-500 mb-6">Our Collections</h2>
            <p class="text-lg md:text-xl text-gray-700 text-primaryTextColor">
                Browse through our carefully curated collections for both men and women. From bold and vibrant pieces to dynamic and versatile outfits, our range is designed to cater to every fashion-forward individual's needs.
            </p>
        </div>
    </section>

    <!-- Why Choose Us Section -->
    <section class="px-6 md:px-16 py-12 bg-bgColor">
        <div class="max-w-4xl mx-auto">
            <h2 class="text-2xl md:text-3xl font-bold text-teal-500 mb-6">Why Choose Us?</h2>
            <ul class="list-disc list-inside space-y-4 text-lg md:text-xl text-gray-700 text-primaryTextColor">
                <li><strong>Quality First:</strong> We believe in providing top-notch quality in every stitch and fabric.</li>
                <li><strong>Trendsetting Designs:</strong> Stay ahead of the fashion curve with our exclusive designs.</li>
                <li><strong>Comfort:</strong> Style without comfort is incomplete, and we ensure our clothes give you the best of both worlds.</li>
                <li><strong>Customer-Centric:</strong> Your satisfaction is our priority. We strive to provide you with an unparalleled shopping experience.</li>
            </ul>
        </div>
    </section>
    <script>
// Detect system preference
const prefersDarkScheme = window.matchMedia('(prefers-color-scheme: dark)');

// Function to apply dark mode based on system preference
function applyDarkModePreference() {
  const isUserPreference = localStorage.getItem('theme'); // Check for user preference
  if (!isUserPreference) {
    if (prefersDarkScheme.matches) {
      document.documentElement.classList.add('dark');
    } else {
      document.documentElement.classList.remove('dark');
    }
  }
}

// Function to toggle dark mode manually
function toggleDarkMode() {
  const isDarkMode = document.documentElement.classList.toggle('dark');
  localStorage.setItem('theme', isDarkMode ? 'dark' : 'light'); // Save user preference
}

// Listen for system preference changes
prefersDarkScheme.addEventListener('change', applyDarkModePreference);

document.addEventListener('DOMContentLoaded', () => {
  const savedTheme = localStorage.getItem('theme');
  if (savedTheme === 'dark') {
    document.documentElement.classList.add('dark');
  } else if (savedTheme === 'light') {
    document.documentElement.classList.remove('dark');
  } else {
    applyDarkModePreference(); 
  }

  // Sync toggle button state
  const toggleButton = document.getElementById('toggleDarkMode');
  if (toggleButton) {
    toggleButton.checked = document.documentElement.classList.contains('dark');
    toggleButton.addEventListener('click', toggleDarkMode);
  }
});
      </script>
</body>
</html>
