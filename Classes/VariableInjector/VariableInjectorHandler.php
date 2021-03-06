<?php
namespace CosmoCode\SimpleForm\VariableInjector;

    /***************************************************************
     *  Copyright notice
     *
     *  (c) 2017 Christian Baer <chr.baer@gmail.com>
     *
     *  All rights reserved
     *
     *  This script is part of the TYPO3 project. The TYPO3 project is
     *  free software; you can redistribute it and/or modify
     *  it under the terms of the GNU General Public License as published by
     *  the Free Software Foundation; either version 3 of the License, or
     *  (at your option) any later version.
     *
     *  The GNU General Public License can be found at
     *  http://www.gnu.org/copyleft/gpl.html.
     *
     *  This script is distributed in the hope that it will be useful,
     *  but WITHOUT ANY WARRANTY; without even the implied warranty of
     *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
     *  GNU General Public License for more details.
     *
     *  This copyright notice MUST APPEAR in all copies of the script!
     ***************************************************************/

/**
 *
 *
 * @package simple_form
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class VariableInjectorHandler implements \TYPO3\CMS\Core\SingletonInterface
{

    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
     * @inject
     */
    protected $objectManager;

    /**
     * @var array
     */
    private $variableInjectors;

    /**
     * @var array
     */
    private $variableInjectorsConfiguration;

    /**
     * @var array
     */
    private $formPluginSettings;

    /**
     * create variableInjectors
     * TODO: write log if exception appears
     */
    public function createVariableInjectorsFromVariableInjectorsConfiguration()
    {
        $this->resetVariableInjectors();
        if (is_array($this->variableInjectorsConfiguration)) {
            foreach ($this->variableInjectorsConfiguration as $singleVariableInjectorConfiguration) {
                /** @var $variableInjector AbstractVariableInjector */
                try {
                    $variableInjector = $this->objectManager->get($singleVariableInjectorConfiguration['variableInjector']);
                    $variableInjector->setVariableInjectorConfiguration($singleVariableInjectorConfiguration['conf']);
                    $variableInjector->setFormPluginSettings($this->formPluginSettings);
                    $this->variableInjectors[] = $variableInjector;
                } catch (\Exception $exception) {
                }
            }
        }
    }

    /**
     * call finish function of all configured preProcessors
     */
    public function getAllInjectedVariables()
    {
        if (empty($this->variableInjectors)) {
            return array();
        }

        $allInjectedVariables = [];

        foreach ($this->variableInjectors as $variableInjector) {
            /** @var $variableInjector AbstractVariableInjector */
            $allInjectedVariables[] = $variableInjector->getInjectVariables();
        }

        return $allInjectedVariables;

    }

    /**
     * @param array $variableInjectorsConfiguration
     */
    public function setVariableInjectorsConfiguration($variableInjectorsConfiguration)
    {
        $this->variableInjectorsConfiguration = $variableInjectorsConfiguration;
    }

    /**
     * @return array
     */
    public function getVariableInjectorsConfiguration()
    {
        return $this->variableInjectorsConfiguration;
    }

    /**
     * pass complete settings (and especially flexform-values)
     *
     * @param array $formPluginSettings
     */
    public function setFormPluginSettings($formPluginSettings)
    {
        $this->formPluginSettings = $formPluginSettings;
    }

    /**
     * @return array
     */
    public function getFormPluginSettings()
    {
        return $this->formPluginSettings;
    }



    private function resetVariableInjectors()
    {
        $this->variableInjectors = array();
    }
}
