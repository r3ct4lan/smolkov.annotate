<?php

/**
 * @var array $arSettings
 * @var string $moduleId
 *
 * @global CMain $APPLICATION
 */

$tabControl = new CAdminTabControl("tabControl", $arSettings);
$tabControl->Begin(); ?>

<form method="POST" action="<?= $APPLICATION->GetCurPage() . '?' . http_build_query([
    'mid' => htmlspecialcharsbx($moduleId),
    'lang' => LANG,
]) ?>">
    <?= bitrix_sessid_post(); ?>
    <?php foreach ($arSettings as $arTab) {
        $tabControl->BeginNextTab();
        foreach ($arTab['FIELDS'] as $arField) { ?>
            <tr>
                <td>
                    <label for="<?= $arField['NAME'] ?>">
                        <?= $arField['TITLE'] ?>:
                    </label>
                </td>
                <td>
                    <?php switch ($arField['TYPE']) {
                        case 'checkbox': ?>
                            <input type="checkbox"
                                   value="Y"
                                   id="<?= $arField['NAME'] ?>"
                                   name="<?= $arField['NAME'] ?>"
                            />
                            <?php break;

                        case 'text':
                        default: ?>
                            <input type="text"
                                   size="30"
                                   id="<?= $arField['NAME'] ?>"
                                   name="<?= $arField['NAME'] ?>"
                            />
                            <?php break;
                    } ?>
                </td>
            </tr>
            <?php
        }
        $tabControl->Buttons($arTab['BUTTONS']);
    }
    $tabControl->End(); ?>
</form>