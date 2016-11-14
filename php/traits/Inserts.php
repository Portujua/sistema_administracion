<?php
	trait Inserts {
		public function agregar_ejemplo($post)
        {
            $json = array();

            $query = $this->db->prepare("insert into Ejemplo (nombre) values (:nombre)");

            $query->execute(array(
                ":nombre" => $post['nombre']
            ));

            $json["status"] = "ok";
            $json["msg"] = "El ejemplo fue añadido correctamente.";

            return json_encode($json);
        }

        public function agregar_persona($post)
        {
            $json = array();

            $query = $this->db->prepare("
                insert into Persona (nombre, segundo_nombre, apellido, segundo_apellido, cedula, email, usuario, contrasena, fecha_nacimiento, fecha_creado, sexo, estado_civil, lugar, direccion, twitter, facebook, instagram) 
                values (:nombre, :snombre, :apellido, :sapellido, :cedula, :email, :usuario, :contrasena, :nacimiento, now(), :sexo, :estado_civil, (select id from Lugar where nombre_completo=:lugar), :direccion, :twitter, :facebook, :instagram)
            ");

            $query->execute(array(
                ":nombre" => $post['nombre'],
                ":snombre" => isset($post['snombre']) ? $post['snombre'] : null,
                ":apellido" => $post['apellido'],
                ":sapellido" => isset($post['sapellido']) ? $post['sapellido'] : null,
                ":cedula" => $post['cedula'],
                ":email" => isset($post['email']) ? $post['email'] : null,
                ":usuario" => isset($post['usuario']) ? $post['usuario'] : null,
                ":contrasena" => isset($post['contrasena']) ? $post['contrasena'] : null,
                ":nacimiento" => $post['nacimiento'],
                ":sexo" => $post['sexo'],
                ":estado_civil" => $post['estado_civil'],
                ":lugar" => $post['lugar'],
                ":direccion" => isset($post['direccion']) ? $post['direccion'] : null,
                ":twitter" => isset($post['twitter']) ? $post['twitter'] : null,
                ":facebook" => isset($post['facebook']) ? $post['facebook'] : null,
                ":instagram" => isset($post['instagram']) ? $post['instagram'] : null
            ));

            $uid = $this->db->lastInsertId();

            /* Telefonos */
            $post['telefonos'] = array();

            if (isset($post['telefono']))
                $post['telefonos'][] = $post['telefono'];

            for ($i = 0; $i < count($post['telefonos']); $i++)
            {
               $query = $this->db->prepare("
                insert into Telefono (tlf, tipo, persona) 
                values (:tlf, 1, (select id from Persona where cedula=:cedula))");

                $query->execute(array(
                    ":tlf" => $post['telefonos'][$i],
                    ":cedula" => $post['cedula']
                )); 
            }

            /* Permisos */
            if (isset($post['permisos']))
            {
                $permisos = explode("]", $post['permisos']);

                foreach ($permisos as $p_)
                {
                    $p = str_replace("[", "", $p_);

                    if (strlen($p) == 0) continue;

                    $query = $this->db->prepare("
                        insert into Permiso_Asignado (permiso, usuario)
                        values (:pid, :uid)
                    ");

                    $query->execute(array(
                        ":pid" => $p,
                        ":uid" => $uid
                    ));
                }
            }

            /* Cursos */
            if (isset($post['cursos']))
            {
                foreach ($post['cursos'] as $c)
                {
                    $s = explode("/", $c['fecha']);
                    $fecha = $s[2] . "-" . $s[1] . "-" . $s[0];

                    $query = $this->db->prepare("
                        insert into Persona_Curso (curso, persona, fecha)
                        values (:curso, :persona, :fecha)
                    ");

                    $query->execute(array(
                        ":curso" => $c['id'],
                        ":persona" => $uid,
                        ":fecha" => $fecha,
                    ));
                }
            }

            $json["status"] = "ok";
            $json["ok"] = true;
            $json["msg"] = $post['nombre'] . " " . $post['apellido'] . " fue añadido correctamente.";

            return json_encode($json);
        }

        public function agregar_curso($post)
        {
            $json = array();

            $query = $this->db->prepare("
                insert into Curso (nombre) 
                values (:nombre)
            ");

            $query->execute(array(
                ":nombre" => $post['nombre']
            ));

            $json["status"] = "ok";
            $json["ok"] = true;
            $json["msg"] = "El curso " . $post['nombre'] . " fue añadido correctamente.";

            return json_encode($json);
        }
	}
?>