document.addEventListener("DOMContentLoaded", function () {
  const btn = document.getElementById("participate");
  if (!btn) return;

  btn.addEventListener("click", async () => {
    const carpoolId = btn.dataset.id;
    const url = (window.BASE_URL || "") + "/reservation/check";

    try {
      const res = await fetch(url, {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
          "X-Requested-With": "XMLHttpRequest",
        },
        credentials: "same-origin",
        body: "carpool_id=" + encodeURIComponent(carpoolId),
      });

      const data = await res.json();
      if (!data.success) {
        if (data.message?.includes("Utilisateur non connecte")) {
          if (
            confirm(
              "Vous devez être connecté. Cliquer sur 'OK' pour créer un compte."
            )
          ) {
            location.href = window.BASE_URL + "/controllers/login.php";
          }
          return;
        }
        alert(data.message || "Erreur");
        return;
      }

      if (data.availableSeats === 0) {
        alert("Désolé, il n'y a plus de places disponibles");
        return;
      }
      if (data.userCredits < data.travelPrice) {
        alert("Vous n'avez pas assez de crédits pour réserver ce covoiturage");
        return;
      }
      if (confirm("Souhaitez-vous vraiment participer à ce covoiturage ?")) {
        updateParticipation(carpoolId);
      }
    } catch (err) {
      console.error("Erreur AJAX :", err);
      alert("Une erreur réseau est survenue.");
    }
  });

  function updateParticipation(carpoolId) {
    fetch("../back/reservation/update_participation.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: "travel_id=" + carpoolId,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          alert("Votre participation a été confirmée !");
        }
      });
  }
});
