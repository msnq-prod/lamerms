<?php
/**
 * Legacy shim for the shared EmailHandler base class.
 * This allows archived providers to extend the same implementation
 * without reaching into src/ directly.
 */
require_once __DIR__ . '/../../../../src/common/libs/Email/EmailHandler.php';

