function showTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
      tab.classList.remove('active');
    });
    
    // Remove active class from all buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
      btn.classList.remove('active');
    });
    
    // Show selected tab
    document.getElementById(tabName).classList.add('active');
    
    // Add active class to clicked button
    event.target.classList.add('active');

    const tabButtons = document.querySelectorAll('.tab-btn');
const tabs = document.querySelectorAll('.tab-content');

tabButtons.forEach(button => {
  button.addEventListener('click', () => {
    // Remove 'active' from all buttons and tabs
    tabButtons.forEach(b => {
        b.classList.remove('active');
        // explicitly set width to 0 on previously active button
        b.style.setProperty('--after-width', '0%');
    });
    tabs.forEach(tab => tab.classList.remove('active'));

    // Add 'active' to the clicked button and corresponding tab
    button.classList.add('active');
    button.style.setProperty('--after-width', '100%'); // Start the slide in
    const tabId = button.getAttribute('data-tab');
    document.getElementById(tabId).classList.add('active');
  });
});

// Set initial width for active tab on load
const initialActive = document.querySelector('.tab-btn.active');
if (initialActive) {
  initialActive.style.setProperty('--after-width', '100%');
}
  }