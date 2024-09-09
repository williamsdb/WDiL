function confirmRedirect(url) {
  if (confirm("Are you sure you want to proceed?")) {
    window.location.href = url;
  }
}

let table = new DataTable('#activities');

$("#trigger" ).dialog();

$('#but').click(function(){
    $("#trigger" ).dialog();
});