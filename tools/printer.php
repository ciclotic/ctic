<?php

require_once __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Yaml\Yaml;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\Printer;

$config = Yaml::parse(file_get_contents(__DIR__.'/config.yml'));

$printers = array();
$connectors = array();
$lastTicket = array();
foreach ($config['printer'] as $printer) {
    $connectors[$printer['id']] = new NetworkPrintConnector($printer['ip'], $printer['port']);
    $printers[$printer['id']] = new Printer($connectors[$printer['id']]);
    $lastTicket[$printer['id']] = array();
}

$mysqli = new mysqli($config['db']['host'], $config['db']['user'], $config['db']['password'], $config['db']['dbname']);
if ($mysqli->connect_errno) {
    echo "Lo sentimos, la base de datos está experimentando problemas. \n";
    echo "Error: Fallo al conectarse a MySQL debido a: \n";
    echo "Errno: " . $mysqli->connect_errno . "\n";
    echo "Error: " . $mysqli->connect_error . "\n";

    exit;
}

$sql = "SELECT id, id_tiquet, id_linea, id_client, hora, quantitat, descripcio, formato, observacio, terminal FROM taules_temp_1 WHERE observacio <> 'HECHO' ORDER BY id DESC";
if (!$result = $mysqli->query($sql)) {
    echo "Lo sentimos, la base de datos está experimentando problemas.\n";

    echo "Error: La ejecución de la consulta falló debido a: \n";
    echo "Query: " . $sql . "\n";
    echo "Errno: " . $mysqli->errno . "\n";
    echo "Error: " . $mysqli->error . "\n";
    exit;
}

if ($result->num_rows === 0) {
    echo "Lo sentimos. Inténtelo de nuevo.\n";
    exit;
}

while ($lineaTicket = $result->fetch_assoc()) {
    $terminalsTicket = str_split($lineaTicket['terminal']);
    foreach ($terminalsTicket as $terminalTicket) {
    if (empty($printers[$terminalTicket])) {
        echo "Error: No se encuentra el terminal asociado al ticket \n";
        continue;
    }
    $printer = $printers[$terminalTicket];
    $sqlCustomer = "SELECT codi, nombre FROM clientes WHERE codi = " . $lineaTicket['id_client'] . " ORDER BY codi DESC LIMIT 1";
    if (!$resultCustomer = $mysqli->query($sqlCustomer)) {
        echo "Lo sentimos, la base de datos está experimentando problemas.\n";

        echo "Error: La ejecución de la consulta falló debido a: \n";
        echo "Query: " . $sqlCustomer . "\n";
        echo "Errno: " . $mysqli->errno . "\n";
        echo "Error: " . $mysqli->error . "\n";
        exit;
    }
    if ($resultCustomer->num_rows != 1) {
        echo "Error: No se encuentra el cliente asociado al ticket \n";
        continue;
    }
    $customer = $resultCustomer->fetch_assoc();

    if (empty($lastTicket[$terminalTicket]) || $lastTicket[$terminalTicket]['id_tiquet'] != $lineaTicket['id_tiquet']) {
        $printer->setTextSize(2, 2);
        $printer->text($customer['nombre'] . "\n");
        $printer->setTextSize(1, 1);
        $printer->text($lineaTicket['hora'] . "\n");
    }
    $printer->text($lineaTicket['quantitat'] . " " . $lineaTicket['descripcio'] . "\n");
    if (!empty($lineaTicket['formato'])) {
        $printer->text($lineaTicket['formato'] . "\n");
    }
    if (!empty($lineaTicket['observacio'])) {
        $printer->text($lineaTicket['observacio'] . "\n");
    }

    $resultCustomer->close();

    $sql2 = "SELECT id, id_tiquet, id_linea, quantitat, complemento, observacio FROM taules_temp_2 WHERE id_tiquet = " . $lineaTicket['id_tiquet'] . " AND id_linea = " . $lineaTicket['id_linea'] . " AND observacio <> 'HECHO' ORDER BY id DESC";
    if (!$result2 = $mysqli->query($sql2)) {
        echo "Lo sentimos, la base de datos está experimentando problemas.\n";

        echo "Error: La ejecución de la consulta falló debido a: \n";
        echo "Query: " . $sql2 . "\n";
        echo "Errno: " . $mysqli->errno . "\n";
        echo "Error: " . $mysqli->error . "\n";
        exit;
    }

    if ($result2->num_rows != 0) {
        while ($lineaComplementTicket = $result2->fetch_assoc()) {
            $printer->text($lineaComplementTicket['quantitat'] . ' ' . $lineaComplementTicket['complemento'] . "\n");
            if (!empty($lineaComplementTicket['observacio'])) {
                $printer->text($lineaComplementTicket['observacio'] . "\n");
            }

            $sqlUpdate = "UPDATE taules_temp_2 SET observacio = 'HECHO' WHERE id = " . $lineaComplementTicket['id'] . " LIMIT 1";
            if (!$resultUpdate = $mysqli->query($sqlUpdate)) {
                echo "Lo sentimos, la base de datos está experimentando problemas.\n";

                echo "Error: La ejecución de la modificación falló debido a: \n";
                echo "Query: " . $sqlUpdate . "\n";
                echo "Errno: " . $mysqli->errno . "\n";
                echo "Error: " . $mysqli->error . "\n";
                exit;
            }
        }
    }
    $result2->free();

    if (!empty($lastTicket[$terminalTicket]) && $lastTicket[$terminalTicket]['id_tiquet'] != $lineaTicket['id_tiquet']) {
        $printer->cut();
        $printer->text("\n");
    }

    $sqlUpdate = "UPDATE taules_temp_1 SET observacio = 'HECHO' WHERE id = " . $lineaTicket['id'] . " LIMIT 1";
    if (!$resultUpdate = $mysqli->query($sqlUpdate)) {
        echo "Lo sentimos, la base de datos está experimentando problemas.\n";

        echo "Error: La ejecución de la modificación falló debido a: \n";
        echo "Query: " . $sqlUpdate . "\n";
        echo "Errno: " . $mysqli->errno . "\n";
        echo "Error: " . $mysqli->error . "\n";
        exit;
    }

    $lastTicket[$terminalTicket] = $lineaTicket;
    }
}

if (!empty($lastTicket)) {
    $printer->cut();
}

foreach ($printers as $printer) {
    $printer->close();
}

$result->free();
$mysqli->close();
