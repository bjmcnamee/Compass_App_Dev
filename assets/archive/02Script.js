// create button to show or hide ALL drugs
function showhide(buttonid) {
    var x = document.getElementById(buttonid);
    if (x.style.display === "none") {x.style.display = "block";}
    else {x.style.display = "none";}}


// create tabs to display individual drug, disease, variant
function openList(evt, TabName) {
    var i, tabcontent, tablinks; // Declare all variables
    tabcontent = document.getElementsByClassName("tabcontent"); // Get all elements with class="tabcontent" and hide them
    for (i = 0; i < tabcontent.length; i++) {tabcontent[i].style.display = "none";}
    tablinks = document.getElementsByClassName("tablinks"); // Get all elements with class="tablinks" and remove the class "active"
    for (i = 0; i < tablinks.length; i++) {tablinks[i].className = tablinks[i].className.replace("active", "");}
    document.getElementById(TabName).style.display = "block"; // Show the current tab, and add an "active" class to the button that opened the tab
    evt.currentTarget.className += "active";
}
