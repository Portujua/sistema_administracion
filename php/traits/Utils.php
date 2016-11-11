<?php
	trait Utils {
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
	}
?>