<?php
/**
 * Legacy wrapper that reuses the shared API bootstrap from the MVP codebase.
 * Keeping this indirection inside /legacy ensures archived endpoints can be
 * restored without touching src/.
 */
require_once __DIR__ . '/../../src/api/apiHead.php';

