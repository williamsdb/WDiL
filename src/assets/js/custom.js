// the confirmation pop-up on things like delete
function confirmRedirect(url) {
  if (confirm("Are you sure you want to proceed?")) {
    window.location.href = url;
  }
}

// display the trigger modal dialog
if (document.getElementById('triggerModal')){
  var triggerModal = document.getElementById('triggerModal');
  triggerModal.addEventListener('shown.bs.modal', function () {
    // Get current local date and time
    const now = new Date();
    
    // Extract the local date and time components
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');  // Months are 0-indexed
    const day = String(now.getDate()).padStart(2, '0');
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');
  
    // Format the date and time for the datetime-local input (YYYY-MM-DDTHH:mm)
    const localDateTime = `${year}-${month}-${day}T${hours}:${minutes}:${seconds}`;
  
    // Set the value of the datetime-local input to the formatted local date and time
    document.getElementById('datetimePicker').value = localDateTime;
  });  
}

// action on submitting the trigger dialog
if (document.getElementById('triggerButton')){
  document.getElementById('triggerButton').addEventListener('click', function(event) {

    // check date is in the past
    const dateInput = document.getElementById('datetimePicker');
    const inputDate = new Date(dateInput.value);
    const currentDate = new Date();
  
    // Clear any previous error message
    const errorMessage = document.getElementById('error-message');
    errorMessage.style.display = 'none';
  
    // Check if the input date is in the future
    if (inputDate >= currentDate) {
        // Show error message and prevent form submission
        errorMessage.style.display = 'block';
        event.preventDefault();
        return;
    }
  
    // Trigger the form submission
    document.getElementById('triggerForm').submit();
  });  
}

// add the activity id to the correct trigger button card
document.querySelectorAll('button[data-wdil]').forEach(button => {
  button.addEventListener('click', function() {
    // Get the value of data-wdil from the clicked button
    const wdilValue = button.dataset.wdil;

    document.getElementById('activityId').value = wdilValue;
  });
});

// action on clicking the show archived checkbox
if (document.getElementById('showArchived')){
  document.getElementById('showArchived').addEventListener('click', function(event) {
    // Get the checkbox element
    var checkbox = event.target;
  
    // Check if the checkbox is checked or not
    if (checkbox.checked) {
        // If checked, redirect to a particular page
        window.location.href = '/?state=1';
    } else {
        // If not checked, redirect to another page (or stay on the same page)
        window.location.href = '/?state=0'; 
    }
  });  
}

if (document.getElementById('colorContainer')){
  const colorContainer = document.querySelector('.color-container');
  let selectedColor = '';
  console.log('colour='.selectedColor);
  colorContainer.addEventListener('click', function(event) {
      const clickedElement = event.target;
      
      // Check if the clicked element is a label or input and get the value
      if (clickedElement.tagName === 'LABEL') {
          const inputId = clickedElement.getAttribute('for');
          const colorInput = document.getElementById(inputId);
          selectedColor = colorInput.value;
      } else if (clickedElement.tagName === 'INPUT') {
          selectedColor = clickedElement.value;
      }
  
      window.location.href = '/?colour='+selectedColor; 
  });  
}

function formatTime(seconds, round = 2, label = true) {
  let minutes = seconds / 60;
  let hours = minutes / 60;
  let days = hours / 24;
  let months = days / 30.44; // approximate months
  let years = days / 365.25; // approximate years

  if (years >= 1) {
      return `${years.toFixed(round)} year${years > 1 ? 's' : ''}`;
  } else if (months >= 1) {
      return `${months.toFixed(round)} month${months > 1 ? 's' : ''}`;
  } else if (days >= 1) {
      return `${days.toFixed(round)} day${days > 1 ? 's' : ''}`;
  } else if (hours >= 1) {
      return `${hours.toFixed(round)} hour${hours > 1 ? 's' : ''}`;
  } else if (minutes >= 1) {
      return `${minutes.toFixed(round)} minute${minutes > 1 ? 's' : ''}`;
  } else {
      return `${seconds.toFixed(round)} second${seconds > 1 ? 's' : ''}`;
  }
}
