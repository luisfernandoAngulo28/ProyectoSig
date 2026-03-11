angular.module('cartCtrl', [])

	.controller('cartController', function($scope, $http, Category, Product, Cart, CartItem) {
		// object to hold all the data for the new comment form
		//$scope.producto = {};

		// loading variable to show the spinning loading icon
		$scope.loading = true;
    	$scope.buttonDisabled = false;

		$scope.getCategories = function() {
			Category.get(function(data){
				$scope.categories = data.categories;
			});
		}
		
		$scope.getProduct = function(productid) {
 			Product.get({ id: productid }, function(data) {
				$scope.single_product = data;
				$scope.single_product.quantity = 0;
			});
		}

		// get all the comments first and bind it to the $scope.comments object
		Cart.get(function(data){
			$scope.cart = data;
			$scope.loading = false;
		});

	    $scope.getTotal = function() {
	        var total = 0;
	        angular.forEach($scope.cart.items, function(item) {
	            total += item.quantity * item.price;
	        })
	        return total;
	    }

		// function to handle submitting the form
		$scope.submitCartItem = function(product) {
    		$scope.buttonDisabled = true;
			$scope.loading = true;

			// save the comment. pass in comment data from the form
			CartItem.save(product);
			product.quantity = 0;
			Cart.get(function(getData){
				$scope.cart = getData;
				$scope.loading = false;
    			$scope.buttonDisabled = false;
			});
		};

		// function to handle deleting a comment
		$scope.deleteCartItem = function(id) {
			$scope.loading = true; 

			CartItem.delete({id: id});

			Cart.get(function(getData){
				$scope.cart = getData;
				$scope.loading = false;
			});
		};

	}).filter('rawHtml', ['$sce', function($sce){
		  return function(val) {
		    return $sce.trustAsHtml(val);
		  };
	}]);
angular.module('resourcesService', ['ngResource'])

	.factory("Cart", function($resource) {
	  return $resource("/api/cart/:id");
	})

	.factory("CartItem", function($resource) {
	  return $resource("/api/cart-item/:id");
	})

	.factory("Category", function($resource) {
	  return $resource("/api/category/:id");
	})

	.factory("Product", function($resource) {
	  return $resource("/api/product/:id");
	});
function start() {
	var angularApp = angular.module('angularApp', [
		'ngResource', 
		'ngAnimate', 
		'perfect_scrollbar', 
		'cartCtrl', 
		'resourcesService'
	], function($interpolateProvider) {
	    $interpolateProvider.startSymbol('<%');
	    $interpolateProvider.endSymbol('%>');
	})
}
start();
//# sourceMappingURL=angular-code.js.map