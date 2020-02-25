<?php


function connectWithDataBase()
{
    $connection = new mysqli('localhost', 'root','root', 'sportsevents');
    #$connection = mysqli_connect('localhost:3306','root','root','sportsevents');
    if ($connection)
    {
        echo 'Successfuly connected with localhost on port 3306';
        return $connection;
    }
    else
    {
        echo "Could not connect with localhost on port 3306\n";
        $connection->connect_error;
    }
}