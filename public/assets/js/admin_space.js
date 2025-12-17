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

  //CREATE A NEW EMPLOYEE

  const newEmployeeBtn = document.getElementById("newEmployeeBtn") ?? null;
  if (newEmployeeBtn !== null) {

    newEmployeeBtn.addEventListener("click", function () {
      document.getElementById("new-employee").style.display = "block";
    });

    const newEmployeeClose = document.getElementById("close-employee-popup");
    newEmployeeClose.addEventListener("click", function () {
      document.getElementById("new-employee").style.display = "none";
    });

    const formNewEmployee = document.getElementById("employee-form-id");

    formNewEmployee.addEventListener("submit", function (e) {
      e.preventDefault();

      const data = {
        pseudo: document.getElementById("pseudo-employee").value,
        email: document.getElementById("mail-employee").value,
        password: document.getElementById("password-employee").value,
      };

      const url = (window.BASE_URL || "") + "/new-employee";
      fetch(url, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data),
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            setTimeout(() => location.reload(), 600);
          } else {
            location.reload();
          }
        })
        .catch((error) => {
          console.error("Erreur Ajax :", error);
        });
    });
  }

  //SUSPEND A USER
  const suspendUser = document.querySelectorAll(".suspend-user");

  suspendUser.forEach((button) => {
    button.addEventListener("click", () => {
      const userId = button.dataset.id;
      const url = (window.BASE_URL || "") + "/suspend-user";
      fetch(url, {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "userId=" + encodeURIComponent(userId),
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

  //REACTIVATE A USER
  const reactivateUser = document.querySelectorAll(".reactivate-user");

  reactivateUser.forEach((button) => {
    button.addEventListener("click", () => {
      const userId = button.dataset.id;
      const url = (window.BASE_URL || "") + "/reactivate-user";
      fetch(url, {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "userId=" + encodeURIComponent(userId),
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
