<?php
require (dirname(__DIR__) . '/registration/config/database.php');

$connect = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

//Industry Populate Dropdown
$queryIndustry = "SELECT * FROM registration_type_of_industry";
$resultIndustry = mysqli_query($connect, $queryIndustry);
$optionsIndustry = "";

while($rowIndustry = mysqli_fetch_array($resultIndustry))
{
    $optionsIndustry = $optionsIndustry."<option value=$rowIndustry[0]>$rowIndustry[1]</option>";
}

//Industry Populate Dropdown
$queryTypeOfRegistration = "SELECT * FROM registration_type_of_registration";
$resultTypeOfRegistration = mysqli_query($connect, $queryTypeOfRegistration);
$optionsTypeOfRegistration = "";

while($rowTypeOfRegistration = mysqli_fetch_array($resultTypeOfRegistration))
{
    $optionsTypeOfRegistration = $optionsTypeOfRegistration."<option value=$rowTypeOfRegistration[0]>$rowTypeOfRegistration[1]</option>";
}


//PSME Chapter Populate Dropdown
$queryPSMEChapter = "SELECT * FROM registration_psme_chapter ORDER BY psme_chapter_description ASC";
$resultPSMEChapter = mysqli_query($connect, $queryPSMEChapter);
$optionsPSMEChapter = "";

while($rowPSMEChapter = mysqli_fetch_array($resultPSMEChapter))
{
    $optionsPSMEChapter = $optionsPSMEChapter."<option value=$rowPSMEChapter[0]>$rowPSMEChapter[1]</option>";
}

//Type Of Membership Populate Dropdown
$queryTypeOfMembership = "SELECT * FROM registration_type_of_membership";
$resultTypeOfMembership = mysqli_query($connect, $queryTypeOfMembership);
$optionsTypeOfMembership = "";

while($rowTypeOfMembership = mysqli_fetch_array($resultTypeOfMembership))
{
    $optionsTypeOfMembership = $optionsTypeOfMembership."<option value=$rowTypeOfMembership[0]>$rowTypeOfMembership[1]</option>";
}



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="assets/css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>PSME Registration</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800&amp;display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lora:400,700,400italic,700italic&amp;display=swap">
    <link rel="stylesheet" href="assets/fonts/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/Application-Form.css">
    <link rel="stylesheet" href="assets/css/select.css">
</head>

<body>
    <nav class="navbar navbar-light navbar-expand-lg fixed-top" id="mainNav">
        <div class="container"><a class="navbar-brand" href="index.php" style="background: url(&quot;assets/img/natconlogo.png&quot;) left / contain no-repeat;">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;PSME</a><button data-bs-toggle="collapse" data-bs-target="#navbarResponsive" class="navbar-toggler" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation"><i class="fa fa-bars"></i></button>
            <div class="collapse navbar-collapse" id="navbarResponsive">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">reGISTER</a></li>
                    <li class="nav-item"><a class="nav-link" href="faq.php">faqS</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact-us.php">Contact us</a></li>
                    <li class="nav-item"><a class="nav-link" href="request-for-soa.html">REQUEST FOR SOA</a></li>
                    <li class="nav-item"><a class="nav-link" href="freebie.php">Freebies</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <header class="masthead" style="background-image: url('assets/img/IMG_3939.JPG');opacity: 0.70;background-color: aquamarine;background-position: center;background-repeat: repeat;">
        <div class="overlay"></div>
        <div class="container">
            <div class="row">
                <div class="col-md-10 col-lg-8 mx-auto position-relative">
                    <div class="text-break site-heading" style="height: 875.062px;padding-top: 111px;padding-bottom: 226px;margin-bottom: -137px;">
                        <h1 style="color: #ffffff;text-shadow: 0px 0px #ffffff;border-width: 8px;border-style: none;height: 118.594px;font-size: 46px;">71st PSME NATIONAL CONVENTION</h1>
                        <p style="font-family: 'Open Sans', sans-serif;font-size: 17px;height: 106.5px;"><br><strong><em><label style="color: rgb(251, 251, 251);">"Empowering Filipino Mechanical Engineers Towards Sustainable GREEN Industry for a Better Philippines"</label></em></strong><br></p>
                        <h1 style="font-size: 30px;color: #ffffff;text-shadow: 0px 0px #ffffff;border-width: 8px;border-style: none;height: 230px;">OCTOBER 12-14, 2023<br>SMX CONVENTION CENTER, PASAY CITY, METRO MANILA, PHILIPPINES</h1><button class="btn btn-primary" type="button" onclick="window.location.href='company.php'">Company Registration here</button>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <div class="container">
        <div class="title">Personal Information</div><br>
        <div class="row">
	        <h1 style="font-size: 70%;">To register, please fill in all the required fields.</h3>
	</div>
        <label name="progressCompanyRegistrant" id="progressCompanyRegistrant"></label>
        <div class="content">
            <div class="form-user-details">
              <div class="row">
                <div class="form-input-box col-md-6">
                  <label class="form-details">Type of Registration</label><label style="color :red"> *</label>
                  <select name="typeOfRegistration" id="typeOfRegistration" class="form-input-box" aria-label="Default select example">
                    <option value="" selected>Type of Registration</option>
                    <?php echo $optionsTypeOfRegistration; ?>
                  </select>
                </div>
                <div class="form-input-box col-md-6">
                  <label class="form-details">Type of Registrant</label><label style="color :red"> *</label>
                  <select name="forRegistration" id="forRegistration" class="form-input-box" aria-label="Default select example">
                    <option value = "" selected>Type of Registrant</option>
                    <option value="individual">Individual</option>
                    <option value="company">Company</option>
                  </select>
                </div>
              </div>
              <div class="row">
                <div class="form-input-box col-md-3">
                  <label class="form-details">First Name</label><label style="color :red"> *</label>
                  <input name="firstName" id="firstName"type="text" class="form-input-box" placeholder="Enter your first name" >
                </div>
                <div class="form-input-box col-md-3">
                  <label class="form-details">Middle Name</label>
                  <input name="middleName" id="middleName" type="text" placeholder="Enter your middle name" >
                </div>
                <div class="form-input-box col-md-3">
                  <label class="form-details">Last Name</label><label style="color :red"> *</label>
                  <input name="lastName" id="lastName" type="text" placeholder="Enter your last name" >
                </div>
                <div class="form-input-box col-md-3">
                  <label class="form-details">Suffix (Leave blank if not applicable.)</label>
                  <select name="suffix" id="suffix" class="form-input-box" aria-label="Default select example">
                    <option value = "" selected>Suffix</option>
                    <option value="Jr.">Jr.</option>
                    <option value="Sr.">Sr.</option>
                    <option value="II">II</option>
                    <option value="III">III</option>
                    <option value="IV">IV</option>
                  </select>
                </div>
              </div>
                
              <div class="row">
                <div class="form-input-box col-md-4">
                  <label class="form-details">Gender</label><label style="color :red"> *</label>
                  <select name="gender" id="gender" class="form-input-box" aria-label="Default select example">
                    <option value = "" selected>Gender</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                  </select>
                </div>
                <div class="form-input-box col-md-4">
                  <label class="form-details">Date of Birth</label><label style="color :red"> *</label>
                  <input type="date" name="birthDate" id="birthDate">
                </div>
                <div class="form-input-box col-md-4">
                  <label class="form-details">Email Address</label><label style="color :red"> *</label>
                  <input name="emailAddress" id="emailAddress" type="text" placeholder="Enter your Email Address" >
                </div>
              </div>

              <div class="row">
                <div class="form-input-box col-md-4">
                  <label class="form-details">Complete Address</label><label style="color :red"> *</label>
                  <input name="completeAddress" id="completeAddress" type="text" placeholder="Enter your Complete Address" >
                </div>
                <div class="form-input-box col-md-4">
                  <label class="form-details">Zip Code</label><label style="color :red"> *</label>
                  <input name="zipCode" id="zipCode" type="text" placeholder="Enter your Zip Code" >
                </div>
                <div class="form-input-box col-md-4">
                  <label class="form-details">Contact Number</label><label style="color :red"> *</label>
                  <input name="contactNumber" id="contactNumber" type="text" placeholder="Enter your Contact Number" >
                </div>
              </div>

              <div class="row">
                <div class="form-input-box col-md-6">
                  <label class="form-details">Company Name</label><label style="color :red"> *</label>
                  <input name="companyName" id="companyName" type="text" placeholder="Enter your Company Name" >
                </div>
                <div class="form-input-box col-md-6">
                  <label class="form-details">Job Position</label><label style="color :red"> *</label>
                  <input name="jobTitle" id="jobTitle" type="text" placeholder="Enter your Job Position" >
                </div>
              </div>

              <div class="row">
                <div class="form-input-box col-md-6">
                  <label class="form-details">Sector</label><label style="color :red"> *</label>
                  <select name="sector" id="sector" class="form-input-box">
                    <option value = "" selected>Sector</option>
                    <option value="private_sector">Private Sector</option>
                    <option value="government">Government</option>
                  
                  </select>
                </div>
                <div class="form-input-box col-md-6">
                  <label class="form-details">Job Industry</label><label style="color :red"> *</label>
                  <select name="industry" id="industry" class="form-input-box">
                    <option value = "" selected>Industry</option>
                    <?php echo $optionsIndustry; ?>
                  </select>
                </div>
              </div>
              <div class="title">PSME Membership Verification</div><br>
              <div class="row">
                    <div class="form-input-box col-md-6">
                        <label class="form-details">Type of Membership</label><label style="color: red"> * </label>
                        <select name="typeOfMembership" id="typeOfMembership" class="form-input-box" aria-label="Default select example">
                        <option value= "" selected>Type of Membership</option>
                        <?php echo $optionsTypeOfMembership; ?>
                        </select>
                    </div>

                    <div class="form-input-box col-md-6">
                        <label class="form-details">PWD ID Number (Leave it blank if not applicable.)</label>
                        <input name="pwdIdNumber" id="pwdIdNumber" type="text" placeholder="Enter your PWD ID Number">
                    </div>
              </div>
              <div class="row">
                    <div class="form-input-box col-md-6">
                        <label class="form-details">PRC Sequence Number</label>
                        <input type="text" name="prcSequenceNumber" id="prcSequenceNumber" placeholder="Enter your PRC Sequence Number">
                    </div>
                    <div class="form-input-box col-md-6">
                        <label class="form-details">Select month you passed</label>
                        <select class="form-input-box" name="monthPassed" id="monthPassed" aria-label="Default select example">
                            <option value="" selected>Month Passed</option>
                            <option value="february">February</option>
                            <option value="august">August</option>
                        </select>
                    </div>
                </div>                
                
                <div class="row">
                    <div class="form-input-box col-md-6">
                        <label class="form-details">PSME Chapter</label>
                        <select name="psmeChapter" id="psmeChapter" class="forminput-box" aria-label="Default select example">
                            <option value="" selected>PSME Chapter</option>
                            <?php echo $optionsPSMEChapter; ?>
                        </select>
                    </div>
                </div>          

            </div>
            <div class="container">
                <div class="title">Professional Credential</div><br>
                
            </div>            
            <div class="form-button">
              <input style="background: green;" type="submit" value="Proceed" onclick="registrationCheck()">
            </div>
        </div>
      </div>
    
</body>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <!--<script src="assets/js/Application-Form-1.js"></script>
    <script src="assets/js/Application-Form.js"></script>
    <script src="assets/js/clean-blog.js"></script>-->
    <script type="text/javascript" src="js/jquery-3.0.0.min.js"></script>
    <script type="text/javascript" src="js/registration_validate.js"></script>
</html>