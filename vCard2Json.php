<?php

$VCARDFILE = "/Users/tao/Desktop/list.vcf";

$content = file_get_contents($VCARDFILE);
$content = preg_replace("/\r\n|\r|\n/", "\n", $content);
$content = explode("\n", $content);


$result = array();

function resetVal(){
    global $tmp, $tel, $email;
    $tmp = array();
    $tmp['name'] = "";
    $tmp['yomi'] = "";
    $tel = array();
    $email = array();
    $tmp['addr'] = "";
    $tmp['bday'] = "";
}
resetVal();

for($i = 0; $i < count($content); $i++){
    $current = $content[$i];
    if($current === 'END:VCARD'){
        $tmp['tel'] = $tel;
        $tmp['email'] = $email;
        array_push($result, $tmp);
        resetVal();
    }

    // NAME
    else if(preg_match('/^FN:(.+)\s*$/', $current, $out) === 1){
        $tmp["name"] = $out[1];
    }

    // NAME YOMI
    else if(preg_match('/^X-PHONETIC-LAST-NAME:(.+)\s*$/', $current, $out) === 1){
        $tmp["yomi"] = mb_convert_kana($out[1], 'HV', 'UTF-8');
        $tmp["yomi"] = str_replace('\\,', '、', $tmp["yomi"]);
    }

    // TEL
    else if(preg_match('/^TEL;TYPE=.+:(.+)\s*$/', $current, $out) === 1){
        array_push($tel, $out[1]);
    }

    // EMAIL
    else if(preg_match('/^EMAIL.+:(.+)\s*$/', $current, $out) === 1){
        array_push($email, $out[1]);
    }

    // ADDRESS
    else if(preg_match('/^ADR;TYPE=.+:(.+)\s*$/', $current, $out) === 1){
        $addr = str_replace(';', '', $out[1]);
        $tmp["addr"] = $addr;
    }

    // BIRTHDAY
    else if(preg_match('/^BDAY:(.+)\s*$/', $current, $out) === 1){
        $tmp['bday'] = $out[1];
    }

}

echo json_encode($result, JSON_UNESCAPED_UNICODE);
