//chart carpools per day
const carpoolsPerDayChart = document.querySelector("#carpools-per-day-chart");
const url1 = (window.BASE_URL || "") + "/chart-carpool-per-day";
fetch(url1)
  .then((response) => {
    return response.json();
  })
  .then((data) => {
    if (carpoolsPerDayChart) {
      createChart(
        carpoolsPerDayChart,
        data,
        "bar",
        "Nb de covoiturages sur les prochains jours",
        "Nb de covoiturages",
        "Dix prochains jours",
        "carpoolDate",
        "nbCarpool"
      );
    } else {
      console.warn("Élément #carpools-per-day-chart introuvable");
    }
  });

//chart credits earned by the platform
const creditsEarnedByPlatform = document.querySelector(
  "#credits-earned-by-platform"
);
const url2 = (window.BASE_URL || "") + "/chart-credits-earned";

fetch(url2)
  .then((response) => {
    return response.json();
  })
  .then((data) => {
    if (creditsEarnedByPlatform) {
      createChart(
        creditsEarnedByPlatform,
        data,
        "bar",
        "Nb de crédits gagnés dans les derniers jours",
        "Nb de crédits gagnés",
        "Dix derniers jours",
        "validationCarpoolDate",
        "carpoolsValidated"
      );
    } else {
      console.warn("Élément #credits-earned-by-platform introuvable.");
    }
  });

function createChart(
  chartElement,
  chartData,
  type,
  label,
  yTitle,
  xTitle,
  labelKey,
  dataKey
) {
  new Chart(chartElement, {
    type: type,
    data: {
      labels: chartData.map((row) => row[labelKey]),
      datasets: [
        {
          label: label,
          data: chartData.map((row) => row[dataKey]),
          backgroundColor: "#68C990",
        },
      ],
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: "bottom",
        },
      },
      scales: {
        y: {
          beginAtZero: true,
          title: {
            display: true,
            text: yTitle,
          },
          ticks: {
            stepSize: 1, // Integer intervals
            precision: 0, // Deletes decimals
          },
        },
        x: {
          title: {
            display: true,
            text: xTitle,
          },
        },
      },
    },
  });
}
