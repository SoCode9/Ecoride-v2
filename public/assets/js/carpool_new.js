/** Popup new car */
function showPopup(event) {
  event.preventDefault();
  document.getElementById("new-car").style.display = "block";

  const licencePlate = document.getElementById("licence-plate");
  const firstRegistrationDate = document.getElementById(
    "first-registration-date"
  );
  const brand = document.getElementById("brand");
  const model = document.getElementById("model");
  const electric = document.getElementById("electric-yes");
  const color = document.getElementById("color");
  const nbPassengers = document.getElementById("nb-passengers");

  licencePlate.setAttribute("required", "");
  firstRegistrationDate.setAttribute("required", "");
  brand.setAttribute("required", "");
  model.setAttribute("required", "");
  electric.setAttribute("required", "");
  color.setAttribute("required", "");
  nbPassengers.setAttribute("required", "");
}

function closePopup() {
  document.getElementById("new-car").style.display = "none";

  const licencePlate = document.getElementById("licence-plate");
  const firstRegistrationDate = document.getElementById(
    "first-registration-date"
  );
  const brand = document.getElementById("brand");
  const model = document.getElementById("model");
  const electric = document.getElementById("electric-yes");
  const color = document.getElementById("color");
  const nbPassengers = document.getElementById("nb-passengers");

  licencePlate.removeAttribute("required", "");
  firstRegistrationDate.removeAttribute("required", "");
  brand.removeAttribute("required", "");
  model.removeAttribute("required", "");
  electric.removeAttribute("required", "");
  color.removeAttribute("required", "");
  nbPassengers.removeAttribute("required", "");
}

document.addEventListener("DOMContentLoaded", function () {
  const carForm = document.getElementById("car-form-id");
  if (carForm) {
    carForm.addEventListener("submit", function (event) {
      event.preventDefault();
      submitJS();
    });
  }
});

/** Refresh only Car's section (not full page) **/
function refreshCarField() {
  fetch((window.BASE_URL || "") + "/car/select")
    .then((response) => response.text())
    .then((html) => {
      let carContainer = document.getElementById("car-field");
      if (!carContainer) {
        console.error("Erreur : car-field introuvable dans le DOM !");
        return;
      }

      carContainer.innerHTML = html;
    })
    .catch((error) => {
      console.error("Erreur de mise à jour car-field :", error);
    });
}

function submitJS() {
  const licencePlate = document.getElementById("licence-plate").value;
  const firstRegistrationDate = document.getElementById(
    "first-registration-date"
  ).value;
  const brand = document.getElementById("brand").value;
  const model = document.getElementById("model").value;
  const electricInput = document.querySelector(
    'input[name="electric"]:checked'
  );
  const electric = electricInput ? electricInput.value : "";
  const color = document.getElementById("color").value;
  const nbPassengers = document.getElementById("nb-passengers").value;

  fetch((window.BASE_URL || "") + "/car/add", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },

    body: new URLSearchParams({
      licence_plate: licencePlate,
      first_registration_date: firstRegistrationDate,
      brand: brand,
      model: model,
      electric: electric,
      color: color,
      nb_passengers: nbPassengers,
    }),
  })
    .then((result) => result.text())
    .then((data) => {
      console.log("Réponse du backend :", data);
      closePopup();
      refreshCarField();
    })
    .catch((error) => {
      console.error("Erreur :", error);
      alert("Une erreur s'est produite lors de l'ajout de la voiture");
    });
}
