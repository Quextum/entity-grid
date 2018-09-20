<?php

namespace App\AdminModule\Controls\EntityGrid;

use App\Common\Controls\Multiplier;

/**
 * Class TActions
 * @package App\AdminModule\Controls\EntityGrid
 */
trait TActions
{

    protected static $ACTION_TYPES = [
        'globalActions', 'groupActions', 'actions'
    ];

    /** @var  Action[] */
    protected $globalActions = [];

    /** @var  Action[] */
    protected $groupActions = [];

    /** @var  Action[] */
    protected $actions = [];

    public function injectActions()
    {
        $this->onPresenterAttached[] = function () {
            /** @var BaseGrid $grid */
            $grid = $this->lookup(BaseGrid::class, false);
            if ($grid) {
                $this->globalActions = $grid->getGlobalActions();
                $this->groupActions = $grid->getGroupActions();
                $this->actions = $grid->getActions();
            }
        };
        $this->onBeforeRender[] = function () {
            $this->template->actions = $this->actions;
            $this->template->groupActions = $this->groupActions;
            $this->template->globalActions = $this->globalActions;
        };
    }

    /**
     * @return boolean
     */
    public function isSelectable(): bool
    {
        return (bool)$this->groupActions;
    }

    protected function createComponentAction():Multiplier
    {
        return new Multiplier(function ($action) {
            return new Button($this->actions[$action]);
        });
    }

    protected function createComponentGlobalAction():Multiplier
    {
        return new Multiplier(function ($action) {
            return new Button($this->globalActions[$action]);
        });
    }

    protected function createComponentGroupAction():Multiplier
    {
        return new Multiplier(function ($action) {
            return new Button($this->groupActions[$action]);
        });
    }

    protected function _addAction(string $type, string $name, callable $callback = null): Action
    {
        $action = $this->$type[$name] = new Action();
        if ($callback) {
            $action->onClick[] = $callback;
        }
        return $action;
    }

    public function addAction(string $name, callable $callback = null): Action
    {
        return $this->_addAction('actions', $name, $callback);
    }

    public function addGroupAction(string $name, callable $callback = null): Action
    {
        return $this->_addAction('groupActions', $name, $callback);
    }

    public function addGlobalAction(string $name, callable $callback = null): Action
    {
        return $this->_addAction('globalActions', $name, $callback);
    }

    /**
     * @return Action[]
     */
    public function getActions(): array
    {
        return $this->actions;
    }

    /**
     * @return Action[]
     */
    public function getGlobalActions(): array
    {
        return $this->globalActions;
    }

    /**
     * @return Action[]
     */
    public function getGroupActions(): array
    {
        return $this->groupActions;
    }

}
