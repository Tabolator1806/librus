<?php
$cookie_file_path = ""; // path do przechowywania ciasteczek 
$ch = curl_init();
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file_path); // "The name of the file containing the cookie data ..."
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // "Set CURLOPT_RETURNTRANSFER to TRUE to return the transfer as a string of the return value of curl_exec()"
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // "true to follow any "Location: " header that the server sends as part of the HTTP header."
get("https://synergia.librus.pl/loguj/portalRodzina?v=1738664710");

$arr = array(
    "action" => "login",
    "login" => "9160565u",
    "pass" => "!1Qaz2Wsx"
);
post("https://api.librus.pl/OAuth/Authorization?client_id=46", $arr);

$arr = array(
    "command" => "open_synergia_window",
    "commandPayload" => array(
        "url" => "https:\/\/synergia.librus.pl\/uczen\/index"
    )
);
post("https://api.librus.pl/OAuth/Authorization/2FA?client_id=46", $arr);

$librusDOM = new DOMDocument();
$xpath = new DOMXPath($librusDOM);
$sortedGrades = array(
    "aplikacje desktopowe"=>[[],[]],
    "aplikacje mobilne"=>[[],[]],
    "aplikacje webowe"=>[[],[]],
    "biologia"=>[[],[]],[],
    "chemia"=>[[],[]],
    "fizyka"=>[[],[]],
    "fizyka techniczna"=>[[],[]],
    "geografia"=>[[],[]],
    "historia"=>[[],[]],
    "język angielski"=>[[],[]],
    "język angielski zawodowy"=>[[],[]],
    "język niemiecki"=>[[],[]],
    "język polski"=>[[],[]],
    "matematyka"=>[[],[]],
    "praktyka zawodowa"=>[[],[]],
    "religia/etyka"=>[[],[]],
    "wiedza o społeczeństwie"=>[[],[]],
    "wychowanie fizyczne"=>[[],[]],
    "zajęcia z wychowawcą"=>[[],[]],
);
$librusFull =  get("https://synergia.librus.pl/przegladaj_oceny/uczen");
$librusJS = str_replace('href="', 'href="https://synergia.librus.pl',$librusFull);
$librusCSS = str_replace('src="', 'src="https://synergia.librus.pl',$librusJS);
$librusHeadless = str_replace('<div id="header"', '<div id="header" style="display:none;" ',$librusCSS);
$librusSortless = str_replace('<table class="right sort_box">', '<table class="right sort_box" style="display:none;">',$librusHeadless);
$librusSpanless = str_replace('<span class="fold"> ', '<span class="fold" style="opacity:0">',$librusSortless);

@$librusDOM->loadHTML($librusSpanless);
$xpath = new DOMXPath($librusDOM);
$grades= $xpath->query('//span[@style!="background-color:#B0C4DE; "]');
foreach ($grades as $gradeNode){
    @$grade = $gradeNode;
    @$subject = $gradeNode->parentNode->previousSibling->nodeValue;
    try{
        if(key_exists($subject,$sortedGrades)){
            $sortedGrades[$subject][0][]=$grade;
        }
        else{
            @$subject = $gradeNode->parentNode->previousSibling->previousSibling->previousSibling->previousSibling->nodeValue;
            if(key_exists($subject,$sortedGrades)){
                $sortedGrades[$subject][1][]=$grade;
            }
            else{
                @$subject = $gradeNode->parentNode->previousSibling->previousSibling->previousSibling->previousSibling->previousSibling->nodeValue;
                if(key_exists($subject,$sortedGrades)){
                    $sortedGrades[$subject][1][]=$grade;
                }
            }
        }
    }
    catch(Exception $e){
        echo $e;
    }
}
$subjectIndex = 0;
$subjects = array(0,1,2,6,14);
$tds = array(39,82,125,289,798);
@$tableBody = $xpath->query('//td');
foreach($tds as $i){
    $tableBody[$i]->nodeValue="";
    $tableBody[$i+4]->nodeValue="";
    foreach($sortedGrades[array_keys($sortedGrades)[$subjects[$subjectIndex]]][0] as $grade){
        $tableBody[$i]->appendChild($grade);
    }foreach($sortedGrades[array_keys($sortedGrades)[$subjects[$subjectIndex]]][1] as $grade){
        $tableBody[$i+4]->appendChild($grade);
    }
    // $tableBody[$i]->appendChild($sortedGrades[array_keys($sortedGrades)[$subjectIndex]][1][0]);
    $subjectIndex+=1;
    
}
$xpath->query('//table')[28]->textContent="";

$subjectIndex = 0;
foreach($sortedGrades as $subject){
    if($subjectIndex!=4){
        if(in_array($subjectIndex,$subjects)){
            $firstUpper = 0;
            $firstLower=0;
            $secondUpper=0;
            $secondLower=0;
            foreach($subject[0] as $firstGrade){
                
            }
        }
    }
    $subjectIndex+=1;
}

echo $librusDOM->saveHTML();

function get($url)
{
    global $ch;
    curl_setopt($ch, CURLOPT_URL, $url); // "The URL to fetch."
    $res = curl_exec($ch);
    return $res;
}

function post($url, $fields)
{
    global $ch;
    $POSTFIELDS = http_build_query($fields);
    curl_setopt($ch, CURLOPT_POST, 1); // "true to do a regular HTTP POST."
    curl_setopt($ch, CURLOPT_POSTFIELDS, $POSTFIELDS); // "The full data to post in a HTTP "POST" operation."
    curl_setopt($ch, CURLOPT_URL, $url);
    $res = curl_exec($ch);
    return $res;
}
