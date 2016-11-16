<?php
	trait Updates {
		//abstract public function generar_tokens_guia($post);

		public function cambiar_estado_persona($post)
        {
            $json = array();

            $query = $this->db->prepare("
                update Persona set estado=:estado where id=:pid
            ");

            $query->execute(array(
                ":pid" => $post['pid'],
                ":estado" => $post['estado']
            ));

            $json["status"] = "ok";
            $json["ok"] = true;
            $json["msg"] = "La persona fue ".($post['estado'] == 1 ? "habilitada" : "deshabilitada")." correctamente.";

            return json_encode($json);
        }

        public function editar_persona($post)
        {
            $json = array();

            $query = $this->db->prepare("
                update Persona set 
                    nombre=:nombre, 
                    segundo_nombre=:snombre, 
                    apellido=:apellido, 
                    segundo_apellido=:sapellido, 
                    cedula=:cedula,
                    tipo_cedula=:tipo_cedula,
                    email=:email, 
                    usuario=:usuario, 
                    contrasena=:contrasena, 
                    fecha_nacimiento=:nacimiento, 
                    sexo=:sexo, 
                    estado_civil=:estado_civil, 
                    lugar=(select id from Lugar where nombre_completo=:lugar), 
                    direccion=:direccion, 
                    twitter=:twitter, 
                    facebook=:facebook,
                    instagram=:instagram,
                    formacion=:formacion
                where id=:id
            ");

            $query->execute(array(
                ":id" => $post['id'],
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
                ":instagram" => isset($post['instagram']) ? $post['instagram'] : null,
                ":formacion" => $post['formacion']
            ));

            /* Borro los telefonos viejos */
            $query = $this->db->prepare("
                delete from Telefono where persona=:id
            ");

            $query->execute(array(
                ":id" => $post['id']
            ));

            /* Añado los nuevos */
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

            /* Borro los permisos */
            $query = $this->db->prepare("
                delete from Permiso_Asignado where usuario=:uid
            ");

            $query->execute(array(
                ":uid" => $post['id']
            ));

            /* Añado los permisos */
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
                    ":uid" => $post['id']
                ));
            }

            /* Borro los cursos */
            $query = $this->db->prepare("
                delete from Persona_Curso where persona=:uid
            ");

            $query->execute(array(
                ":uid" => $post['id']
            ));

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
                        ":persona" => $post['id'],
                        ":fecha" => $fecha,
                        ":sede" => $c['sede'],
                    ));
                }
            }

            $json["status"] = "ok";
            $json["ok"] = true;
            $json["msg"] = $post['nombre'] . " " . $post['apellido'] . " fue modificado correctamente.";

            return json_encode($json);
        }

        public function editar_curso($post)
        {
            $json = array();

            $query = $this->db->prepare("
                update Curso set 
                    nombre=:nombre
                where id=:id
            ");

            $query->execute(array(
                ":id" => $post['id'],
                ":nombre" => $post['nombre']
            ));

            $json["status"] = "ok";
            $json["ok"] = true;
            $json["msg"] = "El curso " . $post['nombre'] . " fue modificado correctamente.";

            return json_encode($json);
        }
	}
?>