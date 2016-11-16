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
                insert into Persona (nombre, segundo_nombre, apellido, segundo_apellido, cedula, email, usuario, contrasena, fecha_nacimiento, fecha_creado, sexo, estado_civil, lugar, direccion, twitter, facebook, instagram, tipo_cedula) 
                values (:nombre, :snombre, :apellido, :sapellido, :cedula, :email, :usuario, :contrasena, :nacimiento, now(), :sexo, :estado_civil, (select id from Lugar where nombre_completo=:lugar), :direccion, :twitter, :facebook, :instagram, :tipo_cedula)
            ");

            $query->execute(array(
                ":nombre" => strtoupper($post['nombre']),
                ":snombre" => isset($post['snombre']) ? strtoupper($post['snombre']) : null,
                ":apellido" => strtoupper($post['apellido']),
                ":sapellido" => isset($post['sapellido']) ? strtoupper($post['sapellido']) : null,
                ":cedula" => $post['cedula'],
                ":tipo_cedula" => $post['tipo_cedula'],
                ":email" => isset($post['email']) ? strtoupper($post['email']) : null,
                ":usuario" => isset($post['usuario']) ? strtoupper($post['usuario']) : null,
                ":contrasena" => isset($post['contrasena']) ? $post['contrasena'] : null,
                ":nacimiento" => $post['nacimiento'],
                ":sexo" => $post['sexo'],
                ":estado_civil" => $post['estado_civil'],
                ":lugar" => $post['lugar'],
                ":direccion" => isset($post['direccion']) ? strtoupper($post['direccion']) : null,
                ":twitter" => isset($post['twitter']) ? $post['twitter'] : null,
                ":facebook" => isset($post['facebook']) ? $post['facebook'] : null,
                ":instagram" => isset($post['instagram']) ? $post['instagram'] : null
            ));

            $uid = $this->db->lastInsertId();

            /* Telefonos */
            $post['telefonos'] = array();

            if (isset($post['telefono']))
                $post['telefonos'][] = array(
                    "tlf" => $post['telefono'],
                    "tipo" => 2
                );

            if (isset($post['telefono_movil']))
                $post['telefonos'][] = array(
                    "tlf" => $post['telefono_movil'],
                    "tipo" => 1
                );

            foreach ($post['telefonos'] as $tlf)
            {
               $query = $this->db->prepare("
                    insert into Telefono (tlf, tipo, persona) 
                    values (:tlf, :tipo, (select id from Persona where cedula=:cedula))
                ");

                $query->execute(array(
                    ":tlf" => $tlf['tlf'],
                    ":tipo" => $tlf['tipo'],
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
                    //$fecha = $s[2] . "-" . $s[1] . "-" . $s[0];
                    $fecha = $s[1] . "-" . $s[0] . "-01";

                    $query = $this->db->prepare("
                        insert into Persona_Curso (curso, persona, fecha, sede)
                        values (:curso, :persona, :fecha, :sede)
                    ");

                    $query->execute(array(
                        ":curso" => $c['id'],
                        ":persona" => $uid,
                        ":fecha" => $fecha,
                        ":sede" => $c['sede'],
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