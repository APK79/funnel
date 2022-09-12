 /* bsCustomFileInput v1.3.1 (https://github.com/Johann-S/bs-custom-file-input)
 * Copyright 2018 Johann-S <johann.servoire@gmail.com>
 * Licensed under MIT (https://github.com/Johann-S/bs-custom-file-input/blob/master/LICENSE)
 */
!function(t,e){"object"==typeof exports&&"undefined"!=typeof module?module.exports=e():"function"==typeof define&&define.amd?define(e):t.bsCustomFileInput=e()}(this,function(){"use strict";var f={CUSTOMFILE:'.custom-file input[type="file"]',CUSTOMFILELABEL:".custom-file-label",FORM:"form",INPUT:"input"},o=function(t){if(0<t.childNodes.length)for(var e=[].slice.call(t.childNodes),n=0;n<e.length;n++){var o=e[n];if(3!==o.nodeType)return o}return t},a=function(t){var e=t.bsCustomFileInput.defaultText,n=t.parentNode.querySelector(f.CUSTOMFILELABEL);n&&(o(n).innerHTML=e)},n=!!window.File,i=function(t){if(t.hasAttribute("multiple")&&n)return[].slice.call(t.files).map(function(t){return t.name}).join(", ");if(-1===t.value.indexOf("fakepath"))return t.value;var e=t.value.split("\\");return e[e.length-1]};function h(){var t=this.parentNode.querySelector(f.CUSTOMFILELABEL);if(t){var e=o(t),n=i(this);n.length?e.innerHTML=n:a(this)}}function d(){for(var t=[].slice.call(this.querySelectorAll(f.INPUT)).filter(function(t){return!!t.bsCustomFileInput}),e=0,n=t.length;e<n;e++)a(t[e])}var p="bsCustomFileInput",y="reset",m="change";return{init:function(t,e){void 0===t&&(t=f.CUSTOMFILE),void 0===e&&(e=f.FORM);for(var n,o,i,r=[].slice.call(document.querySelectorAll(t)),A=[].slice.call(document.querySelectorAll(e)),a=0,l=r.length;a<l;a++){var c=r[a];Object.defineProperty(c,p,{value:{defaultText:(n=c,o=void 0,void 0,o="",i=n.parentNode.querySelector(f.CUSTOMFILELABEL),i&&(o=i.innerHTML),o)},writable:!0}),c.addEventListener(m,h)}for(var s=0,u=A.length;s<u;s++)A[s].addEventListener(y,d),Object.defineProperty(A[s],p,{value:!0,writable:!0})},destroy:function(){for(var t=[].slice.call(document.querySelectorAll(f.FORM)).filter(function(t){return!!t.bsCustomFileInput}),e=[].slice.call(document.querySelectorAll(f.INPUT)).filter(function(t){return!!t.bsCustomFileInput}),n=0,o=e.length;n<o;n++){var i=e[n];a(i),i[p]=void 0,i.removeEventListener(m,h)}for(var r=0,A=t.length;r<A;r++)t[r].removeEventListener(y,d),t[r][p]=void 0}}});

$(document).ready(function(){
	//локализация Moment
	moment.locale('ru');
	
	//Всплывающие подсказки
	$(function () {
		$('[data-toggle="tooltip"]').tooltip()
	})
	
	/* 		кнопка переноса в архив		*/
	// при открытии модального окна
	$('#archive-btn').on('show.bs.modal', function (event) {
	  // получить кнопку, которая его открыло
	  var button = $(event.relatedTarget) 
	  // извлечь информацию из атрибута data-content
	  var content = button.data('content') 
	  // вывести эту информацию в элемент, имеющий id="content"
	  $(this).find('.archived').html(content); 
})
	/*		 кнопка удаления		 */
	// при открытии модального окна
	$('#delete-btn').on('show.bs.modal', function (event) {
	  // получить кнопку, которая его открыло
	  var button = $(event.relatedTarget) 
	  // извлечь информацию из атрибута data-content
	  var content = button.data('content') 
	  // вывести эту информацию в элемент, имеющий id="content"
	  $(this).find('.removed').html(content); 
})

	/*    Числовой ввод     */
	$('#project_amount').keypress(function(event){// ON KEYPRESS DON'T USE keyup
	 var numero= String.fromCharCode(event.keyCode); // getting the value pressed
	 var myArray = ['0','1','2','3','4','5','6','7','8','9',0,1,2,3,4,5,6,7,8,9];
	// values allowed 
		index = myArray.indexOf(numero);// index search the position of numero 
		// if numero doesn't exist in myarray then index=-1 
		
	 var longeur= $('#project_amount').val().length; // gettting the length of the value
	 if(window.getSelection){
	 text = window.getSelection().toString();
	  }
	 if(index>=0&text.length>0){
		 // if numero exist in myarray and the content of the input is selected, we allow the value to appear 
		 // we added that only because the string length must be less then 10 
		  
	 }else	if(index>=0&longeur<12){
			// if numero exist in myarray and the length of the input is less then 10, we allow the value to appear
		 // you can change 10 to the limits that you want
	 }else {return false;
		  // we return false so that the value doesn't appear 
		   }	
	   });
	//кастомизация Select
	$(function () {
		$('.selectpicker').selectpicker();
	});
	
});

							