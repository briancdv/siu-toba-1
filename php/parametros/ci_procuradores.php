<?php
/**
 * Created by Marina Barrios
 * DATE: 31/07/17
 */
require_once('consultas/consultas.php');
class ci_procuradores extends staf_ci
{
	//-----------------------------------------------------------------------------------
	//------------------------------------ CUADRO ---------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cd_procuradores(staf_ei_cuadro $cuadro)
	{
        $dep = consultas::get_dep_habilitada();
        if($dep['id_dep'] == 2)
        {
            if(toba::perfil_de_datos()->posee_dimension('procuracion','staf') == 1)
            {
                $sql = "Select * From tribunal.procuradores";
                $rs = toba::db()->consultar($sql);
                $cuadro->set_datos($rs);
            }else{
                throw new toba_error('No posee permiso para cargar un procurador');
            }
        }elseif($dep['id_dep'] == 1){
            throw new toba_error('No posee permiso para cargar un procurador');
        }elseif($dep['id_dep'] == ''){
            throw new toba_error('Usuario inv&aacute;lido');
        }


	}

	function evt__cd_procuradores__seleccion($seleccion)
	{
        $this->dep('dt_procuradores')->cargar($seleccion);
        $this->dep('dt_procuradores')->set_cursor(0);
	}

	//-----------------------------------------------------------------------------------
	//------------------------------------- FORMULARIO ----------------------------------
	//-----------------------------------------------------------------------------------

	function conf__frm_procuradores(staf_ei_formulario $form)
	{
        if($this->dep('dt_procuradores')->hay_cursor())
        {
            $datos = $this->dep('dt_procuradores')->get();
            $form->descolapsar();
            $form->set_datos($datos);
        }else{
            $form->colapsar();
        }
	}

	function evt__frm_procuradores__alta($datos)
	{
        $this->dep('dt_procuradores')->nueva_fila($datos);
        $this->sincronizar();
	}

	function evt__frm_procuradores__modificacion($datos)
	{
        $this->dep('dt_procuradores')->set($datos);
        $this->sincronizar();
	}

	function evt__frm_procuradores__cancelar()
	{
        $this->dep('dt_procuradores')->resetear();
        $this->colapsar();
	}

    function sincronizar()
    {
        try
        {
            $this->dep('dt_procuradores')->sincronizar();
            $this->dep('dt_procuradores')->resetear();
            $this->colapsar();
        }
        catch(Exception $e)
        {
            toba::notificaion()->error('Error al sincronizar con la Base de Datos');
        }
    }

}
?>