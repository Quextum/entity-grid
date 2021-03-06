<?php

namespace Quextum\EntityGrid;


use Nette\Application\UI\Control;
use Nette\Application\UI\ITemplate;
use Nette\Application\UI\Presenter;
use Nette\Bridges\ApplicationLatte\Template;
use Nette\Database\Table\ActiveRow;
use Nette\Localization\ITranslator;
use Nette\Security\User;


/**
 * Class BaseControl
 * @package App\Common\Controls
 * @method onBeforeRender(BaseControl $this, Template $template);
 * @method onPresenterAttached(Presenter $presenter);
 * @property Template $template
 */
abstract class BaseControl extends Control
{
    /** @var  callable[] */
    public $onPresenterAttached;

    /** @var  callable[] */
    public $onBeforeRender;

    /** @var  ITranslator|null */
    protected $translator;

    /** @var string */
    protected $templateName = 'template.latte';

    /** @var  SessionData */
    protected $session;

    /** @var  BaseGrid */
    protected $grid;

    /** @var  Section */
    protected $section;

    /** @var string */
    protected $locale = 'en';

    /** @var  array */
    protected $customGlobalActions = [];


    public function __construct()
    {
        $this->monitor(Presenter::class, function (Presenter $presenter) {
            $this->presenterAttached($presenter);
            $this->onPresenterAttached($presenter);
        });
        $this->monitor(BaseGrid::class, function (BaseGrid $grid) {
            $this->grid = $grid;
            $this->session = $grid->getSession();
            $this->translator = $grid->getTranslator();
            /*$this->locale = $this->session->locale ?: $this->translator->getLocale();
            bdump($this->locale,'construct');*/
            $this->gridAttached($grid);
        });
        $this->monitor(Section::class, function (Section $section) {
            $this->section = $section;
        });
        $this->onBeforeRender[] = function () {
            $this->template->grid = $this->grid;
            $this->template->section = $this->section;
            $this->template->customGlobalActions = $this->customGlobalActions;
            $this->template->format = function (ActiveRow $entity) {
                $data = (object)$entity->toArray();
                return $data->label??$data->title??$data->name??$entity->getPrimary(false);
            };

        };
    }

    protected function presenterAttached(Presenter $presenter):void
    {

    }

    protected function gridAttached(BaseGrid $grid): void
    {

    }

    /**
     * @return mixed
     */
    public function getTemplateFile()
    {
        return __DIR__ . '/templates/' . $this->templateName;
    }

    /**
     * @param string $templateName
     * @return static
     */
    public function setTemplateName(string $templateName)
    {
        $this->templateName = $templateName;
        return $this;
    }

    /**
     * @return string
     */
    public function getTemplateName(): string
    {
        return $this->templateName;
    }

    protected function beforeRender():void
    {
        $this->onBeforeRender($this, $this->template);
    }

    protected function createTemplate():ITemplate
    {
        /** @var Template $template */
        $template = parent::createTemplate();
        $template->addFilter('gridImage', [$this->grid->getImageLinkProvider(), 'provide']);
        return $template;
    }


    protected function afterRender():void
    {
    }

    public function render():void
    {
        $this->init();
        $this->beforeRender();
        $this->template->setFile($this->getTemplateFile());
        $this->template->render();
        $this->afterRender();
    }

    protected function init():void
    {

    }

    public function getUser(): User
    {
        return $this->getPresenter()->getUser();
    }

    public function lookupRow():?Row
    {
        return $this->lookup(Row::class, false);
    }


    /**
     * @return ITranslator
     */
    public function getTranslator(): ?ITranslator
    {
        return $this->translator;
    }

    /**
     * @param ITranslator $translator
     * @return static
     */
    public function setTranslator(?ITranslator $translator)
    {
        //$this->locale = $this->session->locale ?: $translator->getLocale();
        $this->translator = $translator;
        return $this;
    }

    /**
     * @param $source
     * @param null $key
     */
    public function addCustomGlobalAction($source, $key = null)
    {
        $this->customGlobalActions[$key] = $source;
    }
}