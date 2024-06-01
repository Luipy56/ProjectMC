<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MC Server Status</title>
    <link rel="icon" href="Ojos.ico" type="image/x-icon">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
            <?php
                //Apartado json
                $jsonContent = file_get_contents('/home/Luipy/mcServers/descriptions.json');
                $descriptions = json_decode($jsonContent, true);

                require 'MinecraftQuery.php';
                require 'MinecraftQueryException.php';

                use xPaw\MinecraftQuery;
                use xPaw\MinecraftQueryException;

                $ip = '127.0.0.1';
                $port = 25565;

                $Query = new MinecraftQuery();

                $Query->Connect($ip, $port);
                $info = $Query->GetInfo();
                $players = $Query->GetPlayers(); 

                $version = (isset($info['Version']) ? htmlspecialchars($info['Version']) : 'Desconocida');

            ?>
    <div class="container">
        <h1>Estado del servidor de Minecraft del Team Maza</h1>
        <div class="server-status-borde">
        <div class="server-status">
        <?php
            function parseMinecraftColors($text) {
                $colors = [
                    '0' => '#000000', // Black
                    '1' => '#0000AA', // Dark Blue
                    '2' => '#00AA00', // Dark Green
                    '3' => '#00AAAA', // Dark Aqua
                    '4' => '#AA0000', // Dark Red
                    '5' => '#AA00AA', // Dark Purple
                    '6' => '#FFAA00', // Gold
                    '7' => '#AAAAAA', // Gray
                    '8' => '#555555', // Dark Gray
                    '9' => '#5555FF', // Blue
                    'a' => '#00FF00', // Green
                    'b' => '#55FFFF', // Aqua
                    'c' => '#FF5555', // Red
                    'd' => '#FF55FF', // Light Purple
                    'e' => '#FFFF55', // Yellow
                    'f' => '#FFFFFF', // White
                    'r' => '#DCDCDC'  // Reset to default color
                ];

                // Convert ?<code> to <span> tags
                $text = preg_replace_callback('/\?([0-9a-fr])/', function ($matches) use ($colors) {
                    $color = $matches[1];
                    if ($color == 'r') {
                        return "<br>"; // Inserta un salto de línea cuando se detecta la letra "r"
                    }
                    if (isset($colors[$color])) {
                        return '<span style="color:' . $colors[$color] . '">';
                    }
                    return $matches[0];
                }, $text);

                // Close all opened spans
                $text .= str_repeat('</span>', substr_count($text, '<span'));

                return $text;
            }
            function reemplazarSigno($cadena) {
                return str_replace("?", "¡", $cadena);
            }

            try {
                if (isset($_GET['tryError'])) {
                    throw new MinecraftQueryException();
                }                               

                echo '<div class="server-info">';
                echo '<p>Estado: <span style="color:#0f0;"><b>Encendido</b></span></p>';
                echo '<p>Descripción:<br><div style="margin-left: 10px;"><b> ' . (isset($info['HostName']) ? reemplazarSigno(parseMinecraftColors(htmlspecialchars($info['HostName']))) : 'Desconocido') . '</b></div></p>';
                
                    //Mostrar contenido del array para depuración
                    //echo '<pre>' . print_r($info, true) . '</pre>';

                echo '<p>Versión actual:<span style="color:#0f0;"><b> ' . $version . '</b></span></p>';

                echo
                '<div>
                    <span class="toggle-button" onclick="toggleDescription()">Descripción extendida</span>
                    <div id="extendedDescription" class="extended-description">
                        <p style="text-align:justify;">'; 
                            if (isset($descriptions[$version])) {
                                echo $descriptions[$version];
                            } else {
                                echo "Descripción no encontrada para la versión especificada.";
                            }
                echo    '</p>
                    </div>
                </div><br>';

                if ($players !== false && count($players) > 0) {
                    echo '<p>Jugadores en línea: ' . (isset($info['Players']) ? $info['Players'] : '?') . '/' . (isset($info['MaxPlayers']) ? htmlspecialchars($info['MaxPlayers']) : '?') . '</p>';

                    echo '<h2>Jugadores conectados:</h2><ul>';

                    foreach ($players as $player) {
                        echo '<li>' . htmlspecialchars($player) . '</li>';
                    }
                    echo '</ul>';
                } else {
                    echo '<p style="text-align:center; margin: 0; padding: 0;">Ningún jugador conectado en este momento.</p>';
                }

            } catch (MinecraftQueryException $e) {
                echo '<p>Estado: <span style="color:red;"><b>Apagado</b></span></p>';
                echo '<p>Lo sentimos, pero parece que ninguno de los servidores del Team Maza está actualmente abierto.</p>';
            }
                //echo '</div>';
        ?>
        </div>
        </div>
        <div style='border-top: 1px solid #444;'>
        <p>Recuerda que puedes acceder al servidor utilizando la IP <b>mc.ldeluipy.es</b></p>
        <p style="text-align:right;" ><a href="https://sites.google.com/view/luipyofficialweb/inicio" target="_blank">Más sobre Luipy</a></p>
    </div>
<script>
    function toggleDescription() {
        var description = document.getElementById("extendedDescription");
        if (description.style.display === "none" || description.style.display === "") {
            description.style.display = "block";
        } else {
            description.style.display = "none";
        }
    }
</script>
</body>
</html>
