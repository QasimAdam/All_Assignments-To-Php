
<?php

function calculateGrossSalary($basicSalary, $allowance) {
    return $basicSalary + $allowance;
}

function calculateBonus($basicSalary) {
    if ($basicSalary >= 1000) {
        return $basicSalary * 0.10;
    } else if ($basicSalary >= 500) {
        return $basicSalary * 0.05;
    } else {
        return 0;
    }
}

function calculateServiceBenefit($basicSalary, $yearsOfService) {
    if ($yearsOfService >= 5) {
        return $basicSalary * 0.08;
    } else if ($yearsOfService >= 3) {
        return $basicSalary * 0.05;
    } else {
        return 0;
    }
}

function calculateEmploymentAllowance($basicSalary, $employmentType) {
    if ($employmentType == "Full-Time") {
        return $basicSalary * 0.06;
    } else if ($employmentType == "Part-Time") {
        return $basicSalary * 0.03;
    } else {
        return 0;
    }
}

function calculateTotalBenefits($bonus, $serviceBenefit, $employmentAllowance) {
    return $bonus + $serviceBenefit + $employmentAllowance;
}

function calculateNetSalary($grossSalary, $totalBenefits, $deduction) {
    return $grossSalary + $totalBenefits - $deduction;
}

function checkSalaryStatus($netSalary) {
    if ($netSalary >= 1500) {
        return "High Salary";
    } else if ($netSalary >= 800) {
        return "Medium Salary";
    } else {
        return "Low Salary";
    }
}

function calculateAnnualSalary($netSalary, $months = 12) {
    return $netSalary * $months;
}

?>