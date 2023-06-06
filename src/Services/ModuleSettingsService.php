<?php
namespace Technique102\BitrixEventsAttributes\Services;

use Bitrix\Main\Loader;

final class ModuleSettingsService
{
    private array $classContainer = [];

    public function __construct()
    {
        $res = \CModule::GetList();
        while ($row = $res->Fetch()) {
            if (!Loader::includeModule($row['ID']))
                continue;

            $configuration = \Bitrix\Main\Config\Configuration::getInstance($row['ID']);
            if (!empty($classes = $configuration->get('eventHandlerClasses'))) {
                foreach ($classes as $class) {
                    $this->classContainer[] = $class;
                }
            }
        }
    }

    public function getEventHandlerClasses(): array
    {
        return $this->classContainer;
    }
}
