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

        public function cargar_personas($post, $query_extra = "")
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
                    p.tipo_cedula as tipo_cedula,
                    p.email as email,
                    p.usuario as usuario,
                    p.contrasena as contrasena,
                    date_format(p.fecha_nacimiento, '%d/%m/%Y') as fecha_nacimiento, 
                    date_format(p.fecha_creado, '%d/%m/%Y') as fecha_creado,
                    p.sexo as sexo,
                    p.estado_civil as estado_civil,
                    p.estado as estado,
                    (select upper(nombre_completo) from Lugar where id=p.lugar) as lugar,
                    p.direccion as direccion,
                    p.twitter as twitter,
                    p.facebook as facebook,
                    p.instagram as instagram
                from Persona as p
                ".$query_extra."
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

                for ($k = 0; $k < count($personas[$i]['telefonos']); $k++)
                {
                    if ($personas[$i]['telefonos'][$k]['tipo'] == 'Casa')
                        $personas[$i]['telefono'] = $personas[$i]['telefonos'][$k]['numero'];
                    else
                        $personas[$i]['telefono_movil'] = $personas[$i]['telefonos'][$k]['numero'];
                }

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

                /* Cursos */
                $query = $this->db->prepare("
                    select 
                        c.id as id,
                        c.nombre as nombre,
                        date_format(pc.fecha, '%m/%Y') as fecha,
                        pc.sede as sede
                    from Curso as c, Persona_Curso as pc
                    where pc.curso=c.id and pc.persona=:pid
                ");

                $query->execute(array(
                    ":pid" => $personas[$i]['id']
                ));

                $personas[$i]['cursos'] = $query->fetchAll();
            }

            return json_encode($personas);
        }

        public function cargar_persona($post)
        {
            $personas = json_decode($this->cargar_personas(array(), "where p.cedula='".$post['cedula']."'"));

            return json_encode($personas[0]);
        }

        public function cargar_cursos($post)
        {
            $query = $this->db->prepare("
                select *
                from Curso
            ");

            $query->execute();

            return json_encode($query->fetchAll());
        }

        public function cargar_curso($post)
        {
            $query = $this->db->prepare("
                select *
                from Curso
                where id=:id
            ");

            $query->execute(array(
                ":id" => $post['id']
            ));

            $cursos = $query->fetchAll();

            return json_encode($cursos[0]);
        }
	}
?>