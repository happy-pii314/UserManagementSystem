<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

$conn = mysqli_connect("localhost","root","","user_management");

if(!$conn){
    echo json_encode([
        "success"=>false,
        "message"=>"Database Connection Failed"
    ]);
    exit;
}

$method = $_SERVER["REQUEST_METHOD"];

switch($method){
    case "GET":
        if(isset($_GET["id"])){

            $id = intval($_GET["id"]);
            $result = mysqli_query($conn,"SELECT * FROM users WHERE id=$id");
            if(mysqli_num_rows($result)>0){
                echo json_encode(mysqli_fetch_assoc($result));
            }else{

                echo json_encode([
                    "success"=>false,
                    "message"=>"User Not Found"
                ]);
            }
        }else{
            $result=mysqli_query($conn,"SELECT * FROM users ORDER BY id DESC");
            $users=[];
            while($row=mysqli_fetch_assoc($result)){
                $users[]=$row;
            }
            echo json_encode($users);
        }
    break;

    case "POST":

        $data=json_decode(file_get_contents("php://input"),true);
        $name=mysqli_real_escape_string($conn,$data["name"]);
        $email=mysqli_real_escape_string($conn,$data["email"]);
        $age=intval($data["age"]);

        if($name=="" || $email=="" || $age==""){
            echo json_encode([
                "success"=>false,
                "message"=>"All Fields Required"
            ]);
            exit;
        }

        $check=mysqli_query($conn,"SELECT * FROM users WHERE email='$email'");
        if(mysqli_num_rows($check)>0){
            echo json_encode([
                "success"=>false,
                "message"=>"Email Already Exists"
            ]);
            exit;
        }
        $sql="INSERT INTO users(name,email,age) VALUES('$name','$email','$age')";
        if(mysqli_query($conn,$sql)){

            echo json_encode([
                "success"=>true,
                "message"=>"User Added Successfully"
            ]);

        }else{
            echo json_encode([
                "success"=>false,
                "message"=>"Insert Failed"
            ]);
        }
    break;

    case "PUT":
        parse_str($_SERVER["QUERY_STRING"],$query);
        $id=intval($query["id"]);
        $data=json_decode(file_get_contents("php://input"),true);
        $name=mysqli_real_escape_string($conn,$data["name"]);
        $email=mysqli_real_escape_string($conn,$data["email"]);
        $age=intval($data["age"]);

        $sql="UPDATE users SET
        name='$name',
        email='$email',
        age='$age'
        WHERE id=$id";

        if(mysqli_query($conn,$sql)){
            echo json_encode([
                "success"=>true,
                "message"=>"User Updated Successfully"
            ]);
        }else{
            echo json_encode([
                "success"=>false,
                "message"=>"Update Failed"
            ]);
        }
    break;

    case "DELETE":
        parse_str($_SERVER["QUERY_STRING"],$query);
        $id=intval($query["id"]);
        $sql="DELETE FROM users WHERE id=$id";

        if(mysqli_query($conn,$sql)){

            echo json_encode([
                "success"=>true,
                "message"=>"User Deleted Successfully"
            ]);

        }else{

            echo json_encode([
                "success"=>false,
                "message"=>"Delete Failed"
            ]);

        }
    break;

    default:
        echo json_encode([
            "success"=>false,
            "message"=>"Invalid Request"
        ]);
}