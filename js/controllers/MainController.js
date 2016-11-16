(function(){
	var MainController = function($scope, $http, $location, $routeParams, $interval, $timeout, $window, LoginService, AlertService)
	{		
		$scope.loginService = LoginService;
		$scope.enInicio = true;
		$scope.express = window.location.hash.indexOf('express') != -1;

		$scope.url = window.location;

		$scope.nroResultados = 2; 

		$scope.login_form = {
			username: "root",
			password: "root"
		};

		if (LoginService.getCurrentUser() == null)
			$location.path("/login");

		$scope.paginationCount = function(n, total){
			var k = Math.ceil(total/n);
			var a = [];

			for (var i = 0; i < k; i++)
				a.push(i);

			return a;
		}

		$scope.cerrar_seccion = function(){
			if (window.location.hash.indexOf("editar") != -1 || window.location.hash.indexOf("agregar") != -1)
				$.confirm({
					title: "Confirmar acción",
					content: "Todo los cambios serán descartados, <strong>¿está seguro que desea cerrar esta ventana?</strong>",
					confirm: function(){
						$location.path("/inicio");
					}
				})
			else
				$location.path("/inicio");
		}

		$scope.login = function(){
			LoginService.login($scope.login_form);
		}

		$scope.logout = function(){
			$.confirm({
				title: '',
				content: '¿Está seguro que desea salir del sistema?',
				confirm: function(){
					LoginService.logout();
				},
				cancel: function(){}
			});
		}

		$scope.unset_session = function(){
			LoginService.logout();
		}
	};

	angular.module("adminapp").controller("MainController", MainController);
}());