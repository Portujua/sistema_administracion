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



			.when("/cursos", {
				templateUrl : "views/admin/cursos/cursos.html"
			})
			.when("/cursos/agregar", {
				templateUrl : "views/admin/cursos/agregar.html"
			})
			.when("/cursos/editar/:id", {
				templateUrl : "views/admin/cursos/agregar.html"
			})




			.when("/recuperar/:usuario", {
				templateUrl : "views/admin/recuperar.html"
			})


			.otherwise({redirectTo : "/login"});
	});
}());