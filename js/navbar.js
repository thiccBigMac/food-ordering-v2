function toggleDropdown() {
    const dropdown = document.getElementById("dropdown-menu");
    dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
}

window.onclick = function (event) {
    const dropdown = document.getElementById("dropdown-menu");
    if (!event.target.matches('.nav-username')) {
        if (dropdown && dropdown.style.display === "block") {
            dropdown.style.display = "none";
        }
    }
}