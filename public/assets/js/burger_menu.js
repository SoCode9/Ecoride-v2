document.addEventListener("DOMContentLoaded", function () {
    const currentTab = document.getElementById("current-tab-mobile");
    const currentPage = window.location.pathname.split("/").pop();

    const labels = {
        "index.php": "Accueil",
        "carpool_search.php": "Covoiturages",
        "carpool_details.php": "Covoiturages",
        "login.php": "Connexion",
        "user_space.php": "Mon espace",
        "employee_space.php": "Mon espace",
        "admin_space.php": "Mon espace"
    };

    if (labels[currentPage]) {
        currentTab.textContent = labels[currentPage];
    }
});
var sidenav = document.getElementById("my-sidenav");
var openBtn = document.getElementById("open-btn");
var closeBtn = document.getElementById("close-btn");

openBtn.onclick = openNav;
closeBtn.onclick = closeNav;

/* Set the width of the side navigation to 250px */
function openNav() {
    sidenav.classList.add("active");
}

/* Set the width of the side navigation to 0 */
function closeNav() {
    sidenav.classList.remove("active");
}