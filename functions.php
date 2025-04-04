<?php

/**
 * Beléptető metódus
 * @return bool
 */
function login(): bool
{

    global $link;    //az eljárás idejére láthatóvá válik

    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = filter_input(INPUT_POST, 'password');

//kérjük le az emailhez tartozó jelszót és azonosítót, nevet
    $qry = "SELECT 
            userid, name, password
         FROM users 
         WHERE 
             email = '$email' 
             AND
             status = 1
         LIMIT 1";
    $result = mysqli_query($link, $qry) or die(mysqli_error($link));
    $row = mysqli_fetch_assoc($result);

    if ($row && password_verify($password, $row['password'])) {//jelszó ellenőrzése rendben
        $sid = session_id();//mf azonosító
        $stime = time();//idő most (mp)
        $spass = md5($row['id'] . $sid . SECRET_KEY);//fh azonosító + session id + titkosító kulcs
        $userid = strval($row['userid']);

        $_SESSION['id'] = $sid;
        $_SESSION['userdata'] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'email' => $email,
            'userid' => $userid
        ];
        //var_dump($_SESSION);
        //beragadt belépések tisztítása
        mysqli_query($link, "DELETE FROM sessions WHERE sid = '$sid' LIMIT 1") or die(mysqli_error($link));
        mysqli_query($link, "INSERT INTO sessions(sid,spass,stime)
 VALUES('$sid','$spass','$stime')") or die(mysqli_error($link));

        return true;
    }
    return false;
}

/**
 * Azonosító eljárás (érvényes belépés ellenőrzése)
 * @return bool
 */
function auth(): bool
{
    global $link;
//mf lejárati idő
    $now = time();
    $expired = $now - 15 * 60;//most 15p
    $sid = session_id();
//takarítás - öntísztító sessions
    mysqli_query($link, "DELETE FROM sessions WHERE stime < '$expired'") or die(mysqli_error($link));
//volt-e sikeres azonosítás
    $spass = md5($_SESSION['userdata']['id'] . $sid . SECRET_KEY);
    $qry = "SELECT 
            spass 
        FROM sessions 
        WHERE 
            sid = '{$_SESSION['id']}' 
        LIMIT 1";
    $result = mysqli_query($link, $qry) or die(mysqli_error($link));
    $row = mysqli_fetch_row($result);
    if ($row && $spass === $row[0]) {
        //stime frissítés
        mysqli_query($link, "UPDATE sessions SET stime = $now WHERE sid = '$sid' LIMIT 1") or die(mysqli_error($link));
        return true;
    }
    return false;
}

/**
 * kiléptetés
 * @return void
 */
function logout() :void
{
    global $link;
    $sid = session_id();
    //db
    mysqli_query($link,"DELETE FROM sessions WHERE sid = '$sid'") or die(mysqli_error($link));
    //$_SESSION
    $_SESSION = [];
    //mf
    session_destroy();
}

/**
 * Insert segéd
 * @param string $table
 * @param array $data
 * @return string
 */
function getInsertQuery(string $table, array $data = [] ,  ) :string
{
    $ret =  '';
    if($table !== '' && !empty($data)) {
        $ret .= "INSERT INTO `$table`(`".(implode('`,`',array_keys($data)))."`) VALUES ('".(implode("','",$data))."')";

    }
    return $ret;

}


/**
 * Update query segéd
 * @param string $table
 * @param int $userid
 * @param array $data
 * @return string
 */
function getUpdateQuery(string $table, int $userid , array $data = []) :string
{
    $ret = '';
    $elem = [];
    if($table !== '' && !empty($data)) {
        $ret .= "UPDATE `$table` SET ";
        foreach ($data as $key => $value){
            $elem[] = "`$key` = '$value'";
        }
        //var_dump($elem);
        $ret .= implode(',',$elem);
        $ret .= " WHERE id = $id LIMIT 1";
    }
    return $ret;
}
/**
 * text input mezők value értékének kiolvasása súlyozással db adattömbbel
 * @param string $fieldName
 * @param array $row
 * @return string
 */
function getInputValue(string $fieldName, array $row = []): string
{
    if (filter_input(INPUT_POST, $fieldName) !== null) {
        return filter_input(INPUT_POST, $fieldName);
    }
    if (array_key_exists($fieldName, $row)) {
        return $row[$fieldName];
    }
    return '';
}

/**
 * Admin menu összerakó a settingsben található ADMIN_MENU alapján
 * @return string
 */
function adminMenuHTML(): string
{
    $adminMenuHTML = '<nav><ul>';//<-- ide készítsd el az admin  menüt a html minta alapján foreachel az ADMIN_MENU tömb bejárásával


//menüpontok
    foreach (ADMIN_MENU as $menuId => $menuItem) {
        $adminMenuHTML .= '<li><a href="?p=' . $menuId . '" class="menu-item ' . $menuItem['icon'] . '">' . $menuItem['title'] . '</a></li>';
    }

    $adminMenuHTML .= '</ul></nav>';
    return $adminMenuHTML;
}
