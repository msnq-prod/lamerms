<?php
require_once __DIR__ . '/common/headSecure.php';

$PAGEDATA['pageConfig'] = ["TITLE" => "Dashboard", "BREADCRUMB" => false];

if (isset($_GET['i'])) {
    if ($AUTH->serverPermissionCheck("INSTANCES:FULL_PERMISSIONS_IN_INSTANCE")) {
        $_SESSION['instanceID'] = intval($_GET['i']); //It doesn't even bother to verify the instance ID as the user is trusted to be server admins
        header("Location: " . $CONFIG['ROOTURL'] . "?");
    } else {
        $GLOBALS['AUTH']->setInstance($_GET['i']);
        header("Location: " . $CONFIG['ROOTURL'] . "?");
    }
}

$PAGEDATA['WIDGETS'] = new statsWidgets(explode(",", $AUTH->data['users_widgets']), false);

echo $TWIG->render('dashboard.twig', $PAGEDATA);
