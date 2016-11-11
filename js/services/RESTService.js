(function(){
	angular.module("adminapp").factory('RESTService', function($http, $timeout){
		return {
			getPersonas: function(s){
				$http.get("api/personas").then(function(obj){
					s.personas = obj.data;
					$timeout(function(){$('.selectpicker').selectpicker('refresh');}, 500);
				});
			},

			getPermisos: function(s){
				$http.get("api/permisos").then(function(obj){
					s.permisos = obj.data;
					$timeout(function(){$('.selectpicker').selectpicker('refresh');}, 500);
				});
			},

			getLugares: function(s){
				$http.get("api/lugares").then(function(obj){
					s.lugares = obj.data;
					$timeout(function(){$('.selectpicker').selectpicker('refresh');}, 500);
					s.autocomplete_lugares();
				});
			},

		};
	})
}());