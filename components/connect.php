<?php
    $db_name ='mysql:host=localhost;dbname=course_db';
    $user_name = 'root';
    $user_password = '';

    $conn = new PDO($db_name, $user_name, $user_password);
    if ($conn) {
        //echo "Connected to the database successfully!";
    } else {
        echo "Connection failed!";
    }
    
    function unique_id(){
        $str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $rand=array();
        $length = strlen($str)-1;
        for($i=0; $i<10; $i++){
            $n = mt_rand(0, $length);
            $rand[] = $str[$n];
        }
        return implode($rand);
    }
?>