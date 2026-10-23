
<?php
require_once "functions.php";

if ($["REQUEST_METHOD"] != "POST") {
    echo "Please submit the form first.";
    echo "<a href='index.html'> Back to Form</a>";
    exit;
}

// Get employee information
$name = trim($_POST["employee_name"] ?? "");
$id = trim($_POST["employee_id"] ?? "");
$gender = $_POST["gender"] ?? "";
$department = $_POST["department"] ?? "";
$type = $_POST["employment_type"] ?? "";

$basic = $_POST["basic_salary"] ?? "";
$allowance = $_POST["allowance"] ?? "";
$deduction = $_POST["deduction"] ?? "";
$years = $_POST["years_service"] ?? "";

$errors = [];

if ($name == "") {
    $errors[] = "Please enter employee name.";
}

if ($id == "") {
    $errors[] = "Please enter employee ID.";
}

if ($gender != "Male" && $gender != "Female") {
    $errors[] = "Please select gender.";
}

if ($department == "") {
    $errors[] = "Please select department.";
}

if ($type != "Full-Time" && $type != "Part-Time") {
    $errors[] = "Please select employment type.";
}
if ($basic == "" || !is_numeric($basic) || $basic <= 0) {
    $errors[] = "Enter a valid basic salary.";
}

if ($allowance == "" || !is_numeric($allowance) || $allowance < 0) {
    $errors[] = "Enter a valid allowance.";
}

if ($deduction == "" || !is_numeric($deduction) || $deduction < 0) {
    $errors[] = "Enter a valid deduction.";
}

if ($years == "" || !is_numeric($years) ||
    $years < 0 || floor((float)$years) != (float)$years) {
    $errors[] = "Enter valid years of service.";
}
if (count($errors) == 0) {
    $basic = (float)$basic;
    $allowance = (float)$allowance;
    $deduction = (float)$deduction;
    $years = (int)$years;

    $grossSalary = calculateGrossSalary($basic, $allowance);
    $bonus = calculateBonus($basic);
    $serviceBenefit = calculateServiceBenefit($basic, $years);
    $employmentAllowance = calculateEmploymentAllowance($basic, $type);

    $totalBenefits = calculateTotalBenefits(
        $bonus,
        $serviceBenefit,
        $employmentAllowance
    );

    if ($deduction > $grossSalary + $totalBenefits) {
        $errors[] = "Deduction is too high.";
    } else {
        $netSalary = calculateNetSalary(
            $grossSalary,
            $totalBenefits,
            $deduction
        );

        $salaryStatus = checkSalaryStatus($netSalary);
        $annualSalary = calculateAnnualSalary($netSalary);
    }
}
$fileName = "";
$fileType = "";
$fileSize = 0;
$uploadStatus = "Not uploaded";

if (!isset($_FILES["employee_document"]) ||
    $_FILES["employee_document"]["error"] == UPLOAD_ERR_NO_FILE) {

    $errors[] = "Please select a document.";

} elseif ($_FILES["employee_document"]["error"] != UPLOAD_ERR_OK) {

    $errors[] = "Document upload failed.";

} elseif ($_FILES["employee_document"]["size"] > 2 * 1024 * 1024) {

    $errors[] = "Document must be smaller than 2 MB.";

} else {
    $fileName = basename($_FILES["employee_document"]["name"]);
    $fileType = $_FILES["employee_document"]["type"];
    $fileSize = $_FILES["employee_document"]["size"];
}
if (count($errors) == 0) {
    $folder = ____ . "/uploads/";

    if (!($folder)) {
        ($folder, 0755, true);
    }

    $destination = $folder . $fileName;

    if (file_exists($destination)) {
        $errors[] = "This file already exists.";
    } elseif (move_uploaded_file(
        $_FILES["employee_document"]["tmp_name"],
        $destination
    )) {
        $uploadStatus = "Upload successful";
    } else {
        $errors[] = "Could not save document.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employee Registration Result</title>

    <style>
        body {
            font-family: Arial;
            background: #f2f5f9;
            padding: 20px;
        }

        .container {
            max-width: 900px;
            margin: auto;
            background: white;
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }

        th {
            background: #eaf0f8;
        }

        .error {
            color: red;
        }

        .success {
            color: green;
        }
    </style>
</head>

<body>
<div class="container">

<?php if (count($errors) > 0) { ?>

    <h1>Registration Errors</h1>

    <ul class="error">
        <?php
        foreach ($errors as $error) {
            echo "<li>" .
                htmlspecialchars($error, ENT_QUOTES, "UTF-8") .
                "</li>";
        }
        ?>
    </ul>

<?php } else { ?>

    <h1>Employee Registration Successful</h1>

    <p class="success">Employee registered successfully.</p>

    <table>
        <tr>
            <th>Employee Name</th>
            <td><?php echo htmlspecialchars($name, ENT_QUOTES, "UTF-8"); ?></td>
        </tr>
        <tr>
            <th>Employee ID</th>
            <td><?php echo htmlspecialchars($id, ENT_QUOTES, "UTF-8"); ?></td>
        </tr>
        <tr>
            <th>Gender</th>
            <td><?php echo htmlspecialchars($gender, ENT_QUOTES, "UTF-8"); ?></td>
        </tr>
        <tr>
            <th>Department</th>
            <td><?php echo htmlspecialchars($department, ENT_QUOTES, "UTF-8"); ?></td>
        </tr>
        <tr>
            <th>Employment Type</th>
            <td><?php echo htmlspecialchars($type, ENT_QUOTES, "UTF-8"); ?></td>
        </tr>
        <tr>
            <th>Years of Service</th>
            <td><?php echo $years; ?></td>
        </tr>
        <tr>
            <th>Basic Salary</th>
            <td>$<?php echo number_format($basic, 2); ?></td>
        </tr>
        <tr>
            <th>Allowance</th>
            <td>$<?php echo number_format($allowance, 2); ?></td>
        </tr>
        <tr>
            <th>Gross Salary</th>
            <td>$<?php echo number_format($grossSalary, 2); ?></td>
        </tr>
        <tr>
            <th>Bonus</th>
            <td>$<?php echo number_format($bonus, 2); ?></td>
        </tr>
        <tr>
            <th>Service Benefit</th>
            <td>$<?php echo number_format($serviceBenefit, 2); ?></td>
        </tr>
        <tr>
            <th>Employment Allowance</th>
            <td>$<?php echo number_format($employmentAllowance, 2); ?></td>
        </tr>
        <tr>
            <th>Total Benefits</th>
            <td>$<?php echo number_format($totalBenefits, 2); ?></td>
        </tr>
        <tr>
            <th>Deduction</th>
            <td>$<?php echo number_format($deduction, 2); ?></td>
        </tr>
        <tr>
            <th>Net Salary</th>
            <td>$<?php echo number_format($netSalary, 2); ?></td>
        </tr>
        <tr>
            <th>Salary Status</th>
            <td><?php echo htmlspecialchars($salaryStatus, ENT_QUOTES, "UTF-8"); ?></td>
        </tr>
        <tr>
            <th>Annual Salary</th>
            <td>$<?php echo number_format($annualSalary, 2); ?></td>
        </tr>
        <tr>
            <th>Document Name</th>
            <td><?php echo htmlspecialchars($fileName, ENT_QUOTES, "UTF-8"); ?></td>
        </tr>
        <tr>
            <th>Document Type</th>
            <td><?php echo htmlspecialchars($fileType, ENT_QUOTES, "UTF-8"); ?></td>
        </tr>
        <tr>
            <th>Document Size</th>
            <td><?php echo number_format($fileSize / 1024, 2); ?> KB</td>
        </tr>
        <tr>
            <th>Upload Status</th>
            <td><?php echo $uploadStatus; ?></td>
        </tr>
    </table>

<?php } ?>

    <p><a href="index.html">Back to Registration Form</a></p>

</div>
</body>
</html>