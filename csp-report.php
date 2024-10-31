<?php
$reportData = file_get_contents('php://input');

$cspReport = json_decode($reportData, true);


if ($cspReport && isset($cspReport['csp-report'])) {
    $violatedDirective = $cspReport['csp-report']['violated-directive'] ?? 'N/A';
    $blockedUri = $cspReport['csp-report']['blocked-uri'] ?? 'N/A';
    $documentUri = $cspReport['csp-report']['document-uri'] ?? 'N/A';
    $userToken = $_GET['token'] ?? 'unknown';


    $logMessage = "User Token: $userToken | Document URI: $documentUri | Violated Directive: $violatedDirective | Blocked URI: $blockedUri\n";
    file_put_contents('csp-violations.log', $logMessage, FILE_APPEND);
} else {
    error_log("Invalid CSP report received.");
}
?>
