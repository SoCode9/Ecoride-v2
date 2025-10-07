document.addEventListener("DOMContentLoaded", function () {
  document.addEventListener(
    "submit",
    function (event) {
      const form = event.target;
      if (form.classList.contains("ajax-form")) {
        event.preventDefault();
      }
    },
    true
  );

  document.querySelectorAll(".tab-btn").forEach((button) => {
    button.addEventListener("click", function () {
      document
        .querySelectorAll(".tab-btn")
        .forEach((btn) => btn.classList.remove("active"));
      document
        .querySelectorAll(".tab-content")
        .forEach((content) => content.classList.remove("active"));

      this.classList.add("active");
      document.getElementById(this.dataset.target).classList.add("active");
    });
  });

  const editButton = document.getElementById("edit-button");
  const saveButton = document.getElementById("save-button");
  const addCarButton = document.getElementById("add-car-button");
  const addPrefButton = document.getElementById("add-pref-button");
  const updatePhoto = document.getElementById("edit-photo-icon");

  if (editButton && saveButton) {
    editButton.addEventListener("click", () => {
      editButton.classList.remove("active");
      saveButton.classList.add("active");

      document.querySelectorAll('input[type="radio"]').forEach((checkbox) => {
        checkbox.classList.remove("radio-not-edit");
      });

      if (addCarButton) {
        addCarButton.classList.remove("hidden");
      }

      if (addPrefButton) {
        addPrefButton.classList.remove("hidden");
      }
      if (updatePhoto) {
        updatePhoto.classList.remove("hidden");
      }

      document.querySelectorAll(".delete-car-form").forEach((deleteIcon) => {
        deleteIcon.classList.remove("hidden");
      });

      document.querySelectorAll(".delete-pref-form").forEach((deleteIcon) => {
        deleteIcon.classList.remove("hidden");
      });

      document.querySelectorAll('input[type="radio"]').forEach((checkbox) => {
        checkbox.classList.remove("radio-not-edit");
      });
    });

    saveButton.addEventListener("click", () => {
      saveButton.classList.remove("active");
      editButton.classList.add("active");

      document.querySelectorAll('input[type="radio"]').forEach((checkbox) => {
        checkbox.classList.add("radio-not-edit");
      });

      if (addCarButton) {
        addCarButton.classList.add("hidden");
      }

      if (addPrefButton) {
        addPrefButton.classList.add("hidden");
      }

      document.querySelectorAll('input[type="radio"]').forEach((checkbox) => {
        checkbox.classList.add("radio-not-edit");
      });
    });
  }

  /*if "passager" is selected-> the car and preference sections are not displayed*/
  const roleRadios = document.querySelectorAll('input[name="user_role"]');
  const carSection = document.querySelector(".scrollable-container");

  function toggleCarSection() {
    let selectedRole = document.querySelector(
      'input[name="user_role"]:checked'
    ).id;

    if (selectedRole === "role-passenger") {
      carSection.classList.add("hidden");
    } else {
      carSection.classList.remove("hidden");
    }
  }

  // Execute on loading to apply the right condition on departure
  toggleCarSection();

  // Check the condition when I change the selection
  roleRadios.forEach((radio) => {
    radio.addEventListener("change", toggleCarSection);
  });

  /** Display form to add a new car **/
  if (addCarButton) {
    addCarButton.addEventListener("click", () => {
      document.querySelector(".carForm").classList.remove("hidden");
    });
  }

  /** Display form to add a new preference **/
  if (addPrefButton) {
    addPrefButton.addEventListener("click", () => {
      document.querySelector(".pref-form").classList.remove("hidden");
    });
  }

  saveButton.addEventListener("click", function () {
    addCarButton.classList.add("hidden");

    addPrefButton.classList.add("hidden");

    const carForm = document.querySelector(".carForm");
    carForm.classList.add("hidden");

    const prefForm = document.querySelector(".pref-form");
    prefForm.classList.add("hidden");

    let selectedRole = document.querySelector(
      'input[name="user_role"]:checked'
    ).id;
    let selectedSmokePref = document.querySelector(
      'input[name = "smoke_pref"]:checked'
    ).id;
    let selectedPetPref = document.querySelector(
      'input[name="pet_pref"]:checked'
    ).id;
    let selectedFoodPref = document.querySelector(
      'input[name="food_pref"]:checked'
    ).id;
    let selectedSpeakPref = document.querySelector(
      'input[name="speak_pref"]:checked'
    ).id;
    let selectedMusicPref = document.querySelector(
      'input[name="music_pref"]:checked'
    ).id;

    let roleId;
    if (selectedRole === "role-passenger") roleId = 1;
    if (selectedRole === "role-driver") roleId = 2;
    if (selectedRole === "role-both") roleId = 3;

    let smokePref;
    if (selectedSmokePref === "smoke-yes") smokePref = 1;
    if (selectedSmokePref === "smoke-no") smokePref = 0;
    if (selectedSmokePref === "smoke-undefined") smokePref = "NULL";

    let petPref;
    if (selectedPetPref === "pet-yes") petPref = 1;
    if (selectedPetPref === "pet-no") petPref = 0;
    if (selectedPetPref === "pet-undefined") petPref = "NULL";

    let foodPref;
    if (selectedFoodPref === "food-yes") foodPref = 1;
    if (selectedFoodPref === "food-no") foodPref = 0;
    if (selectedFoodPref === "food-undefined") foodPref = "NULL";

    let speakPref;
    if (selectedSpeakPref === "speak-yes") speakPref = 1;
    if (selectedSpeakPref === "speak-no") speakPref = 0;
    if (selectedSpeakPref === "speak-undefined") speakPref = "NULL";

    let musicPref;
    if (selectedMusicPref === "music-yes") musicPref = 1;
    if (selectedMusicPref === "music-no") musicPref = 0;
    if (selectedMusicPref === "music-undefined") musicPref = "NULL";

    fetch((window.BASE_URL || "") + "/mon-profil/update", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded", //Indicates to the server that data is being sent via POST
      },
      body:
        "role_id=" +
        encodeURIComponent(roleId) +
        "&smoke_pref=" +
        encodeURIComponent(smokePref) +
        "&pet_pref=" +
        encodeURIComponent(petPref) +
        "&food_pref=" +
        encodeURIComponent(foodPref) +
        "&speak_pref=" +
        encodeURIComponent(speakPref) +
        "&music_pref=" +
        encodeURIComponent(musicPref), //sent to server
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          location.reload();
        } else {
          console.error("Erreur : " + data.message);
        }
      })
      .catch((error) => {
        console.error("Erreur Ajax aa:", error);
        alert("Une erreur est survenue. Veuillez réessayer.");
      });
  });

  /** Refresh only Car's section (not full page) **/
  function refreshCarList() {
    fetch((window.BASE_URL || "") + "/car/list")
      .then((response) => response.text())
      .then((html) => {
        let carContainer = document.getElementById("car-container");
        if (!carContainer) {
          console.error("Erreur : car-container introuvable dans le DOM !");
          return;
        }

        carContainer.innerHTML = html;
      })
      .catch((error) => {
        console.error("Erreur de mise à jour car-container :", error);
      });
  }

  /** Add a car **/
  const carForm = document.getElementById("car-form");

  if (carForm) {
    carForm.addEventListener("submit", function (event) {
      event.preventDefault();

      let formData = new FormData(carForm);

      fetch((window.BASE_URL || "") + "/car/add", {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            carForm.reset();
            refreshCarList();
          } else {
            console.error("Erreur :", data.error);
            alert("Erreur : " + data.error);
          }
        })
        .catch((error) => {
          console.error("Erreur AJAX :", error);
        });
    });
  }
});

/** Popup new photo */
function showPopup(id) {
  document.getElementById(id).style.display = "block";
}

function closePopup(id) {
  document.getElementById(id).style.display = "none";
}
