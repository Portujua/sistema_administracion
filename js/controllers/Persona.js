(function(){
	var Persona = function($scope, $http, $location, $routeParams, $timeout, $window, AlertService, RESTService)
	{		
		$scope.safeApply = function(fn) {
		    var phase = this.$root.$$phase;
		    if(phase == '$apply' || phase == '$digest') {
		        if(fn && (typeof(fn) === 'function')) {
		          fn();
		        }
		    } else {
		       this.$apply(fn);
		    }
		};

		$scope.editar = $routeParams.id;

		$scope.cargar_personas = function(){
			RESTService.getPersonas($scope);
		}

		$scope.cargar_persona = function(cedula){
			$.ajax({
			    url: "api/persona/" + cedula,
			    type: "POST",
			    data: {},
			    beforeSend: function(){},
			    success: function(data){
			        $scope.safeApply(function(){
			        	var json = $.parseJSON(data);
			        	$scope.persona = json;
			        })
			    }
			});
		}

		$scope.cargar_permisos = function(){
			RESTService.getPermisos($scope);
		}

		$scope.cargar_lugares = function(){
			RESTService.getLugares($scope);
		}

		$scope.cargar_cursos = function(){
			RESTService.getCursos($scope);
		}

		$scope.autocomplete_lugares = function(){
			var availableTags = [];
			var json = $scope.lugares;

			for (var i = 0; i < json.length; i++)
				if (json[i].tipo == "parroquia")
					availableTags.push({
						label: json[i].nombre_completo,
						value: json[i].nombre_completo
					})

			$( "input[name=lugar]" ).autocomplete({
				source: function(request, response) {
			        var results = $.ui.autocomplete.filter(availableTags, request.term);

			        response(results.slice(0, 10));
			    },
				minLength: 4,
				delay: 0,
				select: function(event, ui){
					$scope.safeApply(function(){
						$scope.persona.lugar = ui.item.value;
					})
				}
			});
		}

		$scope.todos_los_permisos = function(){
			$.confirm({
				title: "Confirmar",
				content: "¿Está seguro que desea marcar todos los permisos?",
				confirm: function(){
					$scope.persona.permisos = "";
					
					for (var i = 0; i < $scope.permisos.length; i++)
						$scope.persona.permisos += "[" + $scope.permisos[i].id + "]";
				}
			})
		}

		$scope.cambiar_permiso = function(pid, riesgo){
			if (riesgo >= 7 && $scope.persona.permisos.indexOf("[" + pid + "]") == -1)
			{
				$.confirm({
					title: "Confirme su acción",
					content: "Este permiso tiene un nivel de riesgo de " + riesgo + " sobre 10, <strong>¿está seguro que desea asignar este permiso?</strong>",
					confirm: function(){
						if ($scope.persona.permisos.indexOf("[" + pid + "]") == -1)
							$scope.persona.permisos += "[" + pid + "]";
						else
						{
							var permisos = "";
							var actuales = $scope.persona.permisos.split(']');

							for (var i = 0; i < actuales.length; i++)
								if (actuales[i].substring(1) != pid)
									permisos += "[" + actuales[i].substring(1) + "]";

							$scope.persona.permisos = permisos;
						}
					}
				})
			}
			else
			{
				if ($scope.persona.permisos.indexOf("[" + pid + "]") == -1)
					$scope.persona.permisos += "[" + pid + "]";
				else
				{
					var permisos = "";
					var actuales = $scope.persona.permisos.split(']');

					for (var i = 0; i < actuales.length; i++)
						if (actuales[i].substring(1) != pid)
							permisos += "[" + actuales[i].substring(1) + "]";

					$scope.persona.permisos = permisos;
				}
			}
		}

		$scope.registrar_persona = function(){
			$.confirm({
				title: 'Confirmar acción',
				content: '¿Está seguro que desea añadir a <strong>' + $scope.persona.nombre + ' ' + $scope.persona.apellido + '</strong>?',
				confirm: function(){
					var post = $scope.persona;

					var nac = post.fecha_nacimiento.split('/');
					post.nacimiento = nac[2] + "-" + nac[1] + "-" + nac[0];

					if (post.facebook && post.facebook.indexOf("https://") != -1)
						post.facebook = post.facebook.match(/\.com\/([A-Za-z0-9\_\-\.]+)([\/\?])?(.+)?/)[1];

					if (post.twitter && post.twitter.indexOf("https://") != -1)
						post.twitter = post.twitter.match(/\.com\/([A-Za-z0-9\_\-\.]+)([\/\?])?(.+)?/)[1];

					if (post.instagram && post.instagram.indexOf("https://") != -1)
						post.instagram = post.instagram.match(/\.com\/([A-Za-z0-9\_\-\.]+)([\/\?])?(.+)?/)[1];

					var fn = "agregar_persona";
					var msg = "Persona añadida con éxito";

					if ($routeParams.cedula)
					{
						fn = "editar_persona";
						msg = "Persona modificada con éxito";
					}

					$http({
						method: 'POST',
						url: "php/run.php?fn=" + fn,
						data: $.param(post),
						headers: {'Content-Type': 'application/x-www-form-urlencoded'}
					}).then(function(obj){
						console.log(obj)
						if (obj.data.ok)
						{
							AlertService.showSuccess(obj.data.msg);
					    	$location.path("/personas");
					    }
					    else
					    	console.log(obj.data);
					});
				},
				cancel: function(){}
			});
		}

		$scope.cambiar_estado = function(id, estado){
			$http({
				method: 'POST',
				url: "php/run.php?fn=cambiar_estado_persona",
				data: $.param({pid:id, estado:estado}),
				headers: {'Content-Type': 'application/x-www-form-urlencoded'}
			}).then(function(obj){
				console.log(obj)
				if (obj.data.ok)
				{
					AlertService.showSuccess(obj.data.msg);
			    	$scope.cargar_personas();
			    	$scope.p_ = null;
			    }
			    else
			    	console.log(obj.data);
			});
		}

		$scope.seleccionar = function(p){
			$scope.p_ = p;
		}

		$scope.anadir_curso = function(){
			$scope.persona.cursos.push({
				id: $scope.cursos[$scope.curso.id].id,
				nombre: $scope.cursos[$scope.curso.id].nombre,
				fecha: $scope.curso.fecha,
				sede: $scope.curso.sede
			});

			$scope.curso = null;
		}

		$scope.eliminar_curso = function(index){
			var aux = [];

			for (var i = 0; i < $scope.persona.cursos.length; i++)
				if (i != index)
					aux.push($scope.persona.cursos[i]);

			$scope.persona.cursos = aux;
		}

		if ($routeParams.cedula)
		{
			$scope.cargar_persona($routeParams.cedula);
		}
	};

	angular.module("adminapp").controller("Persona", Persona);
}());