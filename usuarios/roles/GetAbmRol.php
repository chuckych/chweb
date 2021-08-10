<?php
ini_set('max_execution_time', 180); //180 seconds = 3 minutes
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
session_start();
require __DIR__ . '../../../config/index.php';
require __DIR__ . '../../../config/conect_mysql.php';
E_ALL();
$data = array();

$RecidRol = test_input(FusNuloPOST('RecidRol', 'vacio'));

if($RecidRol=='vacio'){
    $data = array('status' => 'error', 'dato' => 'Error');
    echo json_encode($data);
    exit;
}
$query="SELECT * FROM abm_roles WHERE recid_rol = '$RecidRol' LIMIT 1";
// print_r($query); exit;
$result = mysqli_query($link, $query);
$data  = array();
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) :

            $aFic  = $row['aFic'];
            $mFic  = $row['mFic'];
            $bFic  = $row['bFic'];
            $aNov  = $row['aNov'];
            $mNov  = $row['mNov'];
            $bNov  = $row['bNov'];
            $aHor  = $row['aHor'];
            $mHor  = $row['mHor'];
            $bHor  = $row['bHor'];
            $aONov = $row['aONov'];
            $mONov = $row['mONov'];
            $bONov = $row['bONov'];
            $Proc  = $row['Proc'];
            $aCit  = $row['aCit'];
            $mCit  = $row['mCit'];
            $bCit  = $row['bCit'];
            $aTur  = $row['aTur'];
            $mTur  = $row['mTur'];
            $bTur  = $row['bTur'];

        $data = array(
            'aFic'  => $aFic,
            'mFic'  => $mFic,
            'bFic'  => $bFic,
            'aNov'  => $aNov,
            'mNov'  => $mNov,
            'bNov'  => $bNov,
            'aHor'  => $aHor,
            'mHor'  => $mHor,
            'bHor'  => $bHor,
            'aONov' => $aONov,
            'mONov' => $mONov,
            'bONov' => $bONov,
            'Proc'  => $Proc,
            'aCit'  => $aCit,
            'mCit'  => $mCit,
            'bCit'  => $bCit,
            'aTur'  => $aTur,
            'mTur'  => $mTur,
            'bTur'  => $bTur,
        );
    endwhile;
}else{
    $data = array(
        'aFic'  => '0',
        'mFic'  => '0',
        'bFic'  => '0',
        'aNov'  => '0',
        'mNov'  => '0',
        'bNov'  => '0',
        'aHor'  => '0',
        'mHor'  => '0',
        'bHor'  => '0',
        'aONov' => '0',
        'mONov' => '0',
        'bONov' => '0',
        'Proc'  => '0',
        'aCit'  => '0',
        'mCit'  => '0',
        'bCit'  => '0',
    );
}

echo json_encode($data);
mysqli_free_result($result);        
mysqli_close($link);
exit;



