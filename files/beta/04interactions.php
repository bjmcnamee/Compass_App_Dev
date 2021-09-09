<?php
// START SESSION
session_start();
// REDIRECT TO LOGIN PAGE IF NOT LOGGED IN
if (!isset($_SESSION['loggedin'])) {header('Location: login.html'); exit;}


// Javascript
?><script type="text/javascript" src="04Script.js"></script><?php

// DECLARE VARIABLES
$bar = "<hr style='width:60%;text-align:left;margin-left:0'>";
$urlroot = "https://mor.nlm.nih.gov/RxNav/search?searchBy=RXCUI&searchTerm=";
$ctrlf = "<img src='../../assets/img/ctrlf.png' title='alert level : severe' alt='alert level : severe' height='25'>";
$red = "<img src='../../assets/img/red.png' title='alert level : severe' width='10' height='10'>&nbsp";
$orange = "<img src='../../assets/img/orange.png' title='alert level : moderate' width='10' height='10'>&nbsp";
$green = "<img src='../../assets/img/green.png' title='alert level : minor' width='10' height='10'>&nbsp";
$one = "<img src='../../assets/img/one.png' width='20' height='20'>";
$two = "<img src='../../assets/img/two.png' width='20' height='20'>";
$three = "<img src='../../assets/img/three.png' width='20' height='20'>";
$four = "<img src='../../assets/img/four.png' width='20' height='20'>";
$rx_icon = "<img src='../../assets/img/rx_icon.png' title='NLM RxNorm' height='15'>";
$dm_icon = "<img src='../../assets/img/dailymed_icon.png' title='NLM DailyMed' height='12'>";
$db_icon = "<img src='../../assets/img/drugbank_icon.png' title='DrugBank' height='15'>";
$atc_icon = "<img src='../../assets/img/atc_icon.png' title='ATC' height='15'>";
$help1 = "
            <table>
            <tr><th style='text-align : right'>HOW IT WORKS&nbsp&nbsp&nbsp&nbsp:&nbsp&nbsp&nbsp <br><br><br></th><th style='font-weight:normal'>Search for drug interactions with other drugs, disease or gene variants, and find bespoke drug and disease alerts<br><br><br></th></tr>
            <tr><th style='text-align : right'><b>General Search&nbsp&nbsp&nbsp&nbsp:&nbsp&nbsp&nbsp </b></th>
                <th style='font-weight:normal;text-align : left'>Note : Client details not required for a general search</th></tr>
                <tr><th></th><th style='font-weight:normal;text-align : left'>1. Select any drug combination - at least two for drug-drug interaction</th></tr>
                <tr><th></th><th style='font-weight:normal;text-align : left'>2. Select any disease combination - at least one for drug-disease combinations</th></tr>
                <tr><th></th><th style='font-weight:normal;text-align : left'>3. click <button type='button' disabled>Find Interactions</button><br><br></th></tr>
            <tr><th style='text-align : right'><b>Find existing client&nbsp&nbsp&nbsp&nbsp:&nbsp&nbsp&nbsp </b></th>
                <th style='font-weight:normal;text-align : left'>1. Enter HBT ID, eg 10000, 10001, etc</th></tr>
                <tr><th></th><th style='font-weight:normal;text-align : left'>2. Click <button type='button' disabled>Find</button> to find client record</th></tr>
                <tr><th></th><th style='font-weight:normal;text-align : left'>3. Click on 'Client' tab below to view client record<br><br></th></tr>
            <tr><th style='text-align : right'><b>Add new client&nbsp&nbsp&nbsp&nbsp:&nbsp&nbsp&nbsp </b></th>
                <th style='font-weight:normal;text-align : left'>1. Enter HBT ID</th></tr>
                <tr><th></th><th style='font-weight:normal;text-align : left'>2. Create Baseline for client record - select sex, age, drugs and diseases, and add notes if required</th></tr>
                <tr><th></th><th style='font-weight:normal;text-align : left'>3. Click <button type='button' disabled>Add</button> to add new client profile<br><br></th></tr>
            <tr><th style='text-align : right'><b>Update existing client&nbsp&nbsp&nbsp&nbsp:&nbsp&nbsp&nbsp </b></th>
                <th style='font-weight:normal;text-align : left'>1. Find client as above</th></tr>
                <tr><th></th><th style='font-weight:normal;text-align : left'>2. Create Follow Up for client record - select new/changed drugs and diseases, and add notes if required</th></tr>
                <tr><th></th><th style='font-weight:normal;text-align : left'>3. Click <button type='button' disabled>Update</button> to update client profile</th></tr>
                <tr><th></th><th style='font-weight:normal;text-align : left'>4. View changes in 'Client' tab<br><br></th></tr>
            <tr><th style='text-align : right'><b>Add new Drugs or Diseases&nbsp&nbsp&nbsp&nbsp:&nbsp&nbsp&nbsp </b></th>
                <th style='font-weight:normal;text-align : left'>Click <button type='button' disabled> + 1 </button> , select new drug/disease, then select A, B or C to refresh Drugs/Diseases tabs/summary :</th></tr>
                <tr><th></th><th style='font-weight:normal;text-align : left'>A. for a simple search, click <button type='button' disabled>Find Interactions</button></th></tr>
                <tr><th></th><th style='font-weight:normal;text-align : left'>B. to update existing client, click <button type='button' disabled>Update</button></th></tr>
                <tr><th></th><th style='font-weight:normal;text-align : left'>C. to add a new client, click <button type='button' disabled>Add</button><br><br></th></tr>
            <tr><th style='text-align : right'><b>Tips&nbsp&nbsp&nbsp&nbsp:&nbsp&nbsp&nbsp </b></th>
                <th style='font-weight:normal;text-align : left'>Click <button type='button' disabled>CLS</button> to clear previous selections</th></tr>
                <tr><th></th><th style='font-weight:normal;text-align : left'>Select 'Choose One' in Drugs or Diseases list to remove item</th></tr>
            </table>";
$sex = array('Male','Female');
$age = array('0 to 10','11 to 20','21 to 30','31 to 40','41 to 50','51 to 60','71 to 80','81 to 90','91 to 100', '100+');
$allergies = array('None', 'Antibiotics containing sulfonamides', 'Anticonvulsants', 'Aspirin Ibuprofen and other NSAIDs', 'Chemotherapy drugs','P enicillin + related antibiotics');
$exercise = array('None', 'Low', 'Moderate', 'Very Active');
$alcohol = array('0','1 - 7','8 - 14','15 - 21','22 - 28','29 - 35','36 - 42','43 - 49','50 - 56','57+');
$smoking = array('Yes','No','Recently Quit');
$hbtid = '';
$noneselected = ($_POST['drug1'] == "Choose one" && $_POST['drug2'] == "Choose one" && $_POST['drug3'] == "Choose one" && $_POST['drug4'] == "Choose one" && $_POST['disease1'] == "Choose one" && $_POST['disease2'] == "Choose one" && $_POST['disease3'] == "Choose one" && $_POST['disease4'] == "Choose one");
$variants = array('rs1799853','rs1057910');
$dbinfo = "<br><b>Search Suggestions</b><br><br> Drug-Drug Interactions : drugs (748), interactions (561,000), bespoke alerts (1) 'Lansoprazole', DailyMed pages (527), Drugbank (629), ICD codes (84,000)<br><br> Drug - Disease Interactions : Chronic kidney disease x 132, Aspirin-Asthma x 1, Aspirin-Heart failure x 1<br><br> Drug - Disease Guide :  'Chronic kidney disease' (1)<br><br> Drug - Gene : functionality hidden for now";
$showhelp = 0;


// FUNCTIONS

// CONNECT TO DATABASE
function connectSQLdb() {$servername = "localhost"; $username = $_SESSION['name']; $password = $_SESSION['password']; $dbname = "interactions";
    static $conn;
    mysqli_set_charset($conn, "utf8");
    if ($conn===NULL) {$conn=new mysqli($servername,$username,$password,$dbname);}
    return $conn;}

// FROM DATABASE (CLIENTS TABLE), GET ALL CLIENT FIELDS (AS ARRAY) WHERE HBT ID = $hbtid
function &findClient($hbtid){
    $conn = connectSQLdb();
    $a_client = array();
    if ($conn->connect_error) {die("Connection failed: " . $conn->connect_error);}
    $sql = "SELECT * FROM `clients` WHERE hbtid = '$hbtid'";
    $sqlquery = $conn->query($sql);
    if ($sqlquery->num_rows > 0) {
        while($row = $sqlquery->fetch_assoc()) {
            $a_client[0] = $row["hbtid"];$a_client[1] = $row["drug01"];$a_client[2] = $row["drug02"];$a_client[3] = $row["drug03"];$a_client[4] = $row["drug04"];$a_client[5] = $row["drug05"];$a_client[6] = $row["drug06"];$a_client[7] = $row["drug07"];$a_client[8] = $row["drug08"];$a_client[9] = $row["drug09"];$a_client[10] = $row["drug10"];$a_client[11] = $row["drug11"];$a_client[12] = $row["drug12"];$a_client[13] = $row["drug13"];$a_client[14] = $row["drug14"];$a_client[15] = $row["drug15"];$a_client[16] = $row["drug16"];$a_client[17] = $row["drug17"];$a_client[18] = $row["drug18"];$a_client[19] = $row["drug19"];$a_client[20] = $row["drug20"];$a_client[21] = $row["disease01"];$a_client[22] = $row["disease02"];$a_client[23] = $row["disease03"];$a_client[24] = $row["disease04"];$a_client[25] = $row["disease05"];$a_client[26] = $row["disease06"];$a_client[27] = $row["disease07"];$a_client[28] = $row["disease08"];$a_client[29] = $row["disease09"];$a_client[30] = $row["disease10"];$a_client[40] = $row["status"];$a_client[41] = $row["sex"];$a_client[42] = $row["age"];$a_client[43] = $row["allergies"];$a_client[44] = $row["exercise"];$a_client[45] = $row["alcohol"];$a_client[46] = $row["smoking"];$a_client[47] = $row["height1"];$a_client[48] = $row["height2"];$a_client[49] = $row["weight1"];$a_client[50] = $row["weight2"];$a_client[51] = $row["bmi"];$a_client[52] = $row["history"];
        }}
    return $a_client;}

// TO DATABASE (CLIENTS TABLE), INSERT NEW CLIENT RECORD (ALL CLIENT FIELDS) WHERE CLIENT = $client ARRAY
function &addClient($client){
    $conn = connectSQLdb();
    if ($conn->connect_error) {die("Connection failed: " . $conn->connect_error);}
    $sql = "INSERT INTO `clients` (hbtid, drug01, drug02, drug03, drug04, drug05, drug06, drug07, drug08, drug09, drug10, drug11, drug12, drug13, drug14, drug15, drug16, drug17, drug18, drug19, drug20, disease01, disease02, disease03, disease04, disease05, disease06, disease07, disease08, disease09, disease10, status,  sex, age, allergies, exercise, alcohol, smoking, height1, height2, weight1, weight2, bmi, history) VALUES ('$client[0]', '$client[1]', '$client[2]', '$client[3]', '$client[4]', '$client[5]', '$client[6]', '$client[7]', '$client[8]', '$client[9]', '$client[10]', '$client[11]', '$client[12]', '$client[13]', '$client[14]', '$client[15]', '$client[16]', '$client[17]', '$client[18]', '$client[19]', '$client[20]', '$client[21]', '$client[22]', '$client[23]', '$client[24]', '$client[25]', '$client[26]', '$client[27]', '$client[28]', '$client[29]', '$client[30]', '$client[40]', '$client[41]', '$client[42]','$client[43]', '$client[44]', '$client[45]', '$client[46]', '$client[47]', '$client[48]', '$client[49]', '$client[50]', '$client[51]', '$client[52]')";
    # ERROR / SUCCESS REPORTING
    $green = "<img src='../../assets/img/green.png' title='alert level : minor' width='10' height='10'>&nbsp";
    $red = "<img src='../../assets/img/red.png' title='alert level : severe' width='10' height='10'>&nbsp";
    if ($conn->query($sql) === TRUE) {echo $green." New record created successfully <br><br>";}
    else {echo $red." Error: Record already exists<br><br>";
        if (mysqli_error($conn)!="") {echo $red." ".mysqli_error($conn)."<br><br>";}}}

// TO DATABASE (CLIENTS TABLE), INSERT UPDATE TO EXISTING CLIENT RECORD (ALL CLIENT FIELDS) WHERE CLIENT = $client ARRAY
function &updateClient($client){
    $conn = connectSQLdb();
    if ($conn->connect_error) {die("Connection failed: " . $conn->connect_error);}
    $sql = "UPDATE `clients` set drug01='$client[1]', drug02='$client[2]', drug03='$client[3]', drug04='$client[4]', drug05='$client[5]', drug06='$client[6]', drug07='$client[7]', drug08='$client[8]', drug09='$client[9]', drug10='$client[10]', drug11='$client[11]', drug12='$client[12]', drug13='$client[13]', drug14='$client[14]', drug15='$client[15]', drug16='$client[16]', drug17='$client[17]', drug18='$client[18]', drug19='$client[19]', drug20='$client[20]', disease01='$client[21]', disease02='$client[22]', disease03='$client[23]', disease04='$client[24]', disease05='$client[25]',
                     disease06='$client[26]',disease07='$client[27]',disease08='$client[28]',disease09='$client[29]',disease10='$client[30]', status='$client[40]', sex='$client[41]', age='$client[42]', allergies='$client[43]', exercise='$client[44]', alcohol='$client[45]', smoking='$client[46]', height1='$client[47]', height2='$client[48]', weight1='$client[49]', weight2='$client[50]', bmi='$client[51]', history='$client[52]' WHERE hbtid = '$client[0]'";
    # ERROR / SUCCESS REPORTING
    $green = "<img src='../../assets/img/green.png' title='alert level : minor' width='10' height='10'>&nbsp";
    $red = "<img src='../../assets/img/red.png' title='alert level : severe' width='10' height='10'>&nbsp";
    if ($conn->query($sql) === TRUE) {echo "&nbsp".$green." Record updated successfully&nbsp<br><br>";}
    else {echo "&nbsp".$red." Error: " . $sql . "<br>" . $conn->error;}}

// COUNT NUMBER OF DRUGS ($drugCount STRING) WHERE CLIENT = $client
function &findDrugCount($client){
    $drugCount = 0;
    # count $client[1-20]
    for ($j = 1; $j < 21; $j++) {if ($client[$j] != "") {$drugCount++;}}
    if ($drugCount < 7) {$drugCount = 6;}
    return $drugCount;}

// COUNT NUMBER OF DISEASES ($diseaseCount STRING) WHERE CLIENT = $client
function &findDiseaseCount($client){
    $diseaseCount = 0;
    # count $client[21-30]
    for ($j = 21; $j < 31; $j++) {if ($client[$j] != "") {$diseaseCount++;}}
    if ($diseaseCount < 4) {$diseaseCount = 3;}
    return $diseaseCount;}

// FROM DATABASE (DRUG CODES TABLE), GET ALL DRUGS (ARRAY)
function &getDrugsList(){
    $conn = connectSQLdb();
    $a_drugs = array();
    if ($conn->connect_error) {die("Connection failed: " . $conn->connect_error);}
    $sql = "SELECT * FROM `drug_codes`";
    $sqlquery = $conn->query($sql);
    if ($sqlquery->num_rows > 0) {
        $i = 0;while($row = $sqlquery->fetch_assoc()) {
            $a_drugs[$i] = $row['drug'];$i++;}}
    sort($a_drugs);
    $a_drugs = array_unique($a_drugs);
    array_unshift($a_drugs , "Choose one"); # inserts new element to an array, ie "Choose one"
    return $a_drugs;}

// FROM DATABASE (DRUG CODES TABLE), GET CODES (RX, SPL & DB AS ARRAY) WHERE DRUG NAME = $drug
function &getDrugCodes($drug){
    $conn = connectSQLdb();
    $a_codes = array(0,0,0);
    if ($conn->connect_error) {die("Connection failed: " . $conn->connect_error);}
    $sql = "SELECT * FROM `drug_codes` WHERE drug = '$drug'";
    $sqlquery = $conn->query($sql);
    if ($sqlquery->num_rows > 0) {
        while($row = $sqlquery->fetch_assoc()) {
            $a_codes[0] = $row["rxcui"];
            $a_codes[1] = $row["spl"];
            $a_codes[2] = $row["drugbank"];
            $a_codes[3] = $row["atc"];}}
    return $a_codes;}

function &getDiseasesList(){
    $conn = connectSQLdb();
    $a_diseases = array();
    if ($conn->connect_error) {die("Connection failed: " . $conn->connect_error);}
    $sql = "SELECT * FROM `diseases_codes`";
    $sqlquery = $conn->query($sql);
    if ($sqlquery->num_rows > 0) {
        $i = 0;while($row = $sqlquery->fetch_assoc()) {
            $a_diseases[$i] = $row['disease'];$i++;}}
    sort($a_diseases);
    $a_diseases = array_unique($a_diseases);
    array_unshift($a_diseases , "Choose one"); # inserts new element to an array, ie "Choose one"
    return $a_diseases;}

// FROM DATABASE (DRUG CODES TABLE), GET DRUG NAME AS STRING WHERE RX CODE = $rx_id
function &getDrugName($rx_id){
    $conn = connectSQLdb();
    if ($conn->connect_error) {die("Connection failed: " . $conn->connect_error);}
    $sql = "SELECT * FROM `drug_codes` WHERE rxcui = '$rx_id'";
    $sqlquery = $conn->query($sql);
    if ($sqlquery->num_rows > 0) {
        while($row = $sqlquery->fetch_assoc()) {
            $drug = $row['drug'];}}
    return $drug;}

// FROM DATABASE (DRUG CODES TABLE), GET RX CODE AS STRING WHERE DRUG NAME = $drug
function &getRxCode($drug){
    $conn = connectSQLdb();
    if ($conn->connect_error) {die("Connection failed: " . $conn->connect_error);}
    $sql = "SELECT * FROM `drug_codes` WHERE drug = '$drug'";
    $sqlquery = $conn->query($sql);
    if ($sqlquery->num_rows > 0) {
        while($row = $sqlquery->fetch_assoc()) {
            $rx_id = $row["rxcui"];}}
    return $rx_id;}

// FROM DATABASE (DRUG CODES TABLE), GET DRUG CODES WHERE DRUG NAME = $drug + FORMAT AS LONG STRING (ONE LINE PER CODE)
function &getDrugSourceIds($drug){
    $conn = connectSQLdb();
    $rx_icon = "<img src='../../assets/img/rx_icon.png' title='National Library Medicine RxNorm' height='15'>";
    $dm_icon = "<img src='../../assets/img/dailymed_icon.png' title='National Library Medicine DailyMed' height='12'>";
    $db_icon = "<img src='../../assets/img/drugbank_icon.png' title='DrugBank' height='15'>";
    $atc_icon = "<img src='../../assets/img/atc_icon.png' title='World Health Organisation' height='15'>";
    if ($conn->connect_error) {die("Connection failed: " . $conn->connect_error);}
    $sql = "SELECT * FROM `drug_codes` WHERE drug = '$drug'";
    $sqlquery = $conn->query($sql);
    if ($sqlquery->num_rows > 0) {
        while($row = $sqlquery->fetch_assoc()) {
            if ($row["common"] == "") {$line_common = "";}
            else {$line_common = "Commonly known or available as ".$row["common"]."<br>";}
            $line_rx = "<tr><th>".$rx_icon."</th><th>"."<span style='float:left;font-weight:normal'> NLM : RxCUI id "."</th><th>"."<span style='float:left;font-weight:normal'><a href='https://mor.nlm.nih.gov/RxNav/search?searchBy=String&searchTerm=".$drug."'>".$row["rxcui"]."</a></span></span></th></tr>";
            $line_atc = "<tr><th>".$atc_icon."</th><th>"."<span style='float:left;font-weight:normal'> WHO : ATC id "."</th><th>"."<span style='float:left;font-weight:normal'><a href='https://www.whocc.no/atc_ddd_index/?code=".$row["atc"]."'>".$row["atc"]."</a></span></span></th></tr>";
            $line_db = "<tr><th>".$db_icon."</th><th>"."<span style='float:left;font-weight:normal'> DrugBank : DB id "."</th><th>"."<span style='float:left;font-weight:normal'><a href='https://go.drugbank.com/drugs/".$row["drugbank"]."'>".$row["drugbank"]."</a></span></span></th></tr>";
            $line_dm = "<tr><th>".$dm_icon."</th><th>"."<span style='float:left;font-weight:normal'> DailyMed : SPL id "."</th><th>"."<span style='float:left;font-weight:normal'><a href='https://dailymed.nlm.nih.gov/dailymed/drugInfo.cfm?setid=".$row["spl"]."'>".$row["spl"]."</a></span></span></th></tr>";
        }
        $lines = $line_common."<table>".$line_rx.$line_atc.$line_db.$line_dm."</table>";}
    else {$lines = "0 results";}
    return $lines;}

// FROM DATABASE (DRUG ALERTS TABLE), GET DRUG ALERTS WHERE RX CODE = $rx_id + FORMAT AS LONG STRING (ONE LINE PER CODE)
function &getDrugAlerts($rx_id){
    $conn = connectSQLdb();
    $drug_alert = '0 results';
    if ($conn->connect_error) {die("Connection failed: " . $conn->connect_error);}
    $sql = "SELECT * FROM `drug_alerts` WHERE rxcui = '$rx_id'";
    $sqlquery = $conn->query($sql);
    if ($sqlquery->num_rows > 0) {
        while($row = $sqlquery->fetch_assoc()) {$warning = $row["warning"]; $drug_alert = " ".$row["alert"]." <i>(".$row["version"].")</i><br>";}}
    # CONCATENATE COLOUR FLAG TO ALERT
    $colour='';
    $red = "<img src='../../assets/img/red.png' title='alert level : severe' width='10' height='10'>&nbsp";
    $orange = "<img src='../../assets/img/orange.png' title='alert level : moderate' width='10' height='10'>&nbsp";
    $green = "<img src='../../assets/img/green.png' title='alert level : minor' width='10' height='10'>&nbsp";
    if ($drug_alert!='0 results') {if ($warning == 1) {$colour = $red;} else if ($warning == 2) {$colour = $orange;} else {$colour = $green;}}
    $drug_alert = $colour.$drug_alert;
    return $drug_alert;}

// FROM DATABASE (DAILY MED TABLE), GET DAILY MED PAGE SCRAPE AS LONG STRING WHERE SPL CODE = $spl_id + ADD DIV TAGS TO HTML
function &getDailyMedPage($spl_id){
    $bar = "<hr style='width:60%;text-align:left;margin-left:0'>";
    $dm_icon = "<img src='../../assets/img/dailymed_icon.png' title='NLM DailyMed' width='20'>";
    $dailymed_title =  "<br>".$dm_icon." <b>DAILYMED INTERACTIONS SUMMARY</b>".$bar;
    $conn = connectSQLdb();
    $sql = "SELECT * FROM `dailymed` WHERE spl = '$spl_id'";
    $sqlquery = $conn->query($sql);
    if ($sqlquery->num_rows > 0) {
        while($row = $sqlquery->fetch_assoc()) {$dailymed_page = $dailymed_title.$row["dmcontent"];}}
    return $dailymed_page;}

// FROM DATABASE (DRUGBANK TABLE), GET DRUGBANK PAGE SCRAPE AS LONG STRING WHERE DRUGBANK CODE = $drugbank + ADD DIV TAGS TO HTML
function &getDrugbankPage($DB_id){
    $bar = "<hr style='width:60%;text-align:left;margin-left:0'>";
    $db_icon = "<img src='../../assets/img/drugbank_icon.png' title='DrugBank' width='15'>";
    $drugbank_title = "<br>".$db_icon." <b>DRUGBANK DETAILED PROFILE</b>".$bar;
    $conn = connectSQLdb();
    $sql = "SELECT * FROM `drugbank` WHERE drugbank = '$DB_id'";
    $sqlquery = $conn->query($sql);
    if ($sqlquery->num_rows > 0) {
        while($row = $sqlquery->fetch_assoc()) {$drugbank_page = $drugbank_title.$row["dbcontent"];}}
    else {$drugbank_page = $drugbank_title."0 results";}
    return $drugbank_page;}

// FROM DATABASE (DRUG INTERACTIONS TABLE), GET ALL DRUG INTERACTIONS AS LONG STRING WHERE RX CODE = $rx_id
function &getAllDrugDrugInteractions($rx_id){
    $urlroot = "https://mor.nlm.nih.gov/RxNav/search?searchBy=RXCUI&searchTerm=";
    $conn = connectSQLdb();
    if ($conn->connect_error) {die("Connection failed: " . $conn->connect_error);}
    $sql = "SELECT * FROM `drug_interactions` WHERE rxcui = '$rx_id'";
    $sqlquery = $conn->query($sql);
    $interactions = [];$i=0;
    if ($sqlquery->num_rows > 0) {
        while($row = $sqlquery->fetch_assoc()) {$interactions[$i] = $row["i_drug"]." (Rx <a href=".$urlroot.$row["i_rxcui"].">".$row["i_rxcui"]."</a>) : ".$row["interaction"];$i++;}}
    return $interactions;}

// FROM DATABASE (DRUG INTERACTIONS TABLE), GET SELECT DRUG INTERACTIONS AS STRING WHERE RX CODES = $rx_id, $i_rx_id
function &getSelectDrugDrugInteraction($rx_id,$i_rx_id){
    $interaction = array(0,0,0,0,0);
    $red = "<img src='../../assets/img/red.png' title='alert level : severe' width='10' height='10'>&nbsp";
    $orange = "<img src='../../assets/img/orange.png' title='alert level : moderate' width='10' height='10'>&nbsp";
    $green = "<img src='../../assets/img/green.png' title='alert level : minor' width='10' height='10'>&nbsp";
    $conn = connectSQLdb();
    if ($conn->connect_error) {die("Connection failed: " . $conn->connect_error);}
    $sql = "SELECT * FROM `drug_interactions` WHERE rxcui = '$rx_id' AND i_rxcui = '$i_rx_id'";
    $sqlquery = $conn->query($sql);
    if ($sqlquery->num_rows > 0) {
        while($row = $sqlquery->fetch_assoc()) {
            $interaction[0] = $row["warning"]; $interaction[2] = $row['drug']; $interaction[3] = $row["i_drug"]; $interaction[4] = $row["interaction"];}}
    if ($interaction[0] == 1) {$interaction[1] = $red;} else if ($interaction[0] == 2) {$interaction[1] = $orange;} else {$interaction[1] = $green;}
    if ($interaction[4] == "") {$interaction[4] = "0 results";}
    return $interaction;}

// FROM DATABASE (DISEASE INTERACTIONS TABLE), GET DISEASE CODE AS STRING WHERE DISEASE = $disease
function &getDiseaseCode($disease){
    $conn = connectSQLdb();
    if ($conn->connect_error) {die("Connection failed: " . $conn->connect_error);}
    $sql = "SELECT * FROM `diseases_codes` WHERE disease = '$disease'";
    $sqlquery = $conn->query($sql);
    if ($sqlquery->num_rows > 0) {
        while($row = $sqlquery->fetch_assoc()) {$icd = $row["icd"];}}
    return $icd;}

// FROM DATABASE (ICD CODES TABLE), GET DISEASE AS STRING WHERE DISEASE CODE = $icd
function &getLongDisease($icd){
    $conn = connectSQLdb();
    if ($conn->connect_error) {die("Connection failed: " . $conn->connect_error);}
    $sql = "SELECT * FROM `diseases_codes` WHERE icd = '$icd'";
    $sqlquery = $conn->query($sql);
    if ($sqlquery->num_rows > 0) {
        while($row = $sqlquery->fetch_assoc()) {$long_disease = $row["long_disease"];}}
    return $long_disease;}

// FROM DATABASE (DISEASE INTERACTIONS TABLE), GET DISEASE INTERACTION AS ARRAY WHERE DISEASE CODE = $icd AND RX CODE = $rx_id
function &getDiseaseInteractions($rx_id,$icd){
    $interaction = array(0,0,0,0,0,0,0);
    $red = "<img src='../../assets/img/red.png' title='alert level : severe' width='10' height='10'>&nbsp";
    $orange = "<img src='../../assets/img/orange.png' title='alert level : moderate' width='10' height='10'>&nbsp";
    $green = "<img src='../../assets/img/green.png' title='alert level : minor' width='10' height='10'>&nbsp";
    $conn = connectSQLdb();
    if ($conn->connect_error) {die("Connection failed: " . $conn->connect_error);}
    $sql = "SELECT * FROM `dis_interactions` WHERE rxcui = '$rx_id' AND icd = '$icd'";
    $sqlquery = $conn->query($sql);
    if ($sqlquery->num_rows > 0) {
        while($row = $sqlquery->fetch_assoc()) {$interaction[0] = $row["warning"]; $interaction[2] = $row["rxcui"]; $interaction[3] = $row["issue"]; $interaction[4] = $row["advice"]; $interaction[5] = $row["version"]; }}
    if ($interaction[0] == 1) {$interaction[1] = $red;} else if ($interaction[0] == 2) {$interaction[1] = $orange;} else {$interaction[1] = $green;}
    return $interaction;}

// FROM DATABASE (DISEASE GUIDE TABLE), GET DISEASE GUIDE AS LONG STRING WHERE DISEASE = $disease
// *** change to WHERE ICD = $icd when codes available for all diseases ***
function &getDiseaseGuide($icd) {
    $conn = connectSQLdb();
    if ($conn->connect_error) {die("Connection failed: " . $conn->connect_error);}
    $sql = "SELECT * FROM `disease_guide` WHERE icd = '$icd'";
    $sqlquery = $conn->query($sql);
    if ($sqlquery->num_rows > 0) {
        while($row = $sqlquery->fetch_assoc()) {
            $warning = $row["warning"]; $guide = $row["guide"];}}
    if (!isset($guide)) {$guide = "<br>None found";}
    return $guide;}

// FROM DATABASE (GENE INTERACTIONS TABLE), GET GENE INTERACTIONS AS STRING WHERE DRUG = $drug
function &getGeneInteractions($drug){
    $gene_alerts = array();
    $i=0;
    $conn = connectSQLdb();
    $red = "<img src='../../assets/img/red.png' title='alert level : severe' width='10' height='10'>&nbsp";
    $orange = "<img src='../../assets/img/orange.png' title='alert level : moderate' width='10' height='10'>&nbsp";
    $green = "<img src='../../assets/img/green.png' title='alert level : minor' width='10' height='10'>&nbsp";
    if ($conn->connect_error) {die("Connection failed: " . $conn->connect_error);}
    $sql = "SELECT * FROM `gene_interactions` WHERE drug = '$drug'";
    $sqlquery = $conn->query($sql);
    if ($sqlquery->num_rows > 0) {
        while($row = $sqlquery->fetch_assoc()) {

            $warning = $row["warning"];
            if ($warning == 1) {$colour = $red;} else if ($warning == 2) {$colour = $orange;} else {$colour = $green;}
            // create hyperlink sources from source_CPIC, source_DPWG, source_FDA fields
            if($row["source_CPIC"]!="") {$source="<a href=".$row["source_CPIC"]." target='_blank'> CPIC</a> ";} else {$source="";}
            if($row["source_DPWG"]!="") {$source=$source."<a href=".$row["source_DPWG"]." target='_blank'>DPWG</a> ";} else {$source=$source."";}
            if($row["source_FDA"]!="") {$source=$source."<a href=".$row["source_FDA"]." target='_blank'>FDA</a> ";} else {$source=$source."";}
            if($source =="") {$source=$row["source"];}
            $gene_alert = $colour." ".$drug." - Biomarker <b>".$row["biomarker"]."</b> + Genotype/Phenotype <b>".$row["snip"]."</b> , ".$source." <i>(".$row["version"].")</i>";

            $direct_action_info = '';
            $indirect_action_info = '';
            if (strlen($row["direct_action_info"])>10) {$direct_action_info =  "<br> - Further Information : ".$row["direct_action_info"];}
            if (strlen($row["indirect_action_info"])>10) {$indirect_action_info =  "<br> - Further Information : ".$row["indirect_action_info"];}
            if (strlen($row["direct_action"])>10) {$direct_action = "<br><div style='padding-left: 40px'>DIRECT ACTION : ".$row["direct_action"]."<br> - Therapeutic Recommendation : ".$row["direct_action_therapy"].$direct_action_info."</div>";} else {$direct_action = '';}
            if (strlen($row["indirect_action"])>10) {$indirect_action = "<br><div style='padding-left: 40px'>INDIRECT ACTION : ".$row["indirect_action"]."<br> - Therapeutic Recommendation : ".$row["indirect_action_therapy"].$indirect_action_info."</div>";} else {$indirect_action = '';}
            $actions = $direct_action.$indirect_action;

            if ($row["biomarker"]!="" && $row["snip"]!="") {
                $gene_alerts[$i][0] = $gene_alert;
                $gene_alerts[$i][1] = $actions;
                $i++;}
            }}

        if ($gene_alerts[0] == 0) {$gene_alerts[0] = "0 results";}

    return $gene_alerts;}


// ****************************************************************************************************************************************** //
// FOR SELECT BUTTONS (FIND, UPDATE, ADD OR FIND INTERACTIONS), ASSIGN : HBT ID = $hbtid, $client = QUERY DB, AND $_SESSION['client'] = $client
if ( (isset($_POST['find'])) || (isset($_POST['update'])) || (isset($_POST['add'])) || (isset($_POST['find_interactions'])) ) {
    $hbtid = $_POST['hbtid'];
    $client = findClient($hbtid); # TEST for ($j = 1; $j < 53; $j++) {echo $client[$j]."<br>";}
    $_SESSION['client'] = $client;} # TEST for ($j = 1; $j < 53; $j++) {echo $_SESSION['client'][$j]."<br>";}
# what does this do???
// ****************************************************************************************************************************************** //


// HTML HTML HTML HTML HTML HTML HTML HTML HTML HTML HTML HTML HTML HTML
?><!DOCTYPE html><html>
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"><title>Home Page</title><link href="04Style.css" rel="stylesheet" type="text/css"></head>
<?php // BANNER (TITLE + USER NAME + LOGOUT) ?>
<body class="loggedin">
<nav class="navtop">
    <div>
        <h1>Compass Interactions Database Prototype v<?php echo $_SESSION['phpversion']; ?></h1>
        <a style="color:#c1c4c8"><?php echo $_SESSION['fullname']; ?></a> # user name
        <a href="../login/logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a> # logout link
    </div>
</nav>


<?php // FORM (TABLES : CLIENT, DRUGS, DISEASES, TABS, SUMMARY) ?>
<form enctype="multipart/form-data" action="" method="post">
    <table style = 'font-size:15px; padding:10px; text-align:left'><tr><th>
                <table>

                    <tr><th valign="top">Client</th>
                        <th>
                            <?php
                            // Set VAR when to display Client + HideClient button
                            if (!isset($_SESSION['welcome4'])) {$_SESSION['welcome4'] = 1; $_SESSION['ViewClient'] = 1;} # use once variable - sets view client var to 1 (show client fields at launch)
                            if (isset($_POST['hideClient'])) {$_SESSION['ViewClient']=0;} # if HIDE button clicked then set view client var to 0
                            if (isset($_POST['viewClient'])) {$_SESSION['ViewClient']=1;} # if VIEW button clicked then set view client var to 1

                            // display Hide Client button if View Client var = 1 (ON)
                            ?><?php if ($_SESSION['ViewClient']==1) {?><input type="submit" name="hideClient" value="Hide Client" class="btn btn-primary"/><br><br><?php }

                            // display View Client button if View Client var = 0 (OFF)
                            else {if ($_SESSION['ViewClient']==0) {?><input type="submit" name="viewClient" value="View Client" class="btn btn-primary"/><br><br> <?php }}

                            // display Client fields if View Client var = 1 (ON)
                            if ($_SESSION['ViewClient']==1)

                                // HBT ID field
                            {?>HBT ID <input type="text" name="hbtid" id="hbtid" style="width:50px;" value = "<?php if (isset($_POST['clear'])) {echo "";} else {echo isset($_POST['hbtid']) ? $_POST['hbtid'] : '';}?>">
                                <?php // FIND Client button ?>
                                <input type="submit" name="find" value="Find" class="btn btn-primary"/>
                                <?php // UPDATE Client button ?>
                                <input type="submit" name="update" value="Update" class="btn btn-primary"/>
                                <?php // ADD Client button ?>
                                <input type="submit" name="add" value="Add" class="btn btn-primary"/><br><br>


                                <table>
                                    <?php // ERROR MESSAGES
                                    // IF HBT ID field empty AND Update button selected --> 'hbt id required'
                                    if ( (isset($_POST['update'])) && ($_POST['hbtid'] == '') )
                                    {echo "&nbsp".$red." HBT ID required&nbsp<br><br>";}

// IF HBT ID field empty AND Add/Find buttons selected --> 'hbt id required'
                                    else {if ( ( (isset($_POST['update'])) || (isset($_POST['add'])) || (isset($_POST['find'])) ) && ($_POST['hbtid'] == '') )
                                    {echo "&nbsp".$red." HBT ID required&nbsp<br><br>";}

// IF Client HBT ID not found --> 'not found in db'
                                    else {if ((isset($_POST['find'])) && ($client[0] == ""))
                                    {echo "&nbsp".$red." Error : ID '".$_POST['hbtid']."' not found&nbsp<br><br>";}}} ?>


                                    <?php # Client table 1/2 - sex, age, height and weight ?>
                                    <tr><th>Sex</th><th style="text-align:left">
                                            <select name="sex" id="sex" style="width:100px;"><option selected="selected">
                                                    <?php if (isset($_POST['clear'])) {echo "Choose one";} else {if (isset($_POST['sex']) && $_POST['sex'] != "Choose one" && (!isset($_POST['find']))) {echo $_POST['sex'];}
                                                    else {if ($client[41]!="" && (isset($_POST['find']))) {echo $client[41];}
                                                    else {echo "Choose one";}}}?></option>
                                                <?php foreach($sex as $item)
                                                {echo "<option value='$item'>$item</option>";} ?> </select <input name="sex" type="text"/>
                                        </th></tr>
                                    <tr><th>Age</th><th style="text-align:left">
                                            <select name="age" id="age" style="width:100px;"><option selected="selected">
                                                    <?php if (isset($_POST['clear'])) {echo "Choose one";}
                                                    else {if (isset($_POST['age']) && $_POST['age'] != "Choose one" && (!isset($_POST['find']))) {echo $_POST['age'];}
                                                    else {if ($client[42]!="" && (isset($_POST['find']))) {echo $client[42];}
                                                    else {echo "Choose one";}}}?></option>
                                                <?php foreach($age as $item) {echo "<option value='$item'>$item</option>";} ?> </select <input name="age" type="text"/>
                                        </th></tr>
                                    <tr><th>Height</th><th style="text-align:left">
                                            <input type="text" name="height1" id="height1" style="width:30px;" value = "<?php if (isset($_POST['clear'])) {echo "";} else {if (isset($_POST['height1']) && $_POST['height1'] != "" && (!isset($_POST['find']))) {echo $_POST['height1'];} else {if ($client[47]!="" && (isset($_POST['find']))) {echo (int)$client[47];}}} ?>">
                                            <span style="font-weight:normal"> ft&nbsp&nbsp</span>
                                            <input type="text" name="height2" id="height2" style="width:30px;" value = "<?php if (isset($_POST['clear'])) {echo "";} else {if (isset($_POST['height2']) && $_POST['height2'] != "" && (!isset($_POST['find']))) {echo $_POST['height2'];} else {if ($client[48]!="" && (isset($_POST['find']))) {echo (int)$client[48];}}} ?>">
                                            <span style="font-weight:normal"> in&nbsp&nbsp</span>
                                        </th></tr>
                                    <tr><th>Weight</th><th style="text-align:left">
                                            <input type="text" name="weight1" id="weight1" style="width:30px;" value = "<?php if (isset($_POST['clear'])) {echo "";} else {if (isset($_POST['weight1']) && $_POST['weight1'] != "" && (!isset($_POST['find']))) {echo $_POST['weight1'];} else {if ($client[49]!="" && (isset($_POST['find']))) {echo (int)$client[49];}}} ?>">
                                            <span style="font-weight:normal"> st&nbsp&nbsp</span>
                                            <input type="text" name="weight2" id="weight2" style="width:30px;" value = "<?php if (isset($_POST['clear'])) {echo "";} else {if (isset($_POST['weight2']) && $_POST['weight2'] != "" && (!isset($_POST['find']))) {echo $_POST['weight2'];} else {if ($client[50]!="" && (isset($_POST['find']))) {echo (int)$client[50];}}} ?>">
                                            <span style="font-weight:normal"> llb&nbsp&nbsp</span>

                                            <?php # calculate BMI
                                            # get height/weight from Db if FIND selected
                                            if (isset($_POST['find'])) {$height = (int)$client[47]*12+(int)$client[48]; $weight = (int)$client[49]*14+(int)$client[50];}
                                            # ELSE get height/weight from Post values
                                            else {$height = (int)$_POST['height1']*12+(int)$_POST['height2']; $weight = (int)$_POST['weight1']*14+(int)$_POST['weight2'];}

                                            if (isset($_POST['clear'])) {$height=='0'; $weight=='0'; }
                                            else {if ($height!='0' && $weight!='0')
                                            {$bmi = round( $weight/($height*$height)*703, 1);
                                                echo "&nbsp&nbspBMI <span style='font-weight:normal'>".$bmi." </span>";
                                                # add colour flag based on good, ok and bad BMI values
                                                if ($bmi < 18.5) {echo $orange;} else {if ($bmi > 30) {echo $red;} else {if ($bmi > 24.9) {echo $orange;} else {echo $green;}}}}}?>

                                            <?php # client table 2/2 - exercise, booze, fags, allergies, notes ?>
                                        </th></tr><tr><th>Exercise</th><th style="text-align:left">
                                            <select name="exercise" id="exercise" style="width:100px;"><option selected="selected">
                                                    <?php if (isset($_POST['clear'])) {echo "Choose one";}
                                                    else {if (isset($_POST['exercise']) && $_POST['exercise'] != "Choose one" && (!isset($_POST['find']))) {echo $_POST['exercise'];}
                                                    else {if ($client[44]!="" && (isset($_POST['find']))) {echo $client[44];}
                                                    else {echo "Choose one";}}} ?></option>
                                                <?php foreach($exercise as $item){echo "<option value='$item'>$item</option>";} ?> </select <input name="exercise" type="text"/>
                                        </th></tr>
                                    <tr><th>Alcohol</th><th style="text-align:left">
                                            <select name="alcohol" id="alcohol" style="width:100px;"><option selected="selected">
                                                    <?php if (isset($_POST['clear'])) {echo "Choose one";}
                                                    else {if (isset($_POST['alcohol']) && $_POST['alcohol'] != "Choose one" && (!isset($_POST['find']))) {echo $_POST['alcohol'];}
                                                    else {if ($client[45]!="" && (isset($_POST['find']))) {echo $client[45];}
                                                    else {echo "Choose one";}}} ?></option>
                                                <?php foreach($alcohol as $item){echo "<option value='$item'>$item</option>";} ?> </select <input name="alcohol" type="text"/><span style="font-weight:normal"> units pw</span>
                                        </th></tr>
                                    <tr><th>Smoking</th><th style="text-align:left">
                                            <select name="smoking" id="smoking" style="width:100px;"><option selected="selected">
                                                    <?php if (isset($_POST['clear'])) {echo "Choose one";}
                                                    else {if (isset($_POST['smoking']) && $_POST['smoking'] != "Choose one" && (!isset($_POST['find']))) {echo $_POST['smoking'];}
                                                    else {if ($client[46]!="" && (isset($_POST['find']))) {echo $client[46];}
                                                    else {echo "Choose one";}}} ?></option><?php foreach($smoking as $item){echo "<option value='$item'>$item</option>";} ?> </select <input name="smoking" type="text"/>
                                        </th></tr>
                                    <tr><th>Allergies</th><th style="text-align:left">
                                            <select name="allergies" id="allergies" style="width:183px;"><option selected="selected">
                                                    <?php if (isset($_POST['clear'])) {echo "Choose one";}
                                                    else {if (isset($_POST['allergies']) && $_POST['allergies'] != "Choose one" && (!isset($_POST['find']))) {echo $_POST['allergies'];}
                                                    else {if ($client[43]!="" && (isset($_POST['find']))) {echo $client[43];}
                                                    else {echo "Choose one";}}} ?></option><?php foreach($allergies as $item){echo "<option value='$item'>$item</option>";} ?> </select <input name="allergies" type="text"/>
                                        </th></tr>
                                </table>


                                <?php // onclick="submit_form(); clears Notes field after form submitted leaving it empty ?>
                                <textarea name="notes" cols="30" rows="5" type="text" id="notes" style="width:238px" placeholder="Add client notes here - no limit" onclick="submit_form();  value = "<?php echo isset($_POST['notes']) ? $_POST['notes'] : '' ?>"></textarea><br>


                            <?php }
                            // add spaces to stretch column with dropdown lists + number to avoid number being added to separate line //
                            ?><span style="width:250px;">&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</span>
                        </th></tr>


                    <?php # DRUG DROP DOWN LIST ?>
                    <tr><th valign="top">Drugs
                            <br><br><input type="submit" name="addDrug" value="+ 1" class="btn btn-primary"/></th><th>
                            <?php

                            // Get drugs
                            $drugslist = getDrugsList();
                            if (!isset($_SESSION['welcome2'])) {$_SESSION['welcome2'] = 1; $_SESSION['drugSelectorNr'] = 6; } # use once variable - sets drug count var to 6 (at launch)
                            if ( (isset($_POST['addDrug'])) && ($_SESSION['drugSelectorNr']<20) ) {$_SESSION['drugSelectorNr']++;} # add 1 drug count if +1 selected
                            else {if (isset($_POST['find'])) {$_SESSION['drugSelectorNr'] = &findDrugCount($client);} # set drug count to findDrugCount if FIND, UPDATE or ADD
                            else {if (isset($_POST['clear'])) {$_SESSION['drugSelectorNr'] = 6;}}}

                            // Display drugs
                            for ($j = 1; $j < $_SESSION['drugSelectorNr'] + 1; $j++)
                            {?> <span style="font-weight:normal"><?php echo str_pad($j,2,'0', STR_PAD_LEFT);?> </span><?php # number drugs (with zero left of number if < 10)
                                ?><select name="<?php echo 'drug'.$j; ?>" id="<?php echo 'drug'.$j; ?>" style="width:220px;"><option selected="selected"><?php
                                if (isset($_POST['clear'])) {echo "Choose one";}
                                else {if (isset($_POST['drug'.$j]) && $_POST['drug'.$j] != "Choose one" && (!isset($_POST['find']))) {echo $_POST['drug'.$j];}
                                else {if ($client[$j]!="" && $client[$j]!="Choose one" && (isset($_POST['find']))) {echo $client[$j];}
                                else {echo "Choose one";}}} ?>
                                </option><?php foreach($drugslist as $item){echo "<option value='$item'>$item</option>";} ?></select <input name="<?php echo 'drug'.$j; ?>" type="text"/><br>
                            <?php }
                            if ( (isset($_POST['addDrug'])) && ($_SESSION['drugSelectorNr']==20) ) {echo "<br>&nbsp".$orange." 20 drugs is the max limit<br>";}
                            ?><br>


                            <?php # DISEASE DROP DOWN LIST ?>
                    <tr><th valign="top">Diseases
                            <br><br><input type="submit" name="addDisease" value="+ 1" class="btn btn-primary"/></th><th>
                            <?php

                            // Get diseases
                            $diseaseslist = getDiseasesList();
                            if (!isset($_SESSION['welcome3'])) {$_SESSION['welcome3'] = 1; $_SESSION['diseaseSelectorNr'] = 3; } # use once variable - sets disease count var to 1 (at launch)
                            if ( (isset($_POST['addDisease'])) && ($_SESSION['diseaseSelectorNr']<10) ) {$_SESSION['diseaseSelectorNr']++;} # add 1 drug count if +1 selected
                            else {if (isset($_POST['find'])) {$_SESSION['diseaseSelectorNr'] = &findDiseaseCount($client);} # set drug count to findDrugCount if FIND, UPDATE or ADD
                            else {if (isset($_POST['clear'])) {$_SESSION['diseaseSelectorNr'] = 3;}}}


                            // Display diseases
                            for ($j = 1; $j < $_SESSION['diseaseSelectorNr'] + 1; $j++) {
                                ?><span style="font-weight:normal"><?php echo str_pad($j,2,'0', STR_PAD_LEFT);?> </span><?php
                                ?><select name="<?php echo 'disease'.$j; ?>" id="<?php echo 'disease'.$j; ?>" style="width:220px;"><option selected="selected"><?php
                                if (isset($_POST['clear'])) {echo "Choose one";}
                                else {if (isset($_POST['disease'.$j]) && $_POST['disease'.$j] != "Choose one" && (!isset($_POST['find']))) {echo $_POST['disease'.$j];}
                                else {if ($client[$j+20]!="" && (isset($_POST['find']))) {echo $client[$j+20];}
                                else {echo "Choose one";}}} ?>
                                </option><?php foreach($diseaseslist as $item){echo "<option value='$item'>$item</option>";} ?></select <input name="<?php echo 'disease'.$j; ?>" type="text"/><br>
                            <?php }
                            if ( (isset($_POST['addDisease'])) && ($_SESSION['diseaseSelectorNr']==10) ) {echo "<br>&nbsp".$orange." 10 diseases is the max limit<br>";}
                            ?><br>

                    <tr><th valign="top">Search</th><th><input type="submit" name="find_interactions" value="Find Interactions" class="btn btn-primary"/> <input type="submit" name="clear" value="CLS" class="btn btn-primary"/> <input type="submit" name="help" value="Help" class="btn btn-primary"/><br>

                            <?php

                            # show 'at least one drug required' if none entered and Search button selected
                            if ( (isset($_POST['find_interactions'])) && ($_POST['drug1'] == "Choose one") && ($_POST['drug2'] == "Choose one") && ($_POST['drug3'] == "Choose one") && ($_POST['drug4'] == "Choose one") && ($_POST['drug5'] == "Choose one") && ($_POST['drug6'] == "Choose one") ) {echo "<br>&nbsp".$red." Enter at least one drug&nbsp<br>";}
# show '2 or more drugs for interactions' if one only entered and Search button selected
                            else {if ( (isset($_POST['find_interactions'])) && ($_POST['drug2'] == "Choose one") ) {echo "<br>&nbsp".$orange." Select 2 or more drugs for interactions&nbsp<br>";}}


                            //CALL FUNCTIONS - GET DRUG CODES, LINES, SPECIFIC INTER</th><th>ACTIONS + ALL INTERACTIONS
                            if ( (isset($_POST['add'])) || (isset($_POST['update'])) || (isset($_POST['find_interactions'])) ) {
                                $drug1 = $_POST['drug1'];$drug2 = $_POST['drug2'];$drug3 = $_POST['drug3'];$drug4 = $_POST['drug4'];$drug5 = $_POST['drug5'];$drug6 = $_POST['drug6']; $drug7 = $_POST['drug7'];$drug8 = $_POST['drug8'];$drug9 = $_POST['drug9'];$drug10 = $_POST['drug10'];$drug11 = $_POST['drug11'];$drug12 = $_POST['drug12']; $drug13 = $_POST['drug13']; $drug14 = $_POST['drug14']; $drug15 = $_POST['drug15']; $drug16 = $_POST['drug16']; $drug17 = $_POST['drug17']; $drug18 = $_POST['drug18']; $drug19 = $_POST['drug19']; $drug20 = $_POST['drug20'];
                                $disease1 = $_POST['disease1'];$disease2 = $_POST['disease2'];$disease3 = $_POST['disease3'];$disease4 = $_POST['disease4'];$disease5 = $_POST['disease5'];$disease6 = $_POST['disease6'];$disease7 = $_POST['disease7'];$disease8 = $_POST['disease8'];$disease9 = $_POST['disease9'];$disease10 = $_POST['disease10'];}
                            else {$drug1 = $client[1];$drug2 = $client[2];$drug3 = $client[3];$drug4 = $client[4];$drug5 = $client[5];$drug6 = $client[6];$drug7 = $client[7];$drug8 = $client[8];$drug9 = $client[9];$drug10 = $client[10];$drug11 = $client[11];$drug12 = $client[12]; $drug13 = $client[13]; $drug14 = $client[14]; $drug15 = $client[15]; $drug16 = $client[16]; $drug17 = $client[17]; $drug18 = $client[18]; $drug19 = $client[19]; $drug20 = $client[20]; $disease1 = $client[21];$disease2 = $client[22];$disease3 = $client[23];$disease4 = $client[24];$disease5 = $client[25];$disease6 = $client[26];$disease7 = $client[27];$disease8 = $client[28];$disease9 = $client[29];$disease10 = $client[30];}

                            $drugs = array(0,$drug1,$drug2,$drug3,$drug4,$drug5,$drug6,$drug7,$drug8,$drug9,$drug10,$drug11,$drug12,$drug13,$drug14,$drug15,$drug16,$drug17,$drug18,$drug19,$drug20);
                            $_SESSION['drugs'] = $drugs;
                            $diseases = array(0,$disease1,$disease2,$disease3,$disease4,$disease5,$disease6,$disease7,$disease8,$disease9,$disease10);
                            $_SESSION['diseases'] = $diseases;
                            for ($i = 1; $i < $_SESSION['drugSelectorNr'] + 1; $i++) {
                                if ($drugs[$i] != "Choose one") {
                                    $codes =& getDrugCodes($drugs[$i]);
                                    $rx_id[$i] = $codes[0];$spl_id[$i] = $codes[1];$DB_id[$i] = $codes[2];
                                    $lines[$i] =& getDrugSourceIds($drugs[$i]);
                                    $allinteractions[$i] = &getAllDrugDrugInteractions($rx_id[$i]);
                                    $drug_alert[$i] =& getDrugAlerts($rx_id[$i]);
                                    $dailymed_page[$i] =& getDailyMedPage($spl_id[$i]);
                                    $drugbank_page[$i] =& getDrugbankPage($DB_id[$i]);}}

                            // CHECK FOR DUPLICATE DRUGS / DISEASES
                            if ( (count(array_diff($_SESSION['drugs'], ["Choose one","0",""])) != count(array_unique(array_diff($drugs, ["Choose one","0",""])))) && (!isset($_POST['clear'])) ) {echo "<br>&nbsp".$orange." Duplicate drugs selected&nbsp";}
                            if ( (count(array_diff($_SESSION['diseases'], ["Choose one","0",""])) != count(array_unique(array_diff($diseases, ["Choose one","0",""]))) ) && (!isset($_POST['clear'])) ) {echo "<br>&nbsp".$orange." Duplicate diseases selected&nbsp";}
                            # +1 drug/disease does not activate error message above - need extra condition to check for addDrug / addDisease and $client

                            ?></th></tr></table>


            <th><?php
                // Drug-Drug Summary Section
                if( (!isset($_POST['hideClient'])) && (!isset($_POST['viewClient'])) ) {
                    $all_interactions = '';
                    $uniquelist = array();
                    for ($j = 1; $j < $_SESSION['drugSelectorNr'] + 1; $j++) {
                        for ($k = $j; $k < $_SESSION['drugSelectorNr'] + 1; $k++) {
                            if ($drugs[$j] != "Choose one" && $drugs[$k] != "Choose one" && $drugs[$j] != $drugs[$k]) {
                                $interaction = getSelectDrugDrugInteraction($rx_id[$j], $rx_id[$k]);
                                $interaction_line = $interaction[1] . " " . $drugs[$j] . " - " . $drugs[$k] . " : " . $interaction[4];
                                if ($interaction[3] != '0 results') {
                                    $all_interactions = $all_interactions . $interaction_line . "<br>";
                                }
                            }
                        }
                    }
                    if ($all_interactions == '') {
                        $all_interactions = '0 results';
                    }
                    $all_interactionssummary = 'Drug-Drug</b><br><span style="font-weight:normal">' . $all_interactions . '</span>';

                    // Drug-Disease Summary Section
                    $all_interactions = '';
                    foreach ($diseases as $disease) {
                        foreach ($drugs as $drug) {
                            if ($drug != "Choose one" && $disease != "Choose one") {
                                $rx_id = getRxCode($drug);
                                $icd = getDiseaseCode($disease);
                                $interaction = getDiseaseInteractions($rx_id, $icd);
                                $interaction_line = $interaction[1] . " " . $drug . " - " . $disease . " : " . $interaction[3] . " - " . $interaction[4] . " <i>(" . $interaction[5] . ")</i><br>";
                                if ($interaction[5] != '0 results') {
                                    $all_interactions = $all_interactions . $interaction_line;
                                }
                            }
                        }
                    }
                    if ($all_interactions == '') {
                        $all_interactions = '0 results<br>';
                    }
                    $drug_dis_sum = '<br><b>Drug-Disease</b><br><span style="font-weight:normal">' . $all_interactions . '</span>';

                    // Drug-Gene Summary Section
                    $all_interactions = "";
                    $interactions = array();
                    foreach ($drugs as $drug) {
                        if ($drug != "Choose one"){
                        $interactions = getGeneInteractions($drug);
                        if ($interactions[0] != "0 results"){
                            for ($j = 0; $j < count($interactions)+1; $j++) {
                                $actions = $interactions[$j][1];
                                $gene_alert = $interactions[$j][0];
                                if ($j>1) {$gene_alert = "<br>".$gene_alert;}
                                $all_interactions = $all_interactions.$gene_alert."<br>".$actions;
                    }}}}

                    $drug_gene_sum = '<br><b>Drug-Gene</b><br><span style="font-weight:normal">' . $all_interactions . '</span>';


                    // Top Drug Alerts/Tips Section
                    for ($i = 0; $i < 4; $i++) {
                        if ($drug_alert[$i] != "0 results") {
                            $allalerts = $allalerts . $drug_alert[$i];
                        }
                    }
                    if ($allalerts == "") {
                        $allalerts = $allalerts . "0 results<br>";
                    }
                    $topdrugtips = "<br><b>Drug Alerts/Tips</b><br><span style='font-weight:normal'>" . $allalerts . "</span><br>";

                    // Top Disease Tips Section
                    $topdiseasetips = '<b>Disease Alerts/Tips</b><br><span style="font-weight:normal">0 results</span><br>';

                    $_SESSION['summary'] = "SUMMARY&nbsp&nbsp&nbsp&nbsp&nbspInteractions & Tips/Alerts<br><br>" . $all_interactionssummary . $drug_dis_sum . $drug_gene_sum . $topdrugtips . $topdiseasetips;
                }
                {?> <th style = 'padding:5px; text-align:left; width:1000px; background-color:#ddd' valign="top"><?php }

                if ((isset($_POST['add'])) && ($_POST['hbtid'] != '')) {
                    $client[0] = $_POST['hbtid'];
                    #$_SESSION['client'] = $client;
                    if ($_POST['drug1'] == 'Choose one') {$client[1] = '';} else {$client[1] = $_POST['drug1'];}
                    if ($_POST['drug2'] == 'Choose one') {$client[2] = '';} else {$client[2] = $_POST['drug2'];}
                    if ($_POST['drug3'] == 'Choose one') {$client[3] = '';} else {$client[3] = $_POST['drug3'];}
                    if ($_POST['drug4'] == 'Choose one') {$client[4] = '';} else {$client[4] = $_POST['drug4'];}
                    if ($_POST['drug5'] == 'Choose one') {$client[5] = '';} else {$client[5] = $_POST['drug5'];}
                    if ($_POST['drug6'] == 'Choose one') {$client[6] = '';} else {$client[6] = $_POST['drug6'];}
                    if ($_POST['drug7'] == 'Choose one') {$client[7] = '';} else {$client[7] = $_POST['drug7'];}
                    if ($_POST['drug8'] == 'Choose one') {$client[8] = '';} else {$client[8] = $_POST['drug8'];}
                    if ($_POST['drug9'] == 'Choose one') {$client[9] = '';} else {$client[9] = $_POST['drug9'];}
                    if ($_POST['drug10'] == 'Choose one') {$client[10] = '';} else {$client[10] = $_POST['drug10'];}
                    if ($_POST['drug11'] == 'Choose one') {$client[11] = '';} else {$client[11] = $_POST['drug11'];}
                    if ($_POST['drug12'] == 'Choose one') {$client[12] = '';} else {$client[12] = $_POST['drug12'];}
                    if ($_POST['drug13'] == 'Choose one') {$client[13] = '';} else {$client[13] = $_POST['drug13'];}
                    if ($_POST['drug14'] == 'Choose one') {$client[14] = '';} else {$client[14] = $_POST['drug14'];}
                    if ($_POST['drug15'] == 'Choose one') {$client[15] = '';} else {$client[15] = $_POST['drug15'];}
                    if ($_POST['drug16'] == 'Choose one') {$client[16] = '';} else {$client[16] = $_POST['drug16'];}
                    if ($_POST['drug17'] == 'Choose one') {$client[17] = '';} else {$client[17] = $_POST['drug17'];}
                    if ($_POST['drug18'] == 'Choose one') {$client[18] = '';} else {$client[18] = $_POST['drug18'];}
                    if ($_POST['drug19'] == 'Choose one') {$client[19] = '';} else {$client[19] = $_POST['drug19'];}
                    if ($_POST['drug20'] == 'Choose one') {$client[20] = '';} else {$client[20] = $_POST['drug20'];}
                    if ($_POST['disease1'] == 'Choose one') {$client[21] = '';} else {$client[21] = $_POST['disease1'];}
                    if ($_POST['disease2'] == 'Choose one') {$client[22] = '';} else {$client[22] = $_POST['disease2'];}
                    if ($_POST['disease3'] == 'Choose one') {$client[23] = '';} else {$client[23] = $_POST['disease3'];}
                    if ($_POST['disease4'] == 'Choose one') {$client[24] = '';} else {$client[24] = $_POST['disease4'];}
                    if ($_POST['disease5'] == 'Choose one') {$client[25] = '';} else {$client[25] = $_POST['disease5'];}
                    if ($_POST['disease6'] == 'Choose one') {$client[26] = '';} else {$client[26] = $_POST['disease6'];}
                    if ($_POST['disease7'] == 'Choose one') {$client[27] = '';} else {$client[27] = $_POST['disease7'];}
                    if ($_POST['disease8'] == 'Choose one') {$client[28] = '';} else {$client[28] = $_POST['disease8'];}
                    if ($_POST['disease9'] == 'Choose one') {$client[29] = '';} else {$client[29] = $_POST['disease9'];}
                    if ($_POST['disease10'] == 'Choose one') {$client[30] = '';} else {$client[30] = $_POST['disease10'];}
                    $client[40] = 'Active';
                    if ($_POST['sex'] == 'Choose one') {$client[41] = '';} else {$client[41] = $_POST['sex'];}
                    if ($_POST['age'] == 'Choose one') {$client[42] = '';} else {$client[42] = $_POST['age'];}
                    if ($_POST['allergies'] == 'Choose one') {$client[43] = '';} else {$client[43] = $_POST['allergies'];}
                    if ($_POST['exercise'] == 'Choose one') {$client[44] = '';} else {$client[44] = $_POST['exercise'];}
                    if ($_POST['alcohol'] == 'Choose one') {$client[45] = '';} else {$client[45] = $_POST['alcohol'];}
                    if ($_POST['smoking'] == 'Choose one') {$client[46] = '';} else {$client[46] = $_POST['smoking'];}
                    if ($_POST['height1'] == 'Choose one') {$client[47] = '';} else {$client[47] = $_POST['height1'];}
                    if ($_POST['height2'] == 'Choose one') {$client[48] = '';} else {$client[48] = $_POST['height2'];}
                    if ($_POST['weight1'] == 'Choose one') {$client[49] = '';} else {$client[49] = $_POST['weight1'];}
                    if ($_POST['weight2'] == 'Choose one') {$client[50] = '';} else {$client[50] = $_POST['weight2'];}
                    if (isset($bmi)) {$client[51] = $bmi;} else {$client[51] = '';}

                    if ($_POST['notes'] != "") {$notes = "NOTES : ".$_POST['notes'];} else {$notes = "";}

                    $client[52] = "<b>Baseline</b>&nbsp&nbsp&nbsp".date("d/m/Y")."&nbsp&nbsp<i>".$_SESSION['fullname']."</i><li>DRUGS : ".($client[1]!="" ? $client[1] : "").($client[2]!="" ? "&nbsp&nbsp-&nbsp&nbsp".$client[2] : "").($client[3]!="" ? "&nbsp&nbsp-&nbsp&nbsp".$client[3] : "").($client[4]!="" ? "&nbsp&nbsp-&nbsp&nbsp".$client[4] : "").($client[5]!="" ? "&nbsp&nbsp-&nbsp&nbsp".$client[5] : "").($client[6]!="" ? "&nbsp&nbsp-&nbsp&nbsp".$client[6] : "").($client[7]!="" ? "&nbsp&nbsp-&nbsp&nbsp".$client[7] : "").($client[8]!="" ? "&nbsp&nbsp-&nbsp&nbsp".$client[8] : "").($client[9]!="" ? "&nbsp&nbsp-&nbsp&nbsp".$client[9] : "").($client[10]!="" ? "&nbsp&nbsp-&nbsp&nbsp".$client[10] : "").($client[11]!="" ? "&nbsp&nbsp-&nbsp&nbsp".$client[11] : "").($client[12]!="" ? "&nbsp&nbsp-&nbsp&nbsp".$client[12] : "").($client[13]!="" ? "&nbsp&nbsp-&nbsp&nbsp".$client[13] : "").($client[14]!="" ? "&nbsp&nbsp-&nbsp&nbsp".$client[14] : "").($client[15]!="" ? "&nbsp&nbsp-&nbsp&nbsp".$client[15] : "").($client[16]!="" ? "&nbsp&nbsp-&nbsp&nbsp".$client[16] : "").($client[17]!="" ? "&nbsp&nbsp-&nbsp&nbsp".$client[17] : "").($client[18]!="" ? "&nbsp&nbsp-&nbsp&nbsp".$client[18] : "").($client[19]!="" ? "&nbsp&nbsp-&nbsp&nbsp".$client[19] : "").($client[20]!="" ? "&nbsp&nbsp-&nbsp&nbsp".$client[20] : "")."</li><li>DISEASES : ".($client[21]!="" ? $client[21] : "").($client[22]!="" ? "&nbsp&nbsp-&nbsp&nbsp".$client[22] : "").($client[23]!="" ? "&nbsp&nbsp-&nbsp&nbsp".$client[23] : "").($client[24]!="" ? "&nbsp&nbsp-&nbsp&nbsp".$client[24] : "").($client[25]!="" ? "&nbsp&nbsp-&nbsp&nbsp".$client[25] : "").($client[26]!="" ? "&nbsp&nbsp-&nbsp&nbsp".$client[26] : "").($client[27]!="" ? "&nbsp&nbsp-&nbsp&nbsp".$client[27] : "").($client[28]!="" ? "&nbsp&nbsp-&nbsp&nbsp".$client[28] : "").($client[29]!="" ? "&nbsp&nbsp-&nbsp&nbsp".$client[29] : "").($client[30]!="" ? "&nbsp&nbsp-&nbsp&nbsp".$client[30] : "")."</li><li>LIFESTYLE : ".($client[44]!="" ? "Exercise : ".$client[44] : "").($client[45]!="" ? "&nbsp&nbsp-&nbsp&nbspAlcohol : ".$client[45]." units" : "").($client[46]!="" ? "&nbsp&nbsp-&nbsp&nbspSmoking : ".$client[46] : "").($client[49]!="" ? "&nbsp&nbsp-&nbsp&nbspWeight : ".$client[49]."st" : "").($client[50]!="" ? " ".$client[50]."llbs" : "").($client[51]!="" ? "&nbsp&nbsp-&nbsp&nbspBMI : ".$client[51] : "")."</li>".($notes!="" ? "<li>".$notes."</li>" : "")."</ul><br>".$_SESSION['client'][52];
                    $_SESSION['client'][52] = $client[52];

                    addClient($client);}


                if( (isset($_POST['update'])) && ($_POST['hbtid'] != '') ) {
                    #$client = $_SESSION['client'];
                    if ($_POST['drug1'] == 'Choose one') {$client[1] = '';} else {$client[1] = $_POST['drug1'];}
                    if ($_POST['drug2'] == 'Choose one') {$client[2] = '';} else {$client[2] = $_POST['drug2'];}
                    if ($_POST['drug3'] == 'Choose one') {$client[3] = '';} else {$client[3] = $_POST['drug3'];}
                    if ($_POST['drug4'] == 'Choose one') {$client[4] = '';} else {$client[4] = $_POST['drug4'];}
                    if ($_POST['drug5'] == 'Choose one') {$client[5] = '';} else {$client[5] = $_POST['drug5'];}
                    if ($_POST['drug6'] == 'Choose one') {$client[6] = '';} else {$client[6] = $_POST['drug6'];}
                    if ($_POST['drug7'] == 'Choose one') {$client[7] = '';} else {$client[7] = $_POST['drug7'];}
                    if ($_POST['drug8'] == 'Choose one') {$client[8] = '';} else {$client[8] = $_POST['drug8'];}
                    if ($_POST['drug9'] == 'Choose one') {$client[9] = '';} else {$client[9] = $_POST['drug9'];}
                    if ($_POST['drug10'] == 'Choose one') {$client[10] = '';} else {$client[10] = $_POST['drug10'];}
                    if ($_POST['drug11'] == 'Choose one') {$client[11] = '';} else {$client[11] = $_POST['drug11'];}
                    if ($_POST['drug12'] == 'Choose one') {$client[12] = '';} else {$client[12] = $_POST['drug12'];}
                    if ($_POST['drug13'] == 'Choose one') {$client[13] = '';} else {$client[13] = $_POST['drug13'];}
                    if ($_POST['drug14'] == 'Choose one') {$client[14] = '';} else {$client[14] = $_POST['drug14'];}
                    if ($_POST['drug15'] == 'Choose one') {$client[15] = '';} else {$client[15] = $_POST['drug15'];}
                    if ($_POST['drug16'] == 'Choose one') {$client[16] = '';} else {$client[16] = $_POST['drug16'];}
                    if ($_POST['drug17'] == 'Choose one') {$client[17] = '';} else {$client[17] = $_POST['drug17'];}
                    if ($_POST['drug18'] == 'Choose one') {$client[18] = '';} else {$client[18] = $_POST['drug18'];}
                    if ($_POST['drug19'] == 'Choose one') {$client[19] = '';} else {$client[19] = $_POST['drug19'];}
                    if ($_POST['drug20'] == 'Choose one') {$client[20] = '';} else {$client[20] = $_POST['drug20'];}
                    if ($_POST['disease1'] == 'Choose one') {$client[21] = '';} else {$client[21] = $_POST['disease1'];}
                    if ($_POST['disease2'] == 'Choose one') {$client[22] = '';} else {$client[22] = $_POST['disease2'];}
                    if ($_POST['disease3'] == 'Choose one') {$client[23] = '';} else {$client[23] = $_POST['disease3'];}
                    if ($_POST['disease4'] == 'Choose one') {$client[24] = '';} else {$client[24] = $_POST['disease4'];}
                    if ($_POST['disease5'] == 'Choose one') {$client[25] = '';} else {$client[25] = $_POST['disease5'];}
                    if ($_POST['disease6'] == 'Choose one') {$client[26] = '';} else {$client[26] = $_POST['disease6'];}
                    if ($_POST['disease7'] == 'Choose one') {$client[27] = '';} else {$client[27] = $_POST['disease7'];}
                    if ($_POST['disease8'] == 'Choose one') {$client[28] = '';} else {$client[28] = $_POST['disease8'];}
                    if ($_POST['disease9'] == 'Choose one') {$client[29] = '';} else {$client[29] = $_POST['disease9'];}
                    if ($_POST['disease10'] == 'Choose one') {$client[30] = '';} else {$client[30] = $_POST['disease10'];}
                    $client[40] = 'active';
                    if ($_POST['sex'] == 'Choose one') {$client[41] = $_SESSION['client'][41];} else {$client[41] = $_POST['sex'];}
                    if ($_POST['age'] == 'Choose one') {$client[42] = $_SESSION['client'][42];} else {$client[42] = $_POST['age'];}
                    if ($_POST['allergies'] == 'Choose one') {$client[43] = '';} else {$client[43] = $_POST['allergies'];}
                    if ($_POST['exercise'] == 'Choose one') {$client[44] = '';} else {$client[44] = $_POST['exercise'];}
                    if ($_POST['alcohol'] == 'Choose one') {$client[45] = '';} else {$client[45] = $_POST['alcohol'];}
                    if ($_POST['smoking'] == 'Choose one') {$client[46] = '';} else {$client[46] = $_POST['smoking'];}
                    if ($_POST['height1'] == '') {$client[47] = '';} else {$client[47] = $_POST['height1'];}
                    if ($_POST['height2'] == '') {$client[48] = '';} else {$client[48] = $_POST['height2'];}
                    if ($_POST['weight1'] == '') {$client[49] = '';} else {$client[49] = $_POST['weight1'];}
                    if ($_POST['weight2'] == '') {$client[50] = '';} else {$client[50] = $_POST['weight2'];}
                    if (isset($bmi)) {$client[51] = $bmi;} else {$client[51] = '';}

                    if ($_POST['notes'] != "") {$notes = "NOTES : ".$_POST['notes'];} else {$notes = "";}

                    $client[52] = "<b>Follow Up : </b>".date("d/m/Y")."&nbsp&nbsp<i>".$_SESSION['fullname']."</i><li>DRUGS : ".($client[1]!="" ? $client[1] : "").($client[2]!="" ? "&nbsp&nbsp-&nbsp&nbsp".$client[2] : "").($client[3]!="" ? "&nbsp&nbsp-&nbsp&nbsp".$client[3] : "").($client[4]!="" ? "&nbsp&nbsp-&nbsp&nbsp".$client[4] : "").($client[5]!="" ? "&nbsp&nbsp-&nbsp&nbsp".$client[5] : "").($client[6]!="" ? "&nbsp&nbsp-&nbsp&nbsp".$client[6] : "").($client[7]!="" ? "&nbsp&nbsp-&nbsp&nbsp".$client[7] : "").($client[8]!="" ? "&nbsp&nbsp-&nbsp&nbsp".$client[8] : "").($client[9]!="" ? "&nbsp&nbsp-&nbsp&nbsp".$client[9] : "").($client[10]!="" ? "&nbsp&nbsp-&nbsp&nbsp".$client[10] : "").($client[11]!="" ? "&nbsp&nbsp-&nbsp&nbsp".$client[11] : "").($client[12]!="" ? "&nbsp&nbsp-&nbsp&nbsp".$client[12] : "").($client[13]!="" ? "&nbsp&nbsp-&nbsp&nbsp".$client[13] : "").($client[14]!="" ? "&nbsp&nbsp-&nbsp&nbsp".$client[14] : "").($client[15]!="" ? "&nbsp&nbsp-&nbsp&nbsp".$client[15] : "").($client[16]!="" ? "&nbsp&nbsp-&nbsp&nbsp".$client[16] : "").($client[17]!="" ? "&nbsp&nbsp-&nbsp&nbsp".$client[17] : "").($client[18]!="" ? "&nbsp&nbsp-&nbsp&nbsp".$client[18] : "").($client[19]!="" ? "&nbsp&nbsp-&nbsp&nbsp".$client[19] : "").($client[20]!="" ? "&nbsp&nbsp-&nbsp&nbsp".$client[20] : "")."</li><li>DISEASES : ".($client[21]!="" ? $client[21] : "").($client[22]!="" ? "&nbsp&nbsp-&nbsp&nbsp".$client[22] : "").($client[23]!="" ? "&nbsp&nbsp-&nbsp&nbsp".$client[23] : "").($client[24]!="" ? "&nbsp&nbsp-&nbsp&nbsp".$client[24] : "").($client[25]!="" ? "&nbsp&nbsp-&nbsp&nbsp".$client[25] : "").($client[26]!="" ? "&nbsp&nbsp-&nbsp&nbsp".$client[26] : "").($client[27]!="" ? "&nbsp&nbsp-&nbsp&nbsp".$client[27] : "").($client[28]!="" ? "&nbsp&nbsp-&nbsp&nbsp".$client[28] : "").($client[29]!="" ? "&nbsp&nbsp-&nbsp&nbsp".$client[29] : "").($client[30]!="" ? "&nbsp&nbsp-&nbsp&nbsp".$client[30] : "")."</li><li>LIFESTYLE : ".($client[44]!="" ? "Exercise : ".$client[44] : "").($client[45]!="" ? "&nbsp&nbsp-&nbsp&nbspAlcohol : ".$client[45]." units" : "").($client[46]!="" ? "&nbsp&nbsp-&nbsp&nbspSmoking : ".$client[46] : "").($client[49]!="" ? "&nbsp&nbsp-&nbsp&nbspWeight : ".$client[49]."st" : "").($client[50]!="" ? " ".$client[50]."llbs" : "").($client[51]!="" ? "&nbsp&nbsp-&nbsp&nbspBMI : ".$client[51] : "")."</li>".($notes!="" ? "<li>".$notes."</li>" : "")."</ul><br>".$_SESSION['client'][52];

                    $_SESSION['client'][52] = $client[52];

                    updateClient($client);}


                // WHEN TO SHOW HELP - WHEN UPDATE, ADD OR FIND BUTTON SELECTED AND HBT ID MISSING OR...WHEN FIND BUTTON SELECTED AND HBT ID NOT FOUND IN DATABASE OR...WHEN SEARCH BUTTON SELECTED AND DRUG 1 OR 2 MISSING OR......WHEN CLS BUTTON SELECTED : IF ANY TRUE --> SHOW HELP
                if ( ( ( (isset($_POST['update'])) || (isset($_POST['add'])) || (isset($_POST['find'])) ) && ($_POST['hbtid'] == '') ) || ((isset($_POST['find'])) && ($client[0] == "")) || ( (isset($_POST['find_interactions'])) && ( ($_POST['drug1'] == "Choose one") || ($_POST['drug2'] == "Choose one") ) ) || (isset($_POST['clear'])) || (isset($_POST['help'])) )
                {$showhelp = 1;} else {$showhelp = 0;}
                // Once only welcome message and set help to show
                if (isset($_SESSION['fullname'])) {$array = explode(' ', $_SESSION['fullname']); $welcome = "Welcome, ".array_shift($array)."<br><br><br>";}
                if (!isset($_SESSION['welcome1'])) {$_SESSION['welcome1'] = 1; echo $welcome; $showhelp = 1;} # use once variable - sets view help var to 1 (show help at launch)
                if ($showhelp == 1) {echo $help1;} else {echo $_SESSION['summary'];}

                ?>
            </th></tr></table>
</form>
<?php

// DISPLAY TABS
?><div class="tab">
    <?php
    if ($showhelp == 0) {

        if ( (isset($_POST['addDrug'])) || (isset($_POST['addDisease'])) ) {echo "<br><b>&nbsp&nbspTo display additional drugs/diseases detailed tabs/summary </b><br><br>&nbsp&nbspSIMPLE SEARCH : 1. Select drug/disease + 2. Click 'Find Interactions' for simple drug/disease search<br><br>&nbsp&nbspCLIENT SEARCH : 1. Select drug/disease + 2. Click 'Update' or 'Add' for client account drug/disease search</b><br><br>";}

// DISPLAY CLIENT TAB
        if ( ($_POST['hbtid'] != "") && (!isset($_POST['addDrug'])) && (!isset($_POST['addDisease'])) && (!isset($_POST['hideClient'])) ) {?> <button class="tablinks" onclick="openTab(event, 'Client')">Client<br><?php echo "<b>".$_POST['hbtid']."</b>" ?></button><?php;}

// DISPLAY DRUG TABS
        for ($j = 1; $j < $_SESSION['drugSelectorNr'] + 1; $j++)
        {if ( (isset($_POST['update'])) || (isset($_POST['add'])) || (isset($_POST['find_interactions'])) ) {$tabdrug = $_POST['drug'.$j];}
        else {if (isset($_POST['find'])) {$tabdrug = $client[$j];}}
            if ($tabdrug != "Choose one" && $tabdrug != "")
            {?> <button class="tablinks" onclick="openTab(event, '<?php echo 'Drug '.$j; ?>')"><?php echo 'Drug #'.$j; ?><br><?php echo "<b>".$tabdrug."</b>"; ?></button><?php }
            else {if ($tabdrug != "Choose one" && $tabdrug != "") {?> <button class="tablinks" onclick="openTab(event, '<?php echo 'drug '.$j; ?>')"><?php echo 'Drug #'.$j; ?><br><?php echo "<b>".$tabdrug."</b>"; ?></button><?php }}}

// DISPLAY DISEASE TABS
        for ($j = 1; $j < $_SESSION['diseaseSelectorNr'] + 1; $j++)
        {if ( (isset($_POST['update'])) || (isset($_POST['add'])) || (isset($_POST['find_interactions'])) ) {$tabdisease = $_POST['disease'.$j];}
        else {if (isset($_POST['find'])) {$tabdisease = $client[$j+20];}}
            if ($tabdisease != "Choose one" && $tabdisease != "")
            {?> <button class="tablinks" onclick="openTab(event, '<?php echo 'Disease '.$j; ?>')"><?php echo 'Disease #'.$j; ?><br><?php echo "<b>".$tabdisease."</b>"; ?></button><?php }
            else {if ($tabdisease != "Choose one" && $tabdisease != "") {?> <button class="tablinks" onclick="openTab(event, '<?php echo 'disease '.$j; ?>')"><?php echo 'Disease #'.$j; ?><br><?php echo "<b>".$tabdisease."</b>"; ?></button><?php }}}
    }?></div><?php


// DISPLAY CLIENT TAB CONTENT
?><div id="Client" class="tabcontent"><?php
    echo "<br><b>CLIENT</b><br>".$bar."<b>ID </b>".$client[0]."&nbsp&nbsp&nbsp<b>Status </b>".$client[40]."&nbsp&nbsp&nbsp<b>Sex </b>".$client[41]."&nbsp&nbsp&nbsp<b>Age </b>".$client[42]."&nbsp&nbsp&nbsp<b>Allergies : </b>".$client[43]."&nbsp&nbsp&nbsp<b>Exercise : </b>".$client[44]."&nbsp&nbsp&nbsp<b>Alcohol : </b>".$client[45]." units pw&nbsp&nbsp&nbsp<b>Smoking : </b>".$client[46]."&nbsp&nbsp&nbsp<b>Height : </b>".$client[47]." ft ".$client[48]." in"."&nbsp&nbsp&nbsp<b>Weight : </b>".$client[49]." st ".$client[50]." llb&nbsp&nbsp&nbsp<b>BMI : </b>".$bmi." ";
    if ($bmi < 18.5) {echo $orange;}
    else {if ($bmi > 30) {echo $red;}
    else {if ($bmi > 24.9) {echo $orange;}
    else {echo $green;}}}
    echo "<br><br><br><b>LOG</b><br>".$bar.$client[52];
    ?></div><?php

// DISPLAY DRUG TABS CONTENT
for ($k = 1; $k < $_SESSION['drugSelectorNr']+1; $k++) { ?>
<div id="<?php echo 'Drug '.$k; ?>" class="tabcontent">
    <?php
    echo "<br><b>DRUG SOURCE & ID</b><br>".$bar;
    echo $lines[$k];
    echo "<br><br>".$db_icon."<b> DRUG-DRUG INTERACTIONS</b><br>".$bar;
    # interactions with selected drugs
    echo "<br>Drug #".$k." <b>".$drugs[$k]."</b> with selected drugs :<br>";
    for ($j = 1; $j < $_SESSION['drugSelectorNr'] + 1; $j++) {
        if ( ($drugs[$j] != "Choose one") && ($drugs[$j] != $drugs[$k]) ) {
            $rx_id = getRxCode($drugs[$k]);
            $i_rx_id = getRxCode($drugs[$j]);
            $interaction = getSelectDrugDrugInteraction($rx_id,$i_rx_id);
            echo "Drug #".$j." ".$interaction[1].$drugs[$j]." (Rx <a href=".$urlroot.$i_rx_id.">".$i_rx_id."</a>) : ".$interaction[4]."<br>";}}
    # interactions with all drugs
    echo "<br>Drug #".$k." <b>".$drugs[$k]."</b> with ALL ".count($allinteractions[$k])." drugs : <br>";
    $button = 'buttonid'.$k;
    ?>&nbsp&nbsp&nbsp&nbsp&nbsp<button class="button" onclick="showhide('<?php echo $button; ?>')">Click To View All</button>
    <div id='<?php echo $button; ?>' style="display:none"><?php
    echo "<br><i>'Ctrl' + 'f' to find specific drug</i><br>";
    for ($j = 0; $j < count($allinteractions[$k]); $j++) {echo " #".($j+1)." ".$green." ".$allinteractions[$k][$j]."<br>";} ?>
    </div><?php
    echo "<br><br>".$dailymed_page[$k];
    echo "<br><br>".$drugbank_page[$k];
    ?>
    </div><?php }

// DISPLAY DISEASE TABS CONTENT
for ($j = 1; $j < $_SESSION['diseaseSelectorNr'] + 1; $j++) { ?>
<div id="<?php echo 'Disease '.$j; ?>" class="tabcontent">
    <?php
    $all_interactions = '';
    $icd = getDiseaseCode($diseases[$j]); if (!isset($icd)) {$icd = "0 results";} echo '<br><b>DISEASE<br>'.$bar.'ICD : </b>'.$icd."&nbsp&nbsp-&nbsp&nbsp";
    $longdisease = getLongDisease($icd); echo '<b>ICD full name : </b>'.$longdisease."<br><br>";
    for ($i = 1; $i < $_SESSION['diseaseSelectorNr'] + 1; $i++) {
        if ($drugs[$i] != "Choose one") {
            $rx_id = getRxCode($drugs[$i]);
            $interaction = getDiseaseInteractions($rx_id,$icd);
            $interaction_line =  $interaction[1]." ".$drugs[$i]." - ".$diseases[$j]." : ".$interaction[3]." - ".$interaction[4]." <i>(".$interaction[5].")</i><br>";
            if ($interaction[5] != '0 results') {$all_interactions = $all_interactions.$interaction_line; }}}
    echo "<br><b>ALERTS</b><br>".$bar.$all_interactions;
    echo "<br><br><b>INTERACTION GUIDE</b><br>".$bar;
    echo getDiseaseGuide($icd);
    ?>
    </div><?php }

?>
</body></html>
<?php if ($showhelp == 1) {echo $dbinfo;} ?>