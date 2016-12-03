<?php

session_start();
include_once('config.php');


function login($username, $password) {
    global $db;
    $statement = $db->prepare('SELECT id,password,fullName FROM users WHERE username = ? ');
    $statement->execute([$username]);

    $result = $statement->fetch(PDO::FETCH_ASSOC);
    $hashed_password = $result['password'];

    if(password_verify($password, $hashed_password)){
        $_SESSION['login-user']=$username;
        header("location:../pages/index.php");
        exit();
    }
    else {
        echo "<script>alert('Wrong Password')</script>";
    }
}

function signUp($username,$fullname,$date,$type,$password){
    global $db;
    $photo = 'photo0.png';
    $statement = $db->prepare('INSERT INTO users (username,fullname,birthDate,photoId,type,password) VALUES (?,?,?,?,?,?)');

    if($statement->execute([$username,$fullname,$date,$photo,$type,password_hash($password, PASSWORD_DEFAULT)])){
        $_SESSION['login-user']=$username;
        header("location:../pages/index.php");
        exit();
    }
    else echo "Impossible to regist user";
}

function getUserInfo($idUser,$info){
    if($info == 'password')
        return null;

    global $db;
    $statement = $db->prepare('SELECT * FROM users WHERE id = ? ');
    $statement->execute([$idUser]);
    return $statement->fetch()[$info];
}

function getIdByUserName($userName){
    global $db;
    $statement = $db->prepare('SELECT id FROM users WHERE username = ? ');
    $statement->execute([$userName]);
    return $statement->fetch();
}

function getUserInfoByUserName($username,$info){
    if($info == 'password')
        return null;

    global $db;
    $statement = $db->prepare('SELECT * FROM users WHERE username = ? ');
    $statement->execute([$username]);

    return $statement->fetch()[$info];
}

function updateUserProfile($username,$newUsername,$newFullName){

    global $db;
    $statement = $db->prepare('UPDATE users SET username = ?, fullName = ? WHERE username = ?');
    $statement->execute([$newUsername,$newFullName,$username]);
    return $statement->errorCode();
}

function usernameAlreadyExists($username){
    global $db;
    $statement = $db->prepare('SELECT * FROM users WHERE username = ?');
    $statement->execute([$username]);
    return $statement->fetch();
}

function uploadUserPhoto($username){
    global $db;
    $idPhoto = 'photo'.getUserInfoByUserName($username,'id').'.png';
    $statement = $db->prepare('UPDATE users SET photoId = ? WHERE username = ?');
    $statement->execute([$idPhoto,$username]);
    return $statement->errorCode();
}