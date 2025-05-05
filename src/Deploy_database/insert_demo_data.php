<?php
/**
 * Script de carga automática de datos demo en la base de datos desde archivos CSV.
 * 
 * DESCRIPCIÓN:
 * Este script recorre todos los archivos `.csv` ubicados en la carpeta:
 *     src/Deploy_database/data/
 * 
 * Cada archivo debe tener como nombre el nombre exacto de la tabla en la base de datos (por ejemplo: `Equipo.csv` corresponde a la tabla `Equipo`).
 * 
 * Los encabezados de cada archivo CSV deben coincidir con los nombres de los campos de la tabla correspondiente.
 * Cada fila del archivo CSV representa un registro que será insertado.
 * 
 * REGLAS:
 * - El primer renglón debe contener los nombres de las columnas.
 * - El orden de los campos debe coincidir con el orden de los valores por fila.
 * - Si un campo se llama `password`, será automáticamente hasheado antes de ser insertado.
 * 
 * EJEMPLO DE ESTRUCTURA DE ARCHIVO:
 * Archivo: src/Deploy_database/data/Equipo.csv
 * 
 * Contenido:
 * email,password,nombre_equipo,acronimo,descripcion_lema,telefono,id_tipo_equipo
 * ejemplo@mail.com,123456,Mi Equipo,EQP,"Lema demo",1123456789,1
 * 
 * USO:
 * Ejecutar mediante Makefile con:
 *     make insert_demo_data
 * 
 * Recordar de contar con el archivo .env en la raiz del repositorio
 */

 require_once __DIR__ . '/../../vendor/autoload.php';
 require_once __DIR__ . '/../Core/Database/Database.php';
 require_once __DIR__ . '/../Core/Database/ConnectionBuilder.php';
 
 $config = require __DIR__ . '/../../src/Config/config.php';
 
// Inicializamos la base de datos
 use Paw\Core\Database\Database;
 echo "DB_PASSWORD=[" . getenv('DB_PASSWORD') . "]\n";
 Database::initialize($config['database'], new \Monolog\Logger('db'));
 $pdo = Database::getConnection();
 
 $dataDir = __DIR__ . '/data';
 
 $orderedTables = [
     'Equipo',
     'Comentario',
     'Partido',
     'Desafio',
     'FormularioPartido',
     'ResultadoPartido'
 ];
 
 foreach ($orderedTables as $tableName) {
     $csvPath = "$dataDir/$tableName.csv";
 
     if (!file_exists($csvPath)) {
         echo "Archivo no encontrado: $csvPath. Se salta...\n";
         continue;
     }
 
     echo "Insertando datos en la tabla '$tableName' desde '$csvPath'...\n";
 
     $handle = fopen($csvPath, 'r');
     if (!$handle) {
         echo "No se pudo abrir el archivo: $csvPath\n";
         continue;
     }
 
     $headers = fgetcsv($handle);
     if (!$headers) {
         echo "Archivo CSV vacío o malformado: $csvPath\n";
         fclose($handle);
         continue;
     }
 
     $rowCount = 0;
     while (($data = fgetcsv($handle)) !== false) {
         $row = array_combine($headers, $data);
 
         if (isset($row['password'])) {
             $row['password'] = password_hash($row['password'], PASSWORD_DEFAULT);
         }
 
         $columns = [];
         $placeholders = [];
         $params = [];
 
         foreach ($headers as $col) {
             $columns[] = $col;
 
             if (str_starts_with($row[$col], 'ST_GeomFromText(')) {
                 $placeholders[] = $row[$col];
             } else {
                 $placeholders[] = ":$col";
                 $params[$col] = $row[$col];
             }
         }
 
         $insertQuery = sprintf(
             "INSERT INTO %s (%s) VALUES (%s)",
             $tableName,
             implode(', ', $columns),
             implode(', ', $placeholders)
         );
 
         try {
             $stmt = $pdo->prepare($insertQuery);
             $stmt->execute($params);
             $rowCount++;
         } catch (PDOException $e) {
             echo "Error al insertar fila en $tableName: " . $e->getMessage() . "\n";
         }
     }
 
     fclose($handle);
     echo "Insertadas $rowCount filas en '$tableName'.\n";
 }
 
 echo "Carga de datos demo completada.\n";