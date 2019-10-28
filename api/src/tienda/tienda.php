<?php 

    require_once('../config/dbClassMysql.php');
    $con = new dbClassMysql();

    header("Content-type:application/json");
    header("Access-Control-Allow-Origin: *");

    //franquicias
    $strQuery = "SELECT id_franquicia, nombre FROM franquicia";
    $qTmp = $con->db_consulta($strQuery);
    $franquicias = array();
    while( $rTmp = $con->db_fetch_object($qTmp)) {
        $franquicias[] = array(
            'presupuesto' => '',
            'id_franquicia' => $rTmp->id_franquicia,
            'nombre' => $rTmp->nombre,
            'venta' => ''
        );
    }

    //lts
    $strQuery = "SELECT id_lt, nombre FROM lt";
    $qTmp = $con->db_consulta($strQuery);
    $lts = array();
    while( $rTmp = $con->db_fetch_object($qTmp)) {
        $lts[] = array(
            'id_lt' => $rTmp->id_lt,
            'nombre' => $rTmp->nombre
        );
    }

    //costo por franquicia y lts
    $strQuery = "   SELECT id_franquicia, id_lt, 
                            SUM(costo) AS total_costo, 
                            (SUM(costo) * 0.50) AS porcentaje_costo
                    FROM costo 
                    GROUP BY id_franquicia, id_lt";
    $qTmp = $con->db_consulta($strQuery);
    $costos = array();
    while( $rTmp = $con->db_fetch_object($qTmp)) {
        $costos[] = array(
            'id_franquicia' => $rTmp->id_franquicia,
            'id_lt' => $rTmp->id_lt,
            'total_costo' => $rTmp->total_costo,
            'porcentaje_costo' => $rTmp->porcentaje_costo
        );
    }

    //presupuesto
    $strQuery = "   SELECT  a.id_franquicia,
                        IF ( b.presupuesto IS NULL , 0 ,b.presupuesto) AS presupuesto,  
                        IF ( b.presupuesto IS NULL , 0 , (b.presupuesto / 22)) AS estimado_diario
                    FROM franquicia a
                    LEFT JOIN presupuesto b ON a.id_franquicia = b.id_franquicia";
    $qTmp = $con->db_consulta($strQuery);
    $presupuestos = array();
    while( $rTmp = $con->db_fetch_object($qTmp)) {
        $presupuestos[] = array(
            'id_franquicia' => $rTmp->id_franquicia,
            'presupuesto' => $rTmp->presupuesto,
            'estimado_diario' => $rTmp->estimado_diario
        );
    }

    //recor de facturacion por franquicia
    $strQuery = "   SELECT a.id_franquicia,
                        IF ( b.recor_facturacion IS NULL, 0 , b.recor_facturacion ) AS record_facturacion
                    FROM franquicia a 
                    LEFT JOIN recor_facturacion b ON a.id_franquicia = b.id_franquicia";
    $qTmp = $con->db_consulta($strQuery);
    $record_facturacion = array();
    while( $rTmp = $con->db_fetch_object($qTmp)) {
        $record_facturacion[] = array(
            'id_franquicia' => $rTmp->id_franquicia,
            'record_facturacion' => $rTmp->record_facturacion
        );
    }

    //recor de depositos por franquicia
    $strQuery = "   SELECT a.id_franquicia,
                        IF ( b.record_deposito IS NULL, 0 , b.record_deposito ) AS record_deposito
                    FROM franquicia a 
                    LEFT JOIN record_deposito b ON a.id_franquicia = b.id_franquicia";
    $qTmp = $con->db_consulta($strQuery);
    $record_depositos = array();
    while( $rTmp = $con->db_fetch_object($qTmp)) {
        $record_depositos[] = array(
            'id_franquicia' => $rTmp->id_franquicia,
            'record_deposito' => $rTmp->record_deposito
        );
    }

    //recor de ventas por franquicia
    $strQuery = "   SELECT id_franquicia, id_lt, SUM(venta) AS venta
                    FROM venta
                    GROUP BY id_franquicia, id_lt";
    $qTmp = $con->db_consulta($strQuery);
    $ventas = array();
    while( $rTmp = $con->db_fetch_object($qTmp)) {
        $ventas[] = array(
            'id_franquicia' => $rTmp->id_franquicia,
            'id_lt' => $rTmp->id_lt,
            'venta' => $rTmp->venta
        );
    }

    $res['franquicias'] = $franquicias;
    $res['lts'] = $lts;
    $res['costos'] = $costos;
    $res['presupuestos'] = $presupuestos;
    $res['record_facturacion'] = $record_facturacion;
    $res['record_depositos'] = $record_depositos;
    $res['ventas'] = $ventas;

    print(json_encode($res));
    $con->db_close();
?>