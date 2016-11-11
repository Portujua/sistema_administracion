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
                    email=:email, 
                    usuario=:usuario, 
                    contrasena=:contrasena, 
                    fecha_nacimiento=:nacimiento, 
                    sexo=:sexo, 
                    estado_civil=:estado_civil, 
                    lugar=(select id from Lugar where nombre_completo=:lugar), 
                    direccion=:direccion, 
                    twitter=:twitter, 
                    facebook=:facebook 
                where id=:id
            ");

            $query->execute(array(
                ":id" => $post['id'],
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
                ":facebook" => isset($post['facebook']) ? $post['facebook'] : null
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
                $post['telefonos'][] = $post['telefono'];

            for ($i = 0; $i < count($post['telefonos']); $i++)
            {
               $query = $this->db->prepare("
                    insert into Telefono (tlf, tipo, persona) 
                    values (:tlf, 1, (select id from Persona where cedula=:cedula))
                ");

                $query->execute(array(
                    ":tlf" => $post['telefonos'][$i],
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

            $json["status"] = "ok";
            $json["ok"] = true;
            $json["msg"] = $post['nombre'] . " " . $post['apellido'] . " fue modificado correctamente.";

            return json_encode($json);
        }
	}
?>