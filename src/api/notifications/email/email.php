<?php
require_once __DIR__ . '/../../apiHead.php';
function sendEmail($user, $instanceID, $subject, $html = false, $template = false, $emailData = false) {
    global $DBLIB, $CONFIG, $TWIG, $bCMS, $CONFIGCLASS;
	if (!$user or $user["userData"]["users_email"] == '') return false; //If the user hasn't entered an E-Mail address yet

    if ($CONFIGCLASS->get('EMAILS_ENABLED') !== "Enabled") {
        return true;
    }

    if ($instanceID) {
        $DBLIB->join("instancePositions", "userInstances.instancePositions_id=instancePositions.instancePositions_id", "LEFT");
        $DBLIB->join("instances", "instancePositions.instances_id=instances.instances_id", "LEFT");
        $DBLIB->where("users_userid", $user["userData"]['users_userid']);
        $DBLIB->where("instances.instances_id", $instanceID);
        $DBLIB->where("userInstances_deleted", 0);
        $DBLIB->where("(userInstances.userInstances_archived IS NULL OR userInstances.userInstances_archived >= '" . date('Y-m-d H:i:s') . "')");
        $DBLIB->where("instances.instances_deleted", 0);
        $instance = $DBLIB->getone("userInstances", ["instances.instances_name", "instances.instances_address", "instances.instances_emailHeader"]);
    } else $instance = false;

    $outputHTML = $TWIG->render('api/notifications/email/email_template.twig', ["SUBJECT" => $subject, "HTML"=> $bCMS->cleanString($html), "CONFIG" => $CONFIG, "DATA" => $emailData, "TEMPLATE" => $template, "INSTANCE" => $instance, "FOOTER" => $CONFIGCLASS->get('EMAILS_FOOTER')]); // Subject is escaped by twig, but the HTML is not.

    $provider = $CONFIGCLASS->get('EMAILS_PROVIDER');
    if ($provider !== 'SMTP') {
        $providerName = is_string($provider) && $provider !== '' ? $provider : 'undefined';
        trigger_error(
            'Email provider "' . $providerName . '" is not supported in the MVP build; falling back to SMTP.',
            E_USER_WARNING
        );
    }

    require_once __DIR__ . '/../../../common/libs/Email/SMTPHandler.php';
    return SMTPEmailHandler::sendEmail($user, $subject, $outputHTML);
}

/** @OA\Get(
 *     path="/notifications/email/email.php", 
 *     summary="Email Notifications", 
 *     description="Send an email to the user. This returns a function to call rather than a response.", 
 *     operationId="emailNotifications", 
 *     tags={"notifications"}, 
 *     )
 */