(function(exports, d) {
  function domReady(fn, context) {

    function onReady(event) {
      d.removeEventListener("DOMContentLoaded", onReady);
      fn.call(context || exports, event);
    }

    function onReadyIe(event) {
      if (d.readyState === "complete") {
        d.detachEvent("onreadystatechange", onReadyIe);
        fn.call(context || exports, event);
      }
    }

    d.addEventListener && d.addEventListener("DOMContentLoaded", onReady) ||
    d.attachEvent      && d.attachEvent("onreadystatechange", onReadyIe);
  }

  
  exports.domReady = domReady;
})(window, document);

const SERVER_URL = 'https://pensioenscan.jasperspoor.repl.co';
const API_URL = SERVER_URL + '/api/generate';
const FORM_ID = "#comp-l8ijfnio";
const UPLOAD_DIV_ID = "#comp-l8ikjksx"; 
const UPLOAD_BUTTON_ID = "#comp-l8ikjksx div[role=\"button\"]"; 
const GROSS_WAGE_ID = "#input_comp-l8imfdku";
const RETIREMENT_DATE_ID = "#input_comp-l8imhl32";
const FILE_UPLOAD_ID = "#fileInputcomp-l8ikjksx";
const EMAIL_ID = "#input_comp-l8ijfnkh";
const SUBMIT_BUTTON_ID = "#comp-l8ijfnl4 button"; 
const SUCCESS_MSG_ID = "#comp-l8yhjree";
const LOADING_ANIMATION_ID = "#comp-l8ycfv7x";
const LOADING_VECTOR_SVG = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" height="130" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid"><circle cx="84" cy="50" r="10" fill="#101010">    <animate attributeName="r" repeatCount="indefinite" dur="0.8928571428571428s" calcMode="spline" keyTimes="0;1" values="10;0" keySplines="0 0.5 0.5 1" begin="0s"></animate>    <animate attributeName="fill" repeatCount="indefinite" dur="3.571428571428571s" calcMode="discrete" keyTimes="0;0.25;0.5;0.75;1" values="#101010;#416753;#2f6144;#224634;#101010" begin="0s"></animate></circle><circle cx="16" cy="50" r="10" fill="#101010">  <animate attributeName="r" repeatCount="indefinite" dur="3.571428571428571s" calcMode="spline" keyTimes="0;0.25;0.5;0.75;1" values="0;0;10;10;10" keySplines="0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1" begin="0s"></animate>  <animate attributeName="cx" repeatCount="indefinite" dur="3.571428571428571s" calcMode="spline" keyTimes="0;0.25;0.5;0.75;1" values="16;16;16;50;84" keySplines="0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1" begin="0s"></animate></circle><circle cx="50" cy="50" r="10" fill="#224634">  <animate attributeName="r" repeatCount="indefinite" dur="3.571428571428571s" calcMode="spline" keyTimes="0;0.25;0.5;0.75;1" values="0;0;10;10;10" keySplines="0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1" begin="-0.8928571428571428s"></animate>  <animate attributeName="cx" repeatCount="indefinite" dur="3.571428571428571s" calcMode="spline" keyTimes="0;0.25;0.5;0.75;1" values="16;16;16;50;84" keySplines="0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1" begin="-0.8928571428571428s"></animate></circle><circle cx="84" cy="50" r="10" fill="#2f6144">  <animate attributeName="r" repeatCount="indefinite" dur="3.571428571428571s" calcMode="spline" keyTimes="0;0.25;0.5;0.75;1" values="0;0;10;10;10" keySplines="0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1" begin="-1.7857142857142856s"></animate>  <animate attributeName="cx" repeatCount="indefinite" dur="3.571428571428571s" calcMode="spline" keyTimes="0;0.25;0.5;0.75;1" values="16;16;16;50;84" keySplines="0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1" begin="-1.7857142857142856s"></animate></circle><circle cx="16" cy="50" r="10" fill="#416753">  <animate attributeName="r" repeatCount="indefinite" dur="3.571428571428571s" calcMode="spline" keyTimes="0;0.25;0.5;0.75;1" values="0;0;10;10;10" keySplines="0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1" begin="-2.6785714285714284s"></animate>  <animate attributeName="cx" repeatCount="indefinite" dur="3.571428571428571s" calcMode="spline" keyTimes="0;0.25;0.5;0.75;1" values="16;16;16;50;84" keySplines="0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1;0 0.5 0.5 1" begin="-2.6785714285714284s"></animate></circle></svg>';

function submitForm() {

  let data = new FormData();
  data.append('field:comp-l8imfdku', $(GROSS_WAGE_ID).val());
  data.append('field:comp-l8imhl32', $(RETIREMENT_DATE_ID).val());
  data.append('field:comp-l8ikjksx', $(FILE_UPLOAD_ID)[0].files[0]);
  data.append('field:comp-l8ijfnkh', $(EMAIL_ID).val());

  data.forEach((a,b) => console.log(a,b));
       
  $.ajax({
    url:         API_URL,
    type:        'POST',
    crossDomain:  true,
    processData:  false,
    contentType:  false,
    mimeType:     'multipart/form-data',
    data:         data
  }).done(function(response) {
    var json = $.parseJSON(response);
    window.open(json.downloadUrl);
  }).fail(function() {
    hideLoadingAnimation();  
    showSuccessMessage();
  });
}

// Disable all WIX event listeners by replacing the html
function disableWixUploadEventListeners()
{
  let uploadDiv = $(UPLOAD_DIV_ID);
  uploadDiv.replaceWith(uploadDiv.prop('outerHTML'));
  uploadDiv = $(UPLOAD_DIV_ID);
}

function setupUploadButton()
{
  let fileInput = $(FILE_UPLOAD_ID);
  let uploadButton = $(UPLOAD_BUTTON_ID);
    
  fileInput.attr('accept', '.xml');
  uploadButton.click(function() { fileInput.click() });
}

function hideLoadingAnimation()
{
  $(LOADING_ANIMATION_ID).hide();
  $(FORM_ID).css('opacity', 1);
}

function showLoadingAnimation()
{
  $(LOADING_ANIMATION_ID).show();
  $(FORM_ID).css('opacity', 0);
}

function showSuccessMessage()
{
  $(FORM_ID).hide();
}

function changeLoadingVector()
{
  $(LOADING_ANIMATION_ID).find('svg').replaceWith(LOADING_VECTOR_SVG);
  $(LOADING_ANIMATION_ID).find('svg').css('height', '130px');
}

function setupForm() {

  changeLoadingVector();
  hideLoadingAnimation();
  disableWixUploadEventListeners();
  
  let form = $(FORM_ID);
  let submitButton = $(SUBMIT_BUTTON_ID);

  setupUploadButton();
  
  form.submit((e) => { 
    e.preventDefault(); 
    showLoadingAnimation();
    submitForm(); 
  });

  submitButton.click((e) => {
    e.preventDefault(); 
    showLoadingAnimation();
    submitForm(); 
  });
}


domReady(() => { setTimeout(setupForm, 1000); });