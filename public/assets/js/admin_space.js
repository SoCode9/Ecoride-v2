document.addEventListener("DOMContentLoaded", () => {
  //SUSPEND AN EMPLOYEE
  const suspendEmployee = document.querySelectorAll(".suspend-employee");

  suspendEmployee.forEach((button) => {
    button.addEventListener("click", () => {
      const employeeId = button.dataset.id;
      const url = (window.BASE_URL || "") + "/suspend-employee";
      fetch(url, {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "employeeId=" + encodeURIComponent(employeeId),
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
        });
    });
  });

  //REACTIVATE AN EMPLOYEE
  const reactivateEmployee = document.querySelectorAll(".reactivate-employee");

  reactivateEmployee.forEach((button) => {
    button.addEventListener("click", () => {
      const employeeId = button.dataset.id;
      const url = (window.BASE_URL || "") + "/reactivate-employee";
      fetch(url, {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "employeeId=" + encodeURIComponent(employeeId),
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
        });
    });
  });
});
