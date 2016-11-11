(function(){
	angular.module("adminapp").config(function($routeProvider, $locationProvider){
		$routeProvider
			.when("/login", {
				templateUrl : "views/login.html"
			})
			.when("/inicio", {
				templateUrl : "views/inicio.html"
			})
			.when("/", {
				templateUrl : "views/inicio.html"
			})



			// Admin
			.when("/personas", {
				templateUrl : "views/admin/personas/personas.html"
			})
			.when("/personas/agregar", {
				templateUrl : "views/admin/personas/agregar.html"
			})
			.when("/personas/editar/:cedula", {
				templateUrl : "views/admin/personas/agregar.html"
			})


			.otherwise({redirectTo : "/login"});
	});
}());