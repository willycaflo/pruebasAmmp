<?php
$hostname_conn = "localhost";
$database_conn = "intermod_maerskl";
$username_conn = "root";
$password_conn = "mysql";

function conexion(){
	global $hostname_conn, $database_conn,$username_conn,$password_conn;
	$mysqli = new MySQLI("localhost", "root", "mysql", "intermod_maerskl", 3306); 
	if (mysqli_connect_errno()) {
		printf("Error de conexion: %s\n", mysqli_connect_error());
		exit();
	}
	return $mysqli;
}

$rowsLines = array();
$rowsImages = array();
$response = array();
$SQL = "SELECT DISTINCT
			mov.id_movimiento,
		    cc.nombreempresa_contcliente,
		    cc.latitud_contcliente,
		    cc.longitud_contcliente,
		    co.nombre_corredor,
		    co.latitud_corredor,
		    co.longitud_corredor
		FROM
		    contactoclientes AS cc
		        INNER JOIN
		    movimiento AS mov ON mov.cve_cliente = cc.id_contcliente
		        INNER JOIN
		    catcorredores AS co ON co.id_corredor = mov.cve_corredor
		WHERE
		    mov.terminoMov IS NULL
		        AND (cc.latitud_contcliente IS NOT NULL
		        AND cc.latitud_contcliente <> 0)
		        AND (cc.longitud_contcliente IS NOT NULL
		        AND cc.longitud_contcliente <> 0)
		        AND (co.latitud_corredor IS NOT NULL
		        AND co.latitud_corredor <> 0)
		        AND (co.longitud_corredor IS NOT NULL
		        AND co.longitud_corredor <> 0)
		        ORDER BY cc.latitud_contcliente DESC LIMIT 0,260";

$result = conexion()->query($SQL);
if($result){
	if($result->num_rows > 0){
		$cont = 1;
		while ($row = $result->fetch_assoc()) {
				$aleatorio = rand(0, 900);
				$arco = (($aleatorio/1000)*-1);
				$rowsLines[] =  array(
					'id' => 'line'.$cont,
					#'arc' => -0.85,
					'arc' => $arco,
					'alpha' => 0.3,
					'latitudes' => array($row['latitud_corredor'],$row['latitud_contcliente']),
					'longitudes' => array($row['longitud_corredor'],$row['longitud_contcliente']),
					'color' => "#D93B6F",
				);

				$rowsImages[] = array(
					//'svgPath' => targetSVG,
					'svgPath' => "M9,0C4.029,0,0,4.029,0,9s4.029,9,9,9s9-4.029,9-9S13.971,0,9,0z M9,15.93 c-3.83,0-6.93-3.1-6.93-6.93S5.17,2.07,9,2.07s6.93,3.1,6.93,6.93S12.83,15.93,9,15.93 M12.5,9c0,1.933-1.567,3.5-3.5,3.5S5.5,10.933,5.5,9S7.067,5.5,9,5.5 S12.5,7.067,12.5,9z",
					'title' => utf8_decode($row['nombre_corredor']),
					'latitude' => $row['latitud_corredor'],
	    			'longitude'=> $row['longitud_corredor'],
	    			"color" => "#0782AB"
				);
				$rowsImages[] = array(
					//'svgPath' => targetSVG,
					'svgPath' => "M9,0C4.029,0,0,4.029,0,9s4.029,9,9,9s9-4.029,9-9S13.971,0,9,0z M9,15.93 c-3.83,0-6.93-3.1-6.93-6.93S5.17,2.07,9,2.07s6.93,3.1,6.93,6.93S12.83,15.93,9,15.93 M12.5,9c0,1.933-1.567,3.5-3.5,3.5S5.5,10.933,5.5,9S7.067,5.5,9,5.5 S12.5,7.067,12.5,9z",
					'title' => $row['nombreempresa_contcliente'],
					'latitude' => $row['latitud_contcliente'],
	    			'longitude'=> $row['longitud_contcliente'],
	    			"color" => "#0782AB"
				);

				$rowsImages[] = array(
					'svgPath' => "m2,106h28l24,30h72l-44,-133h35l80,132h98c21,0 21,34 0,34l-98,0 -80,134h-35l43,-133h-71l-24,30h-28l15,-47",
					'positionOnLine' => 0,
					'color' => '#585869',
	    			'animateAlongLine'=> true,
	    			'lineId' => 'line'.$cont,
	    			'flipDirection' => true,
	    			'loop' => true,
	    			'scale' => 0.03,
	    			'positionScale' => 1.8
				);

				$cont++;
		}
	}
}
$response['lines'] = $rowsLines;
$response['images'] = $rowsImages;
#echo "<pre>";
#echo json_encode($rowsLines);
echo json_encode($response);
#echo "</pre>";
