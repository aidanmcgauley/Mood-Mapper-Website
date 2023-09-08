

const editPostBtn = document.getElementById("edit-post");


editPostBtn.addEventListener("click", function() {
  // Get diary details

  const diaryEntry = document.getElementById("diary-entry");

  // Trigs
  const triggerCheckboxes = document.querySelectorAll('input[type="checkbox"]:checked');
  const triggerIds = Array.from(triggerCheckboxes).map(cb => cb.value);



  // Enable edit area, chng btn to GREEN
  if (diaryEntry.disabled === true) {
    diaryEntry.disabled = false;
    editPostBtn.textContent = "Save changes";
    editPostBtn.classList.remove("btn-primary");
    editPostBtn.classList.add("btn-success");

    
    $('input[type="checkbox"]').prop('disabled', false);
  }
  // Disable area, change button back
  else {
    diaryEntry.disabled = true;
    editPostBtn.textContent = "Edit diary and mood triggers";
    editPostBtn.classList.remove("btn-success");
    editPostBtn.classList.add("btn-primary");

    $('input[type="checkbox"]').prop('disabled', true);

    // Get the diary entry value
    const diaryEntryValue = diaryEntry.value;
    const logId = document.getElementById("hiddenLogId").value;
    const diaryEntryId = document.getElementById("hiddenDiaryId").value;

    // Try send ajax
    const xhr = new XMLHttpRequest();
    xhr.open("PATCH", "http://localhost/PROJECT-APIGithub/api.php?update-card");
    xhr.setRequestHeader("Content-Type", "application/json");

    xhr.onload = function() {
      console.log(xhr.status);
      if (xhr.status === 200 || xhr.status === 201) {
        console.log("Diary entry and triggers updated successfully.");
      } else {
        console.error("Error updating diary entry and triggers.");
      }
    };

    xhr.send(JSON.stringify({ 
      diary_entry_id: diaryEntryId,
      diary_entry: diaryEntryValue,
      trigger: triggerIds,
      log_id: logId
    }));
    
  }
});


$(document).ready(function() {
  
  $('input[type="checkbox"]').prop('disabled', true);

  
  $('#edit-post-btn').click(function() {
    $('input[type="checkbox"]').prop('disabled', false);
  });
});
