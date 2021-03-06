<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Contact\Components
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-contact
 */

namespace SilverWare\Contact\Components;

use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\CompositeField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\Tab;
use SilverWare\Components\BaseComponent;
use SilverWare\Contact\Model\ContactItem;

/**
 * An extension of the base component class for a contact component.
 *
 * @package SilverWare\Contact\Components
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-contact
 */
class ContactComponent extends BaseComponent
{
    /**
     * Human-readable singular name.
     *
     * @var string
     * @config
     */
    private static $singular_name = 'Contact Component';
    
    /**
     * Human-readable plural name.
     *
     * @var string
     * @config
     */
    private static $plural_name = 'Contact Components';
    
    /**
     * Description of this object.
     *
     * @var string
     * @config
     */
    private static $description = 'A component which shows contact information';
    
    /**
     * Icon file for this object.
     *
     * @var string
     * @config
     */
    private static $icon = 'silverware-contact/admin/client/dist/images/icons/ContactComponent.png';
    
    /**
     * Defines an ancestor class to hide from the admin interface.
     *
     * @var string
     * @config
     */
    private static $hide_ancestor = BaseComponent::class;
    
    /**
     * Maps field names to field types for this object.
     *
     * @var array
     * @config
     */
    private static $db = [
        'HeadingLevel' => 'Varchar(2)',
        'ShowIcons' => 'Boolean'
    ];
    
    /**
     * Defines the has-many associations for this object.
     *
     * @var array
     * @config
     */
    private static $has_many = [
        'Items' => ContactItem::class
    ];
    
    /**
     * Defines the default values for the fields of this object.
     *
     * @var array
     * @config
     */
    private static $defaults = [
        'ShowIcons' => 1
    ];
    
    /**
     * Defines the allowed children for this object.
     *
     * @var array|string
     * @config
     */
    private static $allowed_children = 'none';
    
    /**
     * Defines the default heading level to use.
     *
     * @var array
     * @config
     */
    private static $heading_level_default = 'h4';
    
    /**
     * Answers a list of field objects for the CMS interface.
     *
     * @return FieldList
     */
    public function getCMSFields()
    {
        // Obtain Field Objects (from parent):
        
        $fields = parent::getCMSFields();
        
        // Insert Items Tab:
        
        $fields->insertAfter(
            Tab::create(
                'Items',
                $this->fieldLabel('Items')
            ),
            'Main'
        );
        
        // Add Items Grid Field to Tab:
        
        $fields->addFieldToTab(
            'Root.Items',
            GridField::create(
                'Items',
                $this->fieldLabel('Items'),
                $this->Items(),
                GridFieldConfig_RecordEditor::create()
            )
        );
        
        // Define Placeholder:
        
        $placeholder = _t(__CLASS__ . '.DROPDOWNDEFAULT', '(default)');
        
        // Create Style Fields:
        
        $fields->addFieldsToTab(
            'Root.Style',
            [
                CompositeField::create([
                    DropdownField::create(
                        'HeadingLevel',
                        $this->fieldLabel('HeadingLevel'),
                        $this->getTitleLevelOptions()
                    )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholder),
                ])->setName('ContactComponentStyle')->setTitle($this->i18n_singular_name())
            ]
        );
        
        // Create Options Fields:
        
        $fields->addFieldsToTab(
            'Root.Options',
            [
                CompositeField::create([
                    CheckboxField::create(
                        'ShowIcons',
                        $this->fieldLabel('ShowIcons')
                    )
                ])->setName('ContactComponentOptions')->setTitle($this->i18n_singular_name())
            ]
        );
        
        // Answer Field Objects:
        
        return $fields;
    }
    
    /**
     * Answers the labels for the fields of the receiver.
     *
     * @param boolean $includerelations Include labels for relations.
     *
     * @return array
     */
    public function fieldLabels($includerelations = true)
    {
        // Obtain Field Labels (from parent):
        
        $labels = parent::fieldLabels($includerelations);
        
        // Define Field Labels:
        
        $labels['ShowIcons'] = _t(__CLASS__ . '.SHOWICONS', 'Show icons');
        $labels['HeadingLevel'] = _t(__CLASS__ . '.HEADINGLEVEL', 'Heading level');
        
        // Answer Field Labels:
        
        return $labels;
    }
    
    /**
     * Answers an array of wrapper class names for the HTML template.
     *
     * @return array
     */
    public function getWrapperClassNames()
    {
        $classes = ['items'];
        
        $this->extend('updateWrapperClassNames', $classes);
        
        return $classes;
    }
    
    /**
     * Answers a list of the enabled items within the receiver.
     *
     * @return ArrayList
     */
    public function getEnabledItems()
    {
        return $this->Items()->filterByCallback(function ($item) {
            return $item->isEnabled();
        });
    }
    
    /**
     * Answers the first enabled item found matching the given class name.
     *
     * @param string $class
     *
     * @return ContactItem
     */
    public function getEnabledItemByClass($class)
    {
        return $this->getEnabledItems()->find('ClassName', $class);
    }
    
    /**
     * Answers the heading tag for the receiver.
     *
     * @return string
     */
    public function getHeadingTag()
    {
        if ($tag = $this->getField('HeadingLevel')) {
            return $tag;
        }
        
        return $this->config()->heading_level_default;
    }
    
    /**
     * Answers true if the object is disabled within the template.
     *
     * @return boolean
     */
    public function isDisabled()
    {
        if (!$this->getEnabledItems()->exists()) {
            return true;
        }
        
        return parent::isDisabled();
    }
}
