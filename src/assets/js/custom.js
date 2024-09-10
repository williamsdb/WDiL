function confirmRedirect(url) {
  if (confirm("Are you sure you want to proceed?")) {
    window.location.href = url;
  }
}

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

document.querySelectorAll('button[data-wdil]').forEach(button => {
  button.addEventListener('click', function() {
    // Get the value of data-wdil from the clicked button
    const wdilValue = button.dataset.wdil;

    document.getElementById('activityId').value = wdilValue;
  });
});