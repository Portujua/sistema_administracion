insert into Telefono_Tipo (nombre) values 
	("Móvil"),
	("Casa"),
	("Trabajo"),
	("Fax"),
	("Otro");

insert into Persona (nombre, apellido, usuario, contrasena, fecha_nacimiento, sexo, lugar, direccion, facebook, cedula) values ("Administrador", "", "root", "root", "1993-03-19", "Masculino", 377, "UD-4 Sector Mucuritas", "https://www.facebook.com/AxlSM", "21115476");

insert into Telefono (tlf, tipo, persona) values 
	("0412-5558283", 1, 1),
	("0414-2491821", 1, 1),
	("0212-4324831", 1, 1);

insert into Permiso_Categoria (nombre) values ('Personas');

insert into Permiso (nombre, descripcion, riesgo, categoria) values ('personas_agregar', 'Podrá añadir nuevas personas al sistema', 6, 1),
	('personas_editar', 'Podrá editar cualquier persona disponible en el sistema', 6, 1),
	('personas_deshabilitar', 'Podrá deshabilitar cualquier persona disponible en el sistema', 5, 1);

insert into Permiso_Categoria (nombre) values ('Cursos');

insert into Permiso (nombre, descripcion, riesgo, categoria) values ('cursos_agregar', 'Podrá añadir nuevos cursos al sistema', 6, 2),
	('cursos_editar', 'Podrá editar cualquier curso disponible en el sistema', 6, 2),
	('cursos_deshabilitar', 'Podrá deshabilitar cualquier curso disponible en el sistema', 5, 2);