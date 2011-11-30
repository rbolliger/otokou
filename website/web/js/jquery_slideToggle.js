$(document).ready(function() {
 // hides the slickbox as soon as the DOM is ready (a little sooner that page load)
  $('#filters').hide();
  
  $('#sf_admin_bar > a#button').click(function() {
    $('#filters').slideToggle(400);
    return false;
  });
});