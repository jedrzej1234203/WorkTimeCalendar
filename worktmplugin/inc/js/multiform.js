    var currentTab = 0;
    showTab(currentTab);

    function showTab(n) {
    var x = document.getElementsByClassName("tab");
    x[n].style.display = "block";
    if (n === 0) {
    document.getElementById("prevBtn").style.display = "none";
} else {
    document.getElementById("prevBtn").style.display = "inline";
}
    if (n === (x.length - 1)) {
    document.getElementById("nextBtn").innerHTML = "Submit";

} else {
    document.getElementById("nextBtn").innerHTML = "Next";
}
    //... and run a function that will display the correct step indicator:
    fixStepIndicator(n)
}

    function nextPrev(n) {
    // This function will figure out which tab to display
    var x = document.getElementsByClassName("tab");
    // Exit the function if any field in the current tab is invalid:
    if (n === 1 && true) document.getElementsByClassName("step")[currentTab].className += " finish";
    // Hide the current tab:
    x[currentTab].style.display = "none";
    // Increase or decrease the current tab by 1:
    currentTab = currentTab + n;
    // if you have reached the end of the form...
    if (currentTab >= x.length) {
    // ... the form gets submitted:
    document.getElementById("multiForm").submit();
    return false;
}
    // Otherwise, display the correct tab:
    showTab(currentTab);
}

    function fixStepIndicator(n) {
    // This function removes the "active" class of all steps...
    var i, x = document.getElementsByClassName("step");
    for (i = 0; i < x.length; i++) {
    x[i].className = x[i].className.replace(" active", "");
}
    //... and adds the "active" class on the current step:
    x[n].className += " active";
}
