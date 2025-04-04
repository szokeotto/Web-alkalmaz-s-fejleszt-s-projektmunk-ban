<?php
require "../config/connect.php";
/** @var $link mysqli */
require '../config/functions.php';
require "../config/settings.php";
session_start();

if (!isset($link)) {
    header('location:index.php');
    exit;
}
//erőforrások
$action = filter_input(INPUT_GET, 'action') ?? 'read'; //create,read,update,delete
$userid = filter_input(INPUT_GET, 'userid', FILTER_VALIDATE_INT) ?? false;//azonosító updatehez és deletehez
$output = '';
/** @var $page int - a modul azonosítója ami az urlekbe kell az index fileban van az erőforrások között onnan veszük át */
/** @var $baseURL string - a indexből */
//űrlap feldolgozása
if (!empty($_POST)) {

    //echo '<pre>' . var_export($_POST, true) . '</pre>';
    $errors = [];//hibák gyűjtő halmaza

    //név legyen min 3 karakter
    $name = trim(strip_tags(filter_input(INPUT_POST, 'name')));

    if (mb_strlen($name, 'utf-8') < 3) {
        $errors['name'] = '<span class="error">Hibás adat! Minimum 3 karakter!</span>';
    }

    //email legyen email
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    if (!$email) {
        $errors['email'] = '<span class="error">Érvénytelen adat!</span>';
    } else {
        //foglalt-e?
        $qry = "SELECT userid FROM users WHERE email = '$email' LIMIT 1";
        $result = mysqli_query($link, $qry) or die(mysqli_error($link));
        $row = mysqli_fetch_row($result);
        //var_dump($row,$id);
        if ($row !== null && (int)$row[0] !== $userid) {
            $errors['email'] = '<span class="error">Már foglalt email!</span>';
        }

    }

    //Jelszavak ellenőrzése min 6 karakter és egyezzenek
    $password = filter_input(INPUT_POST, 'password');
    $repassword = filter_input(INPUT_POST, 'repassword');
    if ($action === 'create' || $password !== '') {//jelszó ellenőrzés új felvitelkor és update esetében ha a jelszó 1 mezőben akár 1 karakter szerepel
        if (mb_strlen($password, 'utf-8') < 6) {
            $errors['password'] = '<span class="error">Legalább 6 karakter!</span>';
        } elseif ($password !== $repassword) {
            $errors['repassword'] = '<span class="error">A jelszavak nem egyeztek!</span>';
        } else {
            //nincs hiba a jelszó mezőn
            //jelszó elkódolása
            /* $secret_key = 'S3cR3T_k3Y!';
             for($i=1;$i<20;$i++){
                 $password = md5($password);
             }*/
            $password = password_hash($password, PASSWORD_BCRYPT);
        }
    }

    /** @todo: status checkbox az űrlapra */
    $status = filter_input(INPUT_POST, 'status') ?? 0;

    if (empty($errors)) {
        //adatok rendberakása
        $data = [
            'name' => $name,
            'email' => $email,
            'status' => $status,
            'password' => $password,
            'time_created' => date('Y-m-d H:i:s')
        ];

        $qry = getInsertQuery('users', $data);
        mysqli_query($link, $qry) or die(mysqli_error($link));
        $insertedId = $userid ?? mysqli_insert_userid($link);//amit az auto increment adott beillesztéskor, vagy a módosított id-ja
        //átirányítás
        header('location:../index.php');
        exit();

    }

}

$formTitle ??= 'Regisztráció';
$rowUser ??= [];
//php űrlap összeállítása ($form)
$form = '<h1 align="center">' . $formTitle . '</h1>
                <form method="post">';//űrlap nyitása
//név mező hozzáfüzése az űrlaphoz
$form .= '<label>
            <span>Név<sup>*</sup></span>
            <input type="text" name="name" placeholder="Nameless One" value="' .getInputValue('name',$rowUser).
    '">';


$form .= $errors['name'] ?? '';//hiba hozzáfűzése ha van

$form .= '</label>';

//Email mező hozzáfüzése az űrlaphoz
$form .= '<label>
            <span>Email<sup>*</sup></span>
            <input type="text" name="email" placeholder="email@cim.hu" value="' . getInputValue('email', $rowUser) . '">'
    . ($errors['email'] ?? '') //hiba ha van
    . '</label>';
//jelszó 1
$form .= '<label>
            <span>Jelszó megadása<sup>*</sup></span>
            <input type="password" name="password" placeholder="******" value="">'
    . ($errors['password'] ?? '') //hiba ha van
    . '</label>';

//jelszó 2
$form .= '<label>
            <span>Jelszó megadása újra<sup>*</sup></span>
            <input type="password" name="repassword" placeholder="******" value="">'
    . ($errors['repassword'] ?? '') //hiba ha van
    . '</label>';
//status
//ha kipipálva küldöd el a formot,de máshol hiba van akkor maradjon ugy (a value legyen 1, checked)
$checked = '';
if (filter_input(INPUT_POST, 'status')) {
    $checked = 'checked';
}elseif( isset($rowUser['status']) && $rowUser['status'] === '1' && empty($_POST)){
    $checked = 'checked';
}
/*
 short if-fel: $checked = filter_input(INPUT_POST, 'status') ? 'checked' : '';
 */
$form .= '<label>
            <span><input type="checkbox" name="status" value="1" ' . $checked . '> Aktív?</span>
          </label>';

$form .= '<button>' . ($buttonTitle ?? 'Regisztráció') . '</button>
        </form>';
$output .= $form;
echo $output;

?>
<!doctype html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Regisztráció - Webprogramozás vizsga feladat</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        form {
            max-width: 400px;
            margin: 1em auto;
        }

        label {
            display: flex;
            flex-direction: column;
        }

        .error {
            font-size: 0.7em;
            color: red;
            font-style: italic;
        }
    </style>
</head>
<body>

</body>
</html>
