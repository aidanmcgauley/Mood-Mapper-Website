// Get a reference to the mood rating radio buttons and mood name select element
var moodRating = document.getElementsByName("moodRating");
var moodNameSelect = document.getElementById("moodName");
var triggers = document.getElementById("trigger");

// Disable the mood name select element initially
moodNameSelect.disabled = true;

// Add an event listener to the mood rating radio buttons
// Loops through to add listener to each radio button
for (var i = 0; i < moodRating.length; i++) {
    moodRating[i].addEventListener("change", function() {
        // Enable the mood name select element when a mood rating is selected
        moodNameSelect.disabled = false;
        
        // Create a new AJAX request
        var xhr = new XMLHttpRequest();
        
        xhr.open("GET", "../src/get_moods.php?mood_rating_id=" + this.value, true);
        
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        
        xhr.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                // Replace the existing options in the mood name select element with the new options
                moodNameSelect.innerHTML = this.responseText;
            }
        };
        
        xhr.send();
    });
}
