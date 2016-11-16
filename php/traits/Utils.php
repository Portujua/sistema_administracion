<?php
	trait Utils {
        public function check_cedula($post)
        {
            $query = $this->db->prepare("
                select *
                from Persona
                where cedula=:ci
            ");

            $query->execute(array(
                ":ci" => $post['ci']
            ));

            $json = array();
            $json['existe'] = $query->rowCount() > 0 ? true : false;
            $json['esValido'] = $query->rowCount() == 0 ? true : false;

            return json_encode($json);
        }

		public function obtener_lugar($post, $lugarfull = array("nombre_completo" => "", "id" => -1, "nombre" => ""), $json = true)
        {
            $query = null;
            $lugar = null;

            if ($lugarfull['id'] == -1)
                $lugarfull['id'] = $post['lid'];

            if (!isset($post['fk']))
            {
                $query = $this->db->prepare("
                    select *
                    from Lugar
                    where id=:lid
                ");

                $query->execute(array(
                    ":lid" => $post['lid']
                ));

                if ($query->rowCount() == 0)
                    return json_encode(array(
                        "nombre_completo" => "Error, lugar inexistente.",
                        "nombre" => "Error, lugar inexistente.",
                        "id" => -1
                    ));
            }
            else
            {
                $query = $this->db->prepare("
                    select *
                    from Lugar
                    where id=:fk
                ");

                $query->execute(array(
                    ":fk" => $post['fk']
                ));
            }

            $lugar = $query->fetchAll();
            $lugar = $lugar[0];

            if (strlen($lugarfull['nombre']) == 0)
                $lugarfull['nombre'] = ucwords(strtolower($lugar['nombre']));

            $lugarfull['nombre_completo'] .= 
                (strlen($lugarfull['nombre_completo']) > 0 ? ", " : "") . 
                ($lugar['tipo'] == "municipio" ? "Municipio " : "") . 
                ucwords(strtolower($lugar['nombre']));

            if ($lugar['lugar'] != null)
            {
                $rec = $this->obtener_lugar(array("fk" => $lugar['lugar'], "lid" => $post['lid']), $lugarfull);

                if (!isset($post['fk']) && $json)
                    return json_encode($rec);
                else
                    return $rec;
            }
            else
                return $lugarfull;
        }

        public function csv_personas()
        {
            $csv = array();
            $csv[] = array("Nombre completo", "Cedula", "Telefonos", "Correo electronico", "Sexo", "Estado Civil", "Direccion", "Facebook", "Twitter", "Instagram");

            $data = json_decode($this->cargar_personas(array()), true);
            
            foreach ($data as $d)
            {
                $tlfs = "";

                foreach ($d['telefonos'] as $tlf)
                    $tlfs .= $tlf['numero'] . " (" . $tlf['tipo'] . ")\r\n";

                $csv[] = array(
                    $d['nombre_completo'],
                    $d['cedula'],
                    $tlfs,
                    $d['email'],
                    $d['sexo'],
                    $d['estado_civil'],
                    $d['direccion'] . ", " . $d['lugar_str'],
                    isset($d['facebook']) ? $d['facebook'] : "",
                    isset($d['twitter']) ? $d['twitter'] : "",
                    isset($d['instagram']) ? $d['instagram'] : ""
                );
            }

            return $csv;
        }
	}
?>