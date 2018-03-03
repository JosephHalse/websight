<?php
$connectstr_dbhost = '';
$connectstr_dbname = '';
$connectstr_dbusername = '';
$connectstr_dbpassword = '';

foreach ($_SERVER as $key => $value) {
    if (strpos($key, "MYSQLCONNSTR_localdb") !== 0) {
        continue;
    }
    
    $connectstr_dbhost = preg_replace("/^.*Data Source=(.+?);.*$/", "\\1", $value);
    $connectstr_dbname = preg_replace("/^.*Database=(.+?);.*$/", "\\1", $value);
    $connectstr_dbusername = preg_replace("/^.*User Id=(.+?);.*$/", "\\1", $value);
    $connectstr_dbpassword = preg_replace("/^.*Password=(.+?)$/", "\\1", $value);
}

$conn = mysqli_connect($connectstr_dbhost, $connectstr_dbusername, $connectstr_dbpassword,$connectstr_dbname);



function get_words_from_type($conn, $type){
    $sql = "SELECT * FROM `mydb`.`words` WHERE `type` = '$type' ";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $i = 0;
        while($row = $result->fetch_assoc()) {
            $words[$i] = $row["name"];
            $i = $i + 1;
        }
    } else {
        return null;
    }
    return $words;
}
function get_words_from_letter($conn, $letter){
    $sql = "SELECT * FROM `mydb`.`words` WHERE `letter` = '$letter' ";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $i = 0;
        while($row = $result->fetch_assoc()) {
            $words[$i] = $row["name"];
            $i = $i + 1;
        }
    } else {
        return null;
    }
    return $words;
}
function get_images_from_color($conn, $color){
    $sql = "SELECT * FROM `mydb`.`pictures` WHERE `color` = '$color' ";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $i = 0;
        while($row = $result->fetch_assoc()) {
            $imges[$i] = '<img src="data:image/jpeg;base64,'.base64_encode( $row['image'] ).'"/>';
            $i = $i + 1;
        }
    } else {
        return null;
    }
    return $imges;
}
function get_images_from_name($conn, $obj_name){
    $sql = "SELECT * FROM `mydb`.`pictures` WHERE `name` = '$obj_name' ";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $i = 0;
        while($row = $result->fetch_assoc()) {
            $imges[$i] = '<img src="data:image/jpeg;base64,'.base64_encode( $row['image'] ).'"/>';
            $i = $i + 1;
        }
    } else {
        return null;
    }
    return $imges;
}
function get_random($array){
    return $array[rand(0,(count($array) - 1))];
}
function interpret_word($conn, $word)
{
    if (get_images_from_name($conn, $word) !== null) {
        return get_images_from_name($conn, $word);
    } elseif (get_words_from_letter($conn, $word) !== null) {
        for ($x = 0; $x < count(get_words_from_letter($conn, $word)); $x++) {
            $rtn[$x] = get_random(get_images_from_name($conn, get_words_from_letter($conn, $word)[$x]));
        }
        return $rtn;
    } elseif (get_words_from_type($conn, $word) !== null) {
        for ($x = 0; $x < count(get_words_from_type($conn, $word)); $x++) {
            $rtn[$x] = get_random(get_images_from_name($conn, get_words_from_type($conn, $word)[$x]));
        }
        return $rtn;
    } elseif (get_images_from_color($conn, $word) !== null) {
        return get_images_from_color($conn, $word);
    } else {
        return null;
    }
}
function int_from_word($word)
{
    if (ctype_digit($word)) {
        return (int)$word;
    } else {
        return null;
    }
}
function truncate_array($array,$n){
    for($x = 0; $x < $n; $x++){
        $rtn[$x] = get_random($array);
    }
    return $rtn;
}
function display_array($array){
    for($x = 0; $x < count($array); $x++){
        echo $array[$x];
    }
}
function interpret_string($conn,$string){
    $words = explode(" ", $string);
    if(int_from_word($words[0]) !== null){
        $n = int_from_word($words[0]);
        display_array(truncate_array(interpret_word($conn,$words[1]),$n));
    }elseif(interpret_word($conn, $words[0]) !== null){
        echo get_random(interpret_word($conn, $words[0]));
    }
}
interpret_string($conn, "4 animal");
$conn->close();
?>
