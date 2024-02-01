<?php
require_once DOCUMENT_ROOT . "conexion.php";
class Modelo
{
    public static function mdlAgregarReversos($rvs)
    {
        try {
            //code...
            $sql = "INSERT INTO tbl_reversos_rvs (rvs_clave, rvs_ruta) VALUES (?,?)";
            $con = Conexion::conectar();
            $pps = $con->prepare($sql);
            $pps->bindValue(1, $rvs['rvs_clave']);
            $pps->bindValue(2, $rvs['rvs_ruta']);
            $pps->execute();
            return $pps->rowCount() > 0;
        } catch (PDOException $th) {
            //throw $th;
        } finally {
            $pps = null;
            $con = null;
        }
    }
    public static function mdlBuscarReversoByRuta($ruta)
    {
        try {
            //code...
            $sql = "SELECT * FROM tbl_reversos_rvs WHERE rvs_ruta = ? AND rvs_status = 1";
            $con = Conexion::conectar();
            $pps = $con->prepare($sql);
            $pps->bindValue(1, $ruta);
            $pps->execute();
            return $pps->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            //throw $th;
        } finally {
            $pps = null;
            $con = null;
        }
    }
    public static function mdlMostrarReversoByClave($rvs_clave)
    {
        try {
            //code...
            $sql = "SELECT * FROM tbl_reversos_rvs WHERE rvs_clave = ?";
            $con = Conexion::conectar();
            $pps = $con->prepare($sql);
            $pps->bindValue(1, $rvs_clave);
            $pps->execute();
            return $pps->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            //throw $th;
        } finally {
            $pps = null;
            $con = null;
        }
    }
    public static function mdlAgregarActaCompletada($ac)
    {
        try {
            //code...
            $sql = "INSERT INTO tbl_actas_realizadas_ar (ar_curp, ar_ruta) VALUES (?,?)";
            $con = Conexion::conectar();
            $pps = $con->prepare($sql);
            $pps->bindValue(1, $ac['ar_curp']);
            $pps->bindValue(2, $ac['ar_ruta']);
            $pps->execute();
            return $pps->rowCount() > 0;
        } catch (PDOException $th) {
            //throw $th;
        } finally {
            $pps = null;
            $con = null;
        }
    }
    public static function mdlBuscarActaByRuta($ruta)
    {
        try {
            //code...
            $sql = "SELECT * FROM tbl_actas_realizadas_ar WHERE ar_ruta = ? AND ar_status = 1";
            $con = Conexion::conectar();
            $pps = $con->prepare($sql);
            $pps->bindValue(1, $ruta);
            $pps->execute();
            return $pps->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            //throw $th;
        } finally {
            $pps = null;
            $con = null;
        }
    }
    public static function mdlMostrarActasAll()
    {
        try {
            //code...
            $sql = "SELECT * FROM tbl_actas_realizadas_ar";
            $con = Conexion::conectar();
            $pps = $con->prepare($sql);
            $pps->execute();
            return $pps->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            //throw $th;
        } finally {
            $pps = null;
            $con = null;
        }
    }
    public static function mdlBuscarActaById($ar_id)
    {
        try {
            //code...
            $sql = "SELECT * FROM tbl_actas_realizadas_ar WHERE ar_id = ? AND ar_status = 1";
            $con = Conexion::conectar();
            $pps = $con->prepare($sql);
            $pps->bindValue(1, $ar_id);
            $pps->execute();
            return $pps->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            //throw $th;
        } finally {
            $pps = null;
            $con = null;
        }
    }
    public static function mdlBuscarReversosById($rvs_id)
    {
        try {
            //code...
            $sql = "SELECT * FROM tbl_reversos_rvs WHERE rvs_id = ? AND rvs_status = 1";
            $con = Conexion::conectar();
            $pps = $con->prepare($sql);
            $pps->bindValue(1, $rvs_id);
            $pps->execute();
            return $pps->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            //throw $th;
        } finally {
            $pps = null;
            $con = null;
        }
    }
    public static function mdlMostrarActas()
    {
        try {
            //code...
            $sql = "SELECT * FROM tbl_actas_realizadas_ar WHERE ar_status = 1 ORDER BY ar_id DESC ";
            $con = Conexion::conectar();
            $pps = $con->prepare($sql);
            $pps->execute();
            return $pps->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            //throw $th;
        } finally {
            $pps = null;
            $con = null;
        }
    }
    public static function mdlMostrarReversos()
    {
        try {
            //code...
            $sql = "SELECT * FROM tbl_reversos_rvs WHERE rvs_status = 1 ORDER BY rvs_id DESC ";
            $con = Conexion::conectar();
            $pps = $con->prepare($sql);
            $pps->execute();
            return $pps->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            //throw $th;
        } finally {
            $pps = null;
            $con = null;
        }
    }


    public static function mdlEliminarActaCompletada($ar_id)
    {
        try {
            //code...
            $sql = "UPDATE tbl_actas_realizadas_ar SET ar_status = 0 WHERE ar_id = ?";
            $con = Conexion::conectar();
            $pps = $con->prepare($sql);
            $pps->bindValue(1, $ar_id);
            $pps->execute();
            return $pps->rowCount() > 0;
        } catch (PDOException $th) {
            //throw $th;
        } finally {
            $pps = null;
            $con = null;
        }
    }
    public static function mdlEliminarActa($ar_id)
    {
        try {
            //code...
            $sql = "DELETE FROM tbl_actas_realizadas_ar WHERE ar_id = ?";
            $con = Conexion::conectar();
            $pps = $con->prepare($sql);
            $pps->bindValue(1, $ar_id);
            $pps->execute();
            return $pps->rowCount() > 0;
        } catch (PDOException $th) {
            //throw $th;
        } finally {
            $pps = null;
            $con = null;
        }
    }

    public static function mdlEliminarReversoCompletado($rvs_id)
    {
        try {
            //code...
            $sql = "UPDATE tbl_reversos_rvs SET rvs_status = 0 WHERE rvs_id = ?";
            $con = Conexion::conectar();
            $pps = $con->prepare($sql);
            $pps->bindValue(1, $rvs_id);
            $pps->execute();
            return $pps->rowCount() > 0;
        } catch (PDOException $th) {
            //throw $th;
        } finally {
            $pps = null;
            $con = null;
        }
    }
    public static function mdlMostrarUsuarios()
    {
        try {
            //code...
            $sql = "SELECT * FROM tbl_usuarios_usr WHERE usr_estado_borrado = 1 ORDER BY usr_id DESC ";
            $con = Conexion::conectar();
            $pps = $con->prepare($sql);
            $pps->execute();
            return $pps->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            //throw $th;
        } finally {
            $pps = null;
            $con = null;
        }
    }
    public static function mdlAgregarUsuario($usr)
    {
        try {
            //code...
            $sql = "INSERT INTO tbl_usuarios_usr (usr_correo, usr_contraseña) VALUES (?,?)";
            $con = Conexion::conectar();
            $pps = $con->prepare($sql);
            $pps->bindValue(1, $usr['usr_correo']);
            $pps->bindValue(2, $usr['usr_contraseña']);
            $pps->execute();
            return $pps->rowCount() > 0;
        } catch (PDOException $th) {
            //throw $th;
        } finally {
            $pps = null;
            $con = null;
        }
    }
    public static function mdlActualizarUsuario($usr)
    {
        try {
            //code...
            $sql = "UPDATE tbl_usuarios_usr SET usr_correo = ?, usr_contraseña = ? WHERE usr_id = ?";
            $con = Conexion::conectar();
            $pps = $con->prepare($sql);
            $pps->bindValue(1, $usr['usr_correo']);
            $pps->bindValue(2, $usr['usr_contraseña']);
            $pps->bindValue(3, $usr['usr_id']);
            $pps->execute();
            return $pps->rowCount() > 0;
        } catch (PDOException $th) {
            //throw $th;
        } finally {
            $pps = null;
            $con = null;
        }
    }
    public static function mdlEliminarUsuario($usr_id)
    {
        try {
            //code...
            $sql = "UPDATE tbl_usuarios_usr SET usr_estado_borrado = 0 WHERE usr_id = ?";
            $con = Conexion::conectar();
            $pps = $con->prepare($sql);
            $pps->bindValue(1, $usr_id);
            $pps->execute();
            return $pps->rowCount() > 0;
        } catch (PDOException $th) {
            //throw $th;
        } finally {
            $pps = null;
            $con = null;
        }
    }
    public static function mdlMostrarUsuariosByCorreo($usr_correo)
    {
        try {
            //code...
            $sql = "SELECT * FROM tbl_usuarios_usr WHERE usr_estado_borrado = 1 AND usr_correo = ?";
            $con = Conexion::conectar();
            $pps = $con->prepare($sql);
            $pps->bindValue(1, $usr_correo);
            $pps->execute();
            return $pps->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            //throw $th;
        } finally {
            $pps = null;
            $con = null;
        }
    }
    public static function mdlMostrarServicios()
    {
        try {
            //code...
            $sql = "SELECT * FROM tbl_servicios_srv WHERE srv_estado_borrado = 1 ORDER BY srv_id DESC ";
            $con = Conexion::conectar();
            $pps = $con->prepare($sql);
            $pps->execute();
            return $pps->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            //throw $th;
        } finally {
            $pps = null;
            $con = null;
        }
    }
    public static function mdlMostrarServiciosByNombre($srv_nombre)
    {
        try {
            //code...
            $sql = "SELECT * FROM tbl_servicios_srv WHERE srv_estado_borrado = 1 AND srv_nombre = ?";
            $con = Conexion::conectar();
            $pps = $con->prepare($sql);
            $pps->bindValue(1, $srv_nombre);
            $pps->execute();
            return $pps->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            //throw $th;
        } finally {
            $pps = null;
            $con = null;
        }
    }
    public static function mdlAgregarServicio($srv)
    {
        try {
            //code...
            $sql = "INSERT INTO tbl_servicios_srv (srv_nombre) VALUES (?)";
            $con = Conexion::conectar();
            $pps = $con->prepare($sql);
            $pps->bindValue(1, $srv['srv_nombre']);
            $pps->execute();
            return $pps->rowCount() > 0;
        } catch (PDOException $th) {
            //throw $th;
        } finally {
            $pps = null;
            $con = null;
        }
    }
    public static function mdlActualizarServicio($srv)
    {
        try {
            //code...
            $sql = "UPDATE tbl_servicios_srv SET srv_nombre = ? WHERE srv_id = ?";
            $con = Conexion::conectar();
            $pps = $con->prepare($sql);
            $pps->bindValue(1, $srv['srv_nombre']);
            $pps->bindValue(2, $srv['srv_id']);
            $pps->execute();
            return $pps->rowCount() > 0;
        } catch (PDOException $th) {
            //throw $th;
        } finally {
            $pps = null;
            $con = null;
        }
    }

    public static function mdlMostrarPaquetesByNombre($pqt_nombre)
    {
        try {
            //code...
            $sql = "SELECT * FROM tbl_paquetes_pqt WHERE pqt_estado_borrado = 1 AND pqt_nombre = ?";
            $con = Conexion::conectar();
            $pps = $con->prepare($sql);
            $pps->bindValue(1, $pqt_nombre);
            $pps->execute();
            return $pps->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            //throw $th;
        } finally {
            $pps = null;
            $con = null;
        }
    }

    public static function mdlAgregarPaquete($pqt)
    {
        try {
            //code...
            $sql = "INSERT INTO tbl_paquetes_pqt (pqt_nombre) VALUES (?)";
            $con = Conexion::conectar();
            $pps = $con->prepare($sql);
            $pps->bindValue(1, $pqt['pqt_nombre']);
            $pps->execute();
            return $con->lastInsertId();
        } catch (PDOException $th) {
            //throw $th;
        } finally {
            $pps = null;
            $con = null;
        }
    }
    public static function mdlAgregarPrecios($prc)
    {
        try {
            //code...
            $sql = "INSERT INTO tbl_precios_prc (prc_id_srv, prc_id_pqt, prc_precio) VALUES (?,?,?)";
            $con = Conexion::conectar();
            $pps = $con->prepare($sql);
            $pps->bindValue(1, $prc['prc_id_srv']);
            $pps->bindValue(2, $prc['prc_id_pqt']);
            $pps->bindValue(3, $prc['prc_precio']);
            $pps->execute();
            return $pps->rowCount() > 0;
        } catch (PDOException $th) {
            //throw $th;
        } finally {
            $pps = null;
            $con = null;
        }
    }
    public static function mdlActualizarPrecios($prc)
    {
        try {
            //code...
            $sql = "UPDATE tbl_precios_prc SET prc_precio = ? WHERE prc_id = ?";
            $con = Conexion::conectar();
            $pps = $con->prepare($sql);
            $pps->bindValue(1, $prc['prc_precio']);
            $pps->bindValue(2, $prc['prc_id']);
            $pps->execute();
            return $pps->rowCount() > 0;
        } catch (PDOException $th) {
            //throw $th;
        } finally {
            $pps = null;
            $con = null;
        }
    }

    public static function mdlMostrarPaquetes()
    {
        try {
            //code...
            $sql = "SELECT * FROM tbl_paquetes_pqt WHERE pqt_estado_borrado = 1 ORDER BY pqt_id DESC ";
            $con = Conexion::conectar();
            $pps = $con->prepare($sql);
            $pps->execute();
            return $pps->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            //throw $th;
        } finally {
            $pps = null;
            $con = null;
        }
    }
    public static function mdlMostrarPreciosByPaquete($prc_id_pqt)
    {
        try {
            //code...
            $sql = "SELECT prc.*, srv.srv_nombre, pqt.* 
            FROM tbl_precios_prc prc
            JOIN tbl_servicios_srv srv ON prc.prc_id_srv = srv.srv_id
            JOIN tbl_paquetes_pqt pqt ON prc.prc_id_pqt = pqt.pqt_id
            WHERE prc.prc_id_pqt = ?";
            $con = Conexion::conectar();
            $pps = $con->prepare($sql);
            $pps->bindValue(1, $prc_id_pqt);
            $pps->execute();
            return $pps->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            //throw $th;
        } finally {
            $pps = null;
            $con = null;
        }
    }

    public static function mdlEliminarPreciosByPaquete($prc_id_pqt)
    {
        try {
            //code...
            $sql = "DELETE FROM tbl_precios_prc WHERE prc_id_pqt = ?";
            $con = Conexion::conectar();
            $pps = $con->prepare($sql);
            $pps->bindValue(1, $prc_id_pqt);
            $pps->execute();
            return $pps->rowCount() > 0;
        } catch (PDOException $th) {
            //throw $th;
        } finally {
            $pps = null;
            $con = null;
        }
    }
    public static function mdlEliminarPreciosByServicio($prc_id_srv)
    {
        try {
            //code...
            $sql = "DELETE FROM tbl_precios_prc WHERE prc_id_srv = ?";
            $con = Conexion::conectar();
            $pps = $con->prepare($sql);
            $pps->bindValue(1, $prc_id_srv);
            $pps->execute();
            return $pps->rowCount() > 0;
        } catch (PDOException $th) {
            //throw $th;
        } finally {
            $pps = null;
            $con = null;
        }
    }
    public static function mdlEliminarPaquete($pqt_id)
    {
        try {
            //code...
            $sql = "UPDATE tbl_paquetes_pqt SET pqt_estado_borrado = 0 WHERE pqt_id = ?";
            $con = Conexion::conectar();
            $pps = $con->prepare($sql);
            $pps->bindValue(1, $pqt_id);
            $pps->execute();
            return $pps->rowCount() > 0;
        } catch (PDOException $th) {
            //throw $th;
        } finally {
            $pps = null;
            $con = null;
        }
    }
    public static function mdlEliminarServicio($srv_id)
    {
        try {
            //code...
            $sql = "UPDATE tbl_servicios_srv SET srv_estado_borrado = 0 WHERE srv_id = ?";
            $con = Conexion::conectar();
            $pps = $con->prepare($sql);
            $pps->bindValue(1, $srv_id);
            $pps->execute();
            return $pps->rowCount() > 0;
        } catch (PDOException $th) {
            //throw $th;
        } finally {
            $pps = null;
            $con = null;
        }
    }
    public static function mdlGuardarClientes($clt)
    {
        try {
            //code...
            $sql = "INSERT INTO tbl_clientes_clt (clt_nombre, clt_wpp, clt_gpo_wpp, clt_nombre_gpo, clt_tipo_corte, clt_paquete) VALUES (?,?,?,?,?,?)";
            $con = Conexion::conectar();
            $pps = $con->prepare($sql);
            $pps->bindValue(1, $clt['clt_nombre']);
            $pps->bindValue(2, $clt['clt_wpp']);
            $pps->bindValue(3, $clt['clt_gpo_wpp']);
            $pps->bindValue(4, $clt['clt_nombre_gpo']);
            $pps->bindValue(5, $clt['clt_tipo_corte']);
            $pps->bindValue(6, $clt['clt_paquete']);
            $pps->execute();
            return $pps->rowCount() > 0;
        } catch (PDOException $th) {
            //throw $th;
        } finally {
            $pps = null;
            $con = null;
        }
    }

    public static function mdlAcutualizarClientes($clt)
    {
        try {
            //code...
            $sql = "UPDATE tbl_clientes_clt SET clt_nombre = ?, clt_wpp = ?, clt_gpo_wpp = ?, clt_nombre_gpo = ?, clt_tipo_corte = ?, clt_paquete = ? WHERE clt_id = ?";
            $con = Conexion::conectar();
            $pps = $con->prepare($sql);
            $pps->bindValue(1, $clt['clt_nombre']);
            $pps->bindValue(2, $clt['clt_wpp']);
            $pps->bindValue(3, $clt['clt_gpo_wpp']);
            $pps->bindValue(4, $clt['clt_nombre_gpo']);
            $pps->bindValue(5, $clt['clt_tipo_corte']);
            $pps->bindValue(6, $clt['clt_paquete']);
            $pps->bindValue(7, $clt['clt_id']);
            $pps->execute();
            return $pps->rowCount() > 0;
        } catch (PDOException $th) {
            //throw $th;
        } finally {
            $pps = null;
            $con = null;
        }
    }

    public static function mdlMostrarClienteById($clt_id)
    {
        try {
            //code...
            $sql = "SELECT * FROM tbl_clientes_clt WHERE clt_id = ?";
            $con = Conexion::conectar();
            $pps = $con->prepare($sql);
            $pps->bindValue(1, $clt_id);
            $pps->execute();
            return $pps->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            //throw $th;
        } finally {
            $pps = null;
            $con = null;
        }
    }
    public static function mdlMostrarClienteByGrupo($clt_gpo_wpp)
    {
        try {
            //code...
            $sql = "SELECT * FROM tbl_clientes_clt WHERE clt_gpo_wpp = ?";
            $con = Conexion::conectar();
            $pps = $con->prepare($sql);
            $pps->bindValue(1, $clt_gpo_wpp);
            $pps->execute();
            return $pps->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            //throw $th;
        } finally {
            $pps = null;
            $con = null;
        }
    }
    public static function mdlMostrarClienteByTipo($clt_tipo_corte)
    {
        try {
            //code...
            $sql = "SELECT * FROM tbl_clientes_clt WHERE clt_tipo_corte = ?";
            $con = Conexion::conectar();
            $pps = $con->prepare($sql);
            $pps->bindValue(1, $clt_tipo_corte);
            $pps->execute();
            return $pps->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            //throw $th;
        } finally {
            $pps = null;
            $con = null;
        }
    }
    public static function mdlMostrarClientes()
    {
        try {
            //code...
            $sql = "SELECT * FROM tbl_clientes_clt ORDER BY clt_id DESC";
            $con = Conexion::conectar();
            $pps = $con->prepare($sql);
            $pps->execute();
            return $pps->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            //throw $th;
        } finally {
            $pps = null;
            $con = null;
        }
    }
    public static function mdlEliminarCliente($clt_id)
    {
        try {
            //code...
            $sql = "DELETE FROM tbl_clientes_clt WHERE clt_id = ?";
            $con = Conexion::conectar();
            $pps = $con->prepare($sql);
            $pps->bindValue(1, $clt_id);
            $pps->execute();
            return $pps->rowCount() > 0;
        } catch (PDOException $th) {
            //throw $th;
        } finally {
            $pps = null;
            $con = null;
        }
    }
}
