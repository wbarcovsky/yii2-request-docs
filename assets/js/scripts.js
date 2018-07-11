var converter = new showdown.Converter();

function toggleBox(box) {
  box.classList.toggle('box-close');
  box.classList.toggle('box-open');
}

function selectTab(element, showTableClass) {
  // Set is-active class to tabs
  $(element.closest('ul')).find('li').removeClass('is-active');
  $(element.closest('li')).addClass('is-active');

  // Show content
  var content = element.closest('.data');
  $(content).find('.tab-content').addClass('hide');
  $(content).find('.' + showTableClass).removeClass('hide');
}

function loadParams(element, hash, tabClass) {
  selectTab(element, tabClass);
  var content = element.closest('.data');
  var load = $(content).find('.load');
  load.removeClass('hide');

  // Send request
  var url = $('body').data('full-info-url');
  $.get(url + '?hash=' + hash, function (data) {
    var json = JSON.parse(data);
    var showData = '';
    switch (tabClass) {
      case 'result-example':
        showData = json.result;
          break;
        case 'params-example':
          showData = json.params;
          break;
        case 'description':
          showData = converter.makeHtml(json.description);
          break;
    }
    if (Array.isArray(showData) && showData.length > 0) {
        $(content).find('.' + tabClass).jsonview(showData[0]);
    } else {
        $(content).find('.' + tabClass).html(showData);
    }
    load.addClass('hide');
  });
}
