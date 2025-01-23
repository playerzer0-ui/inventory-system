var select = document.getElementById("year");
var date = new Date();
var year = date.getFullYear();
for (var i = year - 4; i <= year + 3; i++) {
  var option = document.createElement('option');
  option.value = option.innerHTML = i;
  if (i === year) option.selected = true;
  select.appendChild(option);
}