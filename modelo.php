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
}
