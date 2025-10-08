function showPopupValidate(event) {
  const reservationId = event.target.getAttribute("data-id");
  document.getElementById("idReservation-positive").value = reservationId;
  document.getElementById("idReservation-negative").value = reservationId;
  document.getElementById("validate-carpool").style.display = "block";
}

function closePopupValidate() {
  document.getElementById("validate-carpool").style.display = "none";
  document.getElementById("feedback-positive").style.display = "none";
  document.getElementById("feedback-negative").style.display = "none";
  document.getElementById("yes-button").classList.remove("selected-btn");
  document.getElementById("no-button").classList.remove("selected-btn");

  // reset forms
  document.querySelector("#feedback-positive form")?.reset();
  document.querySelector("#feedback-negative form")?.reset();
}

function handleValidation($bool) {
  if ($bool == true) {
    document.getElementById("feedback-positive").style.display = "block";
    document.getElementById("feedback-negative").style.display = "none";
    document.getElementById("yes-button").classList.add("selected-btn");
    document.getElementById("no-button").classList.remove("selected-btn");

    const commentNegative = document.getElementById("comment-negative") ?? null;
    commentNegative?.removeAttribute("required");
  } else {
    document.getElementById("feedback-negative").style.display = "block";
    document.getElementById("feedback-positive").style.display = "none";
    document.getElementById("yes-button").classList.remove("selected-btn");
    document.getElementById("no-button").classList.add("selected-btn");

    const commentNegative = document.getElementById("comment-negative") ?? null;
    commentNegative?.setAttribute("required", "");
  }
}
