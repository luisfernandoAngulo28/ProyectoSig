angular.module('cartCtrl', [])

	.controller('cartController', function($scope, $http, Product, Cart, CartItem) {
		// object to hold all the data for the new comment form
		//$scope.producto = {};

		// loading variable to show the spinning loading icon
		$scope.loading = true;
    	$scope.buttonDisabled = false;
		$scope.cart = ['items'];

		Product.get(function(data){
			$scope.products = data.products;
		});
		
		$scope.getProduct = function(productid) {
 			Product.get({ id: productid }, function(data) {
				$scope.single_product = data;
				$scope.single_product.quantity = 0;
			});
		}

		// get all the comments first and bind it to the $scope.comments object
		Cart.get(function(data){
			$scope.cart = data.cart;
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
			$scope.loading = true;
			CartItem.save(product, function(){
				product.quantity = 0;
				Cart.get(function(getData){
					$scope.cart = getData.cart;
					$scope.loading = false;
				});
			});
		};

		// function to handle deleting a comment
		$scope.deleteCartItem = function(id) {
			$scope.loading = true; 
			CartItem.delete({id: id}, function(){
				Cart.get(function(getData){
					$scope.cart = getData.cart;
					$scope.loading = false;
				});
			});
		};

	}).filter('rawHtml', ['$sce', function($sce){
		  return function(val) {
		    return $sce.trustAsHtml(val);
		  };
	}]);