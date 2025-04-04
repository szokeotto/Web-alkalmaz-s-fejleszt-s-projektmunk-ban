<?php
//Erőforrások
require "../config/connect.php";/** @var $link mysqli */
require "../config/settings.php";
require "../config/functions.php";//saját eljárások
//var_dump($_SESSION);
session_start();//Munkafolyamat indítása

//A $_SESSION szuperglobális tömb
$loginMessage = '<h1>Kérjük írja be a bejelentkezési adatokat:</h1>';

if (!empty($_POST)) {
    if (login()) {
        //irány az admin/index
        header('location:index.php');
        exit;
    } else {//nem volt ilyen
        $loginMessage = '<span class="error">Nincs ilyen email/jelszó páros!</span>';
    }
}
?><!doctype html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Belépés - Webprogramozás vizsga feladat</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        form {
            max-width: 600px;
            margin: 1em auto;
        }

        label {
            display: flex;
            flex-direction: column;
        }

        .error {
            font-size: 0.9em;
            color: red;
            font-style: italic;
        }
    </style>
</head>
<body>
<form method="post">
    <?php echo $loginMessage; ?>
    <label>
        <span>Email</span>
        <input type="text" name="email" placeholder="email@cim.hu"
               value="<?php echo filter_input(INPUT_POST, 'email'); ?>">
    </label>
    <label>
        <span>Jelszó</span>
        <input type="password" name="password" placeholder="******" value="">
    </label>
    <button>Belépés</button>
    <a href="registration.php">Regisztráció</a>
</form>
</body>
</html>
