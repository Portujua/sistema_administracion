<?php
	trait Selects {
        public function cargar_permisos($post)
        {
            $query = $this->db->prepare("
                select p.id as id, p.nombre as nombre, p.descripcion as descripcion, p.riesgo as riesgo, pc.nombre as categoria
                from Permiso as p, Permiso_Categoria as pc
                where p.categoria=pc.id
                order by pc.id asc
            ");
            $query->execute();

            return json_encode($query->fetchAll());
        }
        
		public function cargar_lugares($post)
        {
            $query = $this->db->prepare("
                select *
                from Lugar
            ");

            $query->execute();

            return json_encode($query->fetchAll());
        }

        public function cargar_lugar($post)
        {
            $query = $this->db->prepare("
                select *
                from Lugar
                where id=:id
            ");

            $query->execute(array(
                ":id" => $post['lid']
            ));

            return json_encode($query->fetchAll());
        }

        public function cargar_personas($post)
        {
            $query = $this->db->prepare("
                select
                    p.id as id,
                    p.nombre as nombre,
                    p.segundo_nombre as segundo_nombre,
                    p.apellido as apellido,
                    p.segundo_apellido as segundo_apellido,
                    concat(
                        p.nombre, ' ',
                        (case when p.segundo_nombre is not null then concat(p.segundo_nombre, ' ') else '' end),
                        p.apellido,
                        (case when p.segundo_apellido is not null then concat(' ', p.segundo_apellido) else '' end)
                    ) as nombre_completo,
                    p.cedula as cedula,
                    p.email as email,
                    p.usuario as usuario,
                    p.contrasena as contrasena,
                    date_format(p.fecha_nacimiento, '%d/%m/%Y') as fecha_nacimiento, 
                    date_format(p.fecha_creado, '%d/%m/%Y') as fecha_creado,
                    p.sexo as sexo,
                    p.estado_civil as estado_civil,
                    p.estado as estado,
                    p.lugar as lugar,
                    (select nombre_completo from Lugar where id=p.lugar) as lugar_str,
                    p.direccion as direccion,
                    p.twitter as twitter,
                    p.facebook as facebook
                from Persona as p
            ");

            $query->execute();
            $personas = $query->fetchAll();

            for ($i = 0; $i < count($personas); $i++)
            {
                $personas[$i]["permisos"] = "";
                $personas[$i]["snombre"] = $personas[$i]["segundo_nombre"];
                $personas[$i]["sapellido"] = $personas[$i]["segundo_apellido"];

                /* Telefonos */
                $query = $this->db->prepare("
                    select 
                        t.tlf as numero,
                        tt.nombre as tipo
                    from Telefono as t, Telefono_Tipo as tt
                    where t.tipo=tt.id and t.persona=:pid
                ");

                $query->execute(array(
                    ":pid" => $personas[$i]['id']
                ));

                $personas[$i]['telefonos'] = $query->fetchAll();

                if (count($personas[$i]['telefonos']) > 0)
                    $personas[$i]['telefono'] = $personas[$i]['telefonos'][0]['numero'];

                /* Permisos */
                $query = $this->db->prepare("
                    select p.id as id
                    from Permiso_Asignado as pa, Permiso as p
                    where pa.permiso=p.id and pa.usuario=:usuario
                ");

                $query->execute(array(
                    ":usuario" => $personas[$i]['id']
                ));

                $permisos = $query->fetchAll();

                foreach ($permisos as $p)
                    $personas[$i]["permisos"] .= "[" . $p['id'] . "]";
            }

            return json_encode($personas);
        }

        public function cargar_persona($post)
        {
            $query = $this->db->prepare("
                select 
                    p.id as id,
                    p.nombre as nombre,
                    p.segundo_nombre as segundo_nombre,
                    p.apellido as apellido,
                    p.segundo_apellido as segundo_apellido,
                    p.cedula as cedula,
                    p.email as email,
                    p.usuario as usuario,
                    p.contrasena as contrasena,
                    date_format(p.fecha_nacimiento, '%d/%m/%Y') as fecha_nacimiento, 
                    date_format(p.fecha_creado, '%d/%m/%Y') as fecha_creado,
                    p.sexo as sexo,
                    p.estado_civil as estado_civil,
                    p.estado as estado,
                    (select nombre_completo from Lugar where id=p.lugar) as lugar,
                    p.direccion as direccion,
                    p.twitter as twitter,
                    p.facebook as facebook
                from Persona as p
                where cedula=:cedula
            ");

            $query->execute(array(
                ":cedula" => $post['cedula']
            ));

            $personas = $query->fetchAll();

            for ($i = 0; $i < count($personas); $i++)
            {
                $personas[$i]["permisos"] = "";
                $personas[$i]["snombre"] = $personas[$i]["segundo_nombre"];
                $personas[$i]["sapellido"] = $personas[$i]["segundo_apellido"];

                /* Telefonos */
                $query = $this->db->prepare("
                    select 
                        t.tlf as numero,
                        tt.nombre as tipo
                    from Telefono as t, Telefono_Tipo as tt
                    where t.tipo=tt.id and t.persona=:pid
                ");

                $query->execute(array(
                    ":pid" => $personas[$i]['id']
                ));

                $personas[$i]['telefonos'] = $query->fetchAll();

                if (count($personas[$i]['telefonos']) > 0)
                    $personas[$i]['telefono'] = $personas[$i]['telefonos'][0]['numero'];

                /* Permisos */
                $query = $this->db->prepare("
                    select p.id as id
                    from Permiso_Asignado as pa, Permiso as p
                    where pa.permiso=p.id and pa.usuario=:usuario
                ");

                $query->execute(array(
                    ":usuario" => $personas[$i]['id']
                ));

                $permisos = $query->fetchAll();

                foreach ($permisos as $p)
                    $personas[$i]["permisos"] .= "[" . $p['id'] . "]";
            }

            return json_encode($personas[0]);
        }
	}
?>