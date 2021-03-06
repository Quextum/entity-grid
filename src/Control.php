<?php
/**
 * Created by PhpStorm.
 * User: prosky
 * Date: 08.10.18
 * Time: 18:05
 */

namespace Quextum\EntityGrid;


use Nette\Application\UI\Presenter;

class Control
{
    /** @var  string */
    protected $id;

    protected $changed = false;

    public $deselect = [];

    /**
     * Control constructor.
     * @param string $id
     */
    public function __construct($id)
    {
        $this->id = $id;
    }


    public function deselect($id):self
    {
        $this->deselect[] = $id;
        $this->changed = true;
        return $this;
    }


    public function send(Presenter $presenter):self
    {
        $this->changed && $presenter->payload->grid[$this->id] = $this;
        return $this;
    }


}