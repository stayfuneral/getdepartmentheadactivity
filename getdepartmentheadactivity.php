<?php

use Bitrix\Iblock\SectionTable;
use Bitrix\Main\Config\Option;
use Bitrix\Bizproc\FieldType;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Query\Query;
use Bitrix\Bizproc\Activity\PropertiesDialog;


Loc::loadMessages(__FILE__);

Loader::includeModule('iblock');

class CBPGetDepartmentHeadActivity extends CBPActivity
{
    public function __construct($name)
    {
        parent::__construct($name);

        $this->arProperties = [
            'Title' => '',
            'DepartmentId' => null,
            'HeadUser' => null,
            'HeadUserPrintable' => null
        ];

        $this->SetPropertiesTypes([
            'HeadUser' => [
                'Type' => 'user'
            ]
        ]);
    }

    private function getIblockStructure()
    {
        return (int) Option::get('intranet', 'iblock_structure');
    }

    public static function getDepartments()
    {
        $departments = [];
        $iblockId = Option::get('intranet', 'iblock_structure');

        $params = [
            'filter' => ['IBLOCK_ID' => $iblockId],
            'select' => ['ID', 'NAME', 'LEFT_MARGIN', 'DEPTH_LEVEL'],
            'order' => [
                'LEFT_MARGIN' => 'ASC',
                'DEPTH_LEVEL' => 'ASC'
            ],
            'group' => ['DEPTH_LEVEL']
        ];

        $deps = SectionTable::getList($params)->fetchAll();

        foreach ($deps as $dep) {
            $sep = str_repeat('.', ($dep['DEPTH_LEVEL'] - 1));
            $departmentId = $dep['ID'];
            $departmentName = $dep['NAME'];

            $departments[$departmentId] = $sep . $departmentName;
        }

        return $departments;
    }

    public static function GetPropertiesDialog($documentType, $activityName, $arWorkflowTemplate, $arWorkflowParameters, $arWorkflowVariables, $arCurrentValues = null, $formName = "")
    {
        $runtime = CBPRuntime::GetRuntime();
        $userService = $runtime->getUserService();

        self::setCurrentValues($arCurrentValues);

        $dialog = new PropertiesDialog(__FILE__, [
            'documentType' => $documentType,
            'activityName' => $activityName,
            'workflowTemplate' => $arWorkflowTemplate,
            'workflowParameters' => $arWorkflowParameters,
            'workflowVariables' => $arWorkflowVariables,
            'currentValues' => $arCurrentValues,
            'formName' => $formName
        ]);

        $dialog->setMap(self::getPropertiesDialogMap());

        $currentActivity = &CBPWorkflowTemplateLoader::FindActivityByName($arWorkflowTemplate, $activityName);

        if(is_array($currentActivity['Properties'])) {
            $arCurrentValues = array_merge($arCurrentValues, $currentActivity['Properties']);
        }

        $dialog->setRuntimeData([
            'formName' => $formName,
            'departments' => self::getDepartments(),
            'selectedDepartment' => $arCurrentValues['DepartmentId']
        ]);


        return $dialog;
    }

    public static function GetPropertiesDialogValues($documentType, $activityName, &$arWorkflowTemplate, &$arWorkflowParameters, &$arWorkflowVariables, $arCurrentValues, &$arErrors)
    {
        $arErrors = [];

        if(empty($arCurrentValues['DepartmentId'])) {
            $arErrors[] = [
                'code' => 'Empty',
                'message' => Loc::getMessage("GDHA_EMPTY_DEPARTMENT")
            ];
        }

        if(!empty($arErrors)) {
            return false;
        }

        $arProperties = [
            'DepartmentId' => $arCurrentValues['DepartmentId']
        ];

        $currentActivity = &CBPWorkflowTemplateLoader::FindActivityByName($arWorkflowTemplate, $activityName);
        $currentActivity['Properties'] = $arProperties;

        return true;
    }

    protected static function getPropertiesDialogMap()
    {
        return [
            'DepartmentId' => [
                'Name' => Loc::getMessage("GDHA_DEPARTMENT_ID"),
                'FieldName' => 'department_id',
                'Type' => FieldType::SELECT,
                'Required' => true
            ]
        ];
    }

    protected static function setCurrentValues(&$currentValues)
    {
        if(!is_array($currentValues)) {
            $currentValues = [
                'DepartmentId' => null
            ];
        }

        return $currentValues;
    }

    public function Execute()
    {
        $userService = $this->workflow->GetRuntime()->getUserService();
        $ufHead = $userService->getDepartmentHead($this->DepartmentId);

        $this->HeadUser = $this->prepareHeadUser($ufHead);
        $this->HeadUserPrintable = CBPHelper::ConvertUserToPrintableForm($ufHead);


        return CBPActivityExecutionStatus::Closed;
    }

    protected function prepareHeadUser($userId)
    {
        return 'user_' . $userId;

    }
}