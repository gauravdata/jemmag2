<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Followupemail2\Block\Adminhtml\Event;

use Aheadworks\Followupemail2\Api\Data\EventInterface;
use Aheadworks\Followupemail2\Api\Data\EventInterfaceFactory;
use Aheadworks\Followupemail2\Api\EventRepositoryInterface;
use Aheadworks\Followupemail2\Model\Event\ProductCondition;
use Aheadworks\Followupemail2\Model\Event\ProductConditionConverter;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Widget\Form\Renderer\FieldsetFactory as RendererFieldsetFactory;
use Magento\Rule\Model\Condition\AbstractCondition as RuleAbstractCondition;
use Magento\Rule\Block\Conditions as BlockConditions;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;

/**
 * Class ProductConditions
 * @package Aheadworks\Followupemail2\Block\Adminhtml\Event
 * @codeCoverageIgnore
 */
class ProductConditions extends \Magento\Backend\Block\Widget\Form\Generic implements TabInterface
{
    /**
     * @var string
     */
    const FORM_NAME = 'aw_followupemail2_event_form';

    /**
     * @var string
     */
    protected $_nameInLayout = 'product_conditions';

    /**
     * @var RendererFieldsetFactory
     */
    private $rendererFieldsetFactory;

    /**
     * @var BlockConditions
     */
    private $conditions;

    /**
     * @var ProductConditionConverter
     */
    private $conditionConverter;

    /**
     * @var EventInterfaceFactory
     */
    private $eventInterfaceFactory;

    /**
     * @var EventRepositoryInterface
     */
    private $eventRepository;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param BlockConditions $conditions
     * @param RendererFieldsetFactory $rendererFieldsetFactory
     * @param ProductConditionConverter $conditionConverter
     * @param EventInterfaceFactory $eventInterfaceFactory
     * @param EventRepositoryInterface $eventRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        BlockConditions $conditions,
        RendererFieldsetFactory $rendererFieldsetFactory,
        ProductConditionConverter $conditionConverter,
        EventInterfaceFactory $eventInterfaceFactory,
        EventRepositoryInterface $eventRepository,
        array $data = []
    ) {
        $this->rendererFieldsetFactory = $rendererFieldsetFactory;
        $this->conditions = $conditions;
        $this->conditionConverter = $conditionConverter;
        $this->eventInterfaceFactory = $eventInterfaceFactory;
        $this->eventRepository = $eventRepository;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareForm()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                /** @var EventInterface $eventDataObject */
                $eventDataObject = $this->eventRepository->get($id);
            } catch (NoSuchEntityException $e) {
                /** @var EventInterface $eventDataObject */
                $eventDataObject = $this->eventInterfaceFactory->create();
            }
        } else {
            /** @var EventInterface $eventDataObject */
            $eventDataObject = $this->eventInterfaceFactory->create();
        }

        /** @var ProductCondition $productConditionModel */
        $productConditionModel = $this->conditionConverter->getCondition($eventDataObject);

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('event_');

        $form = $this->addFieldsetToTab($form, 'product_', $productConditionModel);

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Conditions');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Conditions');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Add fieldset to form
     *
     * @param \Magento\Framework\Data\Form $form
     * @param string $prefix
     * @param mixed $ruleModel
     * @return \Magento\Framework\Data\Form
     */
    private function addFieldsetToTab($form, $prefix, $ruleModel)
    {
        $fieldsetName = $prefix . 'conditions_fieldset';
        $fieldset = $form
            ->addFieldset($fieldsetName, [])
            ->setRenderer(
                $this->rendererFieldsetFactory->create()
                    ->setTemplate('Magento_CatalogRule::promo/fieldset.phtml')
                    ->setNewChildUrl(
                        $this->getUrl(
                            'catalog_rule/promo_catalog/newConditionHtml',
                            [
                                'form'   => $form->getHtmlIdPrefix() . $fieldsetName,
                                'form_namespace' => self::FORM_NAME
                            ]
                        )
                    )
            )
        ;

        $ruleModel->setJsFormObject($form->getHtmlIdPrefix() . $fieldsetName);

        $fieldset
            ->addField(
                $prefix . 'conditions',
                'text',
                [
                    'name' => $prefix . 'conditions',
                    'label' => __('Conditions'),
                    'title' => __('Conditions'),
                    'data-form-part' => self::FORM_NAME
                ]
            )
            ->setRule($ruleModel)
            ->setRenderer($this->conditions);

        $this->setConditionFormName(
            $ruleModel->getConditions(),
            self::FORM_NAME,
            $form->getHtmlIdPrefix() . $fieldsetName
        );

        return $form;
    }

    /**
     * Handles addition of form name to condition and its conditions
     *
     * @param RuleAbstractCondition $conditions
     * @param string $formName
     * @param string $jsFormObject
     * @return void
     */
    protected function setConditionFormName(RuleAbstractCondition $conditions, $formName, $jsFormObject)
    {
        $conditions->setFormName($formName);
        $conditions->setJsFormObject($jsFormObject);
        if ($conditions->getConditions() && is_array($conditions->getConditions())) {
            foreach ($conditions->getConditions() as $condition) {
                $this->setConditionFormName($condition, $formName, $jsFormObject);
            }
        }
    }
}
