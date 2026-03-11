angular.module('resourcesService', ['ngResource'])

	.factory("Cart", function($resource) {
	  return $resource("/api/cart/:id");
	})

	.factory("CartItem", function($resource) {
	  return $resource("/api/cart-item/:id");
	})

	.factory("Product", function($resource) {
	  return $resource("/api/product/:id");
	});