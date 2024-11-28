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
  console.log(`Toggle Clicked ${isDarkMode}`);
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