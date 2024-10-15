<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo $title; ?></title>
    <style>
        /* Estilos CSS para mejorar la presentación */
        body {
            font-family: Arial, sans-serif;
            margin: 10px; /* Márgenes reducidos */
            color: #333;
            background-color: #f4f4f4;
            font-size: 10px; /* Tamaño de fuente más pequeño */
        }
        .container {
            max-width: 800px;
            margin: auto;
            padding: 10px; /* Padding reducido */
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #4CAF50;
            margin-bottom: 10px; /* Margen inferior reducido */
            font-size: 18px; /* Tamaño de fuente más pequeño */
        }
        h3, h4 {
            color: #333;
            margin: 5px 0;
            text-align: center;
            font-size: 12px; /* Tamaño de fuente más pequeño */
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px; /* Margen superior reducido */
            table-layout: fixed; /* Forzar un diseño fijo */
        }
        th, td {
            border: 1px solid #4CAF50;
            padding: 8px; /* Padding reducido */
            text-align: left;
            word-wrap: break-word; /* Ajustar texto largo */
            font-size: 10px; /* Tamaño de fuente más pequeño */
        }
        th {
            background-color: #4CAF50;
            color: #fff;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #e9e9e9;
        }
        .resultado-correcto {
            color: #4CAF50; /* Verde para correcto */
            font-weight: bold; /* Negrita para resaltar */
            font-size: 12px; /* Tamaño de fuente más pequeño */
        }
        .resultado-incorrecto {
            color: #f44336; /* Rojo para incorrecto */
            font-weight: bold; /* Negrita para resaltar */
            font-size: 12px; /* Tamaño de fuente más pequeño */
        }
        .footer {
            text-align: center;
            margin-top: 10px; /* Margen superior reducido */
            color: #777;
            font-size: 8px; /* Tamaño de fuente más pequeño */
        }
        .logo {
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        
        <h1><?php echo $title; ?></h1>
        <h3>Nombre del Estudiante: <?php echo $resultados[0]->nombre; ?></h3>
        <h4>Examen: <?php echo $resultados[0]->nombre_examen; ?></h4>
        
        <table>
            <thead>
                <tr>
                    <th>Enunciado</th>
                    <th>Tipo de Pregunta</th>
                    <th>Respuesta Elegida</th>
                    <th>Respuesta Correcta</th>
                    <th>Resultado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($resultados as $index => $resultado): ?>
                    <tr>
                        <td><?php echo $resultado->enunciado_pregunta; ?></td>
                        <td><?php echo $resultado->tipo_pregunta; ?></td>
                        <td><?php echo $resultado->respuesta_elegida; ?></td>
                        <td><?php echo $resultado->respuesta_correcta; ?></td>
                        <td class="<?php echo $resultado->resultado === 'Correcto' ? 'resultado-correcto' : 'resultado-incorrecto'; ?>">
                            <?php echo $resultado->resultado; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                
            </tbody>
        </table>

        <div class="footer">
            <p>&copy; 2024 www.cityfitnessworld.com . Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>
