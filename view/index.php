<?php
//Erőforrások
require "../config/connect.php";
/** @var $link mysqli */
require "../config/settings.php";
require "../config/functions.php";
session_start();//Munkafolyamat indítása
$sid = session_id();//mf azonosító
$page = filter_input(INPUT_GET, 'p', FILTER_VALIDATE_INT) ?? 0;//oldal paraméter url-ből

//kiléptetünk ha kell
if (filter_input(INPUT_GET, 'logout') !== null) {
    logout();
}
$auth = auth();
if (!$auth) {//ha nem volt azonosítási folyamat
    header('location:login.php');
    exit;
}
if (!isset($link)) {
    header('location:index.php');
    exit;
}
$action = filter_input(INPUT_GET, 'action') ?? 'read'; //create,read,update,delete
$output = '';
/** @var $page int - a modul azonosítója ami az urlekbe kell az index fileban van az erőforrások között onnan veszük át */
/** @var $baseURL string - a indexből */
//űrlap feldolgozása
if (!empty($_POST)) {

//echo '<pre>' . var_export($_POST, true) . '</pre>';
    $errors = [];//hibák gyűjtő halmaza

//A kód mindig 24 karakter hosszú kell, legyen - betűket és számokat tartalmazhat az angol ABC betűiből
    $code = trim(strip_tags(filter_input(INPUT_POST, 'code')));

    if (mb_strlen($code, 'utf-8') != 24) {
        $errors['code'] = '<span class="error">Hibás adat! A kód mindig 24 karakter hosszú kell, legyen - betűket és számokat tartalmazhat az angol ABC betűiből</span>';
    } else {
        //foglalt-e?
        $qry = "SELECT userid FROM codes WHERE code = '$code' LIMIT 1";
        $result = mysqli_query($link, $qry) or die(mysqli_error($link));
        $row = mysqli_fetch_row($result);
        //var_dump($row,$id);
        if ($row !== null && (int)$row[0] !== $code) {
            $errors['code'] = '<span class="error">Ezt a kódot már feltöltötték!</span>';
        }
    }
    $date = date('Y-m-d H:i:s');
    $userid = $_SESSION['userdata']['userid'];


    if (empty($errors)) {
        $qry = "INSERT INTO
    `codes`(
            `userid`,
            `code`,
            `time_upload`
    )
    VALUES (
            '$userid',
            '$code',
            '$date'
    )";
        mysqli_query($link, $qry) or die(mysqli_error($link));
        $insertedId = $userid ?? mysqli_insert_userid($link);//amit az auto increment adott beillesztéskor, vagy a módosított id-ja
        //átirányítás
        /*   header('location:../index.php');
           exit();
        */

    }
}
$formTitle ??= 'Kód feltöltése';
$rowUser ??= [];
//php űrlap összeállítása ($form)
$form = '<h1 align="center">' . $formTitle . '</h1>
                <form method="post">';//űrlap nyitása
//kód mező hozzáfüzése az űrlaphoz
$form .= '<label>
            <span>Kód<sup>*</sup></span>
            <input type="text" name="code" placeholder="Kód" value="' . getInputValue('code', $rowUser) .
    '">';


$form .= $errors['code'] ?? '';//hiba hozzáfűzése ha van

$form .= '</label>';


$form .= '<button>' . ($buttonTitle ?? 'Feltöltés') . '</button>
        </form>';
$output .= $form;

//Meglévő kódok kiírása
$userid = $_SESSION['userdata']['userid'];
$qry = "SELECT userid,code,time_upload FROM codes WHERE userid = '$userid'";
$result = mysqli_query($link, $qry) or die(mysqli_error($link));

$userList = '<h2>Az eddig feltöltött kódok: <br><br></h2>
                     <table>
                      <tr>
                       <th>Kód</th>
                       <th>A feltöltés ideje</th>
                      </tr>';
//ciklus az adatsoroknak
while ($row = mysqli_fetch_assoc($result)) {
    $userList .= '<tr>
                           <td>' . $row['code'] . '</td>
                           <td>' . $row['time_upload'] . '</td>
                          </tr>';
}
$userList .= '</table>';
$output .= $userList;


?><!doctype html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>PHP Vizsga <?php echo '| ' . ADMIN_MENU[$page]['title'] ?? ''; ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        table {
            align-self: center;
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
<?php
//echo '<pre>';
//var_dump($sid,$spass,$_SESSION);
$userBar = "<div>Üdvözlet <b>{$_SESSION['userdata']['name']}</b>! | <a href=\"?logout=true\">Kilépés</a> </div>";


//kiírások

echo $userBar;


echo $output;
?>
</body>
</html>

