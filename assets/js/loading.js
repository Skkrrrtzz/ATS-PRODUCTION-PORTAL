// Get references to the button and loader container
const loaderContainer = document.getElementById("loader");

// Function to show the loader
function showLoader() {
  loaderContainer.style.display = "flex";
}

// Function to handle F5 key press
function handleKeyPress(event) {
  if (event.keyCode === 116) {
    // Check for F5 key (keyCode 116)
    showLoader();
  }
}

// Add an event listener to the 'keydown' event
window.addEventListener("keydown", handleKeyPress);
