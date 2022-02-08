<?php
session_start();
require_once "functions.php";
//Filtrerar bort alla html taggar i $_POST
$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
?>
<?php
if(key_exists('parkera',$_POST) && key_exists('selFordonTyp',$_POST) && key_exists('txtRegNr',$_POST) 
&& key_exists('selParkeringID',$_POST))
{
    if (!empty($_POST['txtRegNr']))
    {
        $regnr = strtoupper(CheckUserInputs($_POST['txtRegNr']));
        $id = CheckUserInputs($_POST['selParkeringID']);
        $typ = strtoupper(CheckUserInputs($_POST['selFordonTyp']));
        //Kolla så fordon inte redan är parkerat (KLAR)
        if (!IsParked($regnr))
        {
            //Kolla om fordonet får plats
            if (CanParkHere($id,$typ))
            {
                //array som innehåller pplatser som är arrayer.
                $arr = array("id"=> $id,"fordontyp" => $typ, 
                "regnr" => $regnr,"starttid" => date("Y/m/d h:i:sa"));
                $_SESSION['Parkering'][] = $arr;
            }
        }
    }
}
if (key_exists('flytta',$_POST) && key_exists('selNyttId',$_POST)
    && key_exists('platsId',$_POST) && key_exists('regnr',$_POST)
    && key_exists('fordonTyp',$_POST))
{
    $regnr = strtoupper(CheckUserInputs($_POST['regnr']));
    $nyttId = $_POST['selNyttId'];
    $typ = strtoupper(CheckUserInputs($_POST['fordonTyp']));
    if (CanParkHere($nyttId,$typ))
    {
        $id = HittaFordon($regnr);
        if ($id>= 0)
        {
            $_SESSION['Parkering'][$id]['id'] = $nyttId;
        }
    }
}
if(key_exists('kill',$_POST))
{
    session_destroy();
    header("Refresh:0");
}

if(key_exists('avsluta',$_POST))
{
    for ($i=0; $i < count($_SESSION['Parkering']) ; $i++) 
    { 
        if($_SESSION['Parkering'][$i]['regnr'] == $_POST['id'])
        {
            $arr = $_SESSION['Parkering'][$i];
            $arr['sluttid'] = date("Y/m/d h:i:sa");
            $arr['summa'] = CalculateSum($arr['starttid'],$arr['sluttid'],$arr['fordontyp']);
            unset($_SESSION['Parkering'][$i]);
            $_SESSION['Parkering'] = array_values($_SESSION['Parkering']);
            $_SESSION['Kvitto'][] = $arr;
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/stil3.css" media="screen" />
    <title>Prag Parking</title>
</head>
<body>
<?php
?>
<h1>Prag Parking - PHP</h1>
<form method="post">
        <input type="submit" id="kill" class="killButton" name="kill" value="Destroy Session" />
    </form>
<main>
<section class="parkera">
<h2>Parkera fordon</h2>
<form method="post">
    <table>
    <tr>
        <td><label for="selParkeringID" id ="lblParkering" class="">Parkeringsplats</label> </td>
        <td>    <select id="selParkeringID" name="selParkeringID">
        <?php 
            for ($i=1; $i <= 20; $i++) { 
                echo "<option value='" .$i."'>".$i."</option>";
            }
        ?>
    </select></td>
    </tr>
    <tr>
        <td><label for="selFordonTyp" id ="lblFordonTyp" class="">Fordontyp</label></td>
        <td>    <select id="selFordonTyp" name="selFordonTyp">
        <option value="bil">Bil</option>
        <option value="mc">MC</option>
        </select><br /></td>
    </tr>
    <tr>
        <td><label for="txtRegNr" id ="lblRegNr" class="">RegNr: </label></td>
        <td><input type="text" id ="txtRegNr" name="txtRegNr" class="userInput" value="" size="10" /><br /></td>
    </tr>
    <tr>
        <td></td><td><input type="submit" id="parkera" class="parkeraButton" name="parkera" value="Parkera" /></td>
    </tr>
    </table>
</form>
<?php
    echo VisaAllaParkeradeFordon();
?>
</section>
<section class="kvitto">
<?php
    echo SkrivKvitto();
    echo VisaAllaKvitton();
?>
</section>
<section class="flytta">
    <?php
    echo FlyttaFordon();
    ?>
</section>
</main>
</body>
</html>