<?php

foreach ($_SESSION as $key => $val) {
    unset($_SESSION[$key]);
}

redirect($site_url);