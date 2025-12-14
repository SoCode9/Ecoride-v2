document.addEventListener("DOMContentLoaded", () => {
  const validateButtons = document.querySelectorAll(".validate-rating");

  validateButtons.forEach((button) => {
    button.addEventListener("click", () => {
      const ratingId = button.dataset.id;
      const url = (window.BASE_URL || "") + "/validate-rating";

      fetch(url, {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "ratingId=" + encodeURIComponent(ratingId),
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            setTimeout(() => location.reload(), 600);
          } else {
            console.error("Erreur : " + data.message);
          }
        })
        .catch((error) => {
          console.error("Erreur Ajax :", error);
          alert("Une erreur est survenue. Veuillez réessayer.");
        });
    });
  });

  const rejectButtons = document.querySelectorAll(".rejected-rating");

  rejectButtons.forEach((button) => {
    button.addEventListener("click", () => {
      const ratingId = button.dataset.id;
      const url = (window.BASE_URL || "") + "/reject-rating";

      fetch(url, {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "ratingId=" + encodeURIComponent(ratingId),
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            setTimeout(() => location.reload(), 600);
          } else {
            console.error("Erreur : " + data.message);
          }
        })
        .catch((error) => {
          console.error("Erreur Ajax :", error);
          alert("Une erreur est survenue. Veuillez réessayer.");
        });
    });
  });
});
