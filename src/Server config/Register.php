<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Accept");
header("Access-Control-Allow-Methods: POST, PUT, PATCH, GET, DELETE, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Origin, X-Api-Key, X-Requested-With, Content-Type, Accept, Authorization");

$data = json_decode(file_get_contents("php://input"));

if(isset($data->username) && isset($data->password) && isset($data->email)) {
  $username = $data->username;
  $password = $data->password;
  $email = $data->email;

  // Connect to the database
  session_start();
  require_once "Server.php";

  try{
    $check_data = $pdo->prepare("SELECT * FROM login WHERE email = :email");
    $check_data->bindParam(":email", $email);
    $check_data->execute();
    $row = $check_data->fetch(PDO::FETCH_ASSOC);
  
    $check_data2 = $pdo->prepare("SELECT * FROM login WHERE username = :username");
    $check_data2->bindParam(":username", $username);
    $check_data2->execute();
    $row2 = $check_data2->fetch(PDO::FETCH_ASSOC);
  
    if($row && $row['email'] == $email){
      echo json_encode(array("message" => "Email already exists"));
    }else if($row2 && $row2['username'] == $username){
      echo json_encode(array("message" => "Username already exists"));
    }else{
      $passwordHash = password_hash($password, PASSWORD_DEFAULT);
      $stmt = $pdo->prepare("INSERT INTO login (Username, Password, Email) VALUES(:username , :password , :email)");
      $stmt->bindParam(":username", $username);

      //$stmt->bindParam(":password", $passwordHash);
      /*structure ของ password ใน db มีขนาดแค่ varchar(20) ซึ่งpasswordHashขนาดมันเยอะเกินไป ถ้าจะเก็บเป็นhashแล้วถึงมาแก้hashเช็ครหัสทีหลัง ให้เปลี่ยนประเภทการเก็บข้อมูลเป็น TEXT แทน*/

      $stmt->bindParam(":password", $password);
      $stmt->bindParam(":email", $email);
      $stmt->execute();
  
      echo 201;
    }
  }catch(PDOException $e){
    echo json_encode(array("message" => "Failed to register user" ,"error"=> $e));
  }
} else {
  $response = array("success" => false, "message" => "Missing username or password or email in request body");
  echo json_encode($response);
}
// Close the database connection
$pdo = null;
?>
