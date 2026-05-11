<?php

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 Not Found</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 50px;
        }

        h1 {
            font-size: 50px;
        }

        p {
            font-size: 20px;
        }

        button {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body class="fadeIn">
    <!-- inicio container -->
    <div class="container shadow pb-2">
        <h1>404 Not Found</h1>
        <p>La página que estás buscando no existe.</p>
        <button onclick="window.history.back();">Volver</button>
    </div>
</body>

</html>