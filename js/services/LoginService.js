(function(){
	angular.module("adminapp").factory('LoginService', function($http, $location, AlertService, $localStorage, $interval){
		return {
			user: $localStorage.user ? $localStorage.user : null,
			isLoggedIn: function(){
				return this.user != null;
			},
			logout: function(){
				$localStorage.$reset();
				window.location.reload(true);
			},
			login: function(loginData){
				var self = this;

				$http({
					method: 'POST',
					url: "php/run.php?fn=login", 
					data: $.param({username:loginData.username, password:loginData.password}),
					headers: {'Content-Type': 'application/x-www-form-urlencoded'}
				}).then(function(obj){
					console.log(obj.data)
					var data = obj.data;
					if (data.error)
						AlertService.showError("Usuario o contraseña inválida");
					else
					{
						self.user = data;
						self.user.password = loginData.password;
						$localStorage.user = self.user;
				        $location.path("/inicio");
					}
				});
			},
			getCurrentUser: function(){
				return this.user;
			},
			menuAdminPersonas: function(){
				if (!this.isLoggedIn()) return false;

				if (this.user.username == "root") return true;

				return this.user.personas_agregar || this.user.personas_editar || this.user.personas_deshabilitar;
			},
			menuAdmin: function(){
				if (!this.isLoggedIn()) return false;

				if (this.user.username == "root") return true;

				return this.menuAdminPersonas();
			}
		};
	})
}());