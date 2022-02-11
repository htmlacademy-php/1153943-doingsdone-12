'use strict';

let $checkbox = document.getElementsByClassName('show_completed');

if ($checkbox.length) {
  $checkbox[0].addEventListener('change', function (event) {
    let is_checked = +event.target.checked;

    let searchParams = new URLSearchParams(window.location.search);
    searchParams.set('show_completed', is_checked);

    window.location = '/?' + searchParams.toString();
  });
}

let $taskCheckboxes = document.getElementsByClassName('tasks');

if ($taskCheckboxes.length) {

  $taskCheckboxes[0].addEventListener('change', function (event) {
    if (event.target.classList.contains('task__checkbox')) {
      let el = event.target;

      let is_checked = +el.checked;
      let task_id = el.getAttribute('value');

      let url = '/?task_id=' + task_id + '&check=' + is_checked;
      window.location = url;
    }
  });
}

flatpickr('#date', {
  enableTime: false,
  dateFormat: "Y-m-d",
  locale: "ru"
});
