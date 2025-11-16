<?php
if (isset($arErrors) && is_array($arErrors)) {
    foreach ($arErrors as $error) {
        CAdminMessage::ShowMessage($error);
    }
}