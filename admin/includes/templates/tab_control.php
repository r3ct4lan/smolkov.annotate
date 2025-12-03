<?php

/**
 * @see ../settings.php
 * @var array $arSettings
 * @var string $moduleId
 *
 * @global CMain $APPLICATION
 */

$tabControl = new CAdminTabControl('tabControl', $arSettings);
$tabControl->Begin(); ?>

<form method='POST' action='<?= $APPLICATION->GetCurPage() . '?' . http_build_query([
    'mid' => htmlspecialcharsbx($moduleId),
    'lang' => LANG,
]) ?>'>
    <?= bitrix_sessid_post(); ?>
    <?php foreach ($arSettings as $arTab) {
        $tabControl->BeginNextTab();
        foreach ($arTab['FIELDS'] as $fKey => $arField) {
            $fieldValue = ($arField['VALUE'] ?: $arField['DEFAULT']) ?: ''; ?>
            <tr>
                <td>
                    <label for='<?= $arField['NAME'] ?>'>
                        <?= $arField['TITLE'] ?>:
                    </label>
                </td>
                <td>
                    <?php switch ($arField['TYPE']) {
                        case 'checkbox':
                            $isChecked = $fieldValue == 'Y' || $fieldValue === true;
                            ?>
                            <input type='checkbox'
                                   value='Y'
                                   id='<?= $arField['NAME'] ?>'
                                   name='<?= $arField['NAME'] ?>'
                                <?= $isChecked ? 'checked' : '' ?>
                            />
                            <?php break;

                        case 'select': ?>
                            <select id='<?= $arField['NAME'] ?>'
                                    name='<?= $arField['NAME'] ?>'>
                                <?php foreach ($arField['OPTIONS'] as $value => $title) {
                                    $isSelected = $value == $fieldValue; ?>
                                    <option value='<?= $value ?>'
                                        <?= $isSelected ? 'selected' : '' ?>
                                    >
                                        <?= $title ?>
                                    </option>
                                <?php } ?>
                            </select>
                            <?php break;

                        case 'file_dialog':
                            $onClickFunction = 'openFileDialog_' . $fKey;
                            $fdConfig = array_merge($arField['FILE_DIALOG_OPTIONS'], [
                                'event' => $onClickFunction,
                                'arResultDest' => ['ELEMENT_ID' => $arField['NAME']],
                            ]);
                            CAdminFileDialog::ShowScript($fdConfig);
                            ?>
                            <input type='text'
                                   size='50'
                                   id='<?= $arField['NAME'] ?>'
                                   name='<?= $arField['NAME'] ?>'
                                   value='<?= $fieldValue ?>'
                            >
                            <input onClick='<?= $onClickFunction ?>()'
                                   type='button'
                                   name='browse'
                                   value='...'
                            >
                            <?php break;

                        case 'string':
                        default: ?>
                            <input type='text'
                                   size='30'
                                   id='<?= $arField['NAME'] ?>'
                                   name='<?= $arField['NAME'] ?>'
                                   value='<?= $fieldValue ?>'
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