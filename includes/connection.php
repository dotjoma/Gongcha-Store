<?php
    $host = "localhost";
    $database = "db_gongcha";
    $user = "root";
    $password = "";
    $dsn = "mysql:host={$host};dbname={$database};";

    try
    {
        $conn = new PDO($dsn, $user, $password);
    }
    catch (PDOException $th)
    {
        echo $th->getMessage();
    }
?>