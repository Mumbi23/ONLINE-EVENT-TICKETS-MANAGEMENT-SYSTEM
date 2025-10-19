// assets/js/app.js

// Create Back-to-Top button dynamically
const backToTopBtn = document.createElement("button");
backToTopBtn.innerHTML = "⬆";
backToTopBtn.id = "backToTop";
document.body.appendChild(backToTopBtn);

// Style via CSS (make sure you add #backToTop in your style.css)
backToTopBtn.style.display = "none";

// Show/hide button on scroll
window.addEventListener("scroll", () => {
  if (window.scrollY > 300) {
    backToTopBtn.style.display = "block";
  } else {
    backToTopBtn.style.display = "none";
  }
});

// Smooth scroll to top when clicked
backToTopBtn.addEventListener("click", () => {
  window.scrollTo({
    top: 0,
    behavior: "smooth",
  });
});
