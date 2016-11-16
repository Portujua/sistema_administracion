create table Lugar (
	id int not null auto_increment,
	nombre varchar(128),
	nombre_completo varchar(512),
	tipo varchar(64),
	lugar int,
	primary key(id),
	foreign key (lugar) references Lugar(id)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

create table Persona (
	id int not null auto_increment,
	nombre varchar(32) not null,
	segundo_nombre varchar(32),
	apellido varchar(32) not null,
	segundo_apellido varchar(32),
	cedula varchar(32),
	email varchar(128),
	usuario varchar(32),
	contrasena varchar(32),
	fecha_nacimiento date,
	fecha_creado datetime,
	sexo varchar(10) not null,
	estado_civil varchar(32),
	estado tinyint(1) default 1,
	lugar int not null,
	direccion varchar(256) not null,
	twitter varchar(256),
	facebook varchar(256),
	instagram varchar(256),
	primary key(id),
	unique(cedula),
	foreign key (lugar) references Lugar(id)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

create table Telefono_Tipo (
	id int not null auto_increment,
	nombre varchar(128) not null,
	primary key(id)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

create table Telefono (
	id int not null auto_increment,
	tlf varchar(128) not null,
	tipo int not null,
	persona int not null,
	primary key(id),
	foreign key (tipo) references Telefono_Tipo(id),
	foreign key (persona) references Persona(id)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

# Categoria de una serie de permisos
create table Permiso_Categoria (
	id int not null auto_increment,
	nombre varchar(64) not null,
	descripcion varchar(128),
	primary key(id)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

# Un permiso que habilita una accion en el sistema
create table Permiso (
	id int not null auto_increment,
	nombre varchar(32) not null,
	descripcion varchar(128) not null,
	riesgo int default 0,
	categoria int not null,
	primary key(id),
	foreign key (categoria) references Permiso_Categoria(id)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

# Un permiso asignado a un personal
create table Permiso_Asignado (
	id int not null auto_increment,
	permiso int not null,
	usuario int not null,
	primary key(id),
	unique(permiso, usuario),
	foreign key (permiso) references Permiso(id),
	foreign key (usuario) references Persona(id)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

create table Log_Login (
	id int not null auto_increment,
	fecha datetime not null,
	username varchar(32) not null,
	primary key(id)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

create table Curso (
	id int not null auto_increment,
	nombre varchar(128),
	estado tinyint(1) default 1,
	primary key(id)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

create table Persona_Curso (
	id int not null auto_increment,
	persona int not null,
	curso int not null,
	fecha date not null,
	sede varchar(256) not null,
	primary key(id),
	foreign key (persona) references Persona(id),
	foreign key (curso) references Curso(id)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;