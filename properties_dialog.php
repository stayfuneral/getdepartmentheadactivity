<?php

use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

?>

<style>
    td.left {
        text-align: right;
        width: 40%;
    }

    td.right {
        width: 60%;
    }
</style>
<tr>
    <td class="left"><span class="adm-required-field"><?= Loc::getMessage("GDHA_DEPARTMENT") ?></span></td>
    <td class="right">
        <select name="DepartmentId" id="">
            <option></option>
            <?php foreach ($departments as $depId => $depName):?>
                <option
                        <?= $selectedDepartment == $depId ? 'selected' : ''?>
                        value="<?=$depId?>">
                    <?=$depName?>
                </option>
            <?php endforeach;?>
        </select>
    </td>
</tr>


