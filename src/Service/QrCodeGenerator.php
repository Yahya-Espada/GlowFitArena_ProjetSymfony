<?php
 
 namespace App\Service;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;

use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\Writer\Result\ResultInterface;
use Endroid\QrCode\Writer\SvgWriter;
use App\Entity\Equipement;


class QrCodeGenerator 
{
 
public function createQrCode( Equipement $equipement): ResultInterface
{
    // Récupérez les informations du reservation
    $rate = $equipement->getRate();
 //   $date_equip = $equipement->getDateEquip();
    $reclamation = $equipement->getReclamation();
    $Type_equip = $equipement->getTypeEquip();



    $info = "
    $rate 
    $reclamation
    $Type_equip
    ";
    //$date_equip

    // Générez le code QR avec les informations du reservation
    $result = Builder::create()
        ->writer(new SvgWriter())
        ->writerOptions([])
        ->data($info)
        ->encoding(new Encoding('UTF-8'))
        ->size(200)
        ->margin(10)
        ->labelText('Vous trouvez vos informations ici')
        ->labelFont(new NotoSans(20))
        ->validateResult(false)
        ->build();

    return $result;
}}