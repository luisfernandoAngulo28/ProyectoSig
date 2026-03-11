function start() {
	var angularApp = angular.module('angularApp', [
		'ngResource', 
		'ngAnimate', 
		'perfect_scrollbar', 
		'wu.masonry', 
		'cartCtrl', 
		'resourcesService'
	], function($interpolateProvider) {
	    $interpolateProvider.startSymbol('<%');
	    $interpolateProvider.endSymbol('%>');
	})
}
start();