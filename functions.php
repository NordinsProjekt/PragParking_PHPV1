<?php
function SkrivKvitto()
{
    if (key_exists('Kvitto',$_SESSION))
    {
        $row = end($_SESSION['Kvitto']);
        $kvitto = "<h2>Senaste kvittot av (". count($_SESSION['Kvitto']). ")</h2>";
        $kvitto .= "<p>Parkerings plats: " .$row['id']."</p>";
        $kvitto .= "<p>Fordontyp: ". $row['fordontyp']. "</p>";
        $kvitto .= "<p>RegNr: " . $row['regnr']. "</p>";
        $kvitto .= "<p>Start: " . $row['starttid']. "</p>";
        $kvitto .= "<p>Slut: " .$row['sluttid'] . "</p>";
        $kvitto .= "<p>Summa: " .$row['summa']. " kr</p>";
    }
    else
    {
        $kvitto = "<p>Inget kvitto hittades</p>";
    }
    return $kvitto;
}
function CheckUserInputs($notsafeText)
{
    $banlist = array(".",";"," ","/",",","<",">",")","(","=","[","]","+","*");
    $safe = str_replace($banlist,"",$notsafeText);
    return $safe;
}
function IsParked($regnr)
{
    foreach ($_SESSION['Parkering'] as $parkering=>$row)
    {
        if ($row['regnr'] == $regnr)
        {
            return true;
        }
    }
    return false;
}
function HittaFordon($regnr)
{
    $x = 0;
    foreach ($_SESSION['Parkering'] as $parkering=>$row)
    {
        if ($row['regnr'] == $regnr)
        {
            return $x;
        }
        $x++;
    }
    return -1;

}
function CalculateSum($timeStart, $timeEnd,$typ)
{
    $diff = date_diff($timeStart,$timeEnd);
    $summa = $diff->h;
    $summa = $summa + ($diff->days*24)+1;
    switch($typ)
    {
        case ("BIL"):
            $summa *=25;
            break;
        case("MC"):
            $summa *=15;
            break;
        default: //Okänt fordon kostar 100kr per timme
            $summa *=100;
            break;
    }
    return $summa;
}
function CanParkHere($id,$typ)
{
    $summa = 0;
    foreach ($_SESSION['Parkering'] as $parkering=>$row)
    {
        if ($row['id'] == $id)
        {
            switch($row['fordontyp'])
            {
                case "BIL":
                    $summa+=2;
                    break;
                case "MC":
                    $summa+=1;
                    break;
                default: //Okänt fordon tar upp 5 enheter.
                    $summa +=5;
                    break;
            }
        }
    }
    if ($typ == "MC" && $summa <=1)
    {
        return true;
    }
    if ($typ == "BIL" && $summa <= 0)
    {
        return true;
    }
    return false;
}
function VisaAllaParkeradeFordon()
{
    if (!empty($_SESSION['Parkering']))
    {
        $text ="";
        $parkering = $_SESSION['Parkering'];
        $text.= "<h2>Parkerade fordon</h2>";
        $text.= "<table><tr><th>Plats Nr</th><th>Typ</th><th>RegNr</th><th>Start tid</th><th></th></tr>";
        $x = 0;
        foreach ($parkering as $row => $arr) 
        {
            $text.= "<tr>";
            $text.= "<td>". $arr['id'] ."</td> <td>" . $arr['fordontyp'] ."</td> <td>". $arr['regnr'] ."</td>
                <td>".$arr['starttid']."</td> <td>";
                $text.= "<form method='post'>";
                $text.= "<input type='submit' name='avsluta' value='Avsluta Parkering' />";
                $text.= "<input type='hidden' name='id' value='".$arr['regnr']."' /></td>";
                $text.= "</form>";
                $text.="</tr>";
        }
        $text.= "</table>";
        return $text;
    }
    else
    {
        $text = "<p>Inga fordon parkerade</p>";
        return $text;
    }
}
function FlyttaFordon()
{
    if (!empty($_SESSION['Parkering']))
    {
        $text ="";
        $parkering = $_SESSION['Parkering'];
        $text.= "<h2>Flytta fordon</h2>";
        $text.= "<table><tr><th>Plats Nr</th><th>Typ</th><th>RegNr</th><th>Start tid</th><th>Ny plats</th><th></th></tr>";
        $x = 0;
        foreach ($parkering as $row => $arr) 
        {
            $text.= "<tr>";
            $text.= "<td>". $arr['id'] ."</td> <td>" . $arr['fordontyp'] ."</td> <td>". $arr['regnr'] ."</td>
                <td>".$arr['starttid']."</td> <td>";
                $text.= "<form method='post'>";
                $text.= " <select id='selNyttId' name='selNyttId'>";
                for ($i=1; $i <= 20; $i++) {
                    if ($i === (int)$arr['id']) //Skriver inte ut den plats som fordonet finns på nu.
                    { }
                    else
                    {
                        $text.= "<option value='" .$i."'>".$i."</option>";
                    }
                }
                $text.= "</select></td>";
                $text.= "<td><input type='submit' name='flytta' value='Flytta fordon' />";
                $text.= "<input type='hidden' name='regnr' value='".$arr['regnr']."' />";
                $text.= "<input type='hidden' name='fordonTyp' value='".$arr['fordontyp']."' />";
                $text.= "<input type='hidden' name='platsId' value='".$arr['platsId']."' /></td>";
                $text.= "</form>";
                $text.="</tr>";
        }
        $text.= "</table>";
        return $text;
    }
    else
    {
        $text = "<p>Inga fordon parkerade</p>";
        return $text;
    }
}
function OptimeraMC()
{
    //Inte klar
    $x = 0;

    foreach ($_SESSION as $parkering => $row) 
    {
        
    }
}
function VisaAllaKvitton()
{
    if (key_exists('Kvitto',$_SESSION))
    {
        $text = "<h2>Alla kvitton</h2><table><tr><th>Kvitto Index</th><th>RegNr</th><th>Typ</th><th>Summa</th></tr>";
        $x = 0;
        foreach ($_SESSION['Kvitto'] as $kvitto=>$row)
        {
            $text .= "<tr><td>" .$x++. "</td><td>" .$row['regnr']. "</td><td>" .$row['fordontyp'].
            "</td><td>" .$row['summa']. " kr</td></tr>";
        }
        $text.= "</table>";
    }
    else
    {
        $text = "";
    }
    return $text;
}
function ListaAllaAktivaParkeringar()
{
    
}
?>