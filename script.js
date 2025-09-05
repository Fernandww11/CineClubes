// Form handling
document.addEventListener("DOMContentLoaded", () => {
  // Login form
  const loginForm = document.getElementById("loginForm")
  if (loginForm) {
    loginForm.addEventListener("submit", (e) => {
      e.preventDefault()
      // Simulate login
      alert("Login realizado com sucesso!")
      window.location.href = "home.html"
    })
  }

  // Register form
  const registerForm = document.getElementById("registerForm")
  if (registerForm) {
    registerForm.addEventListener("submit", (e) => {
      e.preventDefault()
      // Simulate registration
      alert("Registro realizado com sucesso!")
      window.location.href = "index.html"
    })
  }

  // Movie and event cards click handling
  const movieCards = document.querySelectorAll(".movie-card")
  movieCards.forEach((card) => {
    card.addEventListener("click", function () {
      const title = this.querySelector(".movie-title").textContent
      alert(`Abrindo detalhes do filme: ${title}`)
      // Here you would navigate to a detail page
    })
  })

  const eventCards = document.querySelectorAll(".event-card")
  eventCards.forEach((card) => {
    card.addEventListener("click", function () {
      const title = this.querySelector(".event-title").textContent
      alert(`Abrindo detalhes do evento: ${title}`)
      // Here you would navigate to a detail page
    })
  })

  // Campus cards click handling
  const campusCards = document.querySelectorAll(".campus-card")
  campusCards.forEach((card) => {
    card.addEventListener("click", function () {
      const title = this.querySelector(".campus-title").textContent
      alert(`Informações do campus: ${title}`)
    })
  })

  // Search functionality
  const searchInputs = document.querySelectorAll(".search-input")
  searchInputs.forEach((input) => {
    input.addEventListener("keypress", function (e) {
      if (e.key === "Enter") {
        const query = this.value
        if (query.trim()) {
          alert(`Buscando por: ${query}`)
          // Here you would implement search functionality
        }
      }
    })
  })
})

// Smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
  anchor.addEventListener("click", function (e) {
    e.preventDefault()
    const target = document.querySelector(this.getAttribute("href"))
    if (target) {
      target.scrollIntoView({
        behavior: "smooth",
      })
    }
  })
})

// Add loading animation
function showLoading() {
  document.body.style.opacity = "0.7"
  setTimeout(() => {
    document.body.style.opacity = "1"
  }, 500)
}

// Add to navigation links
document.querySelectorAll(".nav-link").forEach((link) => {
  link.addEventListener("click", showLoading)
})
