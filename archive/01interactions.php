<?php
// We need to use sessions, so you should always start sessions using the below code.
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
    header('Location: index.html');
    exit;
}

// VARIABLES
$showhelp = 1;
$shortbar = "<hr style='width:40%;text-align:left;margin-left:0'>";
$urlroot = "https://mor.nlm.nih.gov/RxNav/search?searchBy=RXCUI&searchTerm=";
$ctrlf = "<img src='../assets/img/ctrlf.png' title='alert level : severe' height='25'>";
$red = "<img src='../assets/img/red.png' title='alert level : severe' width='10' height='10'>&nbsp";
$orange = "<img src='../assets/img/orange.png' title='alert level : moderate' width='10' height='10'>&nbsp";
$green = "<img src='../assets/img/green.png' title='alert level : minor' width='10' height='10'>&nbsp";
$one = "<img src='../assets/img/one.png' width='20' height='20'>";
$two = "<img src='../assets/img/two.png' width='20' height='20'>";
$three = "<img src='../assets/img/three.png' width='20' height='20'>";
$four = "<img src='../assets/img/four.png' width='20' height='20'>";
$rx_icon = "<img src='../assets/img/rx_icon.png' title='NLM RxNorm' height='15'>";
$dm_icon = "<img src='../assets/img/dailymed_icon.png' title='NLM DailyMed' height='12'>";
$db_icon = "<img src='../assets/img/drugbank_icon.png' title='DrugBank' height='15'>";
$atc_icon = "<img src='../assets/img/atc_icon.png' title='ATC' height='15'>";
$help1 = "HOW IT WORKS<span style='color:#212021;font-weight:normal'>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbspSearch for drug interactions with other drugs, disease or gene variants + bespoke drug and disease alerts<br><br><b>General Search</b><br>Note : Client details not required for a general search<br>1. Select any drug combination - at least two for drug-drug interaction<br>2. Select any disease combination - at least one for drug-disease combinations<br>3. click <button type='button' disabled>Find Interactions</button><br><br><b>Find existing client</b><br> 1. Enter HBT ID, eg 10000, 10001, etc<br>2. Click <button type='button' disabled>Find</button> to find client record<br>3. Click on 'Client' tab below to view client record<br><br><b>Add new client</b><br>1. Enter HBT ID<br>2. Create Baseline for client record - select sex, age, drugs and diseases, and add notes if required<br>3. Click <button type='button' disabled>Add</button> to add new client profile<br><br><b>Update existing client</b><br>1. Find client as above<br>2. Create Follow Up for client record - select new/changed drugs and diseases, and add notes if required<br>3. Click <button type='button' disabled>Update</button> to update client profile<br>4. View changes in 'Client' tab<br><br>Click <button type='button' disabled>CLS</button> to clear previous selections</span>";
$sex = array('Male','Female');
$age = array('0 to 10','11 to 20','21 to 30','31 to 40','41 to 50','51 to 60','71 to 80','81 to 90','91 to 100', '100+');
$allergies = array('None', 'Antibiotics containing sulfonamides', 'Anticonvulsants', 'Aspirin Ibuprofen and other NSAIDs', 'Chemotherapy drugs','P enicillin + related antibiotics');
$exercise = array('None', 'Low', 'Moderate', 'Very Active');
$alcohol = array('None','1 - 7','8 - 14','15 - 21','22 - 28','29 - 35','36 - 42','43 - 49','50 - 56','57+');
$smoking = array('Yes','No','Recently Quit');
$hbtid = '';
$noneselected = ($_POST['drug1'] == "Choose one" && $_POST['drug2'] == "Choose one" && $_POST['drug3'] == "Choose one" && $_POST['drug4'] == "Choose one" && $_POST['disease1'] == "Choose one" && $_POST['disease2'] == "Choose one" && $_POST['disease3'] == "Choose one" && $_POST['disease4'] == "Choose one");
$diseaseslist = array('','Hypertension','Heart failure','Asthma','Chronic obstructive pulmonary disease','Diabetes','Chronic kidney disease');
$variants = array('rs1799853','rs1057910');
$dbinfo = "<br><b>Search Suggestions</b><br><br> Drug-Drug Interactions : drugs (500), drug interactions (347,000), bespoke alerts (1) Lansoprazole, DailyMed pages (274) out of 500, Drugbank (437) out of 500<br><br> Drug - Disease Interactions : alerts for Asthma (Aspirin x 1), Heart failue (Aspirin x 1) + Chronic kidney disease (various x 118) ";
$showdbinfo = 1;

// FUNCTIONS
function connectSQLdb() {
    $servername = "localhost"; $username = $_SESSION['name']; $password = $_SESSION['password']; $dbname = "interactions";
    static $conn;
    if ($conn===NULL)
    {$conn=new mysqli($servername,$username,$password,$dbname);}
    return $conn;}

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
    return $a_drugs;}

function &findClient($hbtid){
    $conn = connectSQLdb();
    $a_client = array();
    if ($conn->connect_error) {die("Connection failed: " . $conn->connect_error);}
    $sql = "SELECT * FROM `clients` WHERE hbtid = '$hbtid'";
    $sqlquery = $conn->query($sql);
    if ($sqlquery->num_rows > 0) {
        while($row = $sqlquery->fetch_assoc()) {
            $a_client[0] = $row["hbtid"];$a_client[1] = $row["drug01"];$a_client[2] = $row["drug02"];$a_client[3] = $row["drug03"];$a_client[4] = $row["drug04"];$a_client[5] = $row["drug05"];$a_client[6] = $row["drug06"];$a_client[7] = $row["drug07"];$a_client[8] = $row["drug08"];$a_client[9] = $row["drug09"];$a_client[10] = $row["drug10"];$a_client[11] = $row["drug11"];$a_client[12] = $row["drug12"];$a_client[21] = $row["disease01"];$a_client[22] = $row["disease02"];$a_client[23] = $row["disease03"];$a_client[24] = $row["disease04"];$a_client[25] = $row["disease05"];$a_client[26] = $row["disease06"];$a_client[40] = $row["status"];$a_client[41] = $row["sex"];$a_client[42] = $row["age"];$a_client[43] = $row["allergies"];$a_client[44] = $row["exercise"];$a_client[45] = $row["alcohol"];$a_client[46] = $row["smoking"];$a_client[47] = $row["height1"];$a_client[48] = $row["height2"];$a_client[49] = $row["weight1"];$a_client[50] = $row["weight2"];$a_client[51] = $row["bmi"];$a_client[52] = $row["history"];
        }}
    return $a_client;}

function &addClient($client){
    $green = "<img src='../assets/img/green.png' title='alert level : minor' width='10' height='10'>&nbsp";
    $red = "<img src='../assets/img/red.png' title='alert level : severe' width='10' height='10'>&nbsp";
    $conn = connectSQLdb();
    if ($conn->connect_error) {die("Connection failed: " . $conn->connect_error);}
    $sql = "INSERT INTO `clients` (hbtid, drug01, drug02, drug03, drug04, drug05, drug06, drug07, drug08, drug09, drug10, drug11, drug12, disease01, disease02, disease03, disease04, disease05, disease06, status,  sex, age, allergies, exercise, alcohol, smoking, height1, height2, weight1, weight2, bmi, history) VALUES ('$client[0]', '$client[1]', '$client[2]', '$client[3]', '$client[4]', '$client[5]', '$client[6]', '$client[7]', '$client[8]', '$client[9]', '$client[10]', '$client[11]', '$client[12]', '$client[21]', '$client[22]', '$client[23]', '$client[24]', '$client[25]', '$client[26]', '$client[40]', '$client[41]', '$client[42]','$client[43]', '$client[44]', '$client[45]', '$client[46]', '$client[47]', '$client[48]', '$client[49]', '$client[50]', '$client[51]', '$client[52]')";
    if ($conn->query($sql) === TRUE) {
        echo $green." New record created successfully <br><br>";
    } else {
        echo $red." Error: Record already exists<br><br>";
        if (mysqli_error($conn)!="") {echo $red." ".mysqli_error($conn)."<br><br>";}
    }
    return $client;}

function &updateClient($client){
    $green = "<img src='../assets/img/green.png' title='alert level : minor' width='10' height='10'>&nbsp";
    $red = "<img src='../assets/img/red.png' title='alert level : severe' width='10' height='10'>&nbsp";
    $conn = connectSQLdb();
    if ($conn->connect_error) {die("Connection failed: " . $conn->connect_error);}
    $sql = "UPDATE `clients` set drug01='$client[1]', drug02='$client[2]', drug03='$client[3]', drug04='$client[4]', drug05='$client[5]', drug06='$client[6]', drug07='$client[7]', drug08='$client[8]', drug09='$client[9]', drug10='$client[10]', drug11='$client[11]', drug12='$client[12]', disease01='$client[21]', disease02='$client[22]', disease03='$client[23]', disease04='$client[24]', disease05='$client[25]',
                     disease06='$client[26]', status='$client[40]', sex='$client[41]', age='$client[42]', allergies='$client[43]', exercise='$client[44]', alcohol='$client[45]', smoking='$client[46]', height1='$client[47]', height2='$client[48]', weight1='$client[49]', weight2='$client[50]', bmi='$client[51]', history='$client[52]' WHERE hbtid = '$client[0]'";
    if ($conn->query($sql) === TRUE) {
        echo "&nbsp".$green." Record updated successfully&nbsp<br><br>";
    } else {
        echo "&nbsp".$red." Error: " . $sql . "<br>" . $conn->error;
    }
    return $client;}

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
            $a_codes[2] = $row["drugbank"];}}
    return $a_codes;}

function &getDrugName($rx){
    $conn = connectSQLdb();
    if ($conn->connect_error) {die("Connection failed: " . $conn->connect_error);}
    $sql = "SELECT * FROM `drug_codes` WHERE rxcui = '$rx'";
    $sqlquery = $conn->query($sql);
    if ($sqlquery->num_rows > 0) {
        while($row = $sqlquery->fetch_assoc()) {
            $drug = $row["drug"];}}
    return $drug;}

function &getRxCode($drug){
    $conn = connectSQLdb();
    if ($conn->connect_error) {die("Connection failed: " . $conn->connect_error);}
    $sql = "SELECT * FROM `drug_codes` WHERE drug = '$drug'";
    $sqlquery = $conn->query($sql);
    if ($sqlquery->num_rows > 0) {
        while($row = $sqlquery->fetch_assoc()) {
            $rx = $row["rxcui"];}}
    return $rx;}

function &getDrugSourceIds($drug){
    $conn = connectSQLdb();
    $rx_icon = "<img src='../assets/img/rx_icon.png' title='National Library Medicine RxNorm' height='15'>";
    $dm_icon = "<img src='../assets/img/dailymed_icon.png' title='National Library Medicine DailyMed' height='12'>";
    $db_icon = "<img src='../assets/img/drugbank_icon.png' title='DrugBank' height='15'>";
    $atc_icon = "<img src='../assets/img/atc_icon.png' title='World Health Organisation' height='15'>";
    if ($conn->connect_error) {die("Connection failed: " . $conn->connect_error);}
    $sql = "SELECT * FROM `drug_codes` WHERE drug = '$drug'";
    $sqlquery = $conn->query($sql);
    if ($sqlquery->num_rows > 0) {
        while($row = $sqlquery->fetch_assoc()) {
            if ($row["common"] == "") {$line_common = "";}
            else {$line_common = "Commonly known or available as ".$row["common"]."<br>";}
            $line_rx = "<tr><th>".$rx_icon."</th><th>"."<span style='float:left;color:#212021;font-weight:normal'> NLM : RxCUI id "."</th><th>"."<span style='float:left;color:#212021;font-weight:normal'><a href='https://mor.nlm.nih.gov/RxNav/search?searchBy=String&searchTerm=".$drug."'>".$row["rxcui"]."</a></span></span></th></tr>";
            $line_atc = "<tr><th>".$atc_icon."</th><th>"."<span style='float:left;color:#212021;font-weight:normal'> WHO : ATC id "."</th><th>"."<span style='float:left;color:#212021;font-weight:normal'><a href='https://www.whocc.no/atc_ddd_index/?code=".$row["atc"]."'>".$row["atc"]."</a></span></span></th></tr>";
            $line_db = "<tr><th>".$db_icon."</th><th>"."<span style='float:left;color:#212021;font-weight:normal'> DrugBank : DB id "."</th><th>"."<span style='float:left;color:#212021;font-weight:normal'><a href='https://go.drugbank.com/drugs/".$row["drugbank"]."'>".$row["drugbank"]."</a></span></span></th></tr>";
            $line_dm = "<tr><th>".$dm_icon."</th><th>"."<span style='float:left;color:#212021;font-weight:normal'> DailyMed : SPL id "."</th><th>"."<span style='float:left;color:#212021;font-weight:normal'><a href='https://dailymed.nlm.nih.gov/dailymed/drugInfo.cfm?setid=".$row["spl"]."'>".$row["spl"]."</a></span></span></th></tr>";
        }
        $lines = $line_common."<table>".$line_rx.$line_atc.$line_db.$line_dm."</table>";}
    else {$lines = "0 results";}
    return $lines;}

function &getDrugAlerts($rx){
    $red = "<img src='../assets/img/red.png' title='alert level : severe' width='10' height='10'>&nbsp";
    $orange = "<img src='../assets/img/orange.png' title='alert level : moderate' width='10' height='10'>&nbsp";
    $green = "<img src='../assets/img/green.png' title='alert level : minor' width='10' height='10'>&nbsp";
    $conn = connectSQLdb();
    $drug_alert = '0 results';$colour='';
    if ($conn->connect_error) {die("Connection failed: " . $conn->connect_error);}
    $sql = "SELECT * FROM `drug_alerts` WHERE rxcui = '$rx'";
    $sqlquery = $conn->query($sql);
    if ($sqlquery->num_rows > 0) {
        while($row = $sqlquery->fetch_assoc()) {
            $warning = $row["warning"]; $drug_alert = " ".$row["alert"]." <i>(".$row["version"].")</i><br>";}}
    if ($drug_alert!='0 results') {if ($warning == 1) {$colour = $red;} else if ($warning == 2) {$colour = $orange;}
    else {$colour = $green;}}
    $drug_alert = $colour.$drug_alert;
    return $drug_alert;}

function &getDailyMedPage($spl){
    $dm_icon = "<img src='../assets/img/dailymed_icon.png' title='NLM DailyMed' width='20'>";
    $dailymed =  "<br>".$dm_icon." <b>DailyMed Drug-Drug Interactions (General/Summary)</b>";
    $conn = connectSQLdb();
    $sql = "SELECT * FROM `dailymed` WHERE spl = '$spl'";
    $sqlquery = $conn->query($sql);
    if ($sqlquery->num_rows > 0) {
        while($row = $sqlquery->fetch_assoc()) {
            $row2 = $row["dmcontent"];
            $dailymed = $dailymed."<div style = 'font-size:10px; font-weight:normal; padding:30px; text-align:left'>".$row2."</div>";}}
    else {$dailymed = $dailymed."<br><br>0 results<br><br>";}
    return $dailymed;}

function &getDrugbankPage($drugbank_id){
    $db_icon = "<img src='../assets/img/drugbank_icon.png' title='DrugBank' width='15'>";
    $drugbank = "<br>".$db_icon." <b>Drugbank Drug Detailed Report</b>";
    $conn = connectSQLdb();
    $sql = "SELECT * FROM `drugbank` WHERE drugbank = '$drugbank_id'";
    $sqlquery = $conn->query($sql);
    if ($sqlquery->num_rows > 0) {
        while($row = $sqlquery->fetch_assoc()) {
            $row2 = $row["dbcontent"];
            $drugbank = $drugbank."<div style = 'font-size:10px; font-weight:normal; padding:30px; text-align:left'>".$row2."</div>";
        }} else {$drugbank = $drugbank."<br>0 results";}
    return $drugbank;}

function &getAllDrugDrugInteractions($rx){
    $urlroot = "https://mor.nlm.nih.gov/RxNav/search?searchBy=RXCUI&searchTerm=";
    $conn = connectSQLdb();
    if ($conn->connect_error) {die("Connection failed: " . $conn->connect_error);}
    $sql = "SELECT * FROM `drug_interactions` WHERE rxcui = '$rx'";
    $sqlquery = $conn->query($sql);
    $interactions = [];$i=0;
    if ($sqlquery->num_rows > 0) {
        while($row = $sqlquery->fetch_assoc()) {$interactions[$i] = $row["i_drug"]." (Rx <a href=".$urlroot.$row["i_rxcui"].">".$row["i_rxcui"]."</a>) : ".$row["interaction"];$i++;}}
    return $interactions;}

function &getSelectDrugDrugInteraction($rx,$i_rx){
    $interaction = array(0,0,0,0,0);
    $red = "<img src='../assets/img/red.png' title='alert level : severe' width='10' height='10'>&nbsp";
    $orange = "<img src='../assets/img/orange.png' title='alert level : moderate' width='10' height='10'>&nbsp";
    $green = "<img src='../assets/img/green.png' title='alert level : minor' width='10' height='10'>&nbsp";
    $conn = connectSQLdb();
    if ($conn->connect_error) {die("Connection failed: " . $conn->connect_error);}
    $sql = "SELECT * FROM `drug_interactions` WHERE rxcui = '$rx' AND i_rxcui = '$i_rx'";
    $sqlquery = $conn->query($sql);
    if ($sqlquery->num_rows > 0) {
        while($row = $sqlquery->fetch_assoc()) {
            $interaction[0] = $row["warning"]; $interaction[2] = $row["drug"]; $interaction[3] = $row["i_drug"]; $interaction[4] = $row["interaction"];}}
    if ($interaction[0] == 1) {$interaction[1] = $red;} else if ($interaction[0] == 2) {$interaction[1] = $orange;} else {$interaction[1] = $green;}
    if ($interaction[4] == "") {$interaction[4] = "0 results";}
    return $interaction;}

function &getDiseaseCode($disease){
    $conn = connectSQLdb();
    if ($conn->connect_error) {die("Connection failed: " . $conn->connect_error);}
    $sql = "SELECT * FROM `dis_interactions` WHERE disease = '$disease'";
    $sqlquery = $conn->query($sql);
    if ($sqlquery->num_rows > 0) {
        while($row = $sqlquery->fetch_assoc()) {$icd = $row["icd"];}}
    return $icd;}

function &getDiseaseInteractions($rx,$icd){
    $interaction = array(0,0,0,0,0,0,0);
    $red = "<img src='../assets/img/red.png' title='alert level : severe' width='10' height='10'>&nbsp";
    $orange = "<img src='../assets/img/orange.png' title='alert level : moderate' width='10' height='10'>&nbsp";
    $green = "<img src='../assets/img/green.png' title='alert level : minor' width='10' height='10'>&nbsp";
    $conn = connectSQLdb();
    if ($conn->connect_error) {die("Connection failed: " . $conn->connect_error);}
    $sql = "SELECT * FROM `dis_interactions` WHERE icd = '$icd' AND rxcui = '$rx'";
    $sqlquery = $conn->query($sql);
    if ($sqlquery->num_rows > 0) {
        while($row = $sqlquery->fetch_assoc()) {$interaction[0] = $row["warning"]; $interaction[2] = $row["rxcui"]; $interaction[3] = $row["issue"]; $interaction[4] = $row["advice"]; $interaction[5] = $row["version"];}}
    if ($interaction[0] == 1) {$interaction[1] = $red;} else if ($interaction[0] == 2) {$interaction[1] = $orange;} else {$interaction[1] = $green;}
    return $interaction;}

function &getGeneInteractions($drug){
    $gvalert = "0 results";
    $conn = connectSQLdb();
    $red = "<img src='../assets/img/red.png' title='alert level : severe' width='10' height='10'>&nbsp";
    $orange = "<img src='../assets/img/orange.png' title='alert level : moderate' width='10' height='10'>&nbsp";
    $green = "<img src='../assets/img/green.png' title='alert level : minor' width='10' height='10'>&nbsp";
    if ($conn->connect_error) {die("Connection failed: " . $conn->connect_error);}
    $sql = "SELECT * FROM `gene_interactions` WHERE drug = '$drug'";
    $sqlquery = $conn->query($sql);
    if ($sqlquery->num_rows > 0) {
        while($row = $sqlquery->fetch_assoc()) {
            $warning = $row["warning"]; $gvalert = " ".$drug." - "."Variant ".$row["snip"]." : ".$row["alert"]." <i>(".$row["version"].")</i><br>";}}
    if ($gvalert!='0 results') {if ($warning == 1) {$colour = $red;} else if ($warning == 2) {$colour = $orange;}
    else {$colour = $green;}}
    $gvalert = $colour.$gvalert;
    return $gvalert;}

if ( (isset($_POST['find'])) || (isset($_POST['update'])) || (isset($_POST['add'])) ) {
    $hbtid = $_POST['hbtid'];
    $client = findClient($hbtid);
    $_SESSION['client'] = $client;
}

function &getDiseaseGuide($disease) {
    $conn = connectSQLdb();
    if ($conn->connect_error) {die("Connection failed: " . $conn->connect_error);}
    $sql = "SELECT * FROM `disease_guide` WHERE disease = '$disease'";
    $sqlquery = $conn->query($sql);
    if ($sqlquery->num_rows > 0) {
        while($row = $sqlquery->fetch_assoc()) {
            $warning = $row["warning"]; $guide = $row["guide"];}}
    if (!isset($guide)) {$guide = "<br>None found";}
    return $guide;}


// HTML HTML HTML HTML HTML HTML HTML HTML HTML HTML HTML HTML HTML HTML
?><!DOCTYPE html><html>
<head>
    <meta charset="utf-8">
    <title>Home Page</title>
    <link href="style.css" rel="stylesheet" type="text/css">

</head>
<body class="loggedin">
<nav class="navtop">
    <div>
        <h1>Compass Interactions Database Prototype v<?php echo $_SESSION['phpversion']; ?></h1>
        <a style="color:#c1c4c8"><?php echo $_SESSION['fullname'] ?></a>
        <a href="../login/logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
    </div>
</nav>
<form enctype="multipart/form-data" action="" method="post">

    <table style = 'font-size:15px; padding:10px; text-align:left'>
        <tr><th><table><tr><th valign="top">
                            Client</th>
                        <th>
                            <?php if ( (!isset($_POST['viewclient'])) && (!isset($_POST['find'])) && (!isset($_POST['update'])) && (!isset($_POST['add'])) ) {
                                ?><input type="submit" name="viewclient" value="View Client" class="btn btn-primary"/><br><br> <?php }
                            else {if ( (!isset($_POST['hideclient'])) || (isset($_POST['find'])) || (isset($_POST['update'])) || (isset($_POST['add'])) ) {?>
                                <input type="submit" name="hideclient" value="Hide Client" class="btn btn-primary"/>
                                <br><br><?php
                            }}

                            if ( (isset($_POST['viewclient'])) || (isset($_POST['find'])) || (isset($_POST['update'])) || (isset($_POST['add'])) ) {

                                ?>HBT ID <input type="text" name="hbtid" id="hbtid" style="width:60px;" value = "<?php echo isset($_POST['hbtid']) ? $_POST['hbtid'] : '' ?>"> <input type="submit" name="find" value="Find" class="btn btn-primary"/> <input type="submit" name="update" value="Update" class="btn btn-primary"/> <input type="submit" name="add" value="Add" class="btn btn-primary"/><br><br>

                                <table>

                                    <?php
                                    # show 'hbt id required' if none entered and Update buttons selected
                                    if ( (isset($_POST['update'])) && ($_POST['hbtid'] == '') ) {echo "&nbsp".$red." HBT ID required&nbsp<br><br>";}
# show 'hbt id required' if none entered and Add/Find buttons selected
                                    else {if ( ( (isset($_POST['update'])) || (isset($_POST['add'])) || (isset($_POST['find'])) ) && ($_POST['hbtid'] == '') ) {echo "&nbsp".$red." HBT ID required&nbsp<br><br>";}
# warning 'not found in db'
                                    else {if ((isset($_POST['find'])) && ($client[0] == ""))
                                    {echo "&nbsp".$red." Error : ID '".$_POST['hbtid']."' not found&nbsp<br><br>";}}}
                                    ?>

                                    <tr><th>Sex</th><th>
                                            <select name="sex" id="sex" style="width:100px;"><option selected="selected">
                                                    <?php if (isset($_POST['clear'])) {echo "Choose one";} else {if (isset($_POST['sex']) && $_POST['sex'] != "Choose one" && (!isset($_POST['find']))) {echo $_POST['sex'];} else {if ($client[41]!="" && (isset($_POST['find']))) {echo $client[41];} else {echo "Choose one";}}}?>
                                                </option><?php foreach($sex as $item){echo "<option value='$item'>$item</option>";} ?> </select <input name="sex" type="text"/>
                                        </th></tr>
                                    <tr><th>Age</th><th>
                                            <select name="age" id="age" style="width:100px;"><option selected="selected">
                                                    <?php if (isset($_POST['clear'])) {echo "Choose one";} else {if (isset($_POST['age']) && $_POST['age'] != "Choose one" && (!isset($_POST['find']))) {echo $_POST['age'];} else {if ($client[42]!="" && (isset($_POST['find']))) {echo $client[42];} else {echo "Choose one";}}}?>
                                                </option><?php foreach($age as $item){echo "<option value='$item'>$item</option>";} ?> </select <input name="age" type="text"/>
                                        </th></tr>

                                    <tr><th>Height</th><th>
                                            <input type="text" name="height1" id="height1" style="width:30px;" value = "<?php if (isset($_POST['height1']) && $_POST['height1'] != "" && (!isset($_POST['find']))) {echo $_POST['height1'];} else {if ($client[47]!="" && (isset($_POST['find']))) {echo (int)$client[47];}} ?>"><span style="color:#212021;font-weight:normal">ft </span>
                                            <input type="text" name="height2" id="height2" style="width:30px;" value = "<?php if (isset($_POST['height2']) && $_POST['height2'] != "" && (!isset($_POST['find']))) {echo $_POST['height2'];} else {if ($client[48]!="" && (isset($_POST['find']))) {echo (int)$client[48];}} ?>"><span style="color:#212021;font-weight:normal">in </span> </th></tr>

                                    <tr><th>Weight</th><th>
                                            <input type="text" name="weight1" id="weight1" style="width:30px;" value = "<?php if (isset($_POST['weight1']) && $_POST['weight1'] != "" && (!isset($_POST['find']))) {echo $_POST['weight1'];} else {if ($client[49]!="" && (isset($_POST['find']))) {echo (int)$client[49];}} ?>"><span style="color:#212021;font-weight:normal">st </span>
                                            <input type="text" name="weight2" id="weight2" style="width:30px;" value = "<?php if (isset($_POST['weight2']) && $_POST['weight2'] != "" && (!isset($_POST['find']))) {echo $_POST['weight2'];} else {if ($client[50]!="" && (isset($_POST['find']))) {echo (int)$client[50];}} ?>"><span style="color:#212021;font-weight:normal">llb </span>
                                            <?php
                                            $height = (int)$_POST['height1']*12+(int)$_POST['height2'];
                                            $weight = (int)$_POST['weight1']*14+(int)$_POST['weight2'];
                                            if ($height!='0' && $weight!='0') {$bmi = round( $weight/($height*$height)*703, 1);} else {$bmi = '-1';}
                                            if ($bmi>=0) {echo "&nbsp&nbsp&nbsp&nbsp&nbspBMI <span style='color:#212021;font-weight:normal'>".$bmi." </span>";
                                                if ($bmi < 18.5) {echo $orange;} else {if ($bmi > 30) {echo $red;} else {if ($bmi > 24.9) {echo $orange;} else {echo $green;}}}
                                            }

                                            ?>
                                        </th></tr>
                                    <tr><th>Exercise</th><th>
                                            <select name="exercise" id="exercise" style="width:100px;"><option selected="selected">
                                                    <?php if (isset($_POST['clear'])) {echo "Choose one";} else {if (isset($_POST['exercise']) && $_POST['exercise'] != "Choose one" && (!isset($_POST['find']))) {echo $_POST['exercise'];} else {if ($client[44]!="" && (isset($_POST['find']))) {echo $client[44];} else {echo "Choose one";}}} ?>
                                                </option><?php foreach($exercise as $item){echo "<option value='$item'>$item</option>";} ?> </select <input name="exercise" type="text"/>
                                        </th></tr>
                                    <tr><th>Alcohol</th><th>
                                            <select name="alcohol" id="alcohol" style="width:100px;"><option selected="selected">
                                                    <?php if (isset($_POST['clear'])) {echo "Choose one";} else {if (isset($_POST['alcohol']) && $_POST['alcohol'] != "Choose one" && (!isset($_POST['find']))) {echo $_POST['alcohol'];} else {if ($client[45]!="" && (isset($_POST['find']))) {echo $client[45];} else {echo "Choose one";}}} ?>
                                                </option><?php foreach($alcohol as $item){echo "<option value='$item'>$item</option>";} ?> </select <input name="alcohol" type="text"/><span style="color:#212021;font-weight:normal">units pw</span>
                                        </th></tr>
                                    <tr><th>Smoking</th><th>
                                            <select name="smoking" id="smoking" style="width:100px;"><option selected="selected">
                                                    <?php if (isset($_POST['clear'])) {echo "Choose one";} else {if (isset($_POST['smoking']) && $_POST['smoking'] != "Choose one" && (!isset($_POST['find']))) {echo $_POST['smoking'];} else {if ($client[46]!="" && (isset($_POST['find']))) {echo $client[46];} else {echo "Choose one";}}} ?>
                                                </option><?php foreach($smoking as $item){echo "<option value='$item'>$item</option>";} ?> </select <input name="smoking" type="text"/>
                                        </th></tr>
                                    <tr><th>Allergies</th><th>
                                            <select name="allergies" id="allergies" style="width:195px;"><option selected="selected">
                                                    <?php if (isset($_POST['clear'])) {echo "Choose one";} else {if (isset($_POST['allergies']) && $_POST['allergies'] != "Choose one" && (!isset($_POST['find']))) {echo $_POST['allergies'];} else {if ($client[43]!="" && (isset($_POST['find']))) {echo $client[43];} else {echo "Choose one";}}} ?>
                                                </option><?php foreach($allergies as $item){echo "<option value='$item'>$item</option>";} ?> </select <input name="allergies" type="text"/>
                                        </th></tr>
                                </table>
                                <br>
                                Notes<br><input type="text" name="notes" id="notes" style="width:250px;" onclick="submit_form(); value = "<?php echo isset($_POST['notes']) ? $_POST['notes'] : '' ?>">


                                <br><br>

                            <?php }?>
                        </th></tr>

                    <tr><th valign="top">Drugs</th><th>
                            <?php $drugslist = getDrugsList(); ?>
                            <?php # display drug dropdown list x 6 ?>
                            <select name="drug1" id="drug1" style="width:250px;"><option selected="selected"><?php if (isset($_POST['clear'])) {echo "Choose one";} else {if (isset($_POST['drug1']) && $_POST['drug1'] != "Choose one" && (!isset($_POST['find']))) {echo $_POST['drug1'];} else {if ($client[1]!="" && (isset($_POST['find']))) {echo $client[1];} else {echo "Choose one";}}} ?>
                            </option><?php foreach($drugslist as $item){echo "<option value='$item'>$item</option>";} ?></select <input name="drug1" type="text"/><br>

                            <select name="drug2" id="drug2" style="width:250px;"><option selected="selected"><?php if (isset($_POST['clear'])) {echo "Choose one";} else {if (isset($_POST['drug2']) && $_POST['drug2'] != "Choose one" && (!isset($_POST['find']))) {echo $_POST['drug2'];} else {if ($client[2]!="" && (isset($_POST['find']))) {echo $client[2];} else {echo "Choose one";}}} ?>
                                </option><?php foreach($drugslist as $item){echo "<option value='$item'>$item</option>";} ?></select <input name="drug2" type="text"/><br>
                            <select name="drug3" id="drug3" style="width:250px;"><option selected="selected"><?php if (isset($_POST['clear'])) {echo "Choose one";} else {if (isset($_POST['drug3']) && $_POST['drug3'] != "Choose one" && (!isset($_POST['find']))) {echo $_POST['drug3'];} else {if ($client[3]!="" && (isset($_POST['find']))) {echo $client[3];} else {echo "Choose one";}}} ?>
                                </option><?php foreach($drugslist as $item){echo "<option value='$item'>$item</option>";} ?></select <input name="drug3" type="text"/><br>
                            <select name="drug4" id="drug4" style="width:250px;"><option selected="selected"><?php if (isset($_POST['clear'])) {echo "Choose one";} else {if (isset($_POST['drug4']) && $_POST['drug4'] != "Choose one" && (!isset($_POST['find']))) {echo $_POST['drug4'];} else {if ($client[4]!="" && (isset($_POST['find']))) {echo $client[4];} else {echo "Choose one";}}} ?>
                                </option><?php foreach($drugslist as $item){echo "<option value='$item'>$item</option>";} ?></select <input name="drug4" type="text"/><br>
                            <select name="drug5" id="drug5" style="width:250px;"><option selected="selected"><?php if (isset($_POST['clear'])) {echo "Choose one";} else {if (isset($_POST['drug5']) && $_POST['drug5'] != "Choose one" && (!isset($_POST['find']))) {echo $_POST['drug5'];} else {if ($client[5]!="" && (isset($_POST['find']))) {echo $client[5];} else {echo "Choose one";}}} ?>
                                </option><?php foreach($drugslist as $item){echo "<option value='$item'>$item</option>";} ?></select <input name="drug5" type="text"/><br>
                            <select name="drug6" id="drug6" style="width:250px;"><option selected="selected"><?php if (isset($_POST['clear'])) {echo "Choose one";} else {if (isset($_POST['drug6']) && $_POST['drug6'] != "Choose one" && (!isset($_POST['find']))) {echo $_POST['drug6'];} else {if ($client[6]!="" && (isset($_POST['find']))) {echo $client[6];} else {echo "Choose one";}}} ?>
                                </option><?php foreach($drugslist as $item){echo "<option value='$item'>$item</option>";} ?></select <input name="drug6" type="text"/><br>

                            <p style="line-height:0.5"></p>

                    <tr><th valign="top">Diseases</th><th>
                            <select name="disease1" id="disease1" style="width:250px;"><option selected="selected"><?php if (isset($_POST['clear'])) {echo "Choose one";} else {if (isset($_POST['disease1']) && $_POST['disease1'] != "Choose one" && (!isset($_POST['find']))) {echo $_POST['disease1'];} else {if ($client[21]!="" && (isset($_POST['find']))) {echo $client[21];} else {echo "Choose one";}}} ?>
                                </option><?php foreach($diseaseslist as $item){echo "<option value='$item'>$item</option>";} ?></select <input name="disease1" type="text"/><br>
                            <select name="disease2" id="disease2" style="width:250px;"><option selected="selected"><?php if (isset($_POST['clear'])) {echo "Choose one";} else {if (isset($_POST['disease2']) && $_POST['disease2'] != "Choose one" && (!isset($_POST['find']))) {echo $_POST['disease2'];} else {if ($client[22]!="" && (isset($_POST['find']))) {echo $client[22];} else {echo "Choose one";}}} ?>
                                </option><?php foreach($diseaseslist as $item){echo "<option value='$item'>$item</option>";} ?></select <input name="disease2" type="text"/><br>
                            <select name="disease3" id="disease3" style="width:250px;"><option selected="selected"><?php if (isset($_POST['clear'])) {echo "Choose one";} else {if (isset($_POST['disease3']) && $_POST['disease3'] != "Choose one" && (!isset($_POST['find']))) {echo $_POST['disease3'];} else {if ($client[23]!="" && (isset($_POST['find']))) {echo $client[23];} else {echo "Choose one";}}} ?>
                                </option><?php foreach($diseaseslist as $item){echo "<option value='$item'>$item</option>";} ?></select <input name="disease3" type="text"/><br>
                            <select name="disease4" id="disease4" style="width:250px;"><option selected="selected"><?php if (isset($_POST['clear'])) {echo "Choose one";} else {if (isset($_POST['disease4']) && $_POST['disease4'] != "Choose one" && (!isset($_POST['find']))) {echo $_POST['disease4'];} else {if ($client[24]!="" && (isset($_POST['find']))) {echo $client[24];} else {echo "Choose one";}}} ?>
                                </option><?php foreach($diseaseslist as $item){echo "<option value='$item'>$item</option>";} ?></select <input name="disease4" type="text"/><br>

                    <tr><th valign="top">Search</th><th><input type="submit" name="search" value="Find Interactions" class="btn btn-primary"/> <input type="submit" name="clear" value="CLS" class="btn btn-primary"/><br>

                            <?php

                            # show 'at least one drug required' if none entered and Search button selected
                            if ( (isset($_POST['search'])) && ($_POST['drug1'] == "Choose one") )
                            {echo "<br>&nbsp".$red." Enter at least one drug&nbsp<br><br>";}
# show '2 or more drugs for interactions' if one only entered and Search button selected
                            else {if ( (isset($_POST['search'])) && ($_POST['drug2'] == "Choose one") )
                            {echo "<br>&nbsp".$orange." Select 2 or more drugs for interactions&nbsp<br><br>";}}
                            ?>
                        </th></tr></table>
            <th>
                <?php
                //CALL FUNCTIONS - GET DRUG CODES, LINES, SPECIFIC INTER</th><th>ACTIONS + ALL INTERACTIONS

                if ( (isset($_POST['add'])) || (isset($_POST['update'])) || (isset($_POST['search'])) ) {
                    $drug1 = $_POST['drug1'];$drug2 = $_POST['drug2'];$drug3 = $_POST['drug3'];$drug4 = $_POST['drug4'];$drug5 = $_POST['drug5'];$drug6 = $_POST['drug6'];
                    $disease1 = $_POST['disease1'];$disease2 = $_POST['disease2'];$disease3 = $_POST['disease3'];$disease4 = $_POST['disease4'];}
                else {if ( (!isset($_POST['clear'])) && (!isset($_POST['search'])) ) {
                    $drug1 = $_SESSION['client'][1];$drug2 = $_SESSION['client'][2];$drug3 = $_SESSION['client'][3];$drug4 = $_SESSION['client'][4];$drug5 = $_SESSION['client'][5];$drug6 = $_SESSION['client'][6];$drug7 = $_SESSION['client'][7];$drug8 = $_SESSION['client'][8];$drug9 = $_SESSION['client'][9];$drug10 = $_SESSION['client'][10];$drug11 = $_SESSION['client'][11];$drug12 = $_SESSION['client'][12];
                    $disease1 = $_SESSION['client'][21];$disease2 = $_SESSION['client'][22];$disease3 = $_SESSION['client'][23];$disease4 = $_SESSION['client'][24];$disease5 = $_SESSION['client'][25];$disease6 = $_SESSION['client'][26];}}

                $drugs = array($drug1,$drug2,$drug3,$drug4,$drug5,$drug6);
                $diseases = array($disease1,$disease2,$disease3,$disease4);
                for ($i = 0; $i < 6; $i++) {
                    if ($drugs[$i] != "Choose one") {
                        $codes =& getDrugCodes($drugs[$i]);
                        $rx[$i] = $codes[0];$spl[$i] = $codes[1];$drugbank_id[$i] = $codes[2];
                        $lines[$i] =& getDrugSourceIds($drugs[$i]);
                        $allinteractions[$i] = &getAllDrugDrugInteractions($rx[$i]);
                        $drug_alert[$i] =& getDrugAlerts($rx[$i]);
                        $dailymed[$i] =& getDailyMedPage($spl[$i]);
                        $drugbank_id[$i] =& getDrugbankPage($drugbank_id[$i]);}}

                // Drug-Drug Summary Section
                $all_interactions = '';
                // drug 1 v 2, 3, 4, 5 and 6
                $i = 0;

                if ($drugs[$i+1] != "Choose one") {
                    $interaction_text = getSelectDrugDrugInteraction($rx[$i],$rx[$i+1]);
                    $interaction_line =  $interaction_text[1]." ".$drugs[$i]." - ".$drugs[$i+1]." : ".$interaction_text[4];
                    if ($interaction_text[3] != '0 results') {$all_interactions = $all_interactions.$interaction_line."<br>";}}
                if ($drugs[$i+2] != "Choose one") {
                    $interaction_text = getSelectDrugDrugInteraction($rx[$i],$rx[$i+2]);
                    $interaction_line =  $interaction_text[1]." ".$drugs[$i]." - ".$drugs[$i+2]." : ".$interaction_text[4];
                    if ($interaction_text[3] != '0 results') {$all_interactions = $all_interactions.$interaction_line."<br>";}}
                if ($drugs[$i+3] != "Choose one") {
                    $interaction_text = getSelectDrugDrugInteraction($rx[$i],$rx[$i+3]);
                    $interaction_line =  $interaction_text[1]." ".$drugs[$i]." - ".$drugs[$i+3]." : ".$interaction_text[4];
                    if ($interaction_text[3] != '0 results') {$all_interactions = $all_interactions.$interaction_line."<br>";}}
                if ($drugs[$i+4] != "Choose one") {
                    $interaction_text = getSelectDrugDrugInteraction($rx[$i],$rx[$i+4]);
                    $interaction_line =  $interaction_text[1]." ".$drugs[$i]." - ".$drugs[$i+4]." : ".$interaction_text[4];
                    if ($interaction_text[3] != '0 results') {$all_interactions = $all_interactions.$interaction_line."<br>";}}
                if ($drugs[$i+5] != "Choose one") {
                    $interaction_text = getSelectDrugDrugInteraction($rx[$i],$rx[$i+5]);
                    $interaction_line =  $interaction_text[1]." ".$drugs[$i]." - ".$drugs[$i+5]." : ".$interaction_text[4];
                    if ($interaction_text[3] != '0 results') {$all_interactions = $all_interactions.$interaction_line."<br>";}}

                // drug 2 v 3, 4, 5 and 6
                if ($drugs[$i+2] != "Choose one") {
                    $interaction_text = getSelectDrugDrugInteraction($rx[$i+1],$rx[$i+2]);
                    $interaction_line =  $interaction_text[1]." ".$drugs[$i+1]." - ".$drugs[$i+2]." : ".$interaction_text[4];
                    if ($interaction_text[3] != '0 results') {$all_interactions = $all_interactions.$interaction_line."<br>";}}
                if ($drugs[$i+3] != "Choose one") {
                    $interaction_text = getSelectDrugDrugInteraction($rx[$i+1],$rx[$i+3]);
                    $interaction_line =  $interaction_text[1]." ".$drugs[$i+1]." - ".$drugs[$i+3]." : ".$interaction_text[4];
                    if ($interaction_text[3] != '0 results') {$all_interactions = $all_interactions.$interaction_line."<br>";}}
                if ($drugs[$i+4] != "Choose one") {
                    $interaction_text = getSelectDrugDrugInteraction($rx[$i+1],$rx[$i+4]);
                    $interaction_line =  $interaction_text[1]." ".$drugs[$i+1]." - ".$drugs[$i+4]." : ".$interaction_text[4];
                    if ($interaction_text[3] != '0 results') {$all_interactions = $all_interactions.$interaction_line."<br>";}}
                if ($drugs[$i+5] != "Choose one") {
                    $interaction_text = getSelectDrugDrugInteraction($rx[$i+1],$rx[$i+5]);
                    $interaction_line =  $interaction_text[1]." ".$drugs[$i+1]." - ".$drugs[$i+5]." : ".$interaction_text[4];
                    if ($interaction_text[3] != '0 results') {$all_interactions = $all_interactions.$interaction_line."<br>";}}

                // drug 3 v 4, 5 and 6
                if ($drugs[$i+3] != "Choose one") {
                    $interaction_text = getSelectDrugDrugInteraction($rx[$i+2],$rx[$i+3]);
                    $interaction_line =  $interaction_text[1]." ".$drugs[$i+2]." - ".$drugs[$i+3]." : ".$interaction_text[4];
                    if ($interaction_text[3] != '0 results') {$all_interactions = $all_interactions.$interaction_line."<br>";}}
                if ($drugs[$i+4] != "Choose one") {
                    $interaction_text = getSelectDrugDrugInteraction($rx[$i+2],$rx[$i+4]);
                    $interaction_line =  $interaction_text[1]." ".$drugs[$i+2]." - ".$drugs[$i+4]." : ".$interaction_text[4];
                    if ($interaction_text[3] != '0 results') {$all_interactions = $all_interactions.$interaction_line."<br>";}}
                if ($drugs[$i+5] != "Choose one") {
                    $interaction_text = getSelectDrugDrugInteraction($rx[$i+2],$rx[$i+5]);
                    $interaction_line =  $interaction_text[1]." ".$drugs[$i+2]." - ".$drugs[$i+5]." : ".$interaction_text[4];
                    if ($interaction_text[3] != '0 results') {$all_interactions = $all_interactions.$interaction_line."<br>";}}

                // drug 4 v 5 and 6
                if ($drugs[$i+4] != "Choose one") {
                    $interaction_text = getSelectDrugDrugInteraction($rx[$i+3],$rx[$i+4]);
                    $interaction_line =  $interaction_text[1]." ".$drugs[$i+3]." - ".$drugs[$i+4]." : ".$interaction_text[4];
                    if ($interaction_text[3] != '0 results') {$all_interactions = $all_interactions.$interaction_line."<br>";}}
                if ($drugs[$i+5] != "Choose one") {
                    $interaction_text = getSelectDrugDrugInteraction($rx[$i+3],$rx[$i+5]);
                    $interaction_line =  $interaction_text[1]." ".$drugs[$i+3]." - ".$drugs[$i+5]." : ".$interaction_text[4];
                    if ($interaction_text[3] != '0 results') {$all_interactions = $all_interactions.$interaction_line."<br>";}}

                // drug 5 v 6
                if ($drugs[$i+5] != "Choose one") {
                    $interaction_text = getSelectDrugDrugInteraction($rx[$i+4],$rx[$i+5]);
                    $interaction_line =  $interaction_text[1]." ".$drugs[$i+4]." - ".$drugs[$i+5]." : ".$interaction_text[4];
                    if ($interaction_text[3] != '0 results') {$all_interactions = $all_interactions.$interaction_line."<br>";}}

                if ($all_interactions == '') {$all_interactions = '0 results';}

                $all_interactionssummary = 'Drug-Drug</b><br><span style="color:#212021;font-weight:normal">'.$all_interactions.'</span>';

                // Drug-Disease Summary Section
                $all_interactions = '';
                foreach ($diseases as $disease) {
                    foreach ($drugs as $drug) {
                        if ($drug  != "Choose one" && $disease != "Choose one") {
                            $RX = getRxCode($drug);
                            $icd = getDiseaseCode($disease);
                            $interaction_text = getDiseaseInteractions($RX,$icd);
                            $interaction_line =  $interaction_text[1]." ".$drug." - ".$disease." : ".$interaction_text[3]." - ".$interaction_text[4]." <i>(".$interaction_text[5].")</i><br>";
                            if ($interaction_text[5] != '0 results') {$all_interactions = $all_interactions.$interaction_line;
                            }}}}
                if ($all_interactions == '') {$all_interactions = '0 results<br>';}

                $drug_dis_sum = '<br><b>Drug-Disease</b><br><span style="color:#212021;font-weight:normal">'.$all_interactions.'</span>';

                // Drug-Gene Summary Section
                $dv = '';
                foreach ($drugs as $drug) {
                    if ($drug != "Choose one") {
                        $dvalert = getGeneInteractions($drug);
                        if ($dvalert != '0 results') {$dv = $dv.$dvalert;}
                    }}

                if ($dv == '') {$dv = "0 results<br>";}

                $druggenesummary = '<br><b>Drug-Gene</b><br><span style="color:#212021;font-weight:normal">'.$dv.'</span>';

                // Top Drug Alerts/Tips Section
                for ($i = 0; $i < 4; $i++) {
                    if ($drug_alert[$i] != "0 results")
                    {$allalerts = $allalerts.$drug_alert[$i];}}
                if ($allalerts == "") {$allalerts = $allalerts."0 results<br>";}
                $topdrugtips = "<br><b>Drug Alerts/Tips</b><br><span style='color:#212021;font-weight:normal'>".$allalerts."</span><br>";


                // Top Disease Tips Section
                $topdiseasetips = '<b>Disease Alerts/Tips</b><br><span style="color:#212021;font-weight:normal">0 results</span><br>';

                $summary = "SUMMARY&nbsp&nbsp&nbsp&nbsp&nbspInteractions & Tips/Alerts<br><br>".$all_interactionssummary.$drug_dis_sum.$druggenesummary.$topdrugtips.$topdiseasetips;

                {?> <th style = 'padding:5px; text-align:left; width:1000px; background-color:#ddd' valign="top"><?php }

                if ((isset($_POST['add'])) && ($_POST['hbtid'] != '')) {
                    $showdbinfo = 0;
                    $client[0] = $_POST['hbtid'];
                    $_SESSION['client'] = $client;
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
                    if ($_POST['disease1'] == 'Choose one') {$client[21] = '';} else {$client[21] = $_POST['disease1'];}
                    if ($_POST['disease2'] == 'Choose one') {$client[22] = '';} else {$client[22] = $_POST['disease2'];}
                    if ($_POST['disease3'] == 'Choose one') {$client[23] = '';} else {$client[23] = $_POST['disease3'];}
                    if ($_POST['disease4'] == 'Choose one') {$client[24] = '';} else {$client[24] = $_POST['disease4'];}
                    if ($_POST['disease5'] == 'Choose one') {$client[25] = '';} else {$client[25] = $_POST['disease5'];}
                    if ($_POST['disease6'] == 'Choose one') {$client[26] = '';} else {$client[26] = $_POST['disease6'];}
                    if ($_POST['sex'] == 'Choose one') {$client[41] = '';} else {$client[41] = $_POST['sex'];}
                    if ($_POST['age'] == 'Choose one') {$client[42] = '';} else {$client[42] = $_POST['age'];}

                    $client[52] = "<b>Baseline</b>&nbsp&nbsp&nbsp".date("d/m/Y")."&nbsp&nbsp<i>".$_SESSION['fullname']."</i><br><b>Drugs : </b>".$client[1]."&nbsp&nbsp&nbsp".$client[2]."&nbsp&nbsp&nbsp".$client[3]."&nbsp&nbsp&nbsp".$client[4]."&nbsp&nbsp&nbsp".$client[5]."&nbsp&nbsp&nbsp".$client[6]."&nbsp&nbsp&nbsp".$client[7]."&nbsp&nbsp&nbsp".$client[8]."&nbsp&nbsp&nbsp".$client[9]."&nbsp&nbsp&nbsp".$client[10]."&nbsp&nbsp&nbsp".$client[11]."&nbsp&nbsp&nbsp".$client[12]."&nbsp&nbsp&nbsp<b>Diseases : </b>".$client[21]."&nbsp&nbsp&nbsp".$client[22]."&nbsp&nbsp&nbsp".$client[23]."&nbsp&nbsp&nbsp".$client[24]."&nbsp&nbsp&nbsp".$client[25]."&nbsp&nbsp&nbsp".$client[26].$notes."<br><br>".$_SESSION['client'][0];
                    $_SESSION['client'][0] = $client[0];

                    $client[40] = 'Active';

                    if ($_POST['allergies'] == 'Choose one') {$client[43] = '';} else {$client[43] = $_POST['allergies'];}
                    if ($_POST['exercise'] == 'Choose one') {$client[44] = '';} else {$client[44] = $_POST['exercise'];}
                    if ($_POST['alcohol'] == 'Choose one') {$client[45] = '';} else {$client[45] = $_POST['alcohol'];}
                    if ($_POST['smoking'] == 'Choose one') {$client[46] = '';} else {$client[46] = $_POST['smoking'];}
                    if ($_POST['height1'] == 'Choose one') {$client[47] = '';} else {$client[47] = $_POST['height1'];}
                    if ($_POST['height2'] == 'Choose one') {$client[48] = '';} else {$client[48] = $_POST['height2'];}
                    if ($_POST['weight1'] == 'Choose one') {$client[49] = '';} else {$client[49] = $_POST['weight1'];}
                    if ($_POST['weight2'] == 'Choose one') {$client[50] = '';} else {$client[50] = $_POST['weight2'];}
                    if (isset($bmi)) {$client[51] = $bmi;} else {$client[51] = '';}

                    if ($_POST['notes'] != "") {$notes = "<br><b>Notes : </b>".$_POST['notes'];} else {$notes = "";}

                    addClient($client);}


                if( (isset($_POST['update'])) && ($_POST['hbtid'] != '') ) {
                    $showdbinfo = 0;
                    $client = $_SESSION['client'];
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
                    if ($_POST['disease1'] == 'Choose one') {$client[21] = '';} else {$client[21] = $_POST['disease1'];}
                    if ($_POST['disease2'] == 'Choose one') {$client[22] = '';} else {$client[22] = $_POST['disease2'];}
                    if ($_POST['disease3'] == 'Choose one') {$client[23] = '';} else {$client[23] = $_POST['disease3'];}
                    if ($_POST['disease4'] == 'Choose one') {$client[24] = '';} else {$client[24] = $_POST['disease4'];}
                    if ($_POST['disease5'] == 'Choose one') {$client[25] = '';} else {$client[25] = $_POST['disease5'];}
                    if ($_POST['disease6'] == 'Choose one') {$client[26] = '';} else {$client[26] = $_POST['disease6'];}
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

                    if ($_POST['notes'] != "") {$notes = "<br><b>Notes : </b>".$_POST['notes'];} else {$notes = "";}

                    $client[52] = "<b>Follow Up</b>&nbsp&nbsp&nbsp".date("d/m/Y")."&nbsp&nbsp<i>".$_SESSION['fullname']."</i><br><b>Drugs : </b>".$client[1]."&nbsp&nbsp&nbsp".$client[2]."&nbsp&nbsp&nbsp".$client[3]."&nbsp&nbsp&nbsp".$client[4]."&nbsp&nbsp&nbsp".$client[5]."&nbsp&nbsp&nbsp".$client[6]."&nbsp&nbsp&nbsp".$client[7]."&nbsp&nbsp&nbsp".$client[8]."&nbsp&nbsp&nbsp".$client[9]."&nbsp&nbsp&nbsp".$client[10]."&nbsp&nbsp&nbsp".$client[11]."&nbsp&nbsp&nbsp".$client[12]."&nbsp&nbsp&nbsp<b>Diseases : </b>".$client[21]."&nbsp&nbsp&nbsp".$client[22]."&nbsp&nbsp&nbsp".$client[23]."&nbsp&nbsp&nbsp".$client[24]."&nbsp&nbsp&nbsp".$client[25]."&nbsp&nbsp&nbsp".$client[26].$notes."<br><br>".$_SESSION['client'][0];
                    $_SESSION['client'][0] = $client[0];
                    $client[40] = 'active';
                    updateClient($client);}

                // ERROR MESSAGES
                # set $showhelp to 0 if buttons selected, ie not first screen
                if ( (isset($_POST['add'])) || (isset($_POST['find'])) || (isset($_POST['update'])) || (isset($_POST['search'])) ) {$showhelp = 0;}

                # show 'hbt id required' if none entered and Update buttons selected
                if ( (isset($_POST['update'])) && ($_POST['hbtid'] == '') ) {$showdbinfo = 1; $showhelp = 1;}
# show 'hbt id required' if none entered and Add/Find buttons selected
                else {if ( ( (isset($_POST['update'])) || (isset($_POST['add'])) || (isset($_POST['find'])) ) && ($_POST['hbtid'] == '') ) { $showdbinfo = 1; $showhelp = 1; }
# warning 'not found in db'
                else {if ((isset($_POST['find'])) && ($client[40] == ""))
                {$showdbinfo = 1; $showhelp = 1;}
# show 'at least one drug required' if none entered and Search button selected
                else {if ( (isset($_POST['search'])) && ($_POST['drug1'] == "Choose one") )
                {$showdbinfo = 1; $showhelp = 1;}
# show '2 or more drugs for interactions' if one only entered and Search button selected
                else {if ( (isset($_POST['search'])) && ($_POST['drug2'] == "Choose one") )
                {$showdbinfo = 1; $showhelp = 1;} }}}}

                # show help or summary if available
                if ($showhelp == 1) {echo $help1;}
                else {if ( (isset($_POST['add'])) || (isset($_POST['update'])) || (isset($_POST['find'])) || (isset($_POST['search'])) )
                {echo $summary;}}

                ?>
            </th></tr></table>
</form>
<?php // Javascript ?>
<script>

    // create tabs to display individual drug, disease, variant
    function openList(evt, listName) {
        var i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("tabcontent");
        for (i = 0; i < tabcontent.length; i++) {tabcontent[i].style.display = "none";}
        tablinks = document.getElementsByClassName("tablinks");
        for (i = 0; i < tablinks.length; i++) {tablinks[i].className = tablinks[i].className.replace(" active", "");}
        document.getElementById(listName).style.display = "block";
        evt.currentTarget.className += " active";}

    // create button to show or hide ALL drugs
    function showhideFunction() {
        var x = document.getElementById("showhide");
        if (x.style.display === "none") {x.style.display = "block";}
        else {x.style.display = "none";}}
    function showhideFunction2() {
        var x = document.getElementById("showhide2");
        if (x.style.display === "none") {x.style.display = "block";}
        else {x.style.display = "none";}}
    function showhideFunction3() {
        var x = document.getElementById("showhide3");
        if (x.style.display === "none") {x.style.display = "block";}
        else {x.style.display = "none";}}
    function showhideFunction4() {
        var x = document.getElementById("showhide4");
        if (x.style.display === "none") {x.style.display = "block";}
        else {x.style.display = "none";}}
    function showhideFunction5() {
        var x = document.getElementById("showhide5");
        if (x.style.display === "none") {x.style.display = "block";}
        else {x.style.display = "none";}}
    function showhideFunction6() {
        var x = document.getElementById("showhide6");
        if (x.style.display === "none") {x.style.display = "block";}
        else {x.style.display = "none";}}
</script>

<?php
// TAB/BUTTON SECTION
?>
<div class="tab">
    <?php
    if (!isset($_POST['clear'])) {

        if ( ($client[0] != "") && ($_POST['hbtid'] != "") ) {?> <button class="tablinks" onclick="openList(event, 'Client')">Client<br><?php echo "<b>".$_SESSION['client'][0]."</b>";$showdbinfo = 0; ?></button><?php }

        if ($_POST['drug1'] != "Choose one" && $_POST['drug1'] != "" && (isset($_POST['search'])))
        {?> <button class="tablinks" onclick="openList(event, 'Drug 1')">Drug #1<br><?php echo "<b>".$_POST['drug1']."</b>"; ?></button><?php }
        else {if ($drug1 != "Choose one" && $drug1 != "" && (!isset($_POST['search']))) {?> <button class="tablinks" onclick="openList(event, 'Drug 1')">Drug #1<br><?php echo "<b>".$drug1."</b>"; ?></button><?php }}

        if ($_POST['drug2'] != "Choose one" && $_POST['drug2'] != "" && (isset($_POST['search'])))
        {?> <button class="tablinks" onclick="openList(event, 'Drug 2')">Drug #2<br><?php echo "<b>".$_POST['drug2']."</b>"; ?></button><?php }
        else {if ($drug2 != "Choose one" && $drug2 != "" && (!isset($_POST['search']))) {?> <button class="tablinks" onclick="openList(event, 'Drug 2')">Drug #2<br><?php echo "<b>".$drug2."</b>"; ?></button><?php }}

        if ($_POST['drug3'] != "Choose one" && $_POST['drug3'] != "" && (isset($_POST['search'])))
        {?> <button class="tablinks" onclick="openList(event, 'Drug 3')">Drug #3<br><?php echo "<b>".$_POST['drug3']."</b>"; ?></button><?php }
        else {if ($drug3 != "Choose one" && $drug3 != "" && (!isset($_POST['search']))) {?> <button class="tablinks" onclick="openList(event, 'Drug 3')">Drug #3<br><?php echo "<b>".$drug3."</b>"; ?></button><?php }}

        if ($_POST['drug4'] != "Choose one" && $_POST['drug4'] != "" && (isset($_POST['search'])))
        {?> <button class="tablinks" onclick="openList(event, 'Drug 4')">Drug #4<br><?php echo "<b>".$_POST['drug4']."</b>"; ?></button><?php }
        else {if ($drug4 != "Choose one" && $drug4 != "" && (!isset($_POST['search']))) {?> <button class="tablinks" onclick="openList(event, 'Drug 4')">Drug #4<br><?php echo "<b>".$drug4."</b>"; ?></button><?php }}

        if ($_POST['drug5'] != "Choose one" && $_POST['drug5'] != "" && (isset($_POST['search'])))
        {?> <button class="tablinks" onclick="openList(event, 'Drug 5')">Drug #5<br><?php echo "<b>".$_POST['drug5']."</b>"; ?></button><?php }
        else {if ($drug5 != "Choose one" && $drug5 != "" && (!isset($_POST['search']))) {?> <button class="tablinks" onclick="openList(event, 'Drug 5')">Drug #5<br><?php echo "<b>".$drug5."</b>"; ?></button><?php }}

        if ($_POST['drug6'] != "Choose one" && $_POST['drug6'] != "" && (isset($_POST['search'])))
        {?> <button class="tablinks" onclick="openList(event, 'Drug 6')">Drug #6<br><?php echo "<b>".$_POST['drug6']."</b>"; ?></button><?php }
        else {if ($drug6 != "Choose one" && $drug6 != "" && (!isset($_POST['search']))) {?> <button class="tablinks" onclick="openList(event, 'Drug 6')">Drug #6<br><?php echo "<b>".$drug6."</b>"; ?></button><?php }}

        if ($_POST['disease1'] != "Choose one" && $_POST['disease1'] != "" && (isset($_POST['search']))) {?> <button class="tablinks" onclick="openList(event, 'Disease 1')">Disease 1<br><?php echo "<b>".$_POST['disease1']."</b>"; ?></button><?php } else {if ($disease1 != "Choose one" && $disease1 != "" && (!isset($_POST['search']))) {?> <button class="tablinks" onclick="openList(event, 'Disease 1')">Disease #1<br><?php echo "<b>".$disease1."</b>"; ?></button><?php }}

        if ($_POST['disease2'] != "Choose one" && $_POST['disease2'] != "" && (isset($_POST['search']))) {?> <button class="tablinks" onclick="openList(event, 'Disease 2')">Disease 2<br><?php echo "<b>".$_POST['disease2']."</b>"; ?></button><?php } else {if ($disease2 != "Choose one" && $disease2 != "" && (!isset($_POST['search']))) {?> <button class="tablinks" onclick="openList(event, 'Disease 2')">Disease #2<br><?php echo "<b>".$disease2."</b>"; ?></button><?php }}

        if ($_POST['disease3'] != "Choose one" && $_POST['disease3'] != "" && (isset($_POST['search']))) {?> <button class="tablinks" onclick="openList(event, 'Disease 3')">Disease 3<br><?php echo "<b>".$_POST['disease3']."</b>"; ?></button><?php } else {if ($disease3 != "Choose one" && $disease3 != "" && (!isset($_POST['search']))) {?> <button class="tablinks" onclick="openList(event, 'Disease 3')">Disease #3<br><?php echo "<b>".$disease3."</b>"; ?></button><?php }}

        if ($_POST['disease4'] != "Choose one" && $_POST['disease4'] != "" && (isset($_POST['search']))) {?> <button class="tablinks" onclick="openList(event, 'Disease 4')">Disease 4<br><?php echo "<b>".$_POST['disease4']."</b>"; ?></button><?php } else {if ($disease4 != "Choose one" && $disease4 != "" && (!isset($_POST['search']))) {?> <button class="tablinks" onclick="openList(event, 'Disease 4')">Disease #4<br><?php echo "<b>".$disease4."</b>"; ?></button><?php }}

    }?>


</div>
<div id="Client" class="tabcontent">
    <?php
    echo "<b>Client</b><br>".$shortbar."<b>ID </b>".$client[0]."&nbsp&nbsp&nbsp<b>Status </b>".$client[40]."&nbsp&nbsp&nbsp<b>Sex </b>".$client[41]."&nbsp&nbsp&nbsp<b>Age </b>".$client[42]."&nbsp&nbsp&nbsp<b>Allergies : </b>".$client[43]."&nbsp&nbsp&nbsp<b>Exercise : </b>".$client[44]."&nbsp&nbsp&nbsp<b>Alcohol : </b>".$client[45]." units pw&nbsp&nbsp&nbsp<b>Smoking : </b>".$client[46]."&nbsp&nbsp&nbsp<b>Height : </b>".$client[47]." ft ".$client[48]." in"."&nbsp&nbsp&nbsp<b>Weight : </b>".$client[49]." st ".$client[50]." llb&nbsp&nbsp&nbsp<b>BMI : </b>".$bmi." ";
    if ($bmi < 18.5) {echo $orange;}
    else {if ($bmi > 30) {echo $red;}
    else {if ($bmi > 24.9) {echo $orange;}
    else {echo $green;}}}

    echo "<br><br><b>Log</b><br>".$shortbar.$client[0];
    ?>
</div>

<?php
// Display drug pages
if (!isset($_POST['clear'])) {
if ($drugs[0] == "") {$drugs[0] = "Choose one";}
if ($drugs[1] == "") {$drugs[1] = "Choose one";}
if ($drugs[2] == "") {$drugs[2] = "Choose one";}
if ($drugs[3] == "") {$drugs[3] = "Choose one";}
if ($drugs[4] == "") {$drugs[4] = "Choose one";}
if ($drugs[5] == "") {$drugs[5] = "Choose one";}
if ($disease[0] == "") {$disease[0] = "Choose one";}
if ($disease[1] == "") {$disease[1] = "Choose one";}
if ($disease[2] == "") {$disease[2] = "Choose one";}
if ($disease[3] == "") {$disease[3] = "Choose one";}
?>

<?php # display drugs tabs x 12
for ($k = 1; $k < 2; $k++) { ?>
    <div id="<?php echo "Drug ".$k; ?>" class="tabcontent">
        <?php
        $i = 0;
        if ($drugs[$i] != "Choose one") {
            echo "<br><b>Drug Source and ID</b><br><br>";
            echo $lines[$i];
            if ($drug_alert[$i] != "0 results") {
                echo $shortbar."<br><b>Alerts : ".$drugs[$i]."</b><br>";
                echo $drug_alert[$i];}
            echo $shortbar."<br>".$db_icon."<b> Drug-Drug Interactions</b><br>";
            if (count($allinteractions[$i]) == 0) {echo "None available - The absence of an interaction does not necessarily mean no interactions exist.<br><br>";}
            else {
                # interactions with selected drugs
                echo "<br>Drug #".$k." <b>".$drugs[$i]."</b> with selected drugs :<br>";
                if ($drugs[$i+1] != "Choose one")
                {$interaction_text = getSelectDrugDrugInteraction($rx[$i],$rx[$i+1]);
                    echo "Drug #2 ".$interaction_text[1].$drugs[$i+1]." (Rx <a href=".$urlroot.$rx[$i].">".$rx[$i+1]."</a>) : ".$interaction_text[4]."<br>";}
                if ($drugs[$i+2] != "Choose one")
                {$interaction_text = getSelectDrugDrugInteraction($rx[$i],$rx[$i+2]);
                    echo "Drug #3 ".$interaction_text[1].$drugs[$i+2]." (Rx <a href=".$urlroot.$rx[$i].">".$rx[$i+2]."</a>) : ".$interaction_text[4]."<br>";}
                if ($drugs[$i+3] != "Choose one")
                {$interaction_text = getSelectDrugDrugInteraction($rx[$i],$rx[$i+3]);
                    echo "Drug #4 ".$interaction_text[1].$drugs[$i+3]." (Rx <a href=".$urlroot.$rx[$i].">".$rx[$i+3]."</a>) : ".$interaction_text[4]."<br>";}
                if ($drugs[$i+4] != "Choose one")
                {$interaction_text = getSelectDrugDrugInteraction($rx[$i],$rx[$i+4]);
                    echo "Drug #5 ".$interaction_text[1].$drugs[$i+4]." (Rx <a href=".$urlroot.$rx[$i].">".$rx[$i+4]."</a>) : ".$interaction_text[4]."<br>";}
                if ($drugs[$i+5] != "Choose one")
                {$interaction_text = getSelectDrugDrugInteraction($rx[$i],$rx[$i+5]);
                    echo "Drug #6 ".$interaction_text[1].$drugs[$i+5]." (Rx <a href=".$urlroot.$rx[$i].">".$rx[$i+5]."</a>) : ".$interaction_text[4]."<br>";}
                # interactions with all drugs
                echo "<br>Drug #1 <b>".$drugs[$i]."</b> with ALL ".count($allinteractions[$i])." drugs : <br>&nbsp&nbsp&nbsp&nbsp&nbsp<button onclick='showhideFunction()'>Click To View All</button><div id='showhide' style='display:none'>";
                echo "<br><i>'Ctrl' + 'f' to find specific drug</i><br>";
                for ($j = 0; $j < count($allinteractions[$i]); $j++) {echo " #".($j+1)." ".$green." ".$allinteractions[$i][$j]."<br>";}
                echo "</div>";}
            echo $shortbar.$dailymed[$i];
            #echo $shortbar.$drugbank_id[$i];
        }?>
    </div>
<?php } ?>

<div id="Drug 2" class="tabcontent">
    <?php
    $i = 1;
    if ($drugs[$i] != "Choose one") {
        echo "<br><b>Drug Source and ID</b><br><br>";
        echo $lines[$i];
        if ($drug_alert[$i] != "0 results") {
            echo $shortbar."<br><br><b>Alerts : ".$drugs[$i]."</b><br>";
            echo $drug_alert[$i];}
        echo $shortbar."<br>".$db_icon."<b> Drug-Drug Interactions</b><br>";
        # interactions with selected drugs
        echo "<br>Drug #2 <b>".$drugs[$i]."</b> with selected drugs :<br>";
        if ($drugs[$i-1] != "Choose one")
        {$interaction_text = getSelectDrugDrugInteraction($rx[$i],$rx[$i-1]);
            echo "Drug #1 ".$interaction_text[1].$drugs[$i-1]." (Rx <a href=".$urlroot.$rx[$i].">".$rx[$i-1]."</a>) : ".$interaction_text[4]."<br>";}
        if ($drugs[$i+1] != "Choose one")
        {$interaction_text = getSelectDrugDrugInteraction($rx[$i],$rx[$i+1]);
            echo "Drug #3 ".$interaction_text[1].$drugs[$i+1]." (Rx <a href=".$urlroot.$rx[$i].">".$rx[$i+1]."</a>) : ".$interaction_text[4]."<br>";}
        if ($drugs[$i+2] != "Choose one")
        {$interaction_text = getSelectDrugDrugInteraction($rx[$i],$rx[$i+2]);
            echo "Drug #4 ".$interaction_text[1].$drugs[$i+2]." (Rx <a href=".$urlroot.$rx[$i].">".$rx[$i+2]."</a>) : ".$interaction_text[4]."<br>";}
        if ($drugs[$i+3] != "Choose one")
        {$interaction_text = getSelectDrugDrugInteraction($rx[$i],$rx[$i+3]);
            echo "Drug #5 ".$interaction_text[1].$drugs[$i+3]." (Rx <a href=".$urlroot.$rx[$i].">".$rx[$i+3]."</a>) : ".$interaction_text[4]."<br>";}
        if ($drugs[$i+4] != "Choose one")
        {$interaction_text = getSelectDrugDrugInteraction($rx[$i],$rx[$i+4]);
            echo "Drug #6 ".$interaction_text[1].$drugs[$i+4]." (Rx <a href=".$urlroot.$rx[$i].">".$rx[$i+4]."</a>) : ".$interaction_text[4]."<br>";}
        # interactions with all drugs
        echo "<br>Drug #2 <b>".$drugs[$i]."</b> with ALL ".count($allinteractions[$i])." drugs : <br>&nbsp&nbsp&nbsp&nbsp&nbsp<button onclick='showhideFunction2()'>Click To View All</button><div id='showhide2' style='display:none'>";
        echo "<br><i>'Ctrl' + 'f' to find specific drug</i><br>";
        for ($j = 0; $j < count($allinteractions[$i]); $j++) {echo " #".($j+1)." ".$green." ".$allinteractions[$i][$j]."<br>";}
        echo "</div>";
        echo $shortbar.$dailymed[$i];
        #echo $shortbar.$drugbank_id[$i];
    }?>
</div>

<div id="Drug 3" class="tabcontent" >
    <?php
    $i = 2;
    if ($drugs[$i] != "Choose one") {
        echo "<br><b>Drug Source and ID</b><br><br>";
        echo $lines[$i];
        if ($drug_alert[$i] != "0 results") {
            echo $shortbar."<br><br><b>Alerts : ".$drugs[$i]."</b><br>";
            echo $drug_alert[$i];}
        echo $shortbar."<br>".$db_icon."<b> Drug-Drug Interactions</b><br>";
        # interactions with selected drugs
        echo "<br>Drug #3 <b>".$drugs[$i]."</b> with selected drugs :<br>";


        if ($drugs[$i-2] != "Choose one")
        {$interaction_text = getSelectDrugDrugInteraction($rx[$i],$rx[$i-2]);
            echo "Drug #1 ".$interaction_text[1].$drugs[$i-2]." (Rx <a href=".$urlroot.$rx[$i].">".$rx[$i-2]."</a>) : ".$interaction_text[4]."<br>";}
        if ($drugs[$i-1] != "Choose one")
        {$interaction_text = getSelectDrugDrugInteraction($rx[$i],$rx[$i-1]);
            echo "Drug #2 ".$interaction_text[1].$drugs[$i-1]." (Rx <a href=".$urlroot.$rx[$i].">".$rx[$i-1]."</a>) : ".$interaction_text[4]."<br>";}
        if ($drugs[$i+1] != "Choose one")
        {$interaction_text = getSelectDrugDrugInteraction($rx[$i],$rx[$i+1]);
            echo "Drug #4 ".$interaction_text[1].$drugs[$i+1]." (Rx <a href=".$urlroot.$rx[$i].">".$rx[$i+1]."</a>) : ".$interaction_text[4]."<br>";}
        if ($drugs[$i+2] != "Choose one")
        {$interaction_text = getSelectDrugDrugInteraction($rx[$i],$rx[$i+2]);
            echo "Drug #5 ".$interaction_text[1].$drugs[$i+2]." (Rx <a href=".$urlroot.$rx[$i].">".$rx[$i+2]."</a>) : ".$interaction_text[4]."<br>";}
        if ($drugs[$i+3] != "Choose one")
        {$interaction_text = getSelectDrugDrugInteraction($rx[$i],$rx[$i+3]);
            echo "Drug #6 ".$interaction_text[1].$drugs[$i+3]." (Rx <a href=".$urlroot.$rx[$i].">".$rx[$i+3]."</a>) : ".$interaction_text[4]."<br>";}
        # interactions with all drugs
        echo "<br>Drug #3 <b>".$drugs[$i]."</b> with ALL ".count($allinteractions[$i])." drugs : <br>&nbsp&nbsp&nbsp&nbsp&nbsp<button onclick='showhideFunction3()'>Click To View All</button><div id='showhide3' style='display:none'>";
        echo "<br><i>'Ctrl' + 'f' to find specific drug</i><br>";
        for ($j = 0; $j < count($allinteractions[$i]); $j++) {echo " #".($j+1)." ".$green." ".$allinteractions[$i][$j]."<br>";}
        echo "</div>";
        echo $shortbar.$dailymed[$i];
        echo $shortbar.$drugbank_id[$i];
    }?>
</div>

<div id="Drug 4" class="tabcontent" >
    <?php
    $i = 3;
    if ($drugs[$i] != "Choose one") {
        echo "<br><b>Drug Source and ID</b><br><br>";
        echo $lines[$i];
        if ($drug_alert[$i] != "0 results") {
            echo $shortbar."<br><br><b>Alerts : ".$drugs[$i]."</b><br>";
            echo $drug_alert[$i];}
        echo $shortbar."<br>".$db_icon."<b> Drug-Drug Interactions</b><br>";
        # interactions with selected drugs
        echo "<br>Drug #4 <b>".$drugs[$i]."</b> with selected drugs :<br>";
        if ($drugs[$i-3] != "Choose one")
        {$interaction_text = getSelectDrugDrugInteraction($rx[$i],$rx[$i-3]);
            echo "Drug #1 ".$interaction_text[1].$drugs[$i-3]." (Rx <a href=".$urlroot.$rx[$i].">".$rx[$i-3]."</a>) : ".$interaction_text[4]."<br>";}
        if ($drugs[$i-2] != "Choose one")
        {$interaction_text = getSelectDrugDrugInteraction($rx[$i],$rx[$i-2]);
            echo "Drug #2 ".$interaction_text[1].$drugs[$i-2]." (Rx <a href=".$urlroot.$rx[$i].">".$rx[$i-2]."</a>) : ".$interaction_text[4]."<br>";}
        if ($drugs[$i-1] != "Choose one")
        {$interaction_text = getSelectDrugDrugInteraction($rx[$i],$rx[$i-1]);
            echo "Drug #3 ".$interaction_text[1].$drugs[$i-1]." (Rx <a href=".$urlroot.$rx[$i].">".$rx[$i-1]."</a>) : ".$interaction_text[4]."<br>";}
        if ($drugs[$i+1] != "Choose one")
        {$interaction_text = getSelectDrugDrugInteraction($rx[$i],$rx[$i+1]);
            echo "Drug #5 ".$interaction_text[1].$drugs[$i+1]." (Rx <a href=".$urlroot.$rx[$i].">".$rx[$i+1]."</a>) : ".$interaction_text[4]."<br>";}
        if ($drugs[$i+2] != "Choose one")
        {$interaction_text = getSelectDrugDrugInteraction($rx[$i],$rx[$i+2]);
            echo "Drug #6 ".$interaction_text[1].$drugs[$i+2]." (Rx <a href=".$urlroot.$rx[$i].">".$rx[$i+2]."</a>) : ".$interaction_text[4]."<br>";}
        # interactions with all drugs
        echo "<br>Drug #4 <b>".$drugs[$i]."</b> with ALL ".count($allinteractions[$i])." drugs : <br>&nbsp&nbsp&nbsp&nbsp&nbsp<button onclick='showhideFunction4()'>Click To View All</button><div id='showhide4' style='display:none'>";
        echo "<br><i>'Ctrl' + 'f' to find specific drug</i><br>";
        for ($j = 0; $j < count($allinteractions[$i]); $j++) {echo " #".($j+1)." ".$green." ".$allinteractions[$i][$j]."<br>";}
        echo "</div>";
        echo $shortbar.$dailymed[$i];
        echo $shortbar.$drugbank_id[$i];
    }?>
</div>

<div id="Drug 5" class="tabcontent" >
    <?php
    $i = 4;
    if ($drugs[$i] != "Choose one") {
        echo "<br><b>Drug Source and ID</b><br><br>";
        echo $lines[$i];
        if ($drug_alert[$i] != "0 results") {
            echo $shortbar."<br><br><b>Alerts : ".$drugs[$i]."</b><br>";
            echo $drug_alert[$i];}
        echo $shortbar."<br>".$db_icon."<b> Drug-Drug Interactions</b><br>";
        # interactions with selected drugs
        echo "<br>Drug #5 <b>".$drugs[$i]."</b> with selected drugs :<br>";
        if ($drugs[$i-4] != "Choose one")
        {$interaction_text = getSelectDrugDrugInteraction($rx[$i],$rx[$i-4]);
            echo "Drug #1 ".$interaction_text[1].$drugs[$i-4]." (Rx <a href=".$urlroot.$rx[$i].">".$rx[$i-4]."</a>) : ".$interaction_text[4]."<br>";}
        if ($drugs[$i-3] != "Choose one")
        {$interaction_text = getSelectDrugDrugInteraction($rx[$i],$rx[$i-3]);
            echo "Drug #2 ".$interaction_text[1].$drugs[$i-3]." (Rx <a href=".$urlroot.$rx[$i].">".$rx[$i-3]."</a>) : ".$interaction_text[4]."<br>";}
        if ($drugs[$i-2] != "Choose one")
        {$interaction_text = getSelectDrugDrugInteraction($rx[$i],$rx[$i-2]);
            echo "Drug #3 ".$interaction_text[1].$drugs[$i-2]." (Rx <a href=".$urlroot.$rx[$i].">".$rx[$i-2]."</a>) : ".$interaction_text[4]."<br>";}
        if ($drugs[$i-1] != "Choose one")
        {$interaction_text = getSelectDrugDrugInteraction($rx[$i],$rx[$i-1]);
            echo "Drug #4 ".$interaction_text[1].$drugs[$i-1]." (Rx <a href=".$urlroot.$rx[$i].">".$rx[$i-1]."</a>) : ".$interaction_text[4]."<br>";}
        if ($drugs[$i+1] != "Choose one")
        {$interaction_text = getSelectDrugDrugInteraction($rx[$i],$rx[$i+1]);
            echo "Drug #6 ".$interaction_text[1].$drugs[$i+1]." (Rx <a href=".$urlroot.$rx[$i].">".$rx[$i+1]."</a>) : ".$interaction_text[4]."<br>";}
        # interactions with all drugs
        echo "<br>Drug #5 <b>".$drugs[$i]."</b> with ALL ".count($allinteractions[$i])." drugs : <br>&nbsp&nbsp&nbsp&nbsp&nbsp<button onclick='showhideFunction5()'>Click To View All</button><div id='showhide5' style='display:none'>";
        echo "<br><i>'Ctrl' + 'f' to find specific drug</i><br>";
        for ($j = 0; $j < count($allinteractions[$i]); $j++) {echo " #".($j+1)." ".$green." ".$allinteractions[$i][$j]."<br>";}
        echo "</div>";
        echo $shortbar.$dailymed[$i];
        echo $shortbar.$drugbank_id[$i];
    }?>
</div>

<div id="Drug 6" class="tabcontent" >
    <?php
    $i = 5;
    if ($drugs[$i] != "Choose one") {
        echo "<br><b>Drug Source and ID</b><br><br>";
        echo $lines[$i];
        if ($drug_alert[$i] != "0 results") {
            echo $shortbar."<br><br><b>Alerts : ".$drugs[$i]."</b><br>";
            echo $drug_alert[$i];}
        echo $shortbar."<br>".$db_icon."<b> Drug-Drug Interactions</b><br>";
        # interactions with selected drugs
        echo "<br>Drug #6 <b>".$drugs[$i]."</b> with selected drugs :<br>";
        if ($drugs[$i-5] != "Choose one")
        {$interaction_text = getSelectDrugDrugInteraction($rx[$i],$rx[$i-5]);
            echo "Drug #1 ".$interaction_text[1].$drugs[$i-5]." (Rx <a href=".$urlroot.$rx[$i].">".$rx[$i-5]."</a>) : ".$interaction_text[4]."<br>";}
        if ($drugs[$i-4] != "Choose one")
        {$interaction_text = getSelectDrugDrugInteraction($rx[$i],$rx[$i-4]);
            echo "Drug #2 ".$interaction_text[1].$drugs[$i-4]." (Rx <a href=".$urlroot.$rx[$i].">".$rx[$i-4]."</a>) : ".$interaction_text[4]."<br>";}
        if ($drugs[$i-3] != "Choose one")
        {$interaction_text = getSelectDrugDrugInteraction($rx[$i],$rx[$i-3]);
            echo "Drug #3 ".$interaction_text[1].$drugs[$i-3]." (Rx <a href=".$urlroot.$rx[$i].">".$rx[$i-3]."</a>) : ".$interaction_text[4]."<br>";}
        if ($drugs[$i-2] != "Choose one")
        {$interaction_text = getSelectDrugDrugInteraction($rx[$i],$rx[$i-2]);
            echo "Drug #4 ".$interaction_text[1].$drugs[$i-2]." (Rx <a href=".$urlroot.$rx[$i].">".$rx[$i-2]."</a>) : ".$interaction_text[4]."<br>";}
        if ($drugs[$i-1] != "Choose one")
        {$interaction_text = getSelectDrugDrugInteraction($rx[$i],$rx[$i-1]);
            echo "Drug #5 ".$interaction_text[1].$drugs[$i-1]." (Rx <a href=".$urlroot.$rx[$i].">".$rx[$i-1]."</a>) : ".$interaction_text[4]."<br>";}
        # interactions with all drugs
        echo "<br>Drug #6 <b>".$drugs[$i]."</b> with ALL ".count($allinteractions[$i])." drugs : <br>&nbsp&nbsp&nbsp&nbsp&nbsp<button onclick='showhideFunction6()'>Click To View All</button><div id='showhide6' style='display:none'>";
        echo "<br><i>'Ctrl' + 'f' to find specific drug</i><br>";
        for ($j = 0; $j < count($allinteractions[$i]); $j++) {echo " #".($j+1)." ".$green." ".$allinteractions[$i][$j]."<br>";}
        echo "</div>";
        echo $shortbar.$dailymed[$i];
        echo $shortbar.$drugbank_id[$i];
    }?>
</div>

<?php # display disease tabs x 6
for ($j = 1; $j < 7; $j++) { ?>
    <div id="<?php echo "Disease ".$j; ?>" class="tabcontent">
        <?php
        $all_interactions = '';
        for ($i = 0; $i < 6; $i++) {
            if ($drugs[$i] != "Choose one") {
                $rx = getRxCode($drugs[$i]);
                $icd = getDiseaseCode($diseases[$j-1]);
                $interaction_text = getDiseaseInteractions($rx,$icd);
                $interaction_line =  $interaction_text[1]." ".$drugs[$i]." - ".$diseases[$j-1]." : ".$interaction_text[3]." - ".$interaction_text[4]." <i>(".$interaction_text[5].")</i><br>";
                if ($interaction_text[5] != '0 results') {$all_interactions = $all_interactions.$interaction_line; }}}
        echo "<b>Alerts</b><br>".$all_interactions;
        echo "<br><b>Drug-Disease Interaction Guide</b>";
        echo getDiseaseGuide($diseases[$j-1]);
        ?>
    </div>
<?php } ?>


</body></html>
<?php } else {if ($showdbinfo == 1) {echo $dbinfo;}} ?>